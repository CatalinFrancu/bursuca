<?php 

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');
$scheduleNextRound = Util::getRequestParameter('scheduleNextRound');
$scheduleAllRounds = Util::getRequestParameter('scheduleAllRounds');

$tourney = Tourney::get_by_id($id);
if (!$tourney) {
  FlashMessage::add('Nu găsesc turneul cu id-ul cerut.');
  Util::redirect('games');
}

$maxScheduledRound = Model::factory('Game')->where('tourneyId', $tourney->id)->max('round');

// Schedule next round or all remaining rounds on owner request
if ($scheduleNextRound || $scheduleAllRounds) {
  Util::requireLoggedIn();
  $user = Session::getUser();

  try {
    // validation
    if ($user->id != $tourney->userId) {
      throw new Exception('Poți gestiona numai turneele pe care tu le-ai creat.');
    }

    if ($maxScheduledRound == $tourney->numRounds) {
      throw new Exception('Toate rundele acestui turneu au fost deja programate.');      
    }

    $lastRound = $scheduleAllRounds ? $tourney->numRounds : ($maxScheduledRound + 1);
    for ($r = $maxScheduledRound + 1; $r <= $lastRound; $r++) {
      $tourney->scheduleRound($r);
    }

    if ($scheduleAllRounds) {
      FlashMessage::add('Toate rundele rămase au fost create.', 'info');
    } else {
      FlashMessage::add('Runda a fost creată.', 'info');
    }
  } catch (Exception $e) {
    FlashMessage::add($e->getMessage());
  }

  Util::redirect("?id={$tourney->id}");
}

$participants = Participant::get_all_by_tourneyId($tourney->id);
$records = array();
foreach ($participants as $part) {
  $rec = array();
  $agent = Agent::get_by_id($part->agentId);
  $played = Model::factory('Game')
    ->join('player', array('player.gameId', '=', 'game.id'))
    ->where('tourneyId', $tourney->id)
    ->where('status', Game::STATUS_FINISHED)
    ->where('player.agentId', $agent->id)->count();
  $score = Model::factory('Game')->where('tourneyId', $tourney->id)->where('winnerId', $agent->id)->count();
  $rec['agent'] = $agent;
  $rec['user'] = User::get_by_id($agent->userId);
  $rec['played'] = $played;
  $rec['score'] = $score;
  $records[] = $rec;
}
usort($records, function($a, $b) { return $b['score'] - $a['score']; });

SmartyWrap::assign('tourney', $tourney);
SmartyWrap::assign('records', $records);
SmartyWrap::assign('maxScheduledRound', $maxScheduledRound);
SmartyWrap::assign('pageTitle', "turneul {$tourney->id}");
SmartyWrap::addCss('jqueryui', 'jqgrid');
SmartyWrap::addJs('jqueryui', 'jqgrid');
SmartyWrap::display('tourney.tpl');

?>
