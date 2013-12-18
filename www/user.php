<?php

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');
$user = User::get_by_id($id);
if (!$user) {
  FlashMessage::add('Utilizatorul căutat nu există.');
  Util::redirect(Util::$wwwRoot);
}
$gameIds = Model::factory('Game')
  ->select('game.id')
  ->distinct()
  ->join('player', array('game.id', '=', 'player.gameId'))
  ->join('agent', array('player.agentId', '=', 'agent.id'))
  ->where('game.status', Game::STATUS_FINISHED)
  ->where('agent.userId', $user->id)
  ->find_array();

$agents = Model::factory('Agent')->where('userId', $user->id)->order_by_desc('version')->find_many();

SmartyWrap::assign('agents', $agents);
SmartyWrap::assign('displayUser', $user);
SmartyWrap::assign('numGames', count($gameIds));
SmartyWrap::assign('pageTitle', "Utilizator: {$user->username}");
SmartyWrap::display('user.tpl');

?>
