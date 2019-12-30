<?php
include ('class/_init.php');
global $init;
$init = new ublo\_init (['skip_login' => true]);

try {
	$oauth = new ublo\core\_oauth ();
}
catch (Exception $e) {
	$init->exit (1, $e->getMessage());
}
$oauth->out ('access_token');
