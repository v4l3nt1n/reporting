<!DOCTYPE html>
<html>
    <head>
        <title>Tucano Reporting v1.0</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
        <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
        <!-- GCHARTS INIT -->
        <script type="text/javascript">google.load('visualization', '1.1', {packages: ['corechart', 'controls']});</script>
        <script type="text/javascript" src="js/reporting-graphs.js"></script>
        <script type="text/javascript">google.setOnLoadCallback(drawVisualization);</script>
        <!-- GCHARTS END -->
    </head>
    <body>
        <div id="dashboard" class="span12 hero-unit">
            <div style="width: 915px; margin: auto;">
                <div id="chart1" style='width: 1000px; height: 300px;'></div>
                <div id="control1" style='width: 1000px; height: 50px;'></div>
            </div>
            <div style="width: 915px; margin: auto;">
                <div id="chart2" style='width: 1000px; height: 300px;'></div>
                <div id="control2" style='width: 1000px; height: 50px;'></div>
            </div>
        </div>
    </body>
</html>