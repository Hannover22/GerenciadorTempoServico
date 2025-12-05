<?php
require_once("conecta.php");
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = limpeza($_POST['usuario']);
    $pass = limpeza($_POST['senha']);

    if ($usuario === '' || $senha === '') {
        header("Location: login.php?error=" . urlencode("Preencha usuário e senha."));
        exit;
    }

    if ($user === "administrador" && $pass === "123") {
        $_SESSION['tipo'] = "admin";
        header("Location: RecebeServico.php");
        exit;
    } else {
        $stmt = $mysqli->prepare("SELECT nome, senha FROM tb_usuarios WHERE nome = ? AND senha = ?");
        if (!$stmt) {
            header("Location: login.php?error=" . urlencode("Erro no banco de dados."));
            exit;
        }
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $hash = $row['senha'];
            if ($senha === $hash) {
                // sucesso
                session_regenerate_id(true);
                $_SESSION['tipo'] = "membro comum";
                header("Location: RecebeServico.php");
                exit;
            } else {
                header("Location: login.php?error=" . urlencode("Usuário ou senha incorretos."));
                exit;
            }
        } else {
            header("Location: login.php?error=" . urlencode("Usuário ou senha incorretos."));
            exit;
        }
        exit;
    }
    if(isset($_POST['usuario2']) && isset($_POST['senha2']) && isset($_POST['confirmasenha2'])){
        if($_POST['senha2'] === $_POST['confirmasenha2']){
            $user = limpeza($_POST['usuario2']);
            $senha = limpeza($_POST['senha2']);
            $confirmaSenha = limpeza($_POST['confirmasenha2']);
            $cargo = "membro comum";

            $sql = "INSERT INTO tb_usuarios(nome, senha, cargo) VALUES (?,?,?);";
            $stmt = mysqli_prepare($con, $sql);
                    
            mysqli_stmt_bind_param($stmt, "ss", $user, $senha, $cargo);
            mysqli_stmt_execute($stmt);
            header("Location: ".$_SERVER['PHP_SELF']."?ok=1");
            exit;
        }
        else{
            echo "As senhas apresentam diferenças";
        }
    }
    else{
        echo "Todos os campos precisam ser preenchidos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>

<form method="post">
    <label>Usuário:</label>
    <input type="text" name="usuario" required><br><br>

    <label>Senha:</label>
    <input type="password" name="senha" required><br><br>

    <button type="submit">Entrar</button>
    <button type="submit" id="btnCadastrar">Cadastrar</button>

    <div id="Cadastro" class="hidden group">
        <label>Nome de usuário:</label>
        <input type="text" name="usuario2" required><br><br>

        <label>Senha:</label>
        <input type="password" name="senha2" required><br><br>

        <label>Repita sua senha:</label>
        <input type="password" name="confirmasenha2" required><br><br>

        <button type="submit" id="FinalizarCadastro">Finalizar cadastro</button>
    </div>
    <script>
        document.getElementById("btnCadastrar").addEventListener("click", function() {
            if (isset($_POST['btnCadastrar'])) {
                document.getElementById("groupA").classList.remove("hidden");
            } else {
                document.getElementById("groupA").classList.add("hidden");
            }
        }); 
    </script>
</form>

</body>
</html>
