<?php
require_once 'auth.php';
require_once 'config.php';
verificarTipo(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'bloquear') {
        $pdo->prepare("UPDATE usuarios SET status='bloqueado' WHERE id=?")->execute([$id]);
    } elseif ($acao === 'ativar') {
        $pdo->prepare("UPDATE usuarios SET status='ativo' WHERE id=?")->execute([$id]);
    }
    header("Location: admin_usuarios.php");
    exit();
}

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <h1 class="page-title">Gerenciar Usuários</h1>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $u['tipo']; ?>">
                                            <?php echo ucfirst($u['tipo']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $u['status'] === 'ativo' ? 'status-aprovado' : 'status-rejeitado'; ?>">
                                            <?php echo ucfirst($u['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($u['data_cadastro'])); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <?php if ($u['status'] === 'ativo' && $u['tipo'] !== 'admin'): ?>
                                                <button type="submit" name="acao" value="bloquear" class="btn-rejeitar" style="padding:6px 14px; font-size:12.5px;">Bloquear</button>
                                            <?php elseif ($u['status'] === 'bloqueado'): ?>
                                                <button type="submit" name="acao" value="ativar" class="btn-aprovar" style="padding:6px 14px; font-size:12.5px;">Ativar</button>
                                            <?php else: ?>
                                                <span style="color:#a0aec0; font-size:12.5px;">-</span>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>