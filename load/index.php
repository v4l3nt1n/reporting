<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'input_submit.php';
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tucano Tours - Carga de Reportes</title>
    <link rel="shortcut icon" href="img/tucano.ico" type="image/x-icon" />
    <link href="carga_reportes.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="wrapper">
        <div id="content">
            <div id="head">
                <div id="nombre-herr"><img alt="Herramienta de Carga de Reportes" src="img/carga.png" /></div>
                <div id="logos">
                    <a href="http://tucanotours.com.ar" target="_blank"><img id="tucano" alt="Tucano Tours" src="img/tucano-tours.png" width="187"/></a>
                    <a href="http://sci.tucanotours.com.ar" target="_blank"><img id="sci" alt="Sistema de Consolidación Integrada" src="img/sci.png" width="250"/></a>
                </div>
            </div>
            <img alt="" src="img/line_red.png" />
            <?php
                if ($error) {
                    echo '<div id="buscar" align="center" class="verda" style="color: #ed2d2d;">';
                    echo $error;
                    echo '</div>';
                }
            ?>
            <form id="buscar" method="post" action="" enctype="multipart/form-data">
                <input name="files[]" id="files" type="file" multiple="" />
                <input type="submit" value="Subir" class="btn primary" >
            </form>
        </div>
    </div>
</body>
</html>