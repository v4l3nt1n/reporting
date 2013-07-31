<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 60); //300 seconds = 5 minutes
error_reporting(E_ALL);

function classLoader ($pClassName) {
    include(__DIR__ . '\classes\\' . $pClassName . ".php");
}
spl_autoload_register("classLoader");

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
//*/

$action = (!empty($_POST['action'])) ? $_POST['action'] : "";
$action = "fetchSine";

/*
$client_data = array(
    0 => array(
        'graph'         => 'line',
        'id'            => 1,
        'dimension'     => 'sine',
        'value'         => '',
        'value_sabre'   => 'LB',
        'value_amadeus' => 'LB',
        'limit'         => '',
    ),
    1 => array(
        'graph'     => 'line',
        'id'        => 2,
        'dimension' => 'gds',
        'value'     => '',
        'value_sabre'   => 'LB',
        'value_amadeus' => 'LB',
        'limit'     => '10',
    ),
    3 => array(
        'graph'     => 'line',
        'id'        => 3,
        'dimension' => 'tkt',
        'value'     => '',
        'value_sabre'   => 'LB',
        'value_amadeus' => 'LB',
        'limit'     => '',
    ),
    4 => array(
        'graph'     => 'pie',
        'id'        => 4,
        'dimension' => 'descripcion',
        'value'     => '',
        'value_sabre'   => 'LB',
        'value_amadeus' => 'LB',
        'limit'     => '',
    ),
    5 => array(
        'graph'     => 'col',
        'id'        => 5,
        'dimension' => 'sine',
        'value'     => '',
        'value_sabre'   => 'LB',
        'value_amadeus' => 'LB',
        'limit'     => '20',
    ),
);
//*/
/*
$client_data[] = array(
    "graph" => "pie",
    "id" => 4,
    "dimension" => "sine",
    "limit" => 50,
    "filtro" => '',
    "filtro_value" => "Filtro",
    "filtro_gds" => '',
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
    echo $e->getMessage();
}