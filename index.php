<!DOCTYPE html>
<html>
    <head>
        <title>Tucano Reporting v1.0</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="//api.tucanotours.com.ar/bs/css/tucano.bs.css" rel="stylesheet">
        <link href="//api.tucanotours.com.ar/bs/css/todc.bs.css" rel="stylesheet">
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//api.tucanotours.com.ar/bs/js/bootstrap.min.js"></script>
        <!-- GCHARTS INIT -->
        <script type="text/javascript">google.load('visualization', '1.1', {packages: ['corechart', 'controls']});</script>
        <script type="text/javascript" src="js/reporting-graphs.js"></script>
        <!-- GCHARTS END -->
    </head>
    <body>
        <button class="btn">Chartttttt</button>
        <div id="dashboard1" class="span12 hero-unit">
            <div style="width: 915px; margin: auto; display: none;" class="graph">
                <div id="chart1" style='width: 1000px; height: 300px;'></div>
                <div id="control1" style='width: 1000px; height: 50px;'></div>
            </div>
        </div>
        <div id="dashboard2" class="span12 hero-unit">
            <div style="width: 915px; margin: auto; display: none;" class="graph">
                <div id="chart2" style='width: 1000px; height: 300px;'></div>
                <div id="control2" style='width: 1000px; height: 50px;'></div>
            </div>
        </div>
        <div id="dashboard3" class="span12 hero-unit">
            <div style="width: 915px; margin: auto; display: none;" class="graph">
                <div id="chart3" style='width: 1000px; height: 300px;'></div>
                <div id="control3" style='width: 1000px; height: 50px;'></div>
            </div>
        </div>
        <div class="graph span6" style="width: 600px; height: 400px;">
            <div id="piecolchart4"></div>
        </div>
        <div class="graph span6" style="width: 600px; height: 400px;">
            <div id="piecolchart5"></div>
        </div>
    </body>
</html>