$(document).ready(function () {
    var clientObject    = {};
    var clientDashboard = {};
    var clientGraph     = {};
    var that, txt, objdata, value_sabre, value_amadeus;

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
    });

    // Recolecta los datos de los botones y arma el objeto clientObject para enviar al server
    $('.graficar').on('click', function(){
        clientObject.graph         = clientDashboard.find('.type').attr('objdata');
        clientObject.id            = clientDashboard.attr('id').slice(-1);
        clientObject.dimension     = clientDashboard.find('.campo').attr('objdata');
        clientObject.value         = clientDashboard.find('.value').attr('objdata');
        clientObject.value_amadeus = clientDashboard.find('.value_amadeus').attr('objdata');
        clientObject.value_sabre   = clientDashboard.find('.value_sabre').attr('objdata');
        clientObject.limit         = clientDashboard.find('.limit').val();

        console.log(clientObject);
    });
});