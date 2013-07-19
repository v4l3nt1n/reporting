function drawVisualization() {
    var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard'));
/*
    var control = new google.visualization.ControlWrapper({
        'controlType': 'ChartRangeFilter',
        'containerId': 'control',
        'options': {
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
                'chartView': { 'columns': [0, 2] },
                // 1 day in milliseconds = 24 * 60 * 60 * 1000 = 86,400,000
                //'minRangeSize': 86400000
            }
        },
        // Initial range: 2012-02-09 to 2012-03-20.
        'state': {
            'range': {
                'start': new Date(2013, 5, 1),
                'end': new Date(2013, 5, 15)
            }
        }
    });
//*/
/*
    var control = new google.visualization.ControlWrapper({
        'controlType': 'ChartRangeFilter',
        'containerId': 'control',
        // Initial range: 2012-02-09 to 2012-03-20.
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
            'chartView': { 'columns': [0, 2] },
            // 1 day in milliseconds = 24 * 60 * 60 * 1000 = 86,400,000
            //'minRangeSize': 86400000
        }
    };

/*
    var chart = new google.visualization.ChartWrapper({
        'chartType': 'LineChart',
        'containerId': 'chart',
        'options': {
            // Use the same chart area width as the control for axis alignment.
            'chartArea': {'height': '80%', 'width': '90%'},
            'hAxis': {'slantedText': false},
            'vAxis': {'viewWindow': {'min': 0, 'max': 1200}},
            'legend': {'position': 'none'}
        },
        // Convert the first column from 'date' to 'string'.
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
*/
/*
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

    var dataTbl = new google.visualization.DataTable();
*/
/*
    dataTbl.addColumn('date', 'Date');
    dataTbl.addColumn('number', 'Amadeus');
    dataTbl.addColumn('number', 'Sabre');
    /*
    data.addColumn('number', 'Stock close');
    data.addColumn('number', 'Stock high');
    */      
    // Create random stock values, just like it works in reality.
    /*
    var open, close = 300;
    var low, high;
    for (var day = 1; day < 121; ++day) {
    var change = (Math.sin(day / 2.5 + Math.PI) + Math.sin(day / 3) - Math.cos(day * 0.7)) * 150;
    change = change >= 0 ? change + 10 : change - 10;
    open = close;
    close = Math.max(50, open + change);
    low = Math.min(open, close) - (Math.cos(day * 1.7) + 1) * 15;
    low = Math.max(0, low);
    high = Math.max(open, close) + (Math.cos(day * 1.3) + 1) * 15;
    var date = new Date(2012, 0 ,day);
    //data.addRow([date, Math.round(low), Math.round(open), Math.round(close), Math.round(high)]);
    data.addRow([date, Math.round(low), Math.round(open)]);
    }
    */
   
    var lineChartData;
/*
    $.ajax({
        type: 'post',
        //data: {dataArray: initArray},
        cache: false,
        url: "dataGetter.php",
        dataType: "json",
        success: function(data){
            chartOptions.vAxis.viewWindow.max = parseInt(data.max) + 200;
            chart.setOptions(chartOptions);
            var startIndex = parseInt(parseInt(data.data.length) / 3);
            var endIndex = parseInt(parseInt(data.data.length) - startIndex);
            var start = data.data[startIndex].fecha.split('-');
            var end = data.data[endIndex].fecha.split('-');

            control.setState({
                'range': {
                    'start': new Date(start[0], start[1] - 1, start[2]),
                    'end': new Date(end[0], end[1] - 1, end[2])
                }
            });

            for(i=0; i<data.data.length; i++){
                var fecha = data.data[i].fecha.split('-');
                dataTbl.addRow([new Date(parseInt(fecha[0]), parseInt(fecha[1]) - 1, parseInt(fecha[2])), parseInt(data.data[i].count_amadeus), parseInt(data.data[i].count_sabre)]);
            }
            dashboard.bind(control, chart);
            dashboard.draw(dataTbl);
         }
    });
*/
    $.ajax({
        type: 'post',
        //data: {dataArray: initArray},
        cache: false,
        url: "dataGetter.php",
        dataType: "json",
        success: function(data){
            //lineChartData = data;
            for(i=0; i <= data.length; i++){
                prepareLineChart(data[i],i);
            }
         }
    });

    /*
    function prepareLineChart(data){
        //chart.setOptions(chartOptions);
        var startIndex = parseInt(parseInt(data.data.length) / 3);
        var endIndex = parseInt(parseInt(data.data.length) - startIndex);
        var start = data.data[startIndex].fecha.split('-');
        var end = data.data[endIndex].fecha.split('-');

        //control.setOptions(controlOptions);
        control.setState({
            'range': {
                'start': new Date(start[0], start[1] - 1, start[2]),
                'end': new Date(end[0], end[1] - 1, end[2])
            }
        });

        chartOptions.vAxis.viewWindow.max = parseInt(data.settings.max) + 50;

        if (data.dimension == 'gds' || data.dimension == 'sine') {
            dataTbl.addColumn('date', 'Date');
            dataTbl.addColumn('number', 'Amadeus');
            dataTbl.addColumn('number', 'Sabre');

            chart.setOptions(chartOptions);
            control.setOptions(controlOptions);

            for(i=0; i<data.data.length; i++){
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

            chart.setOptions(chartOptions);

            controlOptions.ui.chartView.columns = [0, 1];
            control.setOptions(controlOptions);

            for(i=0; i<data.data.length; i++){
                var fecha = data.data[i].fecha.split('-');
                dataTbl.addRow([
                                new Date(parseInt(fecha[0]), parseInt(fecha[1]) - 1, parseInt(fecha[2])),
                                parseInt(data.data[i].count)
                ]);
            }
        }
    }
    */
   
    function prepareLineChart(data,key){
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
            // Initial range: 2012-02-09 to 2012-03-20.
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
                'chartView': { 'columns': [0, 2] },
                // 1 day in milliseconds = 24 * 60 * 60 * 1000 = 86,400,000
                //'minRangeSize': 86400000
            }
        };

        //chart.setOptions(chartOptions);
        var startIndex = parseInt(parseInt(data.data.length) / 3);
        var endIndex = parseInt(parseInt(data.data.length) - startIndex);
        var start = data.data[startIndex].fecha.split('-');
        var end = data.data[endIndex].fecha.split('-');

        //control.setOptions(controlOptions);
        control.setState({
            'range': {
                'start': new Date(start[0], start[1] - 1, start[2]),
                'end': new Date(end[0], end[1] - 1, end[2])
            }
        });

        chartOptions.vAxis.viewWindow.max = parseInt(data.settings.max) + 50;

        if (data.dimension == 'gds' || data.dimension == 'sine') {
            dataTbl.addColumn('date', 'Date');
            dataTbl.addColumn('number', 'Amadeus');
            dataTbl.addColumn('number', 'Sabre');

            chart.setOptions(chartOptions);
            control.setOptions(controlOptions);

            for(i=0; i<data.data.length; i++){
                var fecha = data.data[i].fecha.split('-');
                dataTbl.addRow([
                                new Date(parseInt(fecha[0]), parseInt(fecha[1]) - 1, parseInt(fecha[2])),
                                parseInt(data.data[i].count_amadeus),
                                parseInt(data.data[i].count_sabre)
                ]);
            }
/*
            var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard' + data.id));
            dashboard.bind(control, chart);
            dashboard.draw(dataTbl);
*/            
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

            chart.setOptions(chartOptions);

            controlOptions.ui.chartView.columns = [0, 1];
            control.setOptions(controlOptions);

            for(i=0; i<data.data.length; i++){
                var fecha = data.data[i].fecha.split('-');
                dataTbl.addRow([
                                new Date(parseInt(fecha[0]), parseInt(fecha[1]) - 1, parseInt(fecha[2])),
                                parseInt(data.data[i].count)
                ]);
            }
        }

        control.setContainerId('control' + data.id);
        chart.setContainerId('chart' + data.id);
        //var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard'));
        dashboard.bind(control, chart);
        dashboard.draw(dataTbl);
    }    
}