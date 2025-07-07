<?php
ob_start();
date_default_timezone_set("Asia/Kolkata");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();


// if($action == 'login'){
// 	$login = $crud->login();
// 	if($login)
// 		echo $login;
// }

if($action == 'save_register'){
	$save = $crud->save_register();
	if($save)
		echo $save;
}

if($action == 'save_meeting'){
	$save = $crud->save_meeting();
	if($save)
		echo $save;
}

if($action == 'delete_meeting'){
	$delete = $crud->delete_meeting();
	if($delete)
		echo $delete;
}







// Personal Code End Here.
ob_end_flush();
?>