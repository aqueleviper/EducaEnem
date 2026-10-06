<?php
require_once 'auth.php';
require_once 'config.php';
verificarTipo(['professor']);

$stmt = $pdo->prepare("SELECT id FROM professores WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$professor_id = $stmt->fetch()['id'];

$stmt = $pdo->prepare("SELECT ct.*, d.nome as disciplina FROM conteudos ct
                       JOIN disciplinas d ON ct.disciplina_id = d.id
                       WHERE ct.professor_id = ?
                       ORDER BY ct.data_criacao DESC");
$stmt->execute([$professor_id]);
$conteudos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Conteúdos</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <h1 class="page-title">Meus Conteúdos</h1>

                <?php if (isset($_SESSION['sucesso'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['sucesso']; unset($_SESSION['sucesso']); ?></div>
                <?php endif; ?>

                <?php if (empty($conteudos)): ?>
                    <div class="empty-state">
                        <p>Você ainda não criou nenhum conteúdo.</p>
                        <a href="novo_conteudo.php" class="btn-primary">Criar primeiro conteúdo</a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Disciplina</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($conteudos as $c): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($c['disciplina']); ?></td>
                                        <td><?php echo ucfirst($c['tipo']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $c['status']; ?>">
                                                <?php
                                                $status_txt = ['pendente' => 'Pendente', 'aprovado' => 'Aprovado', 'rejeitado' => 'Rejeitado'];
                                                echo $status_txt[$c['status']];
                                                ?>
                                            </span>
                                            <?php if ($c['status'] === 'rejeitado' && $c['motivo_rejeicao']): ?>
                                                <small class="motivo"><?php echo htmlspecialchars($c['motivo_rejeicao']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($c['data_criacao'])); ?></td>
                                        <td><?php echo $c['visualizacoes']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>