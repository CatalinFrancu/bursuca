<?php 

require_once '../lib/Util.php';
Util::requireLoggedIn();

$id = Util::getRequestParameter('id');

$user = Session::getUser();
$agent = Agent::get_by_id($id);

if ($agent->userId != $user->id) {
  FlashMessage::add('Puteți vedea codul-sursă doar pentru agenții proprii.');
  Util::redirect('index.php');
}

SmartyWrap::assign('agent', $agent);
SmartyWrap::assign('sourceCode', $agent->getSourceCode());
SmartyWrap::assign('pageTitle', "cod sursă v{$agent->version}");
SmartyWrap::addCss('rainbow');
SmartyWrap::addJs('rainbow');
SmartyWrap::display('agentSourceCode.tpl');

?>
