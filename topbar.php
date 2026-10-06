<?php
$tipo = $_SESSION['usuario_tipo'] ?? 'aluno';
?>
<header class="topbar">
    <button class="hamburger" id="hamburger" aria-label="Abrir menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <form class="search-box" action="conteudos.php" method="GET">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="search" name="q" placeholder="Pesquisar conteúdos, disciplinas..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
    </form>

    <div class="topbar-right">
        <span class="badge badge-<?php echo $tipo; ?>"><?php echo ucfirst($tipo); ?></span>
        <a href="logout.php" class="btn-logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sair
        </a>
    </div>
</header>