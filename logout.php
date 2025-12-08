<?php
session_start();

// Lógica para remover o cookie de persistência
if (isset($_COOKIE['auth_cred'])) {
    setcookie("auth_cred", "", time() - 3600, "/");
}

session_unset();
session_destroy();
header("Location: login.php");
exit;
?>