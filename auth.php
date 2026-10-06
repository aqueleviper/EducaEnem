<?php
session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: index.php");
        exit();
    }
}

function verificarTipo($tipos_permitidos) {
    verificarLogin();
    if (!in_array($_SESSION['usuario_tipo'], $tipos_permitidos)) {
        $_SESSION['erro'] = "Acesso negado.";
        header("Location: dashboard.php");
        exit();
    }
}
?>