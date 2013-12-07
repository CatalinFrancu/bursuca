<?php 

require_once '../lib/Util.php';

$userId = Util::getRequestParameter('userId');
$agentId = Util::getRequestParameter('agentId');
$all = Util::getRequestParameter('all');
$newGamePlayers = Util::getRequestParameter('newGamePlayers');

$user = Session::getUser();

if ($newGamePlayers) {
  Util::requireLoggedIn();
  try {
    $newGamePlayers = array_filter($newGamePlayers);
    if ((count($newGamePlayers) < Game::MIN_PLAYERS) || (count($newGamePlayers) > Game::MAX_PLAYERS)) {
      throw new Exception(sprintf('Partida trebuie să aibă între %s și %s jucători', Game::MIN_PLAYERS, Game::MAX_PLAYERS));
    }
    $agents = array();
    $mine = false;
    foreach ($newGamePlayers as $agentId) {
      $agent = Agent::get_by_id($agentId);
      $agents[] = $agent;
      $mine |= ($agent->userId == $user->id);
    }
    if (!$mine) {
      throw new Exception('Trebuie ca minim unul dintre agenți să îți aparțină.');
    }

    // We're good to go! Create the game and player objects
    $game = Game::create();
    $game->save();

    foreach ($agents as $i => $agent) {
      $player = Model::factory('Player')->create();
      $player->gameId = $game->id;
      $player->agentId = $agent->id;
      $player->position = $i + 1;
      $player->save();
    }

    FlashMessage::add('Partida a fost creată. Ea va fi evaluată în curând.', 'info');
    Util::redirect('games');
  } catch (Exception $e) {
    FlashMessage::add($e->getMessage());
  }
}

// Load the games to show.
// - When the $all, $agentId or $userId parameters are set, obey those
// - Otherwise, if a user is logged in, show that user's games
// - Otherwise, no user is logged in and we show all games
if ($all) {
  $games = Model::factory('Game');
} else if ($agentId) {
  $games = Model::factory('Game')->select('game.*')->distinct()
    ->join('player', array('player.gameId', '=', 'game.id'))
    ->where('player.agentId', $agentId);
} else if ($userId || $user) {
  $realUserId = $userId ? $userId : $user->id;
  $games = Model::factory('Game')->select('game.*')->distinct()
    ->join('player', array('player.gameId', '=', 'game.id'))
    ->join('agent', array('player.agentId', '=', 'agent.id'))
    ->where('agent.userId', $realUserId);
} else {
  $games = Model::factory('Game');
}

$games = $games->order_by_desc('created')->find_many();

// Load the agents and users for each game
$gameRecords = array();
foreach ($games as $g) {
  $agents = array();
  $users = array();
  $players = Model::factory('Player')->where('gameId', $g->id)->order_by_asc('position')->find_many();
  foreach ($players as $p) {
    $agent = Agent::get_by_id($p->agentId);
    $agents[] = $agent;
    $users[] = User::get_by_id($agent->userId);
  }
  $gameRecords[] = array('game' => $g, 'agents' => $agents, 'users' => $users);
}

SmartyWrap::assign('userId', $userId);
SmartyWrap::assign('agentId', $agentId);
SmartyWrap::assign('gameRecords', $gameRecords);
SmartyWrap::assign('pageTitle', 'partide');
SmartyWrap::addCss('select2');
SmartyWrap::addJs('select2');
SmartyWrap::display('games.tpl');

?>
