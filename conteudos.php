<?php
require_once 'auth.php';
require_once 'config.php';
verificarLogin();

$cat_slug = $_GET['cat'] ?? '';
$disciplina_id = $_GET['disc'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$busca = trim($_GET['q'] ?? '');

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
$disciplinas = $pdo->query("SELECT d.*, c.slug as cat_slug FROM disciplinas d JOIN categorias c ON d.categoria_id = c.id")->fetchAll();

$sql = "SELECT ct.*, d.nome as disciplina_nome, c.nome as categoria_nome, u.nome as professor_nome
        FROM conteudos ct
        JOIN disciplinas d ON ct.disciplina_id = d.id
        JOIN categorias c ON d.categoria_id = c.id
        JOIN professores p ON ct.professor_id = p.id
        JOIN usuarios u ON p.usuario_id = u.id
        WHERE ct.status = 'aprovado'";
$params = [];

if ($cat_slug) { $sql .= " AND c.slug = ?"; $params[] = $cat_slug; }
if ($disciplina_id) { $sql .= " AND ct.disciplina_id = ?"; $params[] = $disciplina_id; }
if ($tipo) { $sql .= " AND ct.tipo = ?"; $params[] = $tipo; }
if ($busca) {
    $sql .= " AND (ct.titulo LIKE ? OR ct.descricao LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}
$sql .= " ORDER BY ct.data_criacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$conteudos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdos</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <h1 class="page-title">Biblioteca de Conteudos</h1>

                <div class="filtros">
                    <form method="GET" class="filtros-form">
                        <div class="select-wrapper">
                            <select name="cat" onchange="this.form.submit()">
                                <option value="">Todas as Categorias</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?php echo $c['slug']; ?>" <?php echo $cat_slug === $c['slug'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="select-wrapper">
                            <select name="disc" onchange="this.form.submit()">
                                <option value="">Todas as Disciplinas</option>
                                <?php foreach ($disciplinas as $d): ?>
                                    <option value="<?php echo $d['id']; ?>" <?php echo $disciplina_id == $d['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($d['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="select-wrapper">
                            <select name="tipo" onchange="this.form.submit()">
                                <option value="">Todos os Tipos</option>
                                <option value="videoaula" <?php echo $tipo === 'videoaula' ? 'selected' : ''; ?>>Videoaulas</option>
                                <option value="resumo" <?php echo $tipo === 'resumo' ? 'selected' : ''; ?>>Resumos</option>
                                <option value="simulado" <?php echo $tipo === 'simulado' ? 'selected' : ''; ?>>Simulados</option>
                            </select>
                        </div>
                    </form>
                </div>

                <?php if (empty($conteudos)): ?>
                    <div class="empty-state">
                        <p>Nenhum conteudo encontrado com esses filtros.</p>
                    </div>
                <?php else: ?>
                    <div class="conteudos-grid">
                        <?php foreach ($conteudos as $c): ?>
                            <a href="ver_conteudo.php?id=<?php echo $c['id']; ?>" class="conteudo-card tipo-<?php echo $c['tipo']; ?>">
                                <div class="conteudo-tipo"><?php echo ucfirst($c['tipo']); ?></div>
                                <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                                <p class="conteudo-desc"><?php echo htmlspecialchars(mb_substr($c['descricao'] ?? '', 0, 100)); ?>...</p>
                                <div class="conteudo-meta">
                                    <span><?php echo htmlspecialchars($c['disciplina_nome']); ?></span>
                                    <span>Prof. <?php echo htmlspecialchars($c['professor_nome']); ?></span>
                                    <span><?php echo $c['visualizacoes']; ?> views</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>