<?php

class DataHandler
{
    //propiedades
    const INDICATOR_COLUMN_GRAPH = 'col';
    const INDICATOR_PIE_GRAPH = 'pie';
    const INDICATOR_LINE_GRAPH = 'line';

    const TKT_DB = 'tucanoto_air';
    const SINE_DB = 'tucanoto_api';

    const TKT_SABRE_TABLE = 'tkts_sabre';
    const TKT_AMADEUS_TABLE = 'tkts_amadeus';

    const JOIN_TABLE_CIA = 'codcias';
    const JOIN_FIELD_CIA = 'Descripcion';

    const JOIN_TABLE_SINE = 'usuarios';
    const JOIN_FIELD_SINE = 'usuario';

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

    // metodos
    function __construct($input)
    {
        if ($input == 'fetchSine') {
            $sines = $this->fetchSine();
            echo json_encode($sines);
            return true;            
        } elseif ($input == 'fetchDate') {
            $dates = $this->fetchDate();
            echo json_encode($dates);
            return true;
        } else {
            foreach ($input as $key => $clientObject){
                $this->client_obj[] = $clientObject;
            }
            $this->doQuery();
            echo json_encode($this->graphDataObjects);
        }
    }

    private function doQuery()
    {
        $this->DBHandler();
        foreach ($this->client_obj as $key => $obj) {
            //variable con la clave del array actual para completar el output con todos los datos
            $this->actual_obj_key = $key;
            $this->actual_obj_dim = $obj['dimension'];

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
        $this->db = new PDO(
            'mysql:host=' . $this->host . ';
             dbname=' . $this->dbname,
             $this->db_user,
             $this->db_psw
        );
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
        if ($this->actual_obj_dim == 'emisiones-sine') {
            $sql .= "AND sine =:sine ";
            $sine_token = $this->client_obj[$this->actual_obj_key]['value_sabre'];
        }
        $sql .= "GROUP BY fecha
        HAVING CountSabre > 1
        ORDER BY fecha ASC";

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
        if ($this->actual_obj_dim == 'emisiones-sine') {
            $sql .= "AND sine =:sine ";
            $sine_token = $this->client_obj[$this->actual_obj_key]['value_amadeus'];
        }        
        $sql .= "GROUP BY fecha
        HAVING CountAmadeus > 1
        ORDER BY fecha ASC";

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

        if ($this->actual_obj_dim == 'emisiones-gds' || $this->actual_obj_dim == 'emisiones-sine') {
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

        if ($this->actual_obj_dim == 'emisiones-full') {
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

        // ponemos las opciones que sean necesarias para los graficos
        $graphData['settings']['max'] = $max;
        $graphData['dimension'] = $this->actual_obj_dim;
        $graphData['graph'] = 'LineChart';
        $graphData['id'] = $this->client_obj[$this->actual_obj_key]['id'];

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

        // asigno los valores de las tablas para hacer los joins
        if ($this->actual_obj_dim == 'sine') {
            $join_db = DataHandler::SINE_DB;
            $join_table = DataHandler::JOIN_TABLE_SINE;
            $join_field = DataHandler::JOIN_FIELD_SINE;
            $join_dim_field = 'sine_sabre';
        }

        if ($this->actual_obj_dim == 'iata_num_code') {
            $join_db = DataHandler::TKT_DB;
            $join_table = DataHandler::JOIN_TABLE_CIA;
            $join_field = DataHandler::JOIN_FIELD_CIA;
            $join_dim_field = 'iata_num_code';
        }

        if (empty($this->client_obj[$this->actual_obj_key]['filtro_gds']) &&
            !empty($this->client_obj[$this->actual_obj_key]['limit'])
           )
        {
            $gds_token = "sabre";
        } else {
            $gds_token = $this->client_obj[$this->actual_obj_key]['filtro_gds'];
        }

        if ($gds_token == 'sabre') {
            $sql = "SELECT ".
                $join_db.".".$join_table.".".$join_field." AS dimension,
                COUNT(DISTINCT tkt) AS count
                FROM ".DataHandler::TKT_DB.".".DataHandler::TKT_SABRE_TABLE.
                    " INNER JOIN ".$join_db.".".$join_table.
                        " ON ".
                        $join_db.".".$join_table.".".$join_dim_field.
                        "=".
                        DataHandler::TKT_DB.".".DataHandler::TKT_SABRE_TABLE.".".$this->actual_obj_dim.
                " WHERE ".DataHandler::TKT_DB.".".DataHandler::TKT_SABRE_TABLE.".descripcion != 'VOID' ";

            if (!empty($this->client_obj[$this->actual_obj_key]['filtro']) &&
                $this->client_obj[$this->actual_obj_key]['filtro'] != 'limit'
                ) {
                $sql .= "AND " . $this->client_obj[$this->actual_obj_key]['filtro']."='"
                               . $this->client_obj[$this->actual_obj_key]['filtro_value']."'";
            }

            $sql .= "
                GROUP BY dimension
                HAVING count > 1 
                ORDER BY count DESC ";

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

        if ($this->actual_obj_dim == 'sine') {
            $join_dim_field = 'sine_amadeus';
        }

        if (empty($this->client_obj[$this->actual_obj_key]['filtro_gds']) &&
            !empty($this->client_obj[$this->actual_obj_key]['limit'])
           )
        {
            $gds_token = "amadeus";
        } else {
            $gds_token = $this->client_obj[$this->actual_obj_key]['filtro_gds'];
        }

        if ($gds_token == 'amadeus') {
            $sql = "SELECT ".
                $join_db.".".$join_table.".".$join_field." AS dimension,
                COUNT(DISTINCT tkt) AS count
                FROM ".DataHandler::TKT_DB.".".DataHandler::TKT_AMADEUS_TABLE.
                    " INNER JOIN ".$join_db.".".$join_table.
                        " ON ".
                        $join_db.".".$join_table.".".$join_dim_field.
                        "=".
                        DataHandler::TKT_DB.".".DataHandler::TKT_AMADEUS_TABLE.".".$this->actual_obj_dim.
                " WHERE ".DataHandler::TKT_DB.".".DataHandler::TKT_AMADEUS_TABLE.".descripcion != 'CANX' 
                  AND ".DataHandler::TKT_DB.".".DataHandler::TKT_AMADEUS_TABLE.".descripcion != 'CANN' ";
            if (!empty($this->client_obj[$this->actual_obj_key]['filtro']) &&
                $this->client_obj[$this->actual_obj_key]['filtro'] != 'limit'
                ) {
                $sql .= "AND " . $this->client_obj[$this->actual_obj_key]['filtro']."='"
                                 . $this->client_obj[$this->actual_obj_key]['filtro_value']."'";
            }

            $sql .= "
                GROUP BY dimension
                HAVING count > 1 
                ORDER BY count DESC ";

            try{
                $i = 0;
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

        if ($this->actual_obj_dim == 'descripcion') {
            $fetchPieCol = array_merge($fetchSabre,$fetchAmadeus);
        } elseif (!empty($this->client_obj[$this->actual_obj_key]['filtro_gds'])) {
            if ($this->client_obj[$this->actual_obj_key]['filtro_gds'] == 'sabre') {
                $fetchPieCol = array_merge($fetchSabre);
            } else {
                $fetchPieCol = array_merge($fetchAmadeus);
            }
        } elseif ($this->actual_obj_dim == 'gds') {
            $fetchPieCol = array_merge($fetchAmadeus, $fetchSabre);
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

        // ordeno el array de mayor a menor
        function custom_sort($a,$b) {
             return $a['count']<$b['count'];
        }
        usort($fetchPieCol, "custom_sort");

        if (!empty($this->client_obj[$this->actual_obj_key]['limit'])) {
            array_splice($fetchPieCol, $this->client_obj[$this->actual_obj_key]['limit']);
        }

        $graphData['data'] = $fetchPieCol;
        $graphData['dimension'] = $this->actual_obj_dim;
        $graphData['graph'] = ($this->client_obj[$this->actual_obj_key]['graph'] == 'pie') ? 'PieChart' : 'ColumnChart';
        $graphData['id'] = $this->client_obj[$this->actual_obj_key]['id'];

        return $graphData;
    }

    private function fetchDate()
    {
        $this->DBHandler();
        $i = 0;
        
        $sql = "SELECT DISTINCT month, year FROM tkts_amadeus
                UNION
                SELECT DISTINCT month, year FROM tkts_sabre";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $rawDates[$i] = $data;
                $i++;
            }
        } catch(Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }

        foreach ($rawDates as $key => $date) {
            $dates['month'][] = $date['month'];
            $dates['year'][] = $date['year'];
        }

        $dates['month'] = array_unique($dates['month']);
        $dates['year'] = array_unique($dates['year']);

        return $dates;
    }

    private function fetchSine()
    {
        $this->DBHandler();
        $i = 0;
        
        $sql = "SELECT DISTINCT tucanoto_api.usuarios.usuario,
                                tucanoto_api.usuarios.sine_sabre,
                                tucanoto_api.usuarios.sine_amadeus
                FROM tucanoto_api.usuarios
                    WHERE tucanoto_api.usuarios.sine_amadeus !=  ''
                    OR    tucanoto_api.usuarios.sine_sabre !=  ''
                ORDER BY  tucanoto_api.usuarios.usuario ASC";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $rawUsers[$i] = $data;
                $i++;
            }
        } catch(Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }

        $i = 0;
        foreach ($rawUsers as $key => $user) {
            $users[$i]['usuario']      = $user['usuario'];
            $users[$i]['sine_sabre']   = $user['sine_sabre'];
            $users[$i]['sine_amadeus'] = $user['sine_amadeus'];
            $i++;
        }

        return $users;
    }
}