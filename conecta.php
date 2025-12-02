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

    function totalFuncionarios($con){
        $sql = "SELECT COUNT(*) AS total FROM tb_funcionarios";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    function limpar()
    {}
?>