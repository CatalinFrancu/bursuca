<?php
require_once '../../lib/Util.php';

$rowsPerPage = Util::getRequestParameter('rows');
$page = Util::getRequestParameter('page');
$sidx = Util::getRequestParameter('sidx');
$sord = Util::getRequestParameter('sord');

$query = makeQuery($sord, $sidx);
$count = count($query->find_result_set());
$query = makeQuery($sord, $sidx); // needs to be rebuilt
$users = $query->offset(($page - 1) * $rowsPerPage)->limit($rowsPerPage)->find_array();

$resp = new stdClass();
$resp->page = $page;
$resp->total = ceil($count / $rowsPerPage);
$resp->records = $count;
$resp->rows = $users;
print json_encode($resp);

/*************************************************************************/

function makeQuery($sord, $sidx) {
  $query = Model::factory('User')
    ->raw_query("select user.id as userId, user.username, user.elo, count(*) as versions " .
                "from user join agent on user.id = agent.userId " .
                "group by user.id " .
                "order by {$sidx} {$sord}");
  return $query;
}

?>
