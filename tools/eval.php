<?php

require_once __DIR__ . '/../lib/Util.php';

define('START_CASH', 10);
define('WIN_CASH', 100);
define('MAX_DIE', 6);
define('PASS_MOVE', 'P 0 0');

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

foreach ($engines as $e) {
  printf("%d. %s v%d (%s)\n", $e->player->position, $e->user->username, $e->agent->version, $e->agent->name);
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
    $resp = $e->jp->readLine();
    if (!$e->jp->alive || ($e->validMove($resp, $d1, $d2, $stockPrices) != MOVE_VALID)) {
      $resp = PASS_MOVE;
      $e->jp->kill();
      $numActivePrograms--;
    }
  } else {
    $resp = PASS_MOVE;
  }

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

foreach ($engines as $e) {
  $e->jp->kill();
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
    $this->jp = new JailedProcess($this->agent->getFullBinaryName(), $this->agent->getFullDataName, $player->position);
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
