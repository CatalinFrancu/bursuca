<?php
require_once("../../lib/Util.php");

$id = Util::getRequestParameter('id');
$a = Agent::get_by_id($id);
$u = User::get_by_id($a->userId);
print json_encode("{$u->username} v{$a->version} ({$a->name})");

?>
