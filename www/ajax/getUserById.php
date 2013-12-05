<?php
require_once("../../lib/Util.php");

$id = Util::getRequestParameter('id');
$u = User::get_by_id($id);
print json_encode($u->username);

?>
