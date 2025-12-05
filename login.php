<?php
session_start();
require_once "conecta.php"; // contém a função limpeza()

$erro = isset($_GET['error']) ? $_GET['error'] : "";
$ok = isset($_GET['ok']) ? $_GET['ok'] : "";

// Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnLogin'])) {

    $user = limpeza($_POST['usuario']);
    $pass = limpeza($_POST['senha']);

    if ($user === '' || $pass === '') {
        header("Location: login.php?error=" . urlencode("Preencha usuário e senha."));
        exit;
    }

    // Login Admin fixo
    if ($user === "Admin" && $pass === "123") {
        $_SESSION['tipo'] = "admin";
        header("Location: RecebeServico.php");
        exit;
    }

    // Login usuários do banco
    $sql = "SELECT nome, senha, cargo FROM tb_usuarios WHERE nome = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        if ($pass === $row['senha']) {
            session_regenerate_id(true);
            $_SESSION['tipo'] = $row['cargo'];
            $_SESSION['usuario'] = $row['nome'];
            header("Location: RecebeServico.php");
            exit;

        } else {
            header("Location: login.php?error=" . urlencode("Senha incorreta."));
            exit;
        }

    } else {
        header("Location: login.php?error=" . urlencode("Usuário não encontrado."));
        exit;
    }
}

// Cadastro de novo usuário
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnFinalizarCadastro'])) {

    if (isset($_POST['usuario2'], $_POST['senha2'], $_POST['confirmasenha2'])) {

        $user2 = limpeza($_POST['usuario2']);
        $senha2 = limpeza($_POST['senha2']);
        $confirm2 = limpeza($_POST['confirmasenha2']);

        if ($user2 === '' || $senha2 === '' || $confirm2 === '') {
            header("Location: login.php?error=" . urlencode("Preencha todos os campos do cadastro."));
            exit;
        }

        if ($senha2 !== $confirm2) {
            header("Location: login.php?error=" . urlencode("As senhas não coincidem."));
            exit;
        }

        $cargo = "membro comum";
        $sql = "INSERT INTO tb_usuarios (nome, senha, cargo) VALUES (?,?,?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $user2, $senha2, $cargo);
        mysqli_stmt_execute($stmt);

        header("Location: login.php?ok=" . urlencode("Usuário cadastrado com sucesso!"));
        exit;

    } else {
        header("Location: login.php?error=" . urlencode("Todos os campos devem ser preenchidos."));
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        .hidden { display: none; }
        .msg { color: red; font-weight: bold; }
        .ok { color: green; font-weight: bold; }
    </style>
</head>
<body>

<h2>Login</h2>

<?php if ($erro): ?>
    <p class="msg"><?= $erro ?></p>
<?php endif; ?>

<?php if ($ok): ?>
    <p class="ok"><?= $ok ?></p>
<?php endif; ?>

<form method="post">

    <label>Usuário:</label>
    <input type="text" name="usuario"><br><br>

    <label>Senha:</label>
    <input type="password" name="senha"><br><br>

    <button type="submit" name="btnLogin">Entrar</button>
    <button type="button" id="btnCadastrar">Criar Conta</button>

    <div id="Cadastro" class="hidden">
        <br><hr><br>

        <label>Novo usuário:</label>
        <input type="text" name="usuario2"><br><br>

        <label>Senha:</label>
        <input type="password" name="senha2"><br><br>

        <label>Repita a senha:</label>
        <input type="password" name="confirmasenha2"><br><br>

        <button type="submit" name="btnFinalizarCadastro">Finalizar Cadastro</button>
    </div>
</form>

<script>
document.getElementById("btnCadastrar").addEventListener("click", function() {
    document.getElementById("Cadastro").classList.toggle("hidden");
});
</script>

</body>
</html>
