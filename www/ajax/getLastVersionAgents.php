<?php
require_once '../../lib/Util.php';

// Select the highest version of agent from every user
$query = 'select id from agent where (userId, version) in (select userId, max(version) from agent group by userId)';
$ids = Db::getArray(Model::factory('Agent')->raw_query($query));
print json_encode($ids);

?>
