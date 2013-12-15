<?php

require_once '../lib/Util.php';

SmartyWrap::assign('pageTitle', _('Home page'));
SmartyWrap::addCss('jqueryui', 'jqgrid');
SmartyWrap::addJs('jqueryui', 'jqgrid');
SmartyWrap::display('index.tpl');

?>
