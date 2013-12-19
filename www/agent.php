<?php

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');
$agent = Agent::get_by_id($id);

if (!$agent) {
  FlashMessage::add('Agentul căutat nu există.');
  Util::redirect(Util::$wwwRoot);
}

$owner = User::get_by_id($agent->userId);
$user = Session::getUser();
$showSourceCode = $user && (($user->id == $owner->id) || ($user->admin));

SmartyWrap::assign('agent', $agent);
SmartyWrap::assign('owner', $owner);
SmartyWrap::assign('showSourceCode', $showSourceCode);
if ($showSourceCode) {
  SmartyWrap::assign('sourceCode', $agent->getSourceCode());
}
SmartyWrap::assign('pageTitle', "Agent: {$owner->username} v{$agent->version}");
SmartyWrap::display('agent.tpl');

?>
