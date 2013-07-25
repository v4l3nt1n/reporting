<?php

class SourceHandler
{
    // propiedades
    const CSV_DELIMITER = ';';

    const SOURCE_SABRE = 'sabre';
    const SOURCE_AMADEUS = 'amadeus';

    const INIT_COL_AMADEUS = 'SEQNRO';

    const FILTER_COL_SABRE = 'FECHA';
    const FILTER_COL_AMADEUS = 'TICKET';

    const CANN_COL_AMADEUS = 'TRNC';

    const TICKET_COL_SABRE = 'TICKET';

    private $files_array = array();

    private $ready_array_sabre = array();
    private $ready_array_amadeus = array();

    private $rpad_string_length = 10;

    private $ticket_col;
    private $col_cleaner_cols;
    private $cleaner_source;
    
    private $db;

    private $keys_array_sabre = array(
            'FECHA',
            'AEROLINEA',
            'TICKET',
            'DK',
            'PNR',
            'NOMBRE',
            'APELLIDO',
            'RUTA',
            'CLASE',
            'TOURCODE',
            'MONEDA',
            'FACIAL',
            'IMPUESTOS',
            'COMISION',
            'TOTAL_TKT',
            'MONTO_CASH',
            'MONTO_TARIFA',
            'FOP',
            'GARANTIA',
            'FOP_DETALLADA',
            'CUOTAS',
            'ENDOSO',
            '1ERVUELO',
            'ULTIMOVUELO',
            'BASE',
            'SIGN',
            'HORA',
            'PCC',
            'DESCRIPCION',
            'CORTETRF1',
    );

    private $keys_choose_sabre = array(
            'FECHA',
            'AEROLINEA',
            'TICKET',
            'PNR',
            'NOMBRE',
            'APELLIDO',
            'RUTA',
            'CLASE',
            'TOURCODE',
            'FACIAL',
            'IMPUESTOS',
            'COMISION',
            'TOTAL_TKT',
            'MONTO_CASH',
            'MONTO_TARIFA',
            'FOP',
            'FOP_DETALLADA',
            'BASE',
            'SIGN',
            'HORA',
            'DESCRIPCION',
    );

    private $keys_array_amadeus = array(
        'SEQNRO',
        'CIA',
        'TICKET',
        'TOTAL',
        'TAX',
        'FEE',
        'COMISION',
        'FOP',
        'PAX',
        'SINE',
        'PNR',
        'TRNC',
    );

    private $aux_pcc;
    private $aux_day;
    private $aux_month;
    private $aux_year;

    // metodos

    function __construct($files)
    {
        $this->files_array = $this->orderFilesArray($files);
        $this->determineSource();
        $this->process();
    }

    private function orderFilesArray($file_array)
    {
        $ordered_files = array();
        $file_count = count($file_array['files']['name']);
        $file_keys = array_keys($file_array['files']);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $ordered_files[$i][$key] = $file_array['files'][$key][$i];
            }
        }

        return $ordered_files;
    }

    private function determineSource()
    {
        foreach ($this->files_array as $key => $file) {
            $name = strtolower($file['name']);
            $name = substr($name,0, strpos($name,'.')-1);

            $inputFileName = $file['tmp_name'];

            if (strpos($name, SourceHandler::SOURCE_SABRE) !== FALSE) {
                $this->csvToArray($inputFileName);
            }

            if (strpos($name, SourceHandler::SOURCE_AMADEUS) !== FALSE) {
                $this->xlsToArray($inputFileName, SourceHandler::SOURCE_AMADEUS);
            }
        }
    }

    private function process()
    {
        $this->processSabre();
        $this->processAmadeus();
    }

    private function processSabre()
    {
        // asigno keys para luego elegirlas
        $this->ready_array_sabre = $this->keyAssign($this->ready_array_sabre,$this->keys_array_sabre);
        // quito las rows con las cabecereas
        $this->cleaner_source = SourceHandler::SOURCE_SABRE;
        $this->rowCleaner();
        // quito los tickets en conjuncion
        $this->ticket_col = SourceHandler::TICKET_COL_SABRE;
        $this->ready_array_sabre = array_map(array($this,'dismissCnjTkts'), $this->ready_array_sabre);
        // inserto los datos en la base
        $this->insert_source = SourceHandler::SOURCE_SABRE;
        $this->insertIntoDB();                
    }

    private function processAmadeus()
    {
        // asigno keys para luego elegirlas
        $this->ready_array_amadeus = $this->keyAssign($this->ready_array_amadeus,$this->keys_array_amadeus);
        // quito las rows con las cabecereas
        $this->cleaner_source = SourceHandler::SOURCE_AMADEUS;
        $this->rowCleaner();
        // inserto los datos en la base
        $this->insert_source = SourceHandler::SOURCE_AMADEUS;
        $this->insertIntoDB();        
    }

    private function xlsToArray($inputFileName,$source)
    {
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        if ($source == SourceHandler::SOURCE_AMADEUS) {
            $this->ready_array_amadeus = array_merge($this->ready_array_amadeus,$objPHPExcel->getActiveSheet()->toArray(null,true,true,true));
        }
    }

    private function csvToArray($inputFileName)
    {
        if (($handle = fopen($inputFileName, "r")) !== FALSE) {
            $csvarray = array();
            # Set the parent multidimensional array key to 0.
            $nn = 0;
            while (($data = fgetcsv($handle, 0, SourceHandler::CSV_DELIMITER)) !== FALSE) {
                # Count the total keys in the row.
                $c = count($data);
                # Populate the multidimensional array.
                for ($x=0;$x<$c;$x++) {
                    $csvarray[$nn][$x] = $data[$x];
                }
                $nn++;
            }
            $this->ready_array_sabre = array_merge($this->ready_array_sabre, $csvarray);
            # Close the File.
            fclose($handle);
        }
    }

    private function keyAssign($elem, $keys)
    {
        foreach ($elem as $key => $row) {
            $elem[$key] = array_combine($keys, $row);
        }

        return $elem;
    }

    private function rpad($elem)
    {
        $length = $this->rpad_string_length;
        $elem[$this->ticket_col] = substr($elem[$this->ticket_col], strlen($elem[$this->ticket_col])-$length,$length);
        return $elem;
    }

    private function dismissCnjTkts($elem)
    {
        $elem[$this->ticket_col] = substr($elem[$this->ticket_col], 0, 10);
        return $elem;
    }

    private function rowCleaner()
    {
        if ($this->cleaner_source == SourceHandler::SOURCE_SABRE) {            
            foreach ($this->ready_array_sabre as $key => $ticket) {
                if ($ticket[SourceHandler::FILTER_COL_SABRE] == 'FECHA') {
                    unset($this->ready_array_sabre[$key]);
                } else {
                    $day = substr($ticket[SourceHandler::FILTER_COL_SABRE], 0, 2);
                    $month = substr($ticket[SourceHandler::FILTER_COL_SABRE], 2, 3);
                    $year = substr($ticket[SourceHandler::FILTER_COL_SABRE], 5, 2);
                    if (empty($ticket['DESCRIPCION'])) {
                        $ticket['DESCRIPCION'] = 'ISSUE';
                    }
                    $ticket['day'] = $day;
                    $ticket['month'] = strtoupper($month);
                    $ticket['year'] = '20'. $year;
                    $ticket['gds'] = strtoupper(SourceHandler::SOURCE_SABRE);
                    $this->ready_array_sabre[$key] = $ticket;
                }
            }
        }

        if ($this->cleaner_source == SourceHandler::SOURCE_AMADEUS) {
            foreach ($this->ready_array_amadeus as $key => $ticket) {
                $init_col = trim($ticket[SourceHandler::INIT_COL_AMADEUS]);
                // valido si el elemento contiene la fecha y la seteo para los
                // proximos elementos a una fecha
                if (preg_match('/^\d{1,2}\-[a-zA-Z]{3}$/', $init_col)) {
                    $fecha = explode('-', $init_col);

                    $this->aux_day = $fecha[0];
                    $this->aux_month = $fecha[1];
                    $mes_source = DateTime::createFromFormat('M',$this->aux_month);
                    $mes_today = new DateTime("now",new DateTimeZone('ART'));

                    if ($mes_source > $mes_today) {
                        $this->aux_year = date('y') - 1;
                    } else {
                        $this->aux_year = date('y');
                    }
                }
                //obtengo el office id
                if (trim(substr($ticket[SourceHandler::INIT_COL_AMADEUS],0,strpos($ticket[SourceHandler::INIT_COL_AMADEUS], "-") - 1)) == 'OFFICE') {
                    $this->aux_pcc = trim(substr($ticket[SourceHandler::INIT_COL_AMADEUS],8,strpos($ticket[SourceHandler::INIT_COL_AMADEUS], "-") + 5));
                }

                if (
                    $ticket[SourceHandler::FILTER_COL_AMADEUS] == '' ||
                    $ticket[SourceHandler::INIT_COL_AMADEUS] == '' ||
                    $ticket[SourceHandler::FILTER_COL_AMADEUS] == 'DOC NUMBER' ||
                    $ticket[SourceHandler::CANN_COL_AMADEUS] == 'CANN')
                {
                    unset($this->ready_array_amadeus[$key]);
                }else{
                    $ticket['pcc'] = $this->aux_pcc;
                    $ticket['day'] = $this->aux_day;
                    $ticket['month'] = strtoupper($this->aux_month);
                    $ticket['year'] = '20'.$this->aux_year;
                    $ticket['gds'] = strtoupper(SourceHandler::SOURCE_AMADEUS);
                    $this->ready_array_amadeus[$key] = $ticket;
                }
            }
        }
    }

    private function colCleaner($elem)
    {
        foreach ($elem as $key => $value) {
            if (!in_array($key, $this->col_cleaner_cols)) {
                unset($elem[$key]);
            }
        }
        return $elem;
    }

    public function getSources()
    {
        return array(
                SourceHandler::SOURCE_SABRE => $this->ready_array_sabre,
                SourceHandler::SOURCE_AMADEUS => $this->ready_array_amadeus,
            );
    }

    private function DBHandler()
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=tucanoto_reservas','root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
    }

    private function insertIntoDB()
    {
        $this->DBHandler();

        if ($this->insert_source == SourceHandler::SOURCE_SABRE) {
            $stmt = $this->db->prepare("INSERT INTO tkts_sabre VALUES (null,:fecha,:cia,:tkt,:dk,:pnr,:nombre,:apellido,
                                                                   :ruta,:clase,:tourcode,:moneda,:facial,:impuestos,
                                                                   :comision,:total,:monto_cash,:monto_tarjeta,:fop,
                                                                   :garantia,:fop_detalle,:cuotas,:endoso,:fecha_1ervuelo,
                                                                   :fecha_ultimovuelo,:base_tarifa,:sine,:hora,:pcc,:descripcion,
                                                                   :corte_tarifario1,:day,:month,:year,:gds)");
            try{
                $this->db->beginTransaction();

                foreach ($this->ready_array_sabre as $key => $ticket) {
                    $stmt->execute(array(':fecha'                  => $ticket['FECHA'],
                                         ':cia'                    => $ticket['AEROLINEA'],
                                         ':tkt'                    => $ticket['TICKET'],
                                         ':dk'                     => $ticket['DK'],
                                         ':pnr'                    => $ticket['PNR'],
                                         ':nombre'                 => $ticket['NOMBRE'],
                                         ':apellido'               => $ticket['APELLIDO'],
                                         ':ruta'                   => $ticket['RUTA'],
                                         ':clase'                  => $ticket['CLASE'],
                                         ':tourcode'               => $ticket['TOURCODE'],
                                         ':moneda'                 => $ticket['MONEDA'],
                                         ':facial'                 => $ticket['FACIAL'],
                                         ':impuestos'              => $ticket['IMPUESTOS'],
                                         ':comision'               => $ticket['COMISION'],
                                         ':total'                  => $ticket['TOTAL_TKT'],
                                         ':monto_cash'             => $ticket['MONTO_CASH'],
                                         ':monto_tarjeta'          => $ticket['MONTO_TARIFA'],
                                         ':fop'                    => $ticket['FOP'],
                                         ':garantia'               => $ticket['GARANTIA'],
                                         ':fop_detalle'            => $ticket['FOP_DETALLADA'],
                                         ':cuotas'                 => $ticket['CUOTAS'],
                                         ':endoso'                 => $ticket['ENDOSO'],
                                         ':fecha_1ervuelo'         => $ticket['1ERVUELO'],
                                         ':fecha_ultimovuelo'      => $ticket['ULTIMOVUELO'],
                                         ':base_tarifa'            => $ticket['BASE'],
                                         ':sine'                   => $ticket['SIGN'],
                                         ':hora'                   => $ticket['HORA'],
                                         ':descripcion'            => $ticket['DESCRIPCION'],
                                         ':corte_tarifario1'       => $ticket['CORTETRF1'],
                                         ':pcc'                    => $ticket['PCC'],
                                         ':day'                    => $ticket['day'],
                                         ':month'                  => $ticket['month'],
                                         ':year'                   => $ticket['year'],
                                         ':gds'                    => strtoupper(SourceHandler::SOURCE_SABRE),
                ));
            }
                $this->db->commit();  
            } catch(Exception $e) {
                $this->db->rollBack();
                echo($e->getMessage());
            }
        }

        if ($this->insert_source == SourceHandler::SOURCE_AMADEUS) {
            try{
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("INSERT INTO tkts_amadeus VALUES (null,:nro_seq,
                                                                      :cia,
                                                                      :tkt,
                                                                      :facial,
                                                                      :impuestos,
                                                                      :fee,
                                                                      :comision,
                                                                      :fop,
                                                                      :pax,
                                                                      :sine,
                                                                      :pnr,
                                                                      :tipo_trn,
                                                                      :oid,
                                                                      :day,
                                                                      :month,
                                                                      :year,
                                                                      :gds
                                                                      )");

                foreach ($this->ready_array_amadeus as $key => $ticket) {
                    $stmt->execute(array(':nro_seq'            => $ticket['SEQNRO'],
                                         ':cia'                => $ticket['CIA'],
                                         ':tkt'                => $ticket['TICKET'],
                                         ':facial'             => $ticket['TOTAL'],
                                         ':impuestos'          => $ticket['TAX'],
                                         ':fee'                => $ticket['FEE'],
                                         ':comision'           => $ticket['COMISION'],
                                         ':fop'                => $ticket['FOP'],
                                         ':pax'                => $ticket['PAX'],
                                         ':sine'               => $ticket['SINE'],
                                         ':pnr'                => $ticket['PNR'],
                                         ':tipo_trn'           => $ticket['TRNC'],
                                         ':oid'                => $ticket['pcc'],
                                         ':day'                => $ticket['day'],
                                         ':month'              => $ticket['month'],
                                         ':year'               => $ticket['year'],
                                         ':gds'                => strtoupper(SourceHandler::SOURCE_AMADEUS),
                        ));
                }

                $this->db->commit();
            } catch(Exception $e) {
                $this->db->rollBack();
                echo($e->getMessage());
            }            
        }
    }
}