<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 120); //300 seconds = 5 minutes
error_reporting(E_ALL);

function __autoload($class)
{
	require_once "$class.php";
}

$datos = new DataHandler();