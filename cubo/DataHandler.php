<?php

class DataHandler
{
    //propiedades
/* datos local
    private $dbname = 'tucanoto_air';
    private $host = 'localhost';
    private $db_user = 'root';
    private $db_psw = '';
//*/

    private $dbname = 'tucanoto_air';
    private $host = 'tucanotours.com.ar';
    private $db_user = 'tucanoto_api';
    private $db_psw = '%hRp?17E-1ru';

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

            $sql = "SELECT DISTINCT
                            codcias.Descripcion as Aerolinea,
                            fop_desc.descripcion as FOP,
                            tucanoto_api.usuarios.usuario AS Agente,
                            tkts_amadeus.pcc as PCC,
                            tkts_amadeus.DAY as Dia, 
                            tkts_amadeus.MONTH as Mes,
                            tkts_amadeus.YEAR as Year,
                            tkts_amadeus.tkt as Ticket,
                            tkts_amadeus.descripcion as Descripcion,
                            tkts_amadeus.gds as GDS
                        FROM tkts_amadeus
                            INNER JOIN codcias
                                ON tkts_amadeus.iata_num_code = codcias.iata_num_code
                            INNER JOIN fop_desc
                                ON tkts_amadeus.fop = fop_desc.fop
                            INNER JOIN tucanoto_api.usuarios
                                ON tucanoto_api.usuarios.sine_amadeus = tkts_amadeus.sine
                    UNION
                    SELECT DISTINCT
                        codcias.Descripcion as Aerolinea,
                        fop_desc.descripcion as FOP,
                        tucanoto_api.usuarios.usuario AS Agente,
                        tkts_sabre.pcc as PCC,
                        tkts_sabre.DAY as Dia, 
                        tkts_sabre.MONTH as Mes,
                        tkts_sabre.YEAR as Year,
                        tkts_sabre.tkt as Ticket,
                        tkts_sabre.descripcion as Descripcion,
                        tkts_sabre.gds as GDS
                    FROM tkts_sabre
                        INNER JOIN codcias
                            ON tkts_sabre.iata_num_code = codcias.iata_num_code
                        INNER JOIN fop_desc
                            ON tkts_sabre.fop = fop_desc.fop
                        INNER JOIN tucanoto_api.usuarios
                            ON tucanoto_api.usuarios.sine_sabre = tkts_sabre.sine";

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