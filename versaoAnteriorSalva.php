<?php
require_once "conecta.php";




if(isset($_POST['txtnome']) && isset($_POST['txtprofissao'])){
        
    if(isset($_POST['txtsalario']) && is_numeric($_POST['txtsalario'])){
            
        if(!empty($_POST['datainicio']) && !empty($_POST['datafinal'])){
                
            $nome = limpeza($_POST['txtnome']);
            $profissao = limpeza($_POST['txtprofissao']);
            $salario = limpeza($_POST['txtsalario']);
            $datai = limpeza($_POST['datainicio']);
            $dataf = limpeza($_POST['datafinal']);

            $dataiObj = new DateTime($datai);
            $datafObj = new DateTime($dataf);
            $diff = $dataiObj->diff($datafObj);
            $tempo = $diff->days;

            $datai_mysql = $dataiObj->format('Y-m-d H:i:s'); 
            $dataf_mysql = $datafObj->format('Y-m-d H:i:s');

            $sql = "INSERT INTO tb_funcionarios (nome, profissao, salario, data_inicio, data_final, tempo) VALUES (?,?,?,?,?,?)";
            $stmt = mysqli_prepare($con, $sql);
                
            mysqli_stmt_bind_param($stmt, "ssdssi", $nome, $profissao, $salario, $datai_mysql, $dataf_mysql, $tempo);
            mysqli_stmt_execute($stmt);

            // EVITAR DUPLICAÇÃO AO ATUALIZAR A PÁGINA
            header("Location: ".$_SERVER['PHP_SELF']."?ok=1");
            exit;
        }
    } else {
            echo "O campo salário deve ser preenchido e numérico";
    }
} else {
    echo "Os campos de nome e profissão devem ser preenchido";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabalho Leo</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = new google.visualization.DataTable();
        data.addColumn('string', 'Nome');
        data.addColumn('number', 'Tempo');
        data.addColumn({type:'string', role:'style'});

        data.addRows([
        <?php
            $result = mysqli_query($con, "SELECT nome, tempo FROM tb_funcionarios");
            $i = 0;
            while($row = $result->fetch_assoc()){
                $colors = ["red","green","blue"];
                $color = $colors[$i % count($colors)];
                echo "['".$row['nome']."', ".$row['tempo'].", '$color'],";
                $i++;
            }
        ?>
        ]);

      var options = {
        title: "tempo de serviço em dias",
      };

     var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
     chart.draw(data, options);
    }
    </script>
</head>

<body>

<?php if(isset($_GET['ok'])): ?>
    <p style="color:green; font-weight:bold;">
        Funcionário cadastrado com sucesso!
        <br>Total de funcionários: <?php echo totalFuncionarios($con); ?>
    </p>
<?php endif; ?>

    <form action="" method="post">
        <p>
            <label>Nome do funcionario: </label>
            <input type="text" id="txtnome" name="txtnome">
        </p>
        <p>
            <label>Profissao: </label>
            <input type="text" id="txtprofissao" name="txtprofissao">
        </p>
        <p>
            <label>Salario: </label>
            <input type="text" id="txtsalario" name="txtsalario">
        </p>
        <p>
            <label>Data de inicio: </label>
            <input type="date" id="datainicio" name="datainicio">
        </p>
        <p>
            <label>Data final: </label>
            <input type="date" id="datafinal" name="datafinal">
        </p>
        <p>
            <button type="submit" name="btnSubmit">Cadastrar</button>
        </p>
    </form>

    <div id="barchart_values" style="width: 900px; height: 400px;"></div>

</body>
</html>