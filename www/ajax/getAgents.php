<?php
require_once '../../lib/Util.php';

$query = Util::getRequestParameter('term');
$parts = preg_split("/\s+/", $query);

$agents = Model::factory('Agent')->order_by_asc('userId')->order_by_desc('version')->find_many();
$users = Model::factory('User')->find_many();

$userMap = array();
foreach ($users as $user) {
  $userMap[$user->id] = $user;
}

$matches = array();
foreach ($agents as $a) {
  $u = $users[$a->userId];
  $i = 0;
  while (($i < count($parts)) &&
         ((strpos($u->username, $parts[$i]) !== false) ||
          (strpos($a->name, $parts[$i]) !== false) ||
          (strpos($a->version, $parts[$i]) !== false))) {
    $i++;
  }
  if ($i == count($parts)) {
    $matches[] = $a;
  }
}

$resp = array('results' => array());
foreach ($matches as $a) {
  $u = $users[$a->userId];
  $resp['results'][] = array('id' => $a->id,
                             'text' => "{$u->username} v{$a->version} ({$a->name})");
}
print json_encode($resp);

?>
