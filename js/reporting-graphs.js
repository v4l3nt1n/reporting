$(document).ready(function () {

    var dashBoards = new Array;
    var dataTables = new Array;

    //initGraphs();

    $('button').on('click', function(){
        $('.graph').fadeOut();
        initGraphs();
    });

    function initGraphs () {
        $.ajax({
            type: 'post',
            //data: {dataArray: initArray},
            cache: false,
            url: "dataGetter.php",
            dataType: "json",
            success: function(data){
                var l = data.length;
                for(var i = 0; i < l; i++){
                    var graphObject = drawVisualization(data[i],i);
                    if (data[i].graph == 'LineChart') {
                        dashBoards[i].bind(graphObject.control,graphObject.chart);
                        dashBoards[i].draw(dataTables[i]);
                    }
                    if (data[i].graph == 'PieChart' ||
                        data[i].graph == 'ColumnChart'
                        ) {
                        graphObject.chart.draw(graphObject.table,{title: "PieChart"});
                    }
                }
                dashBoards = [];
                dataTables = [];
                $('.graph').fadeIn();
            }
        });
    }

    function drawVisualization(data) {
        if (data.graph == 'LineChart') {
            return prepareLineChart(data);
        }
        if (data.graph == 'PieChart' || data.graph == 'ColumnChart') {
            return preparePieColChart(data);
        }
    }
       
    function prepareLineChart(data){
        var dId = 'dashboard' + data.id.toString();
        var dashboard = new google.visualization.Dashboard(document.getElementById(dId));
        var dataTbl = new google.visualization.DataTable();

        var chart = new google.visualization.ChartWrapper({
            'chartType': 'LineChart',
            'containerId': 'chart',
            // Convert the first column from 'date' to 'string'.
            'view': {
                'columns': [{
                        'calc': function(dataTable, rowIndex) {
                            return dataTable.getFormattedValue(rowIndex, 0);
                        },
                        'type': 'string'
                    }, 1,2
                ]
            }
        });

        var chartOptions = {
                // Use the same chart area width as the control for axis alignment.
                'chartArea': {'height': '80%', 'width': '90%'},
                'hAxis': {'slantedText': false},
                'vAxis': {'viewWindow': {'min': 0, 'max': 1200}},
                'legend': {'position': 'none'}
        };

        var control = new google.visualization.ControlWrapper({
            'controlType': 'ChartRangeFilter',
            'containerId': 'control',
            'state': {
                'range': {
                    'start': new Date(2013, 5, 1),
                    'end': new Date(2013, 5, 15)
                }
            }
        });

        var controlOptions = {
            // Filter by the date axis.
            'filterColumnIndex': 000,
            'ui': {
                'chartType': 'LineChart',
                'chartOptions': {
                    'chartArea': {'width': '90%'},
                    'hAxis': {'baselineColor': 'none'}
                },
                // Display a single series that shows the closing value of the stock.
                // Thus, this view has two columns: the date (axis) and the stock value (line series).
                'chartView': { 'columns': [0, 1] },
            }
        };

        var startIndex = parseInt(parseInt(data.data.length) / 3);
        var endIndex = parseInt(parseInt(data.data.length) - startIndex);
        var start = data.data[startIndex].fecha.split('-');
        var end = data.data[endIndex].fecha.split('-');

        control.setOptions(controlOptions);
        control.setState({
            'range': {
                'start': new Date(start[0], start[1] - 1, start[2]),
                'end': new Date(end[0], end[1] - 1, end[2])
            }
        });

        if (data.dimension == 'gds' || data.dimension == 'sine') {
            dataTbl.addColumn('date', 'Date');
            dataTbl.addColumn('number', 'Amadeus');
            dataTbl.addColumn('number', 'Sabre');
            chart.setView({
                'view': {
                    'columns': [{
                            'calc': function(dataTable, rowIndex) {
                                return dataTable.getFormattedValue(rowIndex, 0);
                            },
                            'type': 'string'
                        }, 1, 2
                    ]
                }
            });
            chartOptions.vAxis.viewWindow.max = parseInt(data.settings.max);
            chart.setOptions(chartOptions);

            for(i=0; i < data.data.length; i++){
                var fecha = data.data[i].fecha.split('-');
                dataTbl.addRow([
                                new Date(parseInt(fecha[0]), parseInt(fecha[1]) - 1, parseInt(fecha[2])),
                                parseInt(data.data[i].count_amadeus),
                                parseInt(data.data[i].count_sabre)
                ]);
            }          
        }

        if (data.dimension == 'tkt') {
            dataTbl.addColumn('date', 'Date');
            dataTbl.addColumn('number', 'Tickets');

            chart.setView({
                'view': {
                    'columns': [{
                            'calc': function(dataTable, rowIndex) {
                                return dataTable.getFormattedValue(rowIndex, 0);
                            },
                            'type': 'string'
                        }, 1
                    ]
                }
            });
            chartOptions.vAxis.viewWindow.max = parseInt(data.settings.max);
            chart.setOptions(chartOptions);

            for(i=0; i<data.data.length; i++){
                var fecha = data.data[i].fecha.split('-');
                dataTbl.addRow([
                                new Date(parseInt(fecha[0]), parseInt(fecha[1]) - 1, parseInt(fecha[2])),
                                parseInt(data.data[i].count)
                ]);
            }
        }

        control.setContainerId('control' + data.id.toString());
        chart.setContainerId('chart' + data.id.toString());

        var graphObject = {};
        graphObject.control = control;
        graphObject.chart   = chart;

        dashBoards.push(dashboard);
        dataTables.push(dataTbl);

        return graphObject;
    }

    function preparePieColChart(data){
        var dId = 'piecolchart' + data.id.toString();
        if (data.graph == 'PieChart') {
            var chart = new google.visualization.PieChart(document.getElementById(dId));
        }
        
        if (data.graph == 'ColumnChart') {
            var chart = new google.visualization.ColumnChart(document.getElementById(dId));
        }

        var arrayData = new Array;
        arrayData.push([data.dimension.toString(), 'Emisiones']);

        for (var i = 0; i < data.data.length; i++) {
            arrayData.push([data.data[i].dimension,parseInt(data.data[i].count)]);
        }

        var dataTbl = google.visualization.arrayToDataTable(arrayData);

        var graphObject = {};
        graphObject.chart = chart;
        graphObject.table = dataTbl;

        return graphObject;
    }
});
