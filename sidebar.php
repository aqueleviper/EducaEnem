<?php
$tipo = $_SESSION['usuario_tipo'] ?? 'aluno';
$nome = $_SESSION['usuario_nome'] ?? 'Usuario';
$iniciais = strtoupper(mb_substr($nome, 0, 1));
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <a href="dashboard.php">EducaEnem</a>
        <small>Plataforma de Estudos</small>
    </div>
    <nav>
        <a href="dashboard.php" class="<?php echo $pagina_atual === 'dashboard.php' ? 'active' : ''; ?>">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l9-9 9 9"/><path d="M5 10v10h14V10"/></svg>
            Início
        </a>
        <a href="conteudos.php" class="<?php echo $pagina_atual === 'conteudos.php' ? 'active' : ''; ?>">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M4 9h16"/><path d="M9 4v16"/></svg>
            Conteúdos
        </a>
        <?php if ($tipo === 'professor'): ?>
            <a href="meus_conteudos.php" class="<?php echo $pagina_atual === 'meus_conteudos.php' ? 'active' : ''; ?>">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                Meus Conteúdos
            </a>
            <a href="novo_conteudo.php" class="<?php echo $pagina_atual === 'novo_conteudo.php' ? 'active' : ''; ?>">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Criar Conteúdo
            </a>
        <?php endif; ?>
        <?php if ($tipo === 'admin'): ?>
            <a href="admin_conteudos.php" class="<?php echo $pagina_atual === 'admin_conteudos.php' ? 'active' : ''; ?>">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Moderação
            </a>
            <a href="admin_usuarios.php" class="<?php echo $pagina_atual === 'admin_usuarios.php' ? 'active' : ''; ?>">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Usuários
            </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <div class="user-mini">
            <div class="avatar"><?php echo $iniciais; ?></div>
            <div class="info">
                <strong><?php echo htmlspecialchars($nome); ?></strong>
                <span><?php echo ucfirst($tipo); ?></span>
            </div>
        </div>
    </div>
</aside>
<div class="overlay" id="overlay"></div>