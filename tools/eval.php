<?php

require_once __DIR__ . '/../lib/Util.php';

define('START_CASH', 10);
define('WIN_CASH', 100);
define('MAX_DIE', 6);
define('PASS_MOVE', 'P 0 0');
define('MOVE_TIME_MILLIS', 1000);

// Types of (in)valid moves
define('MOVE_VALID', 0);
define('MOVE_SYNTAX_ERROR', 1);  // syntax is not [BSLR] [1-6] [1-6]
define('MOVE_BAD_DICE', 2);      // tried to play different dice
define('MOVE_CANNOT_BUY', 3);    // not enough cash to buy stock
define('MOVE_CANNOT_SELL', 4) ;  // not enough stock to sell
define('MOVE_CANNOT_LOWER', 5) ; // cannot lower price below 1

// Find the oldest pending game
$game = Model::factory('Game')->where('status', Game::STATUS_NEW)->order_by_asc('created')->find_one();
if (!$game) {
  exit;
}
$stockPrices = array(1 => $game->price1,
                     2 => $game->price2,
                     3 => $game->price3,
                     4 => $game->price4,
                     5 => $game->price5,
                     6 => $game->price6);

// Load data about the players
$players = array_values(Model::factory('Player')->where('gameId', $game->id)->order_by_asc('position')->find_many());
$engines = array();
foreach ($players as $i => $p) {
  $engines[] = new Engine($p);
}

// send the initial data to each engine
foreach ($engines as $i => $e) {
  $data = array(count($players), $e->player->position);
  foreach ($engines as $j => $other) {
    if ($j != $i) {
      $data[] = $other->agent->userId;
      $data[] = $other->agent->version;
    }
  }
  $e->jp->writeLine(implode(' ', $data));
  $e->jp->writeLine(implode(' ', $stockPrices));
}

// main game loop
$moves = array();
$numActivePrograms = count($players);
$turn = 0;
$curPlayer = count($players) - 1;
do {
  if (++$curPlayer == count($players)) {
    $turn++;
    $curPlayer = 0;
    print "*********************** TURN {$turn} ******************************\n";
  }
  $e = $engines[$curPlayer];

  // send dice values to the current player
  $d1 = rand(1, MAX_DIE);
  $d2 = rand(1, MAX_DIE);
  $e->jp->writeLine("$d1 $d2");

  // read the current player's move
  if ($e->jp->alive) {
    list($resp, $elapsed) = $e->jp->readLine(MOVE_TIME_MILLIS);
    if (!$e->jp->alive || ($e->validMove($resp, $d1, $d2, $stockPrices) != MOVE_VALID)) {
      $resp = PASS_MOVE;
      $e->jp->kill(Player::REASON_BAD_MOVE);
      $e->player->rank = $numActivePrograms--;
    }
  } else {
    $resp = PASS_MOVE;
  }

  // create a move
  $m = Model::factory('Move')->create();
  $m->gameId = $game->id;
  $m->number = count($moves) + 1;
  $m->time = $elapsed;
  list($m->action, $m->arg, $m->company) = explode(' ', $resp);
  $moves[] = $m;

  // relay the current player's move to other players
  foreach ($engines as $j => $other) {
    if ($j != $curPlayer) {
      $other->jp->writeLine($resp);
    }
  }

  // make the move
  $e->makeMove($resp, $stockPrices);

  printGameState($engines, $stockPrices);
} while (($e->cash < WIN_CASH) && ($numActivePrograms > 1));

$winner = $e;

// Process cleanup
foreach ($engines as $e) {
  $e->jp->kill(Player::REASON_GAME_OVER);
  $e->player->exitCode = $e->jp->exitCode;
  $e->player->killReason = $e->jp->killReason;
}

// Sort the engines by rank and cash and assign ranks to those that lived to the end of the game
usort($engines, 'cmp');
$rank = 1;
foreach ($engines as $e) {
  if (!$e->player->rank) {
    $e->player->rank = $rank++;
  }
}

// Save the game
$game->status = Game::STATUS_FINISHED;
$game->save();

// Save the moves
foreach ($moves as $m) {
  $m->save();
}

// Update ELO ratings. This is a bit iffy because we can have the same engine playing twice.
// First, create a map of agentId -> change in points
$eloMap = array();
foreach ($engines as $e) {
  $eloMap[$e->agent->id] = 0;
}

foreach ($engines as $e) {
  if (($e->agent->id != $winner->agent->id) && $winner->agent->rated && $e->agent->rated) {
    $change = Elo::ratingChange($winner->agent->elo, $e->agent->elo);
    $eloMap[$winner->agent->id] += $change;
    $eloMap[$e->agent->id] -= $change;
  }
}

// Now actually update the ratings. Note that the same agent may appear several times in the game.
// We will save each agent, but that is ok as long as they all have the same ELO.
foreach ($engines as $e) {
  $e->player->eloStart = $e->agent->elo;
  $e->agent->elo += $eloMap[$e->agent->id];
  $e->player->eloEnd = $e->agent->elo;
}

// Save the players and agents
foreach ($engines as $e) {
  $e->player->save();
  $e->agent->save();
}

// Copy persistent data files
foreach ($engines as $e) {
  $e->jp->saveDataFile($e->agent->getFullDataName());
}

print "\nFinal rankings:\n";
foreach ($engines as $e) {
  printf("%d. %s v%d (%s) exit code %d kill reason [%s] rating: %d %+d = %d\n",
         $e->player->rank, $e->user->username, $e->agent->version, $e->agent->name,
         $e->player->exitCode, $e->player->getKillReason(),
         $e->player->eloStart, $e->player->eloEnd - $e->player->eloStart, $e->player->eloEnd);
}

/**************************************************************************/

// An engine is a tuple of (User, Agent, Player, JailedProcess) with game logic.
class Engine {
  public $user;   // author of the program
  public $agent;  // program
  public $player; // $player->position is the only thing we need
  public $jp;     // jailed process

  public $cash;   // current amount of cash
  public $stock;  // array of stock counts

  function __construct($player) {
    $this->player = $player;
    $this->agent = Agent::get_by_id($player->agentId);
    $this->user = User::get_by_id($this->agent->userId);
    $this->jp = new JailedProcess($this->agent->getFullBinaryName(), $this->agent->getFullDataName(), $player->position);
    $this->cash = START_CASH;
    $this->stock = array_fill(1, MAX_DIE, 0);
  }

  function validMove($s, $d1, $d2, $stockPrices) {
    if (!preg_match('/^[BSLR] [1-6] [1-6]$/', $s)) {
      return MOVE_SYNTAX_ERROR;
    }
    list($action, $arg, $company) = explode(' ', $s);
    if ((($arg != $d1) || ($company != $d2)) &&
        (($arg != $d2) || ($company != $d1))) {
      return MOVE_BAD_DICE;
    }
    switch ($action) {
      case 'B': return ($arg * $stockPrices[$company] <= $this->cash) ? MOVE_VALID : MOVE_CANNOT_BUY;
      case 'S': return ($this->stock[$company] >= $arg) ? MOVE_VALID : MOVE_CANNOT_SELL;
      case 'L': return ($stockPrices[$company] > $arg) ? MOVE_VALID : MOVE_CANNOT_LOWER;
      case 'R': return MOVE_VALID;
    }
  }

  function makeMove($s, &$stockPrices) {
    list($action, $arg, $company) = explode(' ', $s);
    switch($action) {
      case 'B':
        $this->stock[$company] += $arg;
        $this->cash -= $arg * $stockPrices[$company];
        $stockPrices[$company] += (int)($arg / 3);
        break;

      case 'S':
        $this->stock[$company] -= $arg;
        $this->cash += $arg * $stockPrices[$company];
        $stockPrices[$company] -= (int)($arg / 3);
        $stockPrices[$company] = max($stockPrices[$company], 1);
        break;

      case 'L':
        $stockPrices[$company] -= $arg;
        break;

      case 'R':
        $stockPrices[$company] += $arg;
        break;
    }
  }
}

function cmp($e1, $e2) {
  if ($e1->player->rank < $e2->player->rank) {
    return -1;
  } else if ($e1->player->rank > $e2->player->rank) {
    return 1;
  } else {
    return $e2->cash - $e1->cash;
  }
}

function printGameState($engines, $stockPrices) {
  printf("+-----------+-------+%s+\n", str_repeat('-----------', count($engines)));
  printf("|           | price |");
  foreach ($engines as $e) {
    printf("  Player %d ", $e->player->position);
  }
  printf("|\n");

  printf("+-----------+-------+%s+\n", str_repeat('-----------', count($engines)));

  for ($company = 1; $company <= MAX_DIE; $company++) {
    printf("| Company %d | %3d   |", $company, $stockPrices[$company]);
    foreach ($engines as $e) {
      printf("%6d     ", $e->stock[$company]);
    }
    printf("|\n");
  }
  printf("+-----------+-------+%s+\n", str_repeat('-----------', count($engines)));
  printf("| cash              |");
  foreach ($engines as $e) {
    printf("%6d     ", $e->cash);
  }
  printf("|\n");
  printf("+-------------------+%s+\n", str_repeat('-----------', count($engines)));
}

?>
