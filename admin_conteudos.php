<?php
require_once 'auth.php';
require_once 'config.php';
verificarTipo(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $acao = $_POST['acao'] ?? '';
    $motivo = trim($_POST['motivo'] ?? '');

    if ($acao === 'aprovar') {
        $stmt = $pdo->prepare("UPDATE conteudos SET status='aprovado', data_aprovacao=NOW(), aprovado_por=?, motivo_rejeicao=NULL WHERE id=?");
        $stmt->execute([$_SESSION['usuario_id'], $id]);
        $_SESSION['sucesso'] = "Conteudo aprovado.";
    } elseif ($acao === 'rejeitar') {
        if (empty($motivo)) {
            $_SESSION['erro'] = "Informe o motivo da rejeicao.";
        } else {
            $stmt = $pdo->prepare("UPDATE conteudos SET status='rejeitado', motivo_rejeicao=?, aprovado_por=? WHERE id=?");
            $stmt->execute([$motivo, $_SESSION['usuario_id'], $id]);
            $_SESSION['sucesso'] = "Conteudo rejeitado.";
        }
    }
    header("Location: admin_conteudos.php");
    exit();
}

$filtro = $_GET['status'] ?? 'pendente';
$stmt = $pdo->prepare("SELECT ct.*, d.nome as disciplina, u.nome as professor_nome, u.email as professor_email
                       FROM conteudos ct
                       JOIN disciplinas d ON ct.disciplina_id = d.id
                       JOIN professores p ON ct.professor_id = p.id
                       JOIN usuarios u ON p.usuario_id = u.id
                       WHERE ct.status = ?
                       ORDER BY ct.data_criacao ASC");
$stmt->execute([$filtro]);
$conteudos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderação</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <h1 class="page-title">Moderação de Conteúdos</h1>

                <?php if (isset($_SESSION['sucesso'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['sucesso']; unset($_SESSION['sucesso']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
                <?php endif; ?>

                <div class="tabs">
                    <a href="?status=pendente" class="tab <?php echo $filtro === 'pendente' ? 'active' : ''; ?>">Pendentes</a>
                    <a href="?status=aprovado" class="tab <?php echo $filtro === 'aprovado' ? 'active' : ''; ?>">Aprovados</a>
                    <a href="?status=rejeitado" class="tab <?php echo $filtro === 'rejeitado' ? 'active' : ''; ?>">Rejeitados</a>
                </div>

                <?php if (empty($conteudos)): ?>
                    <div class="empty-state"><p>Nenhum conteúdo nesta categoria.</p></div>
                <?php else: ?>
                    <?php foreach ($conteudos as $c): ?>
                        <div class="moderacao-card">
                            <div class="moderacao-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                                    <span class="tipo-badge"><?php echo ucfirst($c['tipo']); ?></span>
                                    <span class="disc-badge"><?php echo htmlspecialchars($c['disciplina']); ?></span>
                                </div>
                                <small><?php echo date('d/m/Y H:i', strtotime($c['data_criacao'])); ?></small>
                            </div>
                            <p class="moderacao-desc"><?php echo nl2br(htmlspecialchars($c['descricao'] ?? 'Sem descricao')); ?></p>
                            <p class="moderacao-prof">Professor: <strong><?php echo htmlspecialchars($c['professor_nome']); ?></strong> (<?php echo htmlspecialchars($c['professor_email']); ?>)</p>

                            <?php if ($c['tipo'] === 'videoaula' && $c['url_conteudo']): ?>
                                <div class="moderacao-preview">
                                    <strong>URL:</strong> <a href="<?php echo htmlspecialchars($c['url_conteudo']); ?>" target="_blank"><?php echo htmlspecialchars($c['url_conteudo']); ?></a>
                                </div>
                            <?php endif; ?>

                            <?php if ($c['conteudo_texto']): ?>
                                <details class="moderacao-texto">
                                    <summary>Ver conteúdo em texto</summary>
                                    <div><?php echo nl2br(htmlspecialchars($c['conteudo_texto'])); ?></div>
                                </details>
                            <?php endif; ?>

                            <?php if ($c['status'] === 'pendente'): ?>
                                <form method="POST" class="moderacao-actions">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" name="acao" value="aprovar" class="btn-aprovar">Aprovar</button>
                                    <button type="button" class="btn-rejeitar" onclick="mostrarRejeicao(<?php echo $c['id']; ?>)">Rejeitar</button>
                                    <div id="rejeicao-<?php echo $c['id']; ?>" style="display:none; width:100%; margin-top:10px;">
                                        <input type="text" name="motivo" placeholder="Motivo da rejeicao" class="input-motivo">
                                    </div>
                                </form>
                            <?php elseif ($c['status'] === 'rejeitado' && $c['motivo_rejeicao']): ?>
                                <div class="moderacao-motivo">
                                    <strong>Motivo da rejeição:</strong> <?php echo htmlspecialchars($c['motivo_rejeicao']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script>
    function mostrarRejeicao(id) {
        const div = document.getElementById('rejeicao-' + id);
        div.style.display = div.style.display === 'none' ? 'block' : 'none';
    }
    </script>
</body>
</html>