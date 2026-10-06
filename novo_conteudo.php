<?php
require_once 'auth.php';
require_once 'config.php';
verificarTipo(['professor']);

$stmt = $pdo->prepare("SELECT * FROM professores WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$professor = $stmt->fetch();

$disciplinas = $pdo->query("SELECT d.*, c.nome as cat_nome FROM disciplinas d JOIN categorias c ON d.categoria_id = c.id ORDER BY c.id, d.nome")->fetchAll();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $disciplina_id = $_POST['disciplina_id'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $url = trim($_POST['url_conteudo'] ?? '');
    $texto = trim($_POST['conteudo_texto'] ?? '');

    if (empty($titulo) || empty($disciplina_id) || empty($tipo)) {
        $erro = "Preencha todos os campos obrigatorios.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO conteudos (professor_id, disciplina_id, titulo, descricao, tipo, url_conteudo, conteudo_texto) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$professor['id'], $disciplina_id, $titulo, $descricao, $tipo, $url, $texto]);
        $_SESSION['sucesso'] = "Conteudo enviado. Aguardando aprovacao do administrador.";
        header("Location: meus_conteudos.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Conteúdo</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <h1 class="page-title">Criar Novo Conteúdo</h1>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>

                <div class="form-container">
                    <div class="alert alert-info">
                        Seu conteúdo será revisado por um administrador antes de ser publicado.
                    </div>

                    <form method="POST" class="form-conteudo">
                        <div class="input-group">
                            <label>Título *</label>
                            <input type="text" name="titulo" required maxlength="200" placeholder="Ex: Revolução Francesa">
                        </div>

                        <div class="input-group">
                            <label>Disciplina *</label>
                            <div class="select-wrapper">
                                <select name="disciplina_id" required>
                                    <option value="">Selecione a disciplina</option>
                                    <?php
                                    $cat_atual = '';
                                    foreach ($disciplinas as $d):
                                        if ($cat_atual !== $d['cat_nome']):
                                            if ($cat_atual !== '') echo '</optgroup>';
                                            $cat_atual = $d['cat_nome'];
                                            echo '<optgroup label="' . htmlspecialchars($cat_atual) . '">';
                                        endif;
                                    ?>
                                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['nome']); ?></option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <label>Tipo de Conteúdo *</label>
                            <div class="tipo-selector">
                                <label class="tipo-opt">
                                    <input type="radio" name="tipo" value="videoaula" required onchange="toggleTipo()">
                                    <span>Videoaula</span>
                                </label>
                                <label class="tipo-opt">
                                    <input type="radio" name="tipo" value="resumo" onchange="toggleTipo()">
                                    <span>Resumo</span>
                                </label>
                                <label class="tipo-opt">
                                    <input type="radio" name="tipo" value="simulado" onchange="toggleTipo()">
                                    <span>Simulado</span>
                                </label>
                            </div>
                        </div>

                        <div class="input-group">
                            <label>Descrição</label>
                            <textarea name="descricao" rows="3" placeholder="Breve descricao do conteudo"></textarea>
                        </div>

                        <div class="input-group" id="campo-url" style="display:none;">
                            <label>URL do Video (YouTube)</label>
                            <input type="url" name="url_conteudo" placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <div class="input-group" id="campo-texto" style="display:none;">
                            <label>Conteúdo em Texto</label>
                            <textarea name="conteudo_texto" rows="10" placeholder="Escreva o resumo ou as questoes do simulado"></textarea>
                            <small>Para simulados, separe as questoes com ---</small>
                        </div>

                        <button type="submit" class="btn-primary">Enviar para Aprovação</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script>
    function toggleTipo() {
        const tipo = document.querySelector('input[name="tipo"]:checked')?.value;
        const campoUrl = document.getElementById('campo-url');
        const campoTexto = document.getElementById('campo-texto');
        if (tipo === 'videoaula') {
            campoUrl.style.display = 'block';
            campoTexto.style.display = 'none';
        } else if (tipo === 'resumo' || tipo === 'simulado') {
            campoUrl.style.display = 'none';
            campoTexto.style.display = 'block';
        }
    }
    </script>
</body>
</html>