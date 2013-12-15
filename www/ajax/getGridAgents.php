<?php
require_once '../../lib/Util.php';

$rowsPerPage = Util::getRequestParameter('rows');
$page = Util::getRequestParameter('page');
$sidx = Util::getRequestParameter('sidx');
$sord = Util::getRequestParameter('sord');

$results = Model::factory('Agent')
  ->join('user', array('agent.userId', '=', 'user.id'))
  ->select('userId')
  ->select('agent.id')
  ->select('user.username')
  ->select('agent.version')
  ->select('agent.name')
  ->select('agent.elo')
  ->where('rated', 1);
if ($sord == 'asc') {
  $results = $results->order_by_asc($sidx);
} else {
  $results = $results->order_by_desc($sidx);
}
$count = $results->count();
$results = $results->offset(($page - 1) * $rowsPerPage)->limit($rowsPerPage)->find_array();

$resp = new stdClass();
$resp->page = $page;
$resp->total = ceil($count / $rowsPerPage);
$resp->records = $count;
$resp->rows = array();
foreach ($results as $r) {
  $agentName = sprintf("v%d (%s)", $r['version'], $r['name']);
  $resp->rows[] = array('id' => $r['id'],
                        'cell' => array($r['userId'], $r['id'], $r['username'], $agentName, $r['elo']));
}
print json_encode($resp);

?>
