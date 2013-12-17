<?php 

require_once '../lib/Util.php';

$tourneyPlayers = Util::getRequestParameter('tourneyPlayers');
$numRounds = Util::getRequestParameter('numRounds');
$gameSize = Util::getRequestParameter('gameSize');
$submitButton = Util::getRequestParameter('submitButton');

if ($submitButton) {
  Util::requireLoggedIn();
  $user = Session::getUser();

  try {
    // validation
    $agentIds = StringUtil::explode(',', $tourneyPlayers);
    if (count($agentIds) < 2) {
      throw new Exception('Un turneu trebuie să aibă minim doi jucători.');
    }
    if (!ctype_digit($numRounds) || ($numRounds < 1) || ($numRounds > 100)) {
      throw new Exception('Numărul de runde trebuie să fie un întreg între 1 și 100.');
    }
    if (count($agentIds) % $gameSize) {
      throw new Exception('Numărul de participanți trebuie să se dividă cu mărimea unei mese.');
    }

    // We're good to go! Create objects.
    $t = Model::factory('Tourney')->create();
    $t->userId = $user->id;
    $t->gameSize = $gameSize;
    $t->numRounds = $numRounds;
    $t->save();

    foreach ($agentIds as $agentId) {
      $p = Model::factory('Participant')->create();
      $p->tourneyId = $t->id;
      $p->agentId = $agentId;
      $p->save();
    }

    FlashMessage::add('Turneul a fost creat.', 'info');
    Util::redirect("tourney?id={$t->id}");
  } catch (Exception $e) {
    FlashMessage::add($e->getMessage());
  }
}

SmartyWrap::assign('tourneyPlayers', $tourneyPlayers);
SmartyWrap::assign('numRounds', $numRounds);
SmartyWrap::assign('gameSize', $gameSize);
SmartyWrap::assign('pageTitle', 'turnee');
SmartyWrap::addCss('select2', 'jqueryui', 'jqgrid');
SmartyWrap::addJs('select2', 'jqueryui', 'jqgrid');
SmartyWrap::display('tourneys.tpl');

?>
