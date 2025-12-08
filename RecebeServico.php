<?php
session_start(); // MOVEMOS PARA O TOPO
require_once "conecta.php";
// include "login.php"; // LINHA REMOVIDA, POIS NÃO DEVE INCLUIR UM ARQUIVO DE FORMULÁRIO/LOGIN AQUI

// Chave secreta para assinar o cookie. DEVE SER IDÊNTICA À CHAVE EM login.php!
$SECRETE_KEY = "SuaChaveSecretaMuitoLongaEComplexaParaAssinatura123456"; 

// --- LÓGICA DE AUTOLOGIN VIA COOKIE (SEM DB MODIFICADO) ---
if (!isset($_SESSION['tipo']) && isset($_COOKIE['auth_cred']) && !empty($_COOKIE['auth_cred'])) {
    
    $parts = explode('|', $_COOKIE['auth_cred']);

    if (count($parts) === 2) {
        $username_from_cookie = $parts[0];
        $signature_from_cookie = $parts[1];
        
        // 1. Busca o usuário no DB para obter o hash da senha e o cargo
        $sql_check = "SELECT nome, senha, cargo FROM tb_usuarios WHERE nome = ? LIMIT 1";
        $stmt_check = mysqli_prepare($con, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $username_from_cookie);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if ($row_check = mysqli_fetch_assoc($result_check)) {
            $current_password_hash = $row_check['senha'];

            // 2. Gera uma nova assinatura com as credenciais atuais do banco
            $expected_signature = hash_hmac('sha256', $username_from_cookie . $current_password_hash, $SECRETE_KEY);

            // 3. Compara a assinatura do cookie com a assinatura esperada de forma segura
            if (hash_equals($signature_from_cookie, $expected_signature)) {
                // Assinatura válida: Renova a sessão e loga o usuário
                session_regenerate_id(true);
                $_SESSION['tipo'] = $row_check['cargo'];
                $_SESSION['usuario'] = $row_check['nome'];
            } else {
                // Assinatura inválida (cookie adulterado ou senha alterada)
                setcookie("auth_cred", "", time() - 3600, "/");
            }
        } else {
             // Usuário não encontrado
             setcookie("auth_cred", "", time() - 3600, "/");
        }
    } else {
        // Formato de cookie inválido
        setcookie("auth_cred", "", time() - 3600, "/");
    }
}
// --- FIM DA LÓGICA DE AUTOLOGIN ---


// se não estiver logado → volta para login
if (!isset($_SESSION['tipo'])) {
    header('Location: login.php'); // CORRIGIDO: de 'locale:' para 'Location:'
    exit;
}

// se for usuário comum → ele só vê o gráfico
$acessoAdmin = ($_SESSION['tipo'] === "admin");

if($acessoAdmin){
    if(isset($_POST['txtnome']) && isset($_POST['txtprofissao'])){
            
        if(isset($_POST['txtsalario']) && is_numeric($_POST['txtsalario'])){
                
            if(!empty($_POST['datainicio']) && !empty($_POST['datafinal'])){

                if(isset($_POST['chk']) && $_POST['chk'] == 'a'){ // Adicionado isset()
                    
                    $nome = limpeza($_POST['txtnome']);
                    $profissao = limpeza($_POST['txtprofissao']);
                    $salario = limpeza($_POST['txtsalario']);
                    $datai = limpeza($_POST['datainicio']);
                    $dataf = limpeza($_POST['datafinal']);
                    $clt = "CLT";

                    $dataiObj = new DateTime($datai);
                    $datafObj = new DateTime($dataf);
                    $diff = $dataiObj->diff($datafObj);
                    $tempo = $diff->days;
                    
                    // Tratamento de faltas para CLT:
                    $faltas_clt =
                        intval(isset($_POST['lamspe']) ? $_POST['lamspe'] : 0) +
                        intval(isset($_POST['f_medica']) ? $_POST['f_medica'] : 0) +
                        intval(isset($_POST['a_medica']) ? $_POST['a_medica'] : 0) +
                        intval(isset($_POST['f_medica_acompanhamento']) ? $_POST['f_medica_acompanhamento'] : 0) +
                        intval(isset($_POST['a_medica_acompanhamento']) ? $_POST['a_medica_acompanhamento'] : 0) +
                        intval(isset($_POST['l_saude']) ? $_POST['l_saude'] : 0) +
                        intval(isset($_POST['int_particulares']) ? $_POST['int_particulares'] : 0) +
                        intval(isset($_POST['cargo_publico']) ? $_POST['cargo_publico'] : 0) +
                        intval(isset($_POST['trat_familia']) ? $_POST['trat_familia'] : 0) +
                        intval(isset($_POST['p_suspensao']) ? $_POST['p_suspensao'] : 0) +
                        intval(isset($_POST['justificada']) ? $_POST['justificada'] : 0) +
                        intval(isset($_POST['injustificada']) ? $_POST['injustificada'] : 0);
                    
                    $tempo = $tempo - $faltas_clt;

                    $datai_mysql = $dataiObj->format('Y-m-d H:i:s'); 
                    $dataf_mysql = $datafObj->format('Y-m-d H:i:s');

                    $sql = "INSERT INTO tb_funcionarios (nome, profissao, salario, clt, data_inicio, data_final, tempo) VALUES (?,?,?,?,?,?,?)";
                    $stmt = mysqli_prepare($con, $sql);
                        
                    mysqli_stmt_bind_param($stmt, "ssdsssi", $nome, $profissao, $salario, $clt, $datai_mysql, $dataf_mysql, $tempo);
                    mysqli_stmt_execute($stmt);

                    // EVITAR DUPLICAÇÃO AO ATUALIZAR A PÁGINA
                    header("Location: ".$_SERVER['PHP_SELF']."?ok=1");
                    exit;
                }
                else if(isset($_POST['chk']) && $_POST['chk'] == 'b'){ // Adicionado isset()

                    $nome = limpeza($_POST['txtnome']);
                    $profissao = limpeza($_POST['txtprofissao']);
                    $salario = limpeza($_POST['txtsalario']);
                    $datai = limpeza($_POST['datainicio']);
                    $dataf = limpeza($_POST['datafinal']);
                    $clt = "autarquico";

                    $dataiObj = new DateTime($datai);
                    $datafObj = new DateTime($dataf);
                    $diff = $dataiObj->diff($datafObj);
                    $tempo = $diff->days;

                    // Tratamento de faltas para Autarquia:
                    $faltas_aut =
                        intval(isset($_POST['a_medico2']) ? $_POST['a_medico2'] : 0) +
                        intval(isset($_POST['l_saude2']) ? $_POST['l_saude2'] : 0) +
                        intval(isset($_POST['lc_saude3']) ? $_POST['lc_saude3'] : 0) +
                        intval(isset($_POST['int_particulares2']) ? $_POST['int_particulares2'] : 0) +
                        intval(isset($_POST['mandato_publico']) ? $_POST['mandato_publico'] : 0) +
                        intval(isset($_POST['af_salarios']) ? $_POST['af_salarios'] : 0) +
                        intval(isset($_POST['pen_suspensao2']) ? $_POST['pen_suspensao2'] : 0) +
                        intval(isset($_POST['justificada2']) ? $_POST['justificada2'] : 0) +
                        intval(isset($_POST['justificada_comdesconto']) ? $_POST['justificada_comdesconto'] : 0) +
                        intval(isset($_POST['deliberacao']) ? $_POST['deliberacao'] : 0) +
                        intval(isset($_POST['justificadas2']) ? $_POST['justificadas2'] : 0);
                    
                    $tempo = $tempo - $faltas_aut;
                    $datai_mysql = $dataiObj->format('Y-m-d H:i:s'); 
                    $dataf_mysql = $datafObj->format('Y-m-d H:i:s');

                    $sql2 = "INSERT INTO tb_funcionarios (nome, profissao, salario, clt, data_inicio, data_final, tempo) VALUES (?,?,?,?,?,?,?)";
                    $stmt = mysqli_prepare($con, $sql2);
                        
                    mysqli_stmt_bind_param($stmt, "ssdsssi", $nome, $profissao, $salario, $clt, $datai_mysql, $dataf_mysql, $tempo);
                    mysqli_stmt_execute($stmt);

                    // EVITAR DUPLICAÇÃO AO ATUALIZAR A PÁGINA
                    header("Location: ".$_SERVER['PHP_SELF']."?ok=1");
                    exit;

                }
                else{
                    echo "Selecione se o funcionario é CLT ou de Autarquia";
                }
            }
        } else {
                echo "O campo salário deve ser preenchido e numérico";
        }
    } else {
        echo "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .hidden { display: none; }
        .group { margin-top: 10px; }
        
        .radio-group {
        display: inline-block;
        margin-right: 20px;
        }
    </style>
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
                $colors = ["red","green","blue", "yellow", "purple", "brown"];
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
<?php if ($acessoAdmin): ?>
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
        <div class="radio-group">
                <input type="radio" id="chkA" name="chk" value="a">
                <label for="chkA">Funcionário CLT</label>
                <input type="radio" id="chkB" name="chk" value="b">
                <label for="chkB">Funcionário Autárquico</label>
            </label>
        </div>
        <p>
            <label>Data de inicio: </label>
            <input type="date" id="datainicio" name="datainicio">
        </p>
        <p>
            <label>Data final: </label>
            <input type="date" id="datafinal" name="datafinal">
        </p>

    <button type="button" id="btnAdd">Cadastrar faltas:</button>

    <div id="groupA" class="hidden group">
    <p>
            <label>Abonadas:</label>
            <input type="number" name="abonadas" min="0" max="10000">
        </p>
        <p>
            <label>Falta Lamspe:</label>
            <input type="number" name="lamspe" min="0" max="10000">
        </p>
        <p>
            <label>Faltas médicas:</label>
            <input type="number" name="f_medica" min="0" max="10000">
        </p>
        <p>
            <label>Ausências médicas:</label>
            <input type="number" name="a_medica" min="0" max="10000">
        </p>
        <p>
            <label>Faltas médicas (Acompanhamento):</label>
            <input type="number" name="f_medica_acompanhamento" min="0" max="10000">
        </p>
        <p>
            <label>Ausencias médicas (Acompanhamento):</label>
            <input type="number"  name="a_medica_acompanhamento" min="0" max="10000">
        </p>
        <p>
            <label>Licenças saúde:</label>
            <input type="number" name="l_saude" min="0" max="10000">
        </p>
        <p>
            <label>Licenças para tratar de interesses particulares:</label>
            <input type="number" name="int_particulares" min="0" max="10000">
        </p>
        <p>
            <label>Licenças para tratamento de pessoa da familia:</label>
            <input type="number" name="trat_familia" min="0" max="10000">
        </p>
        <p>
            <label>Afastamentos para concorrer a cargo publico:</label>
            <input type="number" name="cargo_publico" min="0" max="10000">
        </p>
        <p>
            <label>Penalidades de suspensão:</label>
            <input type="number" name="p_suspensao" min="0" max="10000">
        </p>
        <p>
            <label>Penalidades de repreensão:</label>
            <input type="number" name="p_repreensao" min="0" max="10000">
        </p>
        <p>
            <label>Justificada:</label>
            <input type="number" name="justificada" min="0" max="10000">
        </p>
        <p>
            <label>Injustificada:</label>
            <input type="number" name="injustificada" min="0" max="10000">
        </p>

    </div>

    <div id="groupB" class="hidden group">
        <p>
            <label>Abonadas:</label>
            <input type="number" name="abonadas2" min="0" max="10000">
        </p>
        <p>
            <label>Atestado médico (Até 12/08/2010):</label>
            <input type="number" name="a_medico2" min="0" max="10000">
        </p>
        <p>
            <label>Licença saúde (Até 12/08/2010):</label>
            <input type="number" name="l_saude2" min="0" max="10000">
        </p>
        <p>
            <label>Licença saúde (A partir de 13/08/2010):</label>
            <input type="number" name="lc_saude3" min="0" max="10000">
        </p>
        <p>
            <label>Licença para tratar de interesses particulares:</label>
            <input type="number" name="int_particulares2" min="0" max="10000">
        </p>
        <p>
            <label>Afastamento para concorrer a cargo publico(eleições):</label>
            <input type="number" name="cargo_publico2" min="0" max="10000">
        </p>
        <p>
            <label>Afastamento para concorrer a cargo público(mandato):</label>
            <input type="number" name="mandato_publico" min="0" max="10000">
        </p>
        <p>
            <label>Afastamento com prejuízo de salarios:</label>
            <input type="number" name="af_salarios" min="0" max="10000">
        </p>
        <p>
            <label>Penalidade de suspensão:</label>
            <input type="number" name="pen_suspensao2" min="0" max="10000">
        </p>
        <p>
            <label>Penalidade de repreensão:</label>
            <input type="number" name="pen_repreensao2" min="0" max="10000">
        </p>
        <p>
            <label>Justificada(Até 12/08/2010):</label>
            <input type="number" name="justificada2" min="0" max="10000">
        </p>
        <p>
            <label>Justificada (A partir de 13/08/2010 com desconto em folha de pagamento):</label>
            <input type="number" name="justificada_comdesconto" min="0" max="10000">
        </p>
        <p>
            <label>Justificada (A partir de 13/08/2010 sem desconto em folha de pagamento):</label>
            <input type="number" name="justificada_semdesconto" min="0" max="10000">
        </p>
        <p>
            <label>Falta dia nº 05/2010:</label>
            <input type="number" name="deliberacao" min="0" max="10000">
        </p>
        <p>
            <label>Justificadas:</label>
            <input type="number" name="justificadas2" min="0" max="10000">
        </p>
    </div>
    <p>
        <button type="submit" name="btnSubmit">Cadastrar</button>
    </p>
    <div id="barchart_values" style="width: 900px; height: 400px;"></div>
    </form>
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
<?php endif; ?>
<?php if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == "membro comum"): ?>
    <div id="barchart_values" style="width: 900px; height: 400px;"></div>
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
<?php endif; ?>

    <script>
    document.getElementById("btnAdd").addEventListener("click", function() {

        const chkA = document.getElementById("chkA").checked;
        const chkB = document.getElementById("chkB").checked;

        // Grupo A
        if (chkA) {
            document.getElementById("groupA").classList.remove("hidden");
        } else {
            document.getElementById("groupA").classList.add("hidden");
        }

        // Grupo B
        if (chkB) {
            document.getElementById("groupB").classList.remove("hidden");
        } else {
            document.getElementById("groupB").classList.add("hidden");
        }

    });
    </script>

</body>
</html>