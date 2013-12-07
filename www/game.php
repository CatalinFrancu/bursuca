<?php 

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');
$game = Game::get_by_id($id);

if (!$game) {
  FlashMessage::add('Nu găsesc partida cu id-ul cerut.');
  Util::redirect('games');
}

if (!$game->isFinished()) {
  FlashMessage::add('Această partidă nu a fost încă jucată.', 'warning');
}

$players = Model::factory('Player')->where('gameId', $game->id)->order_by_asc('position')->find_many();
$playerRecords = array();

foreach ($players as $p) {
  $rec = array();
  $rec['player'] = $p;
  $rec['agent'] = Agent::get_by_id($p->agentId);
  $rec['user'] = User::get_by_id($rec['agent']->userId);
  $playerRecords[] = $rec;
}

$moves = Model::factory('Move')->where('gameId', $game->id)->order_by_asc('number')->find_many();

SmartyWrap::assign('game', $game);
SmartyWrap::assign('playerRecords', $playerRecords);
SmartyWrap::assign('moves', $moves);
SmartyWrap::assign('pageTitle', 'partide');
SmartyWrap::addJs('replay');
SmartyWrap::display('game.tpl');

?>
