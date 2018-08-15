<?php
session_start();
require 'vendor/autoload.php';
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
$currentversion = '1.3.8';
$errors = [];
$install = new Installer;
set_time_limit(500);
if (isset($_GET['step'])) {
    switch ($_GET['step']) {		
		case 'two':
			if (count($install->requirements()) == 0) {
				$template = 'success.php';
				include('layout.php');
			} else {				
				$template = 'steptwo.php';
				include('layout.php');
			}	
			break;
		case 'three':
		    $template = 'stepthree.php';
			include('layout.php');
			break;
		case 'four':
		    set_time_limit(2000);
		    ini_set('max_execution_time', 2000);
		    $_SESSION["step_steptwo"] = "finished";
		    $installsite = $install->copyfiles($_POST);
		    //echo  json_encode($installsite) ;
		    //$installsite = 'created';
		    if ($installsite == 'created') {
				if(isset($_SESSION['step_steptwo']) && $_SESSION["step_steptwo"] == 'finished') {
				   $template = 'stepfour.php';
			       include('layout.php');
				} else {
		    		$template = 'stepthree.php';
		    		$errors = ['You bi passed a very important step. What are you trying to do?'];
			        include('layout.php');
		    	}
		    }else{
		    	$template = 'stepthree.php';
		    	$errors[] = $installsite;
			    include('layout.php');
		    }			
			break;	
		default:
		    $template = 'terms.php';
			include('layout.php');
			break;
	}
} else {
	$template = 'terms.php';
	include('layout.php');
}
?>



