google.load('visualization', '1.1', {
    packages: ['corechart', 'controls']
});

$(document).ready(function () {
    var clientDashboard = {};
    var clientObject    = {};
    var clientGraph     = {};
    var editModeFlag = false;
    var that, txt, objdata, value_sabre, value_amadeus, dim;    
    var dashBoards = [];
    var dataTables = [];
    
    //initGraphs();
    $( ".draggable" ).draggable();
    
    // boton a eliminar estaba a propositos de testing
    $('#chart').on('click', function(){
        $('.graph').fadeOut();
        initGraphs();
    });

    function initGraphs (clientObject) {
        $.ajax({
            type: 'post',
            data: {dataArray: clientObject},
            cache: false,
            url: "dataGetter.php",
            dataType: "json",
            success: function(data){
                var l = data.length;
                var graphObject;
                for(var i = 0; i < l; i++){
                    graphObject = drawVisualization(data[i],i);
                    if (data[i].graph == 'LineChart') {
                        dashBoards[i].bind(graphObject.control,graphObject.chart);
                        clearClose();
                        dashBoards[i].draw(dataTables[i]);
                    }
                    if (data[i].graph == 'PieChart' ||
                        data[i].graph == 'ColumnChart'
                        ) {
                        clearClose();
                        graphObject.chart.draw(graphObject.table,{title: "PieChart"});
                    }
                }
            }
        });
    }

    function clearClose () {
        editModeFlag = false;
        dashBoards   = [];
        dataTables   = [];
        //$('.graph').fadeIn();
        $('.tools').slideUp();
        clientObject = {};
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

    function fetchDates () {
        $.ajax({
            type: 'post',
            data: 'action=fetchDate',
            cache: false,
            url: "dataGetter.php",
            dataType: "json",
            success: function(data){
                $('.month-filter').html('');
                $('.year-filter').html('');
                
                for (var i = 0 ; i < data.month.length; i++) {
                    $('.month-filter').append('<li><a objdata="month">' + data.month[i] + '</a></li>');
                };

                for (var i = 0 ; i < data.year.length; i++) {
                    $('.year-filter').append('<li><a objdata="' + data.year[i] + '">' + data.year[i] + '</a></li>');
                };
            }
        });
    }

    $('.editar').on('click', function(){
        clientObject    = {};
        $('.tools').slideToggle();
        clientDashboard = $(this).parent();
        editModeFlag = true;
    });

    $('.fetch-date').on('click', function(){
        fetchDates();
    });

    // esta funcion asigna el valor del filtro
    $('.dropdown-submenu').on('click', function(event){
        that = $(event.target);
        txt = that.html();
        objdata = that.attr('objdata');
        fieldContainer = that.parent().parent().parent().parent().parent();
        fieldContainer.children('.btn:first').attr('objdata', objdata).html(txt);
    });

    // Esta funcion asigna el valor elegido al boton correspondiente
    $('.dropdown-menu li a').on('click', function(){
        that = $(this);

        // si es el filtro de la fecha no asigno el valor, uso otra funcion
        if (that.attr('id') == "date-filter") {
            return false;
        }

        fieldContainer = $(this).parent().parent().parent();
        txt = that.html();
        objdata = that.attr('objdata');
        dim = that.attr('dim');

        if (objdata == 'sine') {
            value_sabre = that.attr('value-sabre');
            value_amadeus = that.attr('value-amadeus');
            fieldContainer.children('.btn:first').attr('objdata',objdata)
                                                 .attr('value-amadeus',value_amadeus)
                                                 .attr('value-sabre',value_sabre)
                                                 .html(txt);
        } else {
            fieldContainer.children('.btn:first').attr('objdata',objdata).html(txt);
        }

        if (objdata == 'limit' && dim == 'dim') {
            clientDashboard.find('.gds').fadeOut();
            clientDashboard.find('.gds_value').attr('objdata','');
            clientDashboard.find('.limite').fadeIn();
        }

        if (objdata == 'gds' && dim == 'dim') {
            clientDashboard.find('.limite').fadeOut();
            clientDashboard.find('.limit').val('');
            clientDashboard.find('.gds').fadeIn();
        }
    });

    // Recolecta los datos de los botones y arma el objeto clientObject para enviar al server
    $('.graficar').on('click', function(){
        if (!editModeFlag) {
            alert('Para graficar se debe activar el modo Editar.');
            return false;
        }

        console.log(clientDashboard);

        try {
            clientObject.graph             = clientDashboard.find('.type').attr('objdata');
            clientObject.id                = clientDashboard.attr('id').slice(-1);
            clientObject.dimension         = clientDashboard.find('.campo').attr('objdata');
            clientObject.value             = clientDashboard.find('.value').attr('objdata');
            clientObject.value_sabre       = clientDashboard.find('.sine').attr('value-sabre');
            clientObject.value_amadeus     = clientDashboard.find('.sine').attr('value-amadeus');
            clientObject.limit             = clientDashboard.find('.limit').val();
            clientObject.filtro            = clientDashboard.find('.filtro').attr('objdata');
            clientObject.filtro_value      = clientDashboard.find('.filtro').html();
            clientObject.filtro_gds        = clientDashboard.find('.gds_value').attr('objdata');
        } catch (err) {
            alert('Ocurrió un error, configure el grafico nuevamente.');
            //alert(err);
            return false;
        }

        console.log(clientObject);

        //seteo un limite para cuando traemos la comparecion de los gds
        if (clientObject.dimension == 'gds' && clientObject.graph != 'line') {
            clientObject.limit = 3;
        }

        if (validate(clientObject)) {
            console.log('validado');
            initGraphs(clientObject);
        } else {
            console.log('no validado');
        }
    });
});

function validate (obj) {
    var error = "";

    if (obj.dimension.length == 0 ) {
        error += 'Debes setear un Campo. ';
    }

    if (obj.dimension == 'sine' && obj.graph == 'line') {
        if (typeof obj.value == 'undefined'){
            error += 'Debes indicar un agente. ';
        }
    }

    if (error) {
        alert(error);
        return false;
    } else {
        return true;
    }
}