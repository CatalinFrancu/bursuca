<?php 

require_once '../lib/Util.php';

$userId = Util::getRequestParameter('userId');
$agentId = Util::getRequestParameter('agentId');
$all = Util::getRequestParameter('all');
$newGamePlayers = Util::getRequestParameter('newGamePlayers');

if ($newGamePlayers) {
  Util::requireLoggedIn();
  $user = Session::getUser();

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

    // Now make the game available for judging
    $game->status = Game::STATUS_NEW;
    $game->save();

    FlashMessage::add('Partida a fost creată. Ea va fi evaluată în curând.', 'info');
    Util::redirect('games');
  } catch (Exception $e) {
    FlashMessage::add($e->getMessage());
  }
}

SmartyWrap::assign('userId', $userId);
SmartyWrap::assign('agentId', $agentId);
SmartyWrap::assign('all', $all);
SmartyWrap::assign('pageTitle', 'partide');
SmartyWrap::addCss('select2', 'jqueryui', 'jqgrid');
SmartyWrap::addJs('select2', 'jqueryui', 'jqgrid');
SmartyWrap::display('games.tpl');

?>
