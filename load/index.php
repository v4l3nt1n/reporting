<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'input_submit.php';
}
?>
<html>
<head>
    <title>Prueba subida de reportes de venta para graficos</title>
    <link href="http://api.tucanotours.com.ar/bs/css/tucano.bs.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="css/coef-admin.css" rel="stylesheet">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="http://api.tucanotours.com.ar/bs/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/tktcompare.js"></script>
    <style type="text/css">
    table td {
        font-size: 10px;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <form method="post" action="" enctype="multipart/form-data">
        <input name="files[]" id="file" type="file" multiple=""/>
        <input type="submit" value="go!!!" >
    </form>
</body>

</html>