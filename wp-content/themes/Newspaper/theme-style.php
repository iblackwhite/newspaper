<?php
//this is the less compiler. It is not used in production, it is just for developers who want to use our theme with less


ob_start('ob_gzhandler');
header('Content-type: text/css');


require_once("less_files/less/lessc.inc.php");
$less = new lessc;

//$less->setPreserveComments(true);
echo $less->compileFile("less_files/main.less");
//echo $less->compileFile("external/bootstrap-master/less/bootstrap.less");

