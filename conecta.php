<?php
    $comp = "localhost";
    $user = "root";
    $pw = "";
    $db = "db_projeto";
    $con = mysqli_connect($comp, $user, $pw, $db);

    function limpeza($entrada){
        $saida = trim($entrada);
        $saida = stripslashes($saida);
        $saida = htmlspecialchars($saida);
        return $saida;
    }
?>