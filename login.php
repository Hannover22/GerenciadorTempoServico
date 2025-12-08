<?php
session_start();
require_once "conecta.php"; // contém a função limpeza()

$erro = isset($_GET['error']) ? $_GET['error'] : "";
$ok = isset($_GET['ok']) ? $_GET['ok'] : "";
$logado = isset($_SESSION['tipo']);

// Chave secreta para assinar o cookie. 
// ATENÇÃO: Use uma chave longa, complexa e mantida em segredo.
$SECRETE_KEY = "SuaChaveSecretaMuitoLongaEComplexaParaAssinatura123456"; 

// Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnLogin'])) {

    $user = limpeza($_POST['usuario']);
    $pass = limpeza($_POST['senha']);

    if ($user === '' || $pass === '') {
        header("Location: login.php?error=" . urlencode("Preencha usuário e senha."));
        exit;
    }
    
    // Login usuários do banco
    $sql = "SELECT nome, senha, cargo FROM tb_usuarios WHERE nome = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        if (validarSenhaHash($pass, $row['senha'])) {
            session_regenerate_id(true);
            $_SESSION['tipo'] = $row['cargo'];
            $_SESSION['usuario'] = $row['nome'];

            // LÓGICA DE PERSISTÊNCIA (COOKIE SEM DB MODIFICADO)
            if (isset($_POST['lembrar_de_mim'])) {
                
                $username = $row['nome'];
                $password_hash = $row['senha']; // Hash da senha do banco

                // Cria a assinatura (HMAC)
                $signature = hash_hmac('sha256', $username . $password_hash, $SECRETE_KEY);

                // O valor do cookie é: nome do usuário|assinatura
                $cookie_value = $username . "|" . $signature;
                
                // Configura para expirar em 30 dias (tempo em segundos)
                $expiracao = time() + (86400 * 30); 
                
                // Define o cookie no navegador (HttpOnly=true para segurança)
                setcookie("auth_cred", $cookie_value, $expiracao, "/", "", false, true); 
            }

            header("Location: Main.php");
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

        $hash = registrarSenhaHash($senha2);

        $sql = "INSERT INTO tb_usuarios (nome, senha, cargo) VALUES (?,?,?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $user2, $hash, $cargo);
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

<?php if (!$logado): ?>
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

        <label>
            <input type="checkbox" name="lembrar_de_mim"> Lembrar de mim
        </label><br><br>
        
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
<?php endif; ?> 

<script>
document.getElementById("btnCadastrar").addEventListener("click", function() {
    document.getElementById("Cadastro").classList.toggle("hidden");
});
</script>

</body>
</html>