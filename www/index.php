<?php

require_once '../lib/Util.php';

$agents = Model::factory('Agent')->where('rated', 1)->order_by_desc('elo')->find_many();
$users = array();
foreach ($agents as $i => $a) {
  $users[$i] = User::get_by_id($a->userId);
}

SmartyWrap::assign('agents', $agents);
SmartyWrap::assign('users', $users);
SmartyWrap::assign('pageTitle', _('Home page'));
SmartyWrap::display('index.tpl');

?>
