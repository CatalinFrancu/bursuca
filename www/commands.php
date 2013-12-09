<?php 

/**
 * Saves the move list. This is a download page, not a Smarty template.
 **/

require_once '../lib/Util.php';

$gameId = Util::getRequestParameter('gameId');
$agentId = Util::getRequestParameter('agentId');
$game = Game::get_by_id($gameId);

if (!$game) {
  FlashMessage::add('Nu găsesc partida cu id-ul cerut.');
  Util::redirect('games');
}

if (!$game->isFinished()) {
  FlashMessage::add('Această partidă nu a fost încă jucată.');
  Util::redirect('game?id={$game->id}');
}

$povPlayer = Player::get_by_gameId_agentId($game->id, $agentId); // point-of-view player
if (!$povPlayer) {
  FlashMessage::add('Agentul cerut nu a participat la partidă.');
  Util::redirect('game?id={$game->id}');
}

$players = Model::factory('Player')->where('gameId', $game->id)->order_by_asc('position')->find_many();
$playerRecords = array();

foreach ($players as $p) {
  $rec = array();
  $rec['player'] = $p;
  $rec['agent'] = Agent::get_by_id($p->agentId);
  $playerRecords[] = $rec;
}

$moves = Model::factory('Move')->where('gameId', $game->id)->order_by_asc('number')->find_many();

// prepare the output
$lines = array();
$line1 = array(count($playerRecords), $povPlayer->position);
foreach ($playerRecords as $rec) {
  if ($rec['player']->id != $povPlayer->id) {
    $line1[] = $rec['agent']->userId;
    $line1[] = $rec['agent']->version;
  }
}
$lines[] = implode(' ', $line1);
$lines[] = "{$game->price1} {$game->price2} {$game->price3} {$game->price4} {$game->price5} {$game->price6}";
foreach ($moves as $i => $m) {
  if (($i - $povPlayer->position) % count($playerRecords)) {
    $lines[] = "{$m->action} {$m->arg} {$m->company}";
  } else {
    $lines[] = "{$m->arg} {$m->company}";
  }
}
$s = implode("\n", $lines);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=bursuca.in');
header('Content-Length: ' . strlen($s));
echo $s;
?>
