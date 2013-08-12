<!DOCTYPE html>
<html>
    <head>
        <title>Tucano Reporting v1.0</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="reporting.css" rel="stylesheet">
        <link href="//api.tucanotours.com.ar/bs/css/tucano.bs.css" rel="stylesheet">
        <link href="//api.tucanotours.com.ar/bs/css/todc.bs.css" rel="stylesheet">
        <link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
        <link rel="shortcut icon" href="img/tucano.ico" type="image/x-icon" />
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script src="//api.tucanotours.com.ar/bs/js/bootstrap.min.js"></script>
        <!-- GCHARTS INIT -->
        <script type="text/javascript" src="js/reporting-graphs.js"></script>
        <!-- GCHARTS END -->
        <script>
        $(function() {
            $( ".draggable" ).draggable({ handle: ".drag" });
        });
        </script>
    </head>
    <body>
        <div id="header">
            <div id="logos">
                <a href="http://www.tucanotours.com.ar" target="_blank"><img alt="Tucano Tours SRL" src="img/tucano-tours.png"/></a>
                <a href="http://sci.tucanotours.com.ar" target="_blank"><img alt="Sistema de Consolidación Integrada" src="img/sci.png"/></a>
                <img alt="Herramienta de Reporting" src="img/reporting.png"/>
            </div>
        </div>
        <div id="wrapper">
            <div class="row-fluid">
                <div id="dashboard4" class="span6 dashboard draggable">
                    <div class="row0">
                        <button data-toggle="buttons-checkbox" class="btn btn-inverse btn-mini editar">Editar</button>
                        <div class="graficar">
                            <button class="btn btn-mini">Graficar</button>
                        </div><!--btn graficar-->
                        <div class="drag">
                            <i class="icon-move" title="Arrastrar gráfico"></i>
                        </div>
                    </div>
                    <div class="tools" style="display: none;">
                        <div class="row1">
                            <div class="btn-group">
                                <button class="btn btn-mini btn-inverse type" objdata="">Gráfico</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu grafico">
                                    <li><a objdata="col">Barras</a></li>
                                    <li><a objdata="pie">Torta</a></li>
                                </ul>
                            </div><!--btn grafico-->
                            <div class="btn-group">
                                <button class="btn btn-mini btn-inverse campo" objdata="">Campo</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a objdata="sine">Agente</a></li>
                                    <!-- <li><a objdata="cia">Compañía</a></li> -->
                                    <li><a objdata="iata_num_code">Compañía</a></li>
                                    <li><a objdata="gds">Total Emisiones</a></li>
                                </ul>
                            </div><!--btn campo-->
                        </div><!--row1-->
                        
                        <div class="row2">
                            <!--
                            <div class="btn-group">
                                <button class="btn btn-mini btn-inverse" objdata="">Dimensión</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a dim="dim" objdata="gds">GDS</a></li>
                                    <li><a dim="dim" objdata="limit">Límite</a></li>
                                </ul>
                            </div><!--btn dimension-->

                            <div class="btn-group fetch-filters">
                                <button class="btn btn-mini btn-inverse filtro" objdata="">Filtro</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="date-filter">Mes</a>
                                        <ul class="dropdown-menu month-filter"></ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="date-filter">Año</a>
                                        <ul class="dropdown-menu year-filter"></ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="gds-filter">GDS</a>
                                        <ul class="dropdown-menu gds-filter"></ul>
                                    </li>
                                    <li><a dim="dim" objdata="limit">Límite</a></li>
                                </ul>
                            </div><!--btn filtro-->

                            <div class="limite" style="display:none;">
                                <input type="text" class="input-mini limit" placeholder="limite">
                            </div><!--Limite-->

                            <div class="btn-group fetch-filters">
                                <button class="btn btn-mini btn-inverse filtro" objdata="">Filtro</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="date-filter">Mes</a>
                                        <ul class="dropdown-menu month-filter"></ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="date-filter">Año</a>
                                        <ul class="dropdown-menu year-filter"></ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="gds-filter">GDS</a>
                                        <ul class="dropdown-menu gds-filter"></ul>
                                    </li>
                                    <li><a dim="dim" objdata="limit">Límite</a></li>
                                </ul>
                            </div><!--btn filtro-->

                            <div class="limite" style="display:none;">
                                <input type="text" class="input-mini limit" placeholder="limite">
                            </div><!--Limite-->

                            <div class="btn-group gds" style="display:none;">
                                <button class="btn btn-mini btn-success gds_value" objdata="">GDS</button>
                                <button class="btn btn-mini btn-success dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a objdata="sabre">Sabre</a></li>
                                    <li><a objdata="amadeus">Amadeus</a></li>
                                </ul>
                            </div>

                        </div><!--row2-->
                    </div><!--display:none-->
                    <div>
                        <div id="piecolchart4" class="pie"></div>
                    </div>
                </div><!--dashboard4-->
                <div id="dashboard5" class="span6 dashboard draggable">
                    <div class="row0">
                        <button data-toggle="buttons-checkbox" class="btn btn-inverse btn-mini editar">Editar</button>
                        <div class="graficar">
                            <button class="btn btn-mini">Graficar</button>
                        </div><!--btn graficar-->
                        <div class="drag">
                            <i class="icon-move" title="Arrastrar gráfico"></i>
                        </div>
                    </div>
                    <div class="tools" style="display: none;">
                        <div class="row1">
                            <div class="btn-group">
                                <button class="btn btn-mini btn-inverse type" objdata="">Gráfico</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu grafico">
                                    <li><a objdata="col">Barras</a></li>
                                    <li><a objdata="pie">Torta</a></li>
                                </ul>
                            </div><!--btn grafico-->
                            <div class="btn-group">
                                <button class="btn btn-mini btn-inverse campo" objdata="">Campo</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a objdata="sine">Agente</a></li>
                                    <!-- <li><a objdata="cia">Compañía</a></li> -->
                                    <li><a objdata="iata_num_code">Compañía</a></li>
                                    <li><a objdata="gds">Total Emisiones</a></li>
                                </ul>
                            </div><!--btn campo-->
                        </div><!--row1-->
                        <div class="drag">
                            <i class="icon-move" title="Arrastrar gráfico"></i>
                        </div>
                        <div class="row2">
                            <div class="btn-group">
                                <button class="btn btn-mini btn-inverse" objdata="">Dimensión</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a dim="dim" objdata="gds">GDS</a></li>
                                    <li><a dim="dim" objdata="limit">Límite</a></li>
                                </ul>
                            </div><!--btn dimension-->
                            <div class="limite" style="display:none;">
                                <input type="text" class="input-mini limit" placeholder="limite">
                            </div><!--Limite-->

                            <div class="btn-group gds" style="display:none;">
                                <button class="btn btn-mini btn-success gds_value" objdata="">GDS</button>
                                <button class="btn btn-mini btn-success dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a objdata="sabre">Sabre</a></li>
                                    <li><a objdata="amadeus">Amadeus</a></li>
                                </ul>
                            </div>

                            <div class="btn-group fetch-date">
                                <button class="btn btn-mini btn-inverse filtro" objdata="">Filtro</button>
                                <button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="date-filter">Mes</a>
                                        <ul class="dropdown-menu month-filter">
                                            <!-- <li><a objdata="year">meses</a></li> -->
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" id="date-filter">Año</a>
                                        <ul class="dropdown-menu year-filter">
                                            <!-- <li><a objdata="year">anios</a></li> -->
                                        </ul>
                                    </li>
                                </ul>
                            </div><!--btn filtro-->
                        </div><!--row2-->
                    </div>
                    <div>
                        <div id="piecolchart5" class="pie"></div>
                    </div>
                </div><!--dashboard4-->
            </div><!--row-fluid-->
            <div id="dashboard1" class="span12 dashboard draggable">
               <div class="row00">
                    <button data-toggle="buttons-checkbox" class="btn btn-inverse btn-mini editar">Editar</button>
                    <div class="graficar">
                        <button class="btn btn-mini">Graficar</button>
                    </div><!--btn graficar-->
                    <div class="drag">
                        <i class="icon-move" title="Arrastrar gráfico"></i>
                    </div>
                </div>
                <div class='tools' style="display:none;">
                    <div class="btn-group">
                        <button class="btn btn-inverse campo" >Tipo de Reporte</button>
                        <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a objdata="emisiones-full" href="#">Total Emisiones</a></li>
                            <li><a objdata="emisiones-gds" href="#">Total Emisiones x GDS</a></li>
                            <li><a objdata="emisiones-sine" href="#">Total Emisiones x Sine</a></li>
                        </ul>
                    </div>
                    <div class="criterio">
                        <input type="text" placeholder="Criterio de búsqueda" style='display: none;'></input>
                    </div>
                    <div class="btn-group fetch-sine" style="display:none;">
                        <button class="btn btn-success sine value" objdata="sine">Elija Agente</button>
                        <button class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu sine-filter">
                            <li><a objdata="sine-filter" value-sabre="SB" value-amadeus="AM">pepe_rino</a></li>
                            <li><a objdata="sine-filter" value-sabre="SB" value-amadeus="AM">luis_bernal</a></li>
                        </ul>
                    </div><!--btn agente-->
                </div>
                <div class="graph">
                    <div id="chart1" class="line"></div>
                    <div id="control1" class="line_small"></div>
                </div>
            </div> <!--dashboard1-->
            <div id="dashboard2" class="span12 dashboard draggable">
                <div class="row00">
                    <button data-toggle="buttons-checkbox" class="btn btn-inverse btn-mini editar">Editar</button>
                    <div class="graficar">
                        <button class="btn btn-mini">Graficar</button>
                    </div><!--btn graficar-->
                    <div class="drag">
                        <i class="icon-move" title="Arrastrar gráfico"></i>
                    </div>
                </div>
                <div class='tools' style="display:none;">
                    <div class="btn-group">
                        <button class="btn btn-inverse campo" >Tipo de Reporte</button>
                        <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a objdata="emisiones-full" href="#">Total Emisiones</a></li>
                            <li><a objdata="emisiones-gds" href="#">Total Emisiones x GDS</a></li>
                            <li><a objdata="emisiones-sine" href="#">Total Emisiones x Sine</a></li>
                        </ul>
                    </div>
                    <div class="criterio">
                        <input type="text" placeholder="Criterio de búsqueda" style='display: none;'></input>
                    </div>
                    <div class="btn-group fetch-sine" style="display:none;">
                        <button class="btn btn-success sine value" objdata="sine">Elija Agente</button>
                        <button class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu sine-filter">
                            <li><a objdata="sine-filter" value-sabre="SB" value-amadeus="AM">pepe_rino</a></li>
                            <li><a objdata="sine-filter" value-sabre="SB" value-amadeus="AM">luis_bernal</a></li>
                        </ul>
                    </div><!--btn agente-->
                </div>
                <div class="graph">
                    <div id="chart2" class="line"></div>
                    <div id="control2" class="line_small"></div>
                </div>
            </div> <!--dashboard1-->
        </div><!--wrapper-->
    </body>
</html>