<?php

class DataHandler
{
    //propiedades
    private $dbname = 'tucanoto_air';
    private $host = 'localhost';
    private $db_user = 'root';
    private $db_psw = '';

    // metodos
    function __construct()
    {
        $this->DBHandler();
        echo json_encode($this->getDataToCube());
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

    private function getDataToCube()
    {
        try {
            $sql = "SELECT DISTINCT cia,fop,sine,pcc,day,month,year,tkt,descripcion,gds FROM tkts_amadeus
                    UNION 
                    SELECT DISTINCT cia,fop,sine,pcc,day,month,year,tkt,descripcion,gds FROM tkts_sabre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $error = "OK";
            $i = 0;
            $cubefetch = array();

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
                $cubefetch["rows"][$i] = $data;
                $i++;
            }
        } catch (Exception $e){
            throw new Exception("Error al traer los datos: " . $e->getMessage(), 1);            
        }

        return $cubefetch;
    }
}