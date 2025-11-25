<?php
    if(isset($_POST['txtnome']) && isset($_POST['txtprofissao'])){
        if(isset($_POST['txtsalario']) && is_numeric($_POST['txtsalario'])){
            if(null != ($_POST['datainicio']) && null != ($_POST('datafinal'))){
                $nome = $_POST['txtnome'];
                $profissao = $_POST['txtprofissao'];
                $salario = $_POST['txtsalario'];
                $datai = $_POST['datainicio'];
                $dataf = $_POST['datafinal'];
                $sql = "INSERT INTO tb_funcionarios VALUES(nome, profissao, salario, data_inicio, data_fim) VALUES(?,?,?,?,?)";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "sssss", $desc);
                mysqli_stmt_execute($stmt);
                echo mysqli_stmt_affected_rows($stmt) . "Registros afetados";
            }
        }
        else{
            echo "O campo salário deve ser preenchido e numérico";
        }
    }
    else{
        echo "Os campos de nome e profissão devem ser preenchido";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <p>
            <label for="txtdesc">Nome do funcionario: </label>
            <input type="text" id="txtnome" name="txtnome">
        </p>
        <p>
            <label for="txtdesc">Profissao: </label>
            <input type="text" id="txtprofissao" name="txtprofissao">
        </p>
        <p>
            <label for="txtdesc">Salario: </label>
            <input type="text" id="txtsalario" name="txtsalario">
        </p>
        <p>
            <label for="txtdesc">Data de inicio: </label>
            <input type="date" id="datainicio" name="datainicio">
        </p>
        <p>
            <label for="txtdesc">Data final: </label>
            <input type="date" id="datafinal" name="datafinal">
        </p>
    </form>
</body>
</html>