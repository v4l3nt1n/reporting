<!DOCTYPE html>
<html>
    <head>
        <title>Tucano Reporting v1.0</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="reporting.css" rel="stylesheet">
        <link href="//api.tucanotours.com.ar/bs/css/tucano.bs.css" rel="stylesheet">
        <link href="//api.tucanotours.com.ar/bs/css/todc.bs.css" rel="stylesheet">
        <link rel="shortcut icon" href="img/tucano.ico" type="image/x-icon" />
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//api.tucanotours.com.ar/bs/js/bootstrap.min.js"></script>
        <!-- GCHARTS INIT -->
        <script type="text/javascript">google.load('visualization', '1.1', {packages: ['corechart', 'controls']});</script>
        <script type="text/javascript" src="js/reporting-graphs.js"></script>
        <!-- GCHARTS END -->
    </head>
    <body>

        <div class="container">
            <div id="header" class="shadow_inset">
                HOlña
            </div>
            <button id="chart" class="btn">Chartttttt</button>
            <div id="dashboard1" class="span12 dashboard">
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
                <div class="graph">
                    <div id="chart1" class="line"></div>
                    <div id="control1" class="line_small"></div>
                </div>
            </div>
            <div id="dashboard2" class="span12 dashboard">
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
                <div class="graph">
                    <div id="chart2" class="line"></div>
                    <div id="control2" class="line_small"></div>
                </div>
            </div>
            <div id="dashboard3" class="span12 dashboard">
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
                <div class="graph">
                    <div id="chart3" class="line"></div>
                    <div id="control3" class="line_small"></div>
                </div>
            </div>
            <div class="row-fluid">
                <div id="dashboard4" class="span6 dashboard">
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
                    <div>
                        <div id="piecolchart4" class="pie"></div>
                    </div>
                </div>
                <div id="dashboard5" class="span6 dashboard">
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
                    <div>
                        <div id="piecolchart5" class="pie"></div>
                    </div>
                </div>
            </div>
        </div><!--container-->
    </body>
</html>