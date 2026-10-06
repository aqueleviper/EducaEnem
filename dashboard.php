<?php
require_once 'auth.php';
require_once 'config.php';
verificarLogin();

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

$dados_professor = null;
if ($usuario['tipo'] === 'professor') {
    $stmt = $pdo->prepare("SELECT * FROM professores WHERE usuario_id = ?");
    $stmt->execute([$usuario['id']]);
    $dados_professor = $stmt->fetch();
}

$stats = ['total' => 0, 'pendentes' => 0, 'aprovados' => 0, 'usuarios' => 0];

if ($usuario['tipo'] === 'professor' && $dados_professor) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total,
        SUM(status='pendente') as pendentes,
        SUM(status='aprovado') as aprovados
        FROM conteudos WHERE professor_id = ?");
    $stmt->execute([$dados_professor['id']]);
    $stats = $stmt->fetch();
} elseif ($usuario['tipo'] === 'admin') {
    $stats['usuarios'] = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $stats['pendentes'] = $pdo->query("SELECT COUNT(*) FROM conteudos WHERE status='pendente'")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <div class="welcome-section">
                    <h1>Olá, <?php echo htmlspecialchars($usuario['nome']); ?></h1>
                    <p>Bem-vindo ao EducaEnem.</p>
                </div>

                <div class="stats-grid">
                    <?php if ($usuario['tipo'] === 'professor'): ?>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo $stats['total'] ?? 0; ?></span>
                            <span class="stat-label">Conteúdos Criados</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo $stats['pendentes'] ?? 0; ?></span>
                            <span class="stat-label">Aguardando Aprovação</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo $stats['aprovados'] ?? 0; ?></span>
                            <span class="stat-label">Aprovados</span>
                        </div>
                    <?php elseif ($usuario['tipo'] === 'admin'): ?>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo $stats['usuarios']; ?></span>
                            <span class="stat-label">Usuários Cadastrados</span>
                        </div>
                        <div class="stat-card highlight">
                            <span class="stat-number"><?php echo $stats['pendentes']; ?></span>
                            <span class="stat-label">Conteúdos Pendentes</span>
                        </div>
                    <?php else: ?>
                        <div class="stat-card">
                            <span class="stat-label">Continue seus estudos para o ENEM</span>
                        </div>
                    <?php endif; ?>
                </div>

                <h2 class="section-title">Categorias de Estudo</h2>
                <div class="categorias-grid">
                    <a href="conteudos.php?cat=ciencias-humanas" class="categoria-card cat-1">
                        <h3>Ciências Humanas</h3>
                        <p>História, Geografia, Filosofia, Sociologia</p>
                    </a>
                    <a href="conteudos.php?cat=ciencias-natureza" class="categoria-card cat-2">
                        <h3>Ciências da Natureza</h3>
                        <p>Biologia, Física, Química</p>
                    </a>
                    <a href="conteudos.php?cat=linguagens" class="categoria-card cat-3">
                        <h3>Linguagens</h3>
                        <p>Português, Literatura, Redação, Idiomas</p>
                    </a>
                    <a href="conteudos.php?cat=matematica" class="categoria-card cat-4">
                        <h3>Matemática</h3>
                        <p>Matemática e Raciocínio Lógico</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>