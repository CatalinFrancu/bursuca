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
         ((stripos($u->username, $parts[$i]) !== false) ||
          (stripos($a->name, $parts[$i]) !== false) ||
          (stripos($a->version, $parts[$i]) !== false))) {
    $i++;
  }
  if ($i == count($parts)) {
    $matches[] = $a;
  }
}

$resp = array('results' => array());
foreach ($matches as $a) {
  $u = $users[$a->userId];
  $text = sprintf("%s v%d (%s)", $u->username, $a->version, $a->name);
  if (!$a->rated) {
    $text .= ' (unrated)';
  }
  $resp['results'][] = array('id' => $a->id, 'text' => $text);
}
print json_encode($resp);

?>
