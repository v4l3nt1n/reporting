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
        <script>
            $(function() {
                $( ".draggable" ).draggable();
            });
        </script>
        <!-- GCHARTS INIT -->
        <script type="text/javascript">google.load('visualization', '1.1', {packages: ['corechart', 'controls']});</script>
        <script type="text/javascript" src="js/reporting-graphs.js"></script>
        <!-- GCHARTS END -->
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
            <button id="chart" class="btn">Chartttttt</button>
            <div id="dashboard1" class="span12 dashboard draggable">
                <div class="btn-group">
                    <button class="btn btn-inverse">Tipo de gráfico</button>
                    <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Líneas</a></li>
                        <li><a href="#">Barras</a></li>
                        <li><a href="#">Torta</a></li>
                    </ul>
                </div>
                <div class="criterio">
                    <input type="text" placeholder="Criterio de búsqueda"></input>
                </div>
                <div class="graficar">
                    <button class="btn btn-inverse">Graficar</button>
                </div>
                <div class="drag">
                    <i class="icon-move" title="Arrastrar gráfico"></i>
                </div>
                <div class="graph">
                    <div id="chart1" class="line"></div>
                    <div id="control1" class="line_small"></div>
                </div>
            </div><!--dashboard1-->
            <div id="dashboard2" class="span12 dashboard draggable">
                <div class="btn-group">
                    <button class="btn btn-inverse">Tipo de gráfico</button>
                    <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Líneas</a></li>
                        <li><a href="#">Barras</a></li>
                        <li><a href="#">Torta</a></li>
                    </ul>
                </div>
                <div class="criterio">
                    <input type="text" placeholder="Criterio de búsqueda"></input>
                </div>
                <div class="graficar">
                    <button class="btn btn-inverse">Graficar</button>
                </div>
                <div class="drag">
                    <i class="icon-move" title="Arrastrar gráfico"></i>
                </div>
                <div class="graph">
                    <div id="chart2" class="line"></div>
                    <div id="control2" class="line_small"></div>
                </div>
            </div><!--dashboard2-->
            <div id="dashboard3" class="span12 dashboard draggable">
                <div class="btn-group">
                    <button class="btn btn-inverse">Tipo de gráfico</button>
                    <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Líneas</a></li>
                        <li><a href="#">Barras</a></li>
                        <li><a href="#">Torta</a></li>
                    </ul>
                </div>
                <div class="criterio">
                    <input type="text" placeholder="Criterio de búsqueda"></input>
                </div>
                <div class="graficar">
                    <button class="btn btn-inverse">Graficar</button>
                </div>
                <div class="drag">
                    <i class="icon-move" title="Arrastrar gráfico"></i>
                </div>
                <div class="graph">
                    <div id="chart3" class="line"></div>
                    <div id="control3" class="line_small"></div>
                </div>
            </div><!--dashboard3-->
            <div class="row-fluid">
                <div id="dashboard4" class="span6 dashboard draggable">
                    <div class="btn-group">
                        <button class="btn btn-inverse">Tipo de gráfico</button>
                        <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Líneas</a></li>
                            <li><a href="#">Barras</a></li>
                            <li><a href="#">Torta</a></li>
                        </ul>
                    </div>
                    <div class="graficar">
                        <button class="btn btn-inverse">Graficar</button>
                    </div>
                    <div class="drag">
                        <i class="icon-move" title="Arrastrar gráfico"></i>
                    </div>
                    <div>
                        <div id="piecolchart4" class="pie"></div>
                    </div>
                </div><!--dashboard4-->
                <div id="dashboard5" class="span6 dashboard draggable">
                    <div class="btn-group">
                        <button class="btn btn-inverse">Tipo de gráfico</button>
                        <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Líneas</a></li>
                            <li><a href="#">Barras</a></li>
                            <li><a href="#">Torta</a></li>
                        </ul>
                    </div>
                    <div class="graficar">
                        <button class="btn btn-inverse">Graficar</button>
                    </div>
                    <div class="drag">
                        <i class="icon-move" title="Arrastrar gráfico"></i>
                    </div>
                    <div>
                        <div id="piecolchart5" class="pie"></div>
                    </div>
                </div><!--dashboard5-->
            </div><!--row-fluid-->
        </div><!--wrapper-->
    </body>
</html>