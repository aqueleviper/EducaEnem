<?php
require_once 'auth.php';
require_once 'config.php';
verificarLogin();

$id = $_GET['id'] ?? 0;

// Buscar conteudo COM dados do professor (especialidade, departamento, formacao)
$stmt = $pdo->prepare("SELECT ct.*, 
                              d.nome as disciplina, 
                              c.nome as categoria, 
                              c.slug as cat_slug,
                              u.nome as professor_nome, 
                              u.email as professor_email,
                              p.especialidade as professor_especialidade,
                              p.departamento as professor_departamento,
                              p.formacao as professor_formacao,
                              p.telefone as professor_telefone
                       FROM conteudos ct
                       JOIN disciplinas d ON ct.disciplina_id = d.id
                       JOIN categorias c ON d.categoria_id = c.id
                       JOIN professores p ON ct.professor_id = p.id
                       JOIN usuarios u ON p.usuario_id = u.id
                       WHERE ct.id = ? AND ct.status = 'aprovado'");
$stmt->execute([$id]);
$conteudo = $stmt->fetch();

if (!$conteudo) {
    $_SESSION['erro'] = "Conteudo nao encontrado ou ainda nao foi aprovado.";
    header("Location: conteudos.php");
    exit();
}

// Incrementar visualizacoes (uma vez por sessao por conteudo)
$chave = 'view_conteudo_' . $id;
if (!isset($_SESSION[$chave])) {
    $pdo->prepare("UPDATE conteudos SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);
    $_SESSION[$chave] = true;
}

// Extrair ID do YouTube se for videoaula
$youtube_id = '';
if ($conteudo['tipo'] === 'videoaula' && !empty($conteudo['url_conteudo'])) {
    $url = $conteudo['url_conteudo'];
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
        $youtube_id = $m[1];
    }
}

// Processar simulado (separa por ---)
$questoes = [];
if ($conteudo['tipo'] === 'simulado' && !empty($conteudo['conteudo_texto'])) {
    $blocos = preg_split('/\s*---\s*/', $conteudo['conteudo_texto']);
    foreach ($blocos as $bloco) {
        $bloco = trim($bloco);
        if ($bloco !== '') $questoes[] = $bloco;
    }
}

// Mapeamento de formacao para exibicao amigavel
$formacoes_label = [
    'GRADUACAO'       => 'Graduação',
    'ESPECIALIZACAO'  => 'Especialização',
    'MESTRADO'        => 'Mestrado',
    'DOUTORADO'       => 'Doutorado',
    'POS_DOUTORADO'   => 'Pós-Doutorado',
];

// Mapeamento de departamento
$departamentos_label = [
    'TI'  => 'Tecnologia da Informação',
    'ENG' => 'Engenharia',
    'ADM' => 'Administração',
    'MAT' => 'Matemática',
    'FIS' => 'Física',
    'BIO' => 'Biologia',
    'QUI' => 'Química',
    'HUM' => 'Humanas',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($conteudo['titulo']); ?></title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'topbar.php'; ?>

            <div class="content">
                <a href="conteudos.php" class="voltar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Voltar para conteúdos
                </a>

                <div class="conteudo-header">
                    <div class="conteudo-header-meta">
                        <span class="conteudo-tipo tipo-<?php echo $conteudo['tipo']; ?>">
                            <?php echo ucfirst($conteudo['tipo']); ?>
                        </span>
                        <span class="cat-tag"><?php echo htmlspecialchars($conteudo['categoria']); ?></span>
                        <span class="disc-tag"><?php echo htmlspecialchars($conteudo['disciplina']); ?></span>
                    </div>
                    <h1 class="conteudo-titulo"><?php echo htmlspecialchars($conteudo['titulo']); ?></h1>

                    <div class="conteudo-info">
                        <div class="autor">
                            <div class="avatar"><?php echo strtoupper(mb_substr($conteudo['professor_nome'], 0, 1)); ?></div>
                            <div>
                                <strong><?php echo htmlspecialchars($conteudo['professor_nome']); ?></strong>
                                <small>
                                    <?php 
                                    if (!empty($conteudo['professor_especialidade'])) {
                                        echo htmlspecialchars($conteudo['professor_especialidade']);
                                    } else {
                                        echo 'Professor';
                                    }
                                    ?>
                                </small>
                            </div>
                        </div>
                        <div class="meta-info">
                            <span><?php echo date('d/m/Y', strtotime($conteudo['data_criacao'])); ?></span>
                            <span><?php echo $conteudo['visualizacoes']; ?> visualizações</span>
                        </div>
                    </div>

                    <?php if (!empty($conteudo['descricao'])): ?>
                        <p class="conteudo-resumo"><?php echo nl2br(htmlspecialchars($conteudo['descricao'])); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($conteudo['tipo'] === 'videoaula'): ?>
                    <div class="video-container">
                        <?php if ($youtube_id): ?>
                            <iframe
                                src="https://www.youtube.com/embed/<?php echo $youtube_id; ?>"
                                title="<?php echo htmlspecialchars($conteudo['titulo']); ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>URL do vídeo inválida ou não informada.</p>
                                <?php if ($conteudo['url_conteudo']): ?>
                                    <a href="<?php echo htmlspecialchars($conteudo['url_conteudo']); ?>" target="_blank" class="btn-primary" style="width:auto; padding:10px 24px;">Abrir link externo</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($conteudo['tipo'] === 'resumo'): ?>
                    <div class="conteudo-texto">
                        <?php if (!empty($conteudo['conteudo_texto'])): ?>
                            <?php echo nl2br(htmlspecialchars($conteudo['conteudo_texto'])); ?>
                        <?php else: ?>
                            <p class="texto-vazio">Este resumo ainda não possui conteúdo em texto.</p>
                        <?php endif; ?>
                    </div>

                <?php elseif ($conteudo['tipo'] === 'simulado'): ?>
                    <?php if (empty($questoes)): ?>
                        <div class="empty-state">
                            <p>Este simulado ainda não possui questões cadastradas.</p>
                        </div>
                    <?php else: ?>
                        <div class="simulado-header">
                            <h2>Simulado</h2>
                            <span class="questoes-count"><?php echo count($questoes); ?> <?php echo count($questoes) === 1 ? 'questao' : 'questoes'; ?></span>
                        </div>

                        <form id="formSimulado" class="simulado-form">
                            <?php foreach ($questoes as $i => $q): ?>
                                <div class="questao-card">
                                    <div class="questao-numero">Questao <?php echo $i + 1; ?></div>
                                    <div class="questao-texto"><?php echo nl2br(htmlspecialchars($q)); ?></div>
                                </div>
                            <?php endforeach; ?>

                            <div class="simulado-actions">
                                <button type="button" class="btn-primary" onclick="finalizarSimulado()">Finalizar Simulado</button>
                                <button type="button" class="btn-voltar" onclick="reiniciarSimulado()">Reiniciar</button>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="conteudo-footer">
                    <div class="info-box">
                        <h3>Sobre este conteúdo</h3>
                        <ul>
                            <li><strong>Categoria:</strong> <?php echo htmlspecialchars($conteudo['categoria']); ?></li>
                            <li><strong>Disciplina:</strong> <?php echo htmlspecialchars($conteudo['disciplina']); ?></li>
                            <li><strong>Tipo:</strong> <?php echo ucfirst($conteudo['tipo']); ?></li>
                            <li><strong>Publicado em:</strong> <?php echo date('d/m/Y', strtotime($conteudo['data_aprovacao'] ?? $conteudo['data_criacao'])); ?></li>
                        </ul>
                    </div>

                    <div class="info-box professor-box">
                        <h3>Professor</h3>
                        <div class="professor-header">
                            <div class="avatar"><?php echo strtoupper(mb_substr($conteudo['professor_nome'], 0, 1)); ?></div>
                            <div>
                                <p class="professor-nome"><?php echo htmlspecialchars($conteudo['professor_nome']); ?></p>
                                <p class="email"><?php echo htmlspecialchars($conteudo['professor_email']); ?></p>
                            </div>
                        </div>

                        <ul class="professor-detalhes">
                            <?php if (!empty($conteudo['professor_especialidade'])): ?>
                                <li>
                                    <span class="label">Especialidade</span>
                                    <span class="valor"><?php echo htmlspecialchars($conteudo['professor_especialidade']); ?></span>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($conteudo['professor_departamento'])): ?>
                                <li>
                                    <span class="label">Área / Departamento</span>
                                    <span class="valor">
                                        <?php 
                                        echo htmlspecialchars(
                                            $departamentos_label[$conteudo['professor_departamento']] 
                                            ?? $conteudo['professor_departamento']
                                        ); 
                                        ?>
                                    </span>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($conteudo['professor_formacao'])): ?>
                                <li>
                                    <span class="label">Formação</span>
                                    <span class="valor">
                                        <?php 
                                        echo htmlspecialchars(
                                            $formacoes_label[$conteudo['professor_formacao']] 
                                            ?? $conteudo['professor_formacao']
                                        ); 
                                        ?>
                                    </span>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($conteudo['professor_telefone'])): ?>
                                <li>
                                    <span class="label">Telefone</span>
                                    <span class="valor"><?php echo htmlspecialchars($conteudo['professor_telefone']); ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script>
    function finalizarSimulado() {
        if (confirm('Deseja finalizar o simulado? Suas respostas não serão salvas.')) {
            alert('Simulado finalizado. Bom estudo!');
        }
    }

    function reiniciarSimulado() {
        if (confirm('Deseja reiniciar o simulado? Todas as respostas serão apagadas.')) {
            document.getElementById('formSimulado').reset();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    </script>
</body>
</html>