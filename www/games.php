<?php 

require_once '../lib/Util.php';

$userId = Util::getRequestParameter('userId');
$agentId = Util::getRequestParameter('agentId');
$all = Util::getRequestParameter('all');

$user = Session::getUser();

// By default, show the logged in user's games
if (!$userId && $user) {
  $userId = $user->id;
}

SmartyWrap::assign('userId', $userId);
SmartyWrap::assign('agentId', $agentId);
SmartyWrap::assign('pageTitle', 'partide');
SmartyWrap::addCss('select2');
SmartyWrap::addJs('select2');
SmartyWrap::display('games.tpl');

?>
