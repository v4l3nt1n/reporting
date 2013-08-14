<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 60); //300 seconds = 5 minutes
//error_reporting(E_ALL);

include 'Classes/SourceHandler.php';
include 'Classes/PHPExcel/IOFactory.php';

set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');

$error = '';

try {
	$source_handler = new SourceHandler($_FILES);
	$fuentes = $source_handler->getSources();
	$error = "Los archivos se subieron con exito.";
} catch (Exception $e) {
	$error = $e->getMessage();
}

unset($source_handler);