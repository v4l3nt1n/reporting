<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 60); //300 seconds = 5 minutes
error_reporting(E_ALL);

function classLoader ($pClassName) {
    include(__DIR__ . '\classes\\' . $pClassName . ".php");
}
spl_autoload_register("classLoader");

$client_data = array(
    'graphs' => array(
            'line',
        ),
    'ids' => array(
            '1',
        ),
    //'client_field' => 'tkt',
    'client_field' => 'gds',
    'client_field_value' => 'RN',
    'client_limit' => '',
);

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
    //*
    1 => array(
        'graph'     => 'line',
        'id'        => 2,
        'dimension' => 'tkt',
        'value'     => '',
        'limit'     => '',
    ),
    //*/
);

$dataGetter = new DataHandler($client_data);