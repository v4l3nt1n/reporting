<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 60); //300 seconds = 5 minutes
error_reporting(E_ALL);
/*
function classLoader ($pClassName) {
    include(__DIR__ . '\classes\\' . $pClassName . ".php");
}
*/
function classLoader ($pClassName) {
    include(__DIR__ . '/classes/' . $pClassName . ".php");
}

spl_autoload_register("classLoader");

$action = (!empty($_POST['action'])) ? $_POST['action'] : "";

/* ARRAY PARA IMPLEMENTAR EL INICIO CON GRAFICOS
$client_data = array(
    0 => array(
        "graph" => "pie",
        "id" => 4,
        "dimension" => "sine",
        "limit" => 50,
        "filtro" => '',
        "filtro_value" => "Filtro",
        "filtro_gds" => '',
    ),
    1 => array(
        "graph" => "line",
        "id" => 1,
        "dimension" => "emisiones-sine",
        "limit" => '',
        "filtro" => 'sine-filter',
        "filtro_value" => "",
        "filtro_gds" => '',
        'value_sabre'   => 'DV',
        'value_amadeus' => 'DV',        
    ),
);
//*/
try {
    if ($action) {
        $graphObjects = new DataHandler($action);
    } else {
        $client_data[] = $_POST['dataArray'];
        $graphObjects = new DataHandler($client_data);
    }
} catch (Exception $e) {
    $data['error'] = $e->getMessage();
    echo json_encode($data);
}