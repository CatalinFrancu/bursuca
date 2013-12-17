<?php
require_once '../../lib/Util.php';

$rowsPerPage = Util::getRequestParameter('rows');
$page = Util::getRequestParameter('page');
$sidx = Util::getRequestParameter('sidx');
$sord = Util::getRequestParameter('sord');

$query = makeQuery($sord, $sidx);
$count = count($query->find_result_set());
$query = makeQuery($sord, $sidx); // needs to be rebuilt
$tourneys = $query->offset(($page - 1) * $rowsPerPage)->limit($rowsPerPage)->find_many();

$resp = new stdClass();
$resp->page = $page;
$resp->total = ceil($count / $rowsPerPage);
$resp->records = $count;
$resp->rows = array();
foreach ($tourneys as $t) {
  $u = User::get_by_id($t->userId);
  $status = $t->getStatus();
  $resp->rows[] = array('id' => $t->id,
                        'userId' => $u->id,
                        'username' => $u->username,
                        'participants' => $t->countParticipants(),
                        'numRounds' => $t->numRounds,
                        'gameSize' => $t->gameSize,
                        'status' => $status,
                        'statusName' => $t->getStatusName($status));
}
print json_encode($resp);

/*************************************************************************/

function makeQuery($sord, $sidx) {
  $query = Model::factory('Tourney');
  if ($sord == 'asc') {
    $query = $query->order_by_asc($sidx);
  } else {
    $query = $query->order_by_desc($sidx);
  }
  return $query;
}

?>
