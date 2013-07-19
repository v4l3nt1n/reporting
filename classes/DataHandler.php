<?php

class DataHandler
{
    //propiedades
    private $client_graph = array();
    private $client_ids   = array();
    private $client_field;
    private $client_field_value;
    private $client_limit;

    private $client_obj;

    private $query_results = array();

    private $ready_output = array();

    private $lineChartData;

    private $actual_obj_key;
    private $actual_obj_dim;

    // metodos
    function __construct($input)
    {
        foreach ($input as $key => $clientObject){
            $this->client_obj[] = $clientObject;
        }

        /*
        echo "<pre>";
        print_r($this->client_obj);
        echo "</pre>";
        die();
/*        
        $this->client_graph = $input['graphs'];
        $this->client_ids = $input['ids'];
        $this->client_field = $input['client_field'];
        $this->client_field_value = $input['client_field_value'];
        $this->client_limit = $input['client_limit'];
*/        
        $this->doQuery();

        echo json_encode($this->lineChartData);
    }

    private function doQuery()
    {

        foreach ($this->client_obj as $key => $obj) {
            //variable con la clave del array actual para completar el output con todos los datos
            $this->actual_obj_key = $key;
            $this->actual_obj_dim = $obj['dimension'];

            if ($obj['graph'] == "column") {
                $this->fetchColumnChart();
            }

            if ($obj['graph'] == "pie") {
                $this->fetchPieChart();
            }

            if ($obj['graph'] == "line") {
                $this->fetchLineChart($obj['dimension'],$obj['limit']);
            }
        }
    }
    
    private function DBHandler()
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=tucanoto_reservas','root', '');
        //$this->db = new PDO('mysql:host=localhost;dbname=tucanoto_reservas','root', 'csidnrpa');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }    

    private function fetchLineChart($dimension,$limit)
    {
        $this->DBHandler();
        if ($dimension == 'gds'){
            $this->lineChartData[] = $this->lineByGds();
        }
        
        if ($dimension == 'tkt'){
            $this->lineChartData[] = $this->lineByTkt();
        }

        if ($dimension == 'sine'){
            $this->lineChartData[] = $this->lineBySine();
        }
    }

    private function lineByTkt()
    {
        $lineData = array();
        $fetchSabre = array();
        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(fecha, '%d%M%y') AS fecha,
        COUNT(DISTINCT tkt) AS CountSabre,
        day,
        month,
        year
        FROM tkts_sabre ";
        if ($this->client_field != 'tkt') {
            //$sql .= "WHERE ".$this->client_field."=:client_field_value ";
        }
        $sql .= "GROUP BY fecha
        HAVING CountSabre > 1
        ORDER BY fecha ASC
        ";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(//':client_field'            => $this->client_field,
                                 ':client_field_value'      => $this->client_field_value,
                    ));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchSabre[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            //$this->db->rollBack();
            echo($e->getMessage());
        }

        $fetchAmadeus = array();
        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(CONCAT(day,month,year), '%d%M%Y') AS fecha,
        COUNT(DISTINCT tkt) AS CountAmadeus,
        day,
        month,
        year
        FROM tkts_amadeus ";
        
        if ($this->client_field != 'tkt') {
            //$sql .= "WHERE ".$this->client_field."=:client_field_value ";
        }

        $sql .= "GROUP BY fecha
        HAVING CountAmadeus > 1
        ORDER BY fecha ASC
        ";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(//':client_field'            => $this->client_field,
                                 ':client_field_value'      => substr($this->client_field_value,1),
                    ));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchAmadeus[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            //$this->db->rollBack();
            echo($e->getMessage());
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

        for ($i=0; $i < $count ; $i++) {
            /*
            $lineData['data'][] = array(
                'fecha' => $mayorFecha[$i]['fecha'],
                'count' => $fetchSabre[$i]['CountSabre'] + $fetchAmadeus[$i]['CountAmadeus'],
                'day' => $mayorFecha[$i]['day'],
                'month' => $mayorFecha[$i]['month'],
                'year' => $mayorFecha[$i]['year'],
            );
            */

            /* ----------------- */

            if ($i < $count_sabre && $i > $count_amadeus){
                $lineData['data'][] = array(
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
                $lineData['data'][] = array(
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
                $lineData['data'][] = array(
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

        /*
        echo "<pre>";
        print_r($lineData);
        echo "</pre>";
        //*/

        // ponemos las opciones que sean necesarias para los graficos
        $lineData['settings']['max'] = $max;
        $lineData['dimension'] = $this->actual_obj_dim;
        $lineData['id'] = $this->client_obj[$this->actual_obj_key]['id'];
        //echo json_encode($lineData);
        return $lineData;
    }

    
    private function lineByGds()
    {
        $lineData = array();
        $fetchSabre = array();
        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(fecha, '%d%M%y') AS fecha,
        COUNT(DISTINCT tkt) AS CountSabre,
        day,
        month,
        year
        FROM tkts_sabre ";
        if ($this->client_field != 'tkt') {
            //$sql .= "WHERE ".$this->client_field."=:client_field_value ";
        }
        $sql .= "GROUP BY fecha
        HAVING CountSabre > 1
        ORDER BY fecha ASC
        ";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(//':client_field'            => $this->client_field,
                                 ':client_field_value'      => $this->client_field_value,
                    ));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchSabre[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            //$this->db->rollBack();
            echo($e->getMessage());
        }

        $fetchAmadeus = array();
        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(CONCAT(day,month,year), '%d%M%Y') AS fecha,
        COUNT(DISTINCT tkt) AS CountAmadeus,
        day,
        month,
        year
        FROM tkts_amadeus ";
        
        if ($this->client_field != 'tkt') {
            //$sql .= "WHERE ".$this->client_field."=:client_field_value ";
        }

        $sql .= "GROUP BY fecha
        HAVING CountAmadeus > 1
        ORDER BY fecha ASC
        ";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(//':client_field'            => $this->client_field,
                                 ':client_field_value'      => substr($this->client_field_value,1),
                    ));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchAmadeus[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            //$this->db->rollBack();
            echo($e->getMessage());
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

        for ($i=0; $i < $count ; $i++) {
            if ($i < $count_sabre && $i > $count_amadeus){
                $lineData['data'][] = array(
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
                $lineData['data'][] = array(
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
                $lineData['data'][] = array(
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

        /*
        echo "<pre>";
        print_r($lineData);
        echo "</pre>";
        //*/

        // ponemos las opciones que sean necesarias para los graficos
        $lineData['settings']['max'] = $max;
        $lineData['dimension'] = $this->actual_obj_dim;
        $lineData['id'] = $this->client_obj[$this->actual_obj_key]['id'];
        //echo json_encode($lineData);
        return $lineData;
    }

    private function lineBySine()
    {
        $lineData = array();
        $fetchSabre = array();
        $i = 0;

        $sql = "SELECT
        STR_TO_DATE(fecha, '%d%M%y') AS fecha,
        COUNT(DISTINCT tkt) AS CountSabre,
        day,
        month,
        year
        FROM tkts_sabre
        WHERE sine = :sine
        GROUP BY fecha
        HAVING CountSabre > 1
        ORDER BY fecha ASC
        ";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(':sine' => $this->client_obj[$this->actual_obj_key]['value_sabre']));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchSabre[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            //$this->db->rollBack();
            echo($e->getMessage());
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
        WHERE sine = :sine
        GROUP BY fecha
        HAVING CountAmadeus > 1
        ORDER BY fecha ASC
        ";

        try{
            //$this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(':sine' => $this->client_obj[$this->actual_obj_key]['value_amadeus']));
            //$this->db->commit();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $fetchAmadeus[$i] = $data;
                $i++;
            }

        } catch(Exception $e) {
            //$this->db->rollBack();
            echo($e->getMessage());
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

        for ($i=0; $i < $count ; $i++) {
            if ($i < $count_sabre && $i > $count_amadeus){
                $lineData['data'][] = array(
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
                $lineData['data'][] = array(
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
                $lineData['data'][] = array(
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

        /*
        echo "<pre>";
        print_r($lineData);
        echo "</pre>";
        //*/

        // ponemos las opciones que sean necesarias para los graficos
        $lineData['settings']['max'] = $max;
        $lineData['dimension'] = $this->actual_obj_dim;
        $lineData['id'] = $this->client_obj[$this->actual_obj_key]['id'];
                
        //echo json_encode($lineData);
        return $lineData;
    }


}