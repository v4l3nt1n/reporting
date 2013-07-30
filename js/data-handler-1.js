$(document).ready(function () {
    var clientDashboard = {};
    var clientObject    = {};
    var clientGraph     = {};
    var that, txt, objdata, value_sabre, value_amadeus;

    $('.editar').on('click', function(){
        $('.tools').slideToggle();
    });

    // Esta funcion asigna el valor elegido al boton correspondiente
    $('.dropdown-menu li a').on('click', function(){
        clientDashboard = $(this).parent().parent().parent().parent();
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
        } else {
            fieldContainer.children('.btn:first').attr('objdata',objdata).html(txt);
        }
        //clientDashboard = {};
        clientObject    = {};
    });

    // Recolecta los datos de los botones y arma el objeto clientObject para enviar al server
    $('.graficar').on('click', function(){
        try {
            clientObject.graph = clientDashboard.find('.type').attr('objdata');
            clientObject.id            = clientDashboard.attr('id').slice(-1);
            clientObject.dimension     = clientDashboard.find('.campo').attr('objdata');
            clientObject.value         = clientDashboard.find('.value').attr('objdata');
            clientObject.value_sabre   = clientDashboard.find('.sine').attr('value-sabre');
            clientObject.value_amadeus = clientDashboard.find('.sine').attr('value-amadeus');
            clientObject.limit         = clientDashboard.find('.limit').val();
        } catch (err) {
            alert('Debe elegir un Gráfico.');
            return false;
        }


        if (validate(clientObject)) {
            console.log('validado');
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