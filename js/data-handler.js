$(document).ready(function () {
    var clientDashboard = {};
    var clientObject    = {};
    var clientGraph     = {};
    var editModeFlag = false;
    var that, txt, objdata, value_sabre, value_amadeus;

    $('.editar').on('click', function(){
        clientObject    = {};
        $('.tools').slideToggle();
        clientDashboard = $(this).parent();
        editModeFlag = true;
    });

    // Esta funcion asigna el valor elegido al boton correspondiente
    $('.dropdown-menu li a').on('click', function(){
        //clientDashboard = $(this).parent().parent().parent().parent();
        fieldContainer = $(this).parent().parent().parent();
        that = $(this);
        txt = that.html();
        objdata = that.attr('objdata');

        if (objdata == 'sine') {
            value_sabre = that.attr('value-sabre');
            value_amadeus = that.attr('value-amadeus');
            fieldContainer.children('.btn:first').attr('objdata',objdata)
                                                 .attr('value-amadeus',value_amadeus)
                                                 .attr('value-sabre',value_sabre)
                                                 .html(txt);
            // hago la llamada para traerme los sines disponibles para filtrar
            // populateSines();
            // muestro el elemento con los sines
        } else {
            fieldContainer.children('.btn:first').attr('objdata',objdata).html(txt);
        }

        if (objdata == 'limit') {
            //alert(objdata);
            clientDashboard.find('.limite').fadeIn();
        } else {
            clientDashboard.find('.limite').fadeOut();
        }

        if (objdata == 'gds') {
            clientDashboard.find('.gds').fadeIn();
        } else {
            clientDashboard.find('.gds').fadeOut();
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
            clientObject.graph         = clientDashboard.find('.type').attr('objdata');
            clientObject.id            = clientDashboard.attr('id').slice(-1);
            clientObject.dimension     = clientDashboard.find('.campo').attr('objdata');
            clientObject.value         = clientDashboard.find('.value').attr('objdata');
            clientObject.value_sabre   = clientDashboard.find('.sine').attr('value-sabre');
            clientObject.value_amadeus = clientDashboard.find('.sine').attr('value-amadeus');
            clientObject.limit         = clientDashboard.find('.limit').val();
        } catch (err) {
            alert('Ocurrió un error, configure el grafico nuevamente.');
            //alert(err);
            return false;
        }

        console.log(clientObject);

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

    if (obj.dimension == 'sine' && (obj.graph == 'pie' || obj.graph == 'col')) {
        if (typeof obj.value_sabre == 'undefined' ||
            typeof obj.value_amadeus == 'undefined')
        {
            error += 'Debes indicar un agente. ';
        }
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