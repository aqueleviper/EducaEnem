<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - EducaEnem</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body class="auth-page">
    <div class="container">
        <div class="form-box">
            <h2>Cadastro</h2>
            <p class="subtitulo">Passo 1 de 2</p>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>

            <form id="cadastroForm" action="cadastro_passo2.php" method="POST">
                <div class="input-group">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha (minimo 6 caracteres)</label>
                    <input type="password" id="senha" name="senha" required minlength="6">
                </div>
                <div class="input-group">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>

                <button type="submit">Avançar</button>
                <p class="link">Já tem conta? <a href="index.php">Faça login</a></p>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>