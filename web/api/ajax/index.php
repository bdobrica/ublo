<?php
include ('class/_init.php');
global $init;
$init = new ublo\_init ();

$object = $init->request ('object', 'class:ublo\\api\\ajax');

if (is_null ($object)) {
	$init->exit (1, 'invalid object provided');
}

$ctrl_class = 'ublo\\api\\ajax\\ctrl\\' . $object;
$view_class = 'ublo\\api\\ajax\\view\\' . $object;

$object_data = (object) [
	'object'	=> $object,
	'object_id'	=> $init->request ('object_id', 'int|list'),
	'action'	=> $init->request ('action', 'slug'),
	'reason'	=> $init->request ('reason', 'text'),
	'submit'	=> $init->request ('submit', 'bool'),
];

$ctrl = null;
if ($object_data->submit) {
	$ctrl = new $ctrl_class ($object_data);

	if (method_exists ($ctrl, $object_data->action)) {
		$ctrl->{$object_data->action} ();
	}
}

$view = new $view_class ($ctrl);
if (!method_exists ($view, $object_data->action)) {
	$view->{$object_data->action} ();
}

$view->out ();
