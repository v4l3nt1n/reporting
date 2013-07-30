<?php

class DataHandler
{
    //propiedades
    const INDICATOR_COLUMN_GRAPH = 'col';
    const INDICATOR_PIE_GRAPH = 'pie';
    const INDICATOR_LINE_GRAPH = 'line';

    private $dbname = 'tucanoto_air';
    private $host = 'localhost';
    private $db_user = 'root';
    private $db_psw = '';

    private $client_obj;

    private $query_results = array();

    private $ready_output = array();

    private $graphDataObjects;

    private $actual_obj_key;
    private $actual_obj_dim;
    private $actual_obj_lim;

    // metodos
    function __construct($input)
    {
        foreach ($input as $key => $clientObject){
            $this->client_obj[] = $clientObject;
        }

        $this->doQuery();

        echo json_encode($this->graphDataObjects);
    }

    private function doQuery()
    {
        $this->DBHandler();
        foreach ($this->client_obj as $key => $obj) {
            //variable con la clave del array actual para completar el output con todos los datos
            $this->actual_obj_key = $key;
            $this->actual_obj_dim = $obj['dimension'];
            $this->actual_obj_lim = $obj['limit'];

            if ($obj['graph'] == DataHandler::INDICATOR_COLUMN_GRAPH ||
                $obj['graph'] == DataHandler::INDICATOR_PIE_GRAPH) {
                $this->fetchPieColChart();
            }

            if ($obj['graph'] == DataHandler::INDICATOR_LINE_GRAPH) {
                $this->fetchLineChart();
            }
        }
    }
    
    private function DBHandler()
    {
        //$this->db = new PDO('mysql:host=127.0.0.1;dbname=tucanoto_reservas','root', '');

        $this->db = new PDO(
            'mysql:host=' . $this->host . ';
             dbname=' . $this->dbname,
             $this->db_user,
             $this->db_psw
        );

        //$this->db = new PDO('mysql:host=localhost;dbname=tucanoto_reservas','root', 'csidnrpa');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function fetchLineChart()
    {
        $this->graphDataObjects[] = $this->lineChartProcess();
    }

    private function lineChartProcess()
    {
        $graphData = array();
        $fetchSabre = array();
        $sine_token = "";

        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(fecha, '%d%M%y') AS fecha,
        COUNT(DISTINCT tkt) AS CountSabre,
        day,
        month,
        year
        FROM tkts_sabre 
        WHERE descripcion != 'VOID' ";
        if ($this->actual_obj_dim == 'sine') {
            $sql .= "AND sine =:sine ";
            $sine_token = $this->client_obj[$this->actual_obj_key]['value_sabre'];
        }
        $sql .= "GROUP BY fecha
        HAVING CountSabre > 1
        ORDER BY fecha ASC";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(':sine' => $sine_token));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchSabre[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }

        $fetchAmadeus = array();
        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(CONCAT(day,month,year), '%d%M%Y') AS fecha,
        COUNT(DISTINCT tkt) AS CountAmadeus,
        day,
        month,
        year
        FROM tkts_amadeus 
        WHERE descripcion != 'CANX' 
        AND descripcion != 'CANN' ";
        if ($this->actual_obj_dim == 'sine') {
            $sql .= "AND sine =:sine ";
            $sine_token = $this->client_obj[$this->actual_obj_key]['value_amadeus'];
        }        
        $sql .= "GROUP BY fecha
        HAVING CountAmadeus > 1
        ORDER BY fecha ASC";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(':sine' => $sine_token));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchAmadeus[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }

        $count_amadeus = count($fetchAmadeus);
        $count_sabre = count($fetchSabre);

        if ($count_amadeus > $count_sabre) {
            $mayorFecha = $fetchAmadeus;
        }

        if ($count_amadeus < $count_sabre) {
            $mayorFecha = $fetchSabre;
        }

        if ($count_amadeus == $count_sabre) {
            $mayorFecha = $fetchAmadeus;
        }

        $count = count($mayorFecha);
        $max = 0;

        if ($this->actual_obj_dim == 'gds' || $this->actual_obj_dim == 'sine') {
            for ($i=0; $i < $count ; $i++) {
                if ($i < $count_sabre && $i > $count_amadeus){
                    $graphData['data'][] = array(
                        'fecha' => $mayorFecha[$i]['fecha'],
                        'count_sabre' => $fetchSabre[$i]['CountSabre'],
                        'count_amadeus' => 0,
                        'day' => $mayorFecha[$i]['day'],
                        'month' => $mayorFecha[$i]['month'],
                        'year' => $mayorFecha[$i]['year'],
                    );
                    ($max < $fetchSabre[$i]['CountSabre']) ? $max = $fetchSabre[$i]['CountSabre'] : $max;
                }

                if ($i > $count_sabre && $i < $count_amadeus){
                    $graphData['data'][] = array(
                        'fecha' => $mayorFecha[$i]['fecha'],
                        'count_sabre' => 0,
                        'count_amadeus' => $fetchAmadeus[$i]['CountAmadeus'],
                        'day' => $mayorFecha[$i]['day'],
                        'month' => $mayorFecha[$i]['month'],
                        'year' => $mayorFecha[$i]['year'],
                    );
                    ($max < $fetchAmadeus[$i]['CountAmadeus']) ? $max = $fetchAmadeus[$i]['CountAmadeus'] : $max;
                }
                
                if ($i < $count_sabre && $i < $count_amadeus){
                    $graphData['data'][] = array(
                        'fecha' => $mayorFecha[$i]['fecha'],
                        'count_sabre' => $fetchSabre[$i]['CountSabre'],
                        'count_amadeus' => $fetchAmadeus[$i]['CountAmadeus'],
                        'day' => $mayorFecha[$i]['day'],
                        'month' => $mayorFecha[$i]['month'],
                        'year' => $mayorFecha[$i]['year'],
                    );
                    ($max < $fetchAmadeus[$i]['CountAmadeus']) ? $max = $fetchAmadeus[$i]['CountAmadeus'] : $max;
                    ($max < $fetchSabre[$i]['CountSabre']) ? $max = $fetchSabre[$i]['CountSabre'] : $max;
                }
            }
        }

        if ($this->actual_obj_dim == 'tkt') {
            for ($i=0; $i < $count ; $i++) {
                if ($i < $count_sabre && $i > $count_amadeus){
                    $graphData['data'][] = array(
                        'fecha' => $mayorFecha[$i]['fecha'],
                        'count' => $fetchSabre[$i]['CountSabre'],
                        'day' => $mayorFecha[$i]['day'],
                        'month' => $mayorFecha[$i]['month'],
                        'year' => $mayorFecha[$i]['year'],
                    );
                    $sum = $fetchSabre[$i]['CountSabre'];
                    ($max < $sum) ? $max = $sum : $max;
                }

                if ($i > $count_sabre && $i < $count_amadeus){
                    $graphData['data'][] = array(
                        'fecha' => $mayorFecha[$i]['fecha'],
                        'count' => $fetchAmadeus[$i]['CountAmadeus'],
                        'day' => $mayorFecha[$i]['day'],
                        'month' => $mayorFecha[$i]['month'],
                        'year' => $mayorFecha[$i]['year'],
                    );
                    $sum = $fetchAmadeus[$i]['CountAmadeus'];
                    ($max < $sum) ? $max = $sum : $max;
                }
                
                if ($i < $count_sabre && $i < $count_amadeus){
                    $graphData['data'][] = array(
                        'fecha' => $mayorFecha[$i]['fecha'],
                        'count' => $fetchSabre[$i]['CountSabre'] + $fetchAmadeus[$i]['CountAmadeus'],
                        'day' => $mayorFecha[$i]['day'],
                        'month' => $mayorFecha[$i]['month'],
                        'year' => $mayorFecha[$i]['year'],
                    );
                    $sum = $fetchSabre[$i]['CountSabre'] + $fetchAmadeus[$i]['CountAmadeus'];
                    ($max < $sum) ? $max = $sum : $max;
                }
            }
        }

        //var_dump($max);

        // ponemos las opciones que sean necesarias para los graficos
        $graphData['settings']['max'] = $max;
        $graphData['dimension'] = $this->actual_obj_dim;
        $graphData['graph'] = 'LineChart';
        $graphData['id'] = $this->client_obj[$this->actual_obj_key]['id'];
        //echo json_encode($graphData);
        return $graphData;
    }

    private function fetchPieColChart()
    {
        $this->graphDataObjects[] = $this->pieColProcess();
    }

    private function pieColProcess()
    {
        $fetchSabre = array();
        $fetchAmadeus = array();
        $fetchPieCol = array();
        $sine_token = '';
        $gds_token = '';
        $i = 0;

        if (empty($this->client_obj[$this->actual_obj_key]['filtro_gds']) &&
            !empty($this->client_obj[$this->actual_obj_key]['limit'])
           )
        {
            $gds_token = "sabre";
        } else {
            $gds_token = $this->client_obj[$this->actual_obj_key]['filtro_gds'];
        }

        if ($gds_token == 'sabre') {
            $sql = "SELECT
                ".$this->actual_obj_dim." AS dimension,
                COUNT(*) AS count
                FROM tkts_sabre
                WHERE descripcion != 'VOID' ";

            if (!empty($this->client_obj[$this->actual_obj_key]['filtro'])) {
                $sql .= "AND " . $this->client_obj[$this->actual_obj_key]['filtro']."='"
                               . $this->client_obj[$this->actual_obj_key]['filtro_value']."'";
            }

            $sql .= "
                GROUP BY dimension
                HAVING count > 1 
                ORDER BY count DESC ";

            if ($this->client_obj[$this->actual_obj_key]['limit'] > 0) {
                $sql .= " LIMIT 0 , ".$this->client_obj[$this->actual_obj_key]['limit'].";";
            }

            //echo $sql . "<br>";

            try{
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array(':sine' => $sine_token));
                
                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $fetchSabre[$i] = $data;
                    $i++;
                }
            } catch(Exception $e) {
                throw new Exception($e->getMessage(), 1);
            }            
        }
/*
    echo "<pre>";
    print_r($this->client_obj);
    echo "</pre>";
//*/

        if (empty($this->client_obj[$this->actual_obj_key]['filtro_gds']) &&
            !empty($this->client_obj[$this->actual_obj_key]['limit'])
           )
        {
            $gds_token = "amadeus";
        } else {
            $gds_token = $this->client_obj[$this->actual_obj_key]['filtro_gds'];
        }

        if ($gds_token == 'amadeus') {
            $sql = "SELECT
                ".$this->actual_obj_dim." AS dimension,
                COUNT(DISTINCT tkt) AS count
                FROM tkts_amadeus
                WHERE descripcion != 'CANX' 
                AND descripcion != 'CANN' ";

            if (!empty($this->client_obj[$this->actual_obj_key]['filtro'])) {
                $sql .= "AND " . $this->client_obj[$this->actual_obj_key]['filtro']."='"
                                 . $this->client_obj[$this->actual_obj_key]['filtro_value']."'";
            }

            $sql .= "
                GROUP BY dimension
                HAVING count > 1 
                ORDER BY count DESC ";

            if ($this->client_obj[$this->actual_obj_key]['limit'] > 0) {
                $sql .= " LIMIT 0 , ".$this->client_obj[$this->actual_obj_key]['limit'].";";
            }

            //echo $sql . "<br>";

            try{
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array(':sine' => $sine_token));
                
                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $fetchAmadeus[$i] = $data;
                    $i++;
                }
            } catch(Exception $e) {
                throw new Exception($e->getMessage(), 1);
            }
        }
/*
        echo "<pre>";
        print_r($fetchAmadeus);
        print_r($fetchSabre);
        echo "</pre>";
        die();
//*/

        if ($this->actual_obj_dim == 'descripcion') {
            $fetchPieCol = array_merge($fetchSabre,$fetchAmadeus);
        } elseif (!empty($this->client_obj[$this->actual_obj_key]['filtro_gds'])) {
            if ($this->client_obj[$this->actual_obj_key]['filtro_gds'] == 'sabre') {
                $fetchPieCol = array_merge($fetchSabre);
            } else {
                $fetchPieCol = array_merge($fetchAmadeus);
            }
        } else {
            foreach ($fetchSabre as $key => $dataSb) {
                foreach ($fetchAmadeus as $key => $dataAm) {
                    if ($dataSb['dimension'] == $dataAm['dimension']) {
                        $fetchPieCol[] = array(
                            'dimension' => $dataSb['dimension'],
                            'count'     => $dataSb['count'] + $dataAm['count'],
                        );
                    }
                }
            }
        }

        $graphData['data'] = $fetchPieCol;
        $graphData['dimension'] = $this->actual_obj_dim;
        $graphData['graph'] = ($this->client_obj[$this->actual_obj_key]['graph'] == 'pie') ? 'PieChart' : 'ColumnChart';
        $graphData['id'] = $this->client_obj[$this->actual_obj_key]['id'];
/*
        echo "<pre>";
        print_r($fetchSabre);
        echo "</pre>";
        die();
//*/
        return $graphData;
    }
}