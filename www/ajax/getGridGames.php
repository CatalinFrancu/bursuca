<?php
require_once '../../lib/Util.php';

$tourneyId = Util::getRequestParameter('tourneyId');
$userId = Util::getRequestParameter('userId');
$agentId = Util::getRequestParameter('agentId');
$all = Util::getRequestParameter('all');
$rowsPerPage = Util::getRequestParameter('rows');
$page = Util::getRequestParameter('page');
$sidx = Util::getRequestParameter('sidx');
$sord = Util::getRequestParameter('sord');

$query = makeQuery($tourneyId, $userId, $agentId, $all, $sord, $sidx);
$count = count($query->find_result_set());
$query = makeQuery($tourneyId, $userId, $agentId, $all, $sord, $sidx); // needs to be rebuilt
$games = $query->offset(($page - 1) * $rowsPerPage)->limit($rowsPerPage)->find_many();

$resp = new stdClass();
$resp->page = $page;
$resp->total = ceil($count / $rowsPerPage);
$resp->records = $count;
$resp->rows = array();
foreach ($games as $g) {
  $resp->rows[] = array('id' => $g->id,
                        'tourneyId' => $g->tourneyId,
                        'round' => $g->round,
                        'winnerData' => getWinnerData($g->winnerId),
                        'playerData' => getPlayerData($g->id),
                        'status' => $g->status,
                        'statusName' => $g->getStatusName());
}
print json_encode($resp);

/*************************************************************************/

// Load the games to show.
// - When the $tourneyId, $all, $agentId or $userId parameters are set, obey those
// - Otherwise, if a user is logged in, show that user's games
// - Otherwise, no user is logged in and we show all games
function makeQuery($tourneyId, $userId, $agentId, $all, $sord, $sidx) {
  $user = Session::getUser();
  if ($all) {
    $query = Model::factory('Game');
  } else if ($tourneyId) {
    $query = Model::factory('Game')->where('tourneyId', $tourneyId);
  } else if ($agentId) {
    $query = Model::factory('Game')->distinct()->select('game.*')
      ->join('player', array('player.gameId', '=', 'game.id'))
      ->where('player.agentId', $agentId);
  } else if ($userId || $user) {
    $realUserId = $userId ? $userId : $user->id;
    $query = Model::factory('Game')->distinct()->select('game.*')
      ->join('player', array('player.gameId', '=', 'game.id'))
      ->join('agent', array('player.agentId', '=', 'agent.id'))
      ->where('agent.userId', $realUserId);
  } else {
    $query = Model::factory('Game');
  }
  if ($sord == 'asc') {
    $query = $query->order_by_asc($sidx);
  } else {
    $query = $query->order_by_desc($sidx);
  }
  $query = $query->order_by_desc('game.id');
  return $query;
}

function getPlayerData($gameId) {
  $results = array();
  $players = Model::factory('Player')->where('gameId', $gameId)->order_by_asc('position')->find_many();
  foreach ($players as $p) {
    $a = Agent::get_by_id($p->agentId);
    $u = User::get_by_id($a->userId);
    $results[] = array('userId' => $u->id,
                       'username' => $u->username,
                       'agentId' => $a->id,
                       'version' => $a->version);
  }
  return $results;
}

function getWinnerData($agentId) {
  $a = Agent::get_by_id($agentId);
  $u = User::get_by_id($a->userId);
  return array('userId' => $u->id,
               'username' => $u->username,
               'agentId' => $a->id,
               'version' => $a->version);
}

?>
