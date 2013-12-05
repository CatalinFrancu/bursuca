<?php
require_once '../../lib/Util.php';

$query = Util::getRequestParameter('term');
$users = Model::factory('User')->where_like('username', "%{$query}%")->order_by_asc('username')->limit(10)->find_many();

$resp = array('results' => array());
foreach ($users as $u) {
  $resp['results'][] = array('id' => $u->id, 'text' => $u->username);
}
print json_encode($resp);

?>
