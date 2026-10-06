<?php
session_start();

if (!isset($_POST['nome']) || !isset($_POST['email']) || !isset($_POST['senha'])) {
    header("Location: cadastro.php");
    exit();
}

$_SESSION['cadastro_dados'] = [
    'nome' => $_POST['nome'],
    'email' => $_POST['email'],
    'senha' => $_POST['senha']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo filemtime('styles.css'); ?>">
</head>
<body class="auth-page">
    <div class="container">
        <div class="form-box cadastro-box" style="max-width: 550px;">
            <h2>Cadastro</h2>
            <p class="subtitulo">Passo 2 de 2 - Escolha seu perfil</p>

            <form id="cadastroPasso2" action="cadastro_action.php" method="POST">
                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($_SESSION['cadastro_dados']['nome']); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['cadastro_dados']['email']); ?>">
                <input type="hidden" name="senha" value="<?php echo htmlspecialchars($_SESSION['cadastro_dados']['senha']); ?>">

                <div class="form-section">
                    <h4>Voce e:</h4>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="tipo" value="aluno" checked onchange="toggleCampos()">
                            <span class="radio-label">Aluno</span>
                            <small>Apenas estuda e acessa conteúdos</small>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tipo" value="professor" onchange="toggleCampos()">
                            <span class="radio-label">Professor</span>
                            <small>Ministra aulas e cria conteúdos</small>
                        </label>
                    </div>
                </div>

                <div id="campos-professor" class="form-section" style="display:none;">
                    <h4>Dados do Professor</h4>
                    <div class="input-group">
                        <label for="especialidade">Especialidade</label>
                        <input type="text" id="especialidade" name="especialidade" placeholder="Ex: Desenvolvimento Web">
                    </div>
                    <div class="input-group">
                        <label for="departamento">Departamento</label>
                        <select id="departamento" name="departamento">
                            <option value="">Selecione...</option>
                            <option value="TI">Tecnologia da Informação</option>
                            <option value="ENG">Engenharia</option>
                            <option value="ADM">Administração</option>
                            <option value="MAT">Matemática</option>
                            <option value="FIS">Física</option>
                            <option value="BIO">Biologia</option>
                            <option value="QUI">Química</option>
                            <option value="HUM">Humanas</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999">
                    </div>
                    <div class="input-group">
                        <label for="formacao">Formação</label>
                        <select id="formacao" name="formacao">
                            <option value="">Selecione...</option>
                            <option value="GRADUACAO">Graduação</option>
                            <option value="ESPECIALIZACAO">Especialização</option>
                            <option value="MESTRADO">Mestrado</option>
                            <option value="DOUTORADO">Doutorado</option>
                            <option value="POS_DOUTORADO">Pós-Doutorado</option>
                        </select>
                    </div>
                </div>

                <div class="botoes-navegacao">
                    <button type="button" class="btn-voltar" onclick="window.location.href='cadastro.php'">Voltar</button>
                    <button type="submit" class="btn-cadastrar">Finalizar Cadastro</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleCampos() {
            const tipo = document.querySelector('input[name="tipo"]:checked').value;
            const camposProfessor = document.getElementById('campos-professor');

            if (tipo === 'professor') {
                camposProfessor.style.display = 'block';
                document.querySelectorAll('#campos-professor input, #campos-professor select').forEach(el => {
                    if (el.id !== 'telefone' && el.id !== 'formacao') {
                        el.setAttribute('required', 'required');
                    }
                });
            } else {
                camposProfessor.style.display = 'none';
                document.querySelectorAll('#campos-professor input, #campos-professor select').forEach(el => {
                    el.removeAttribute('required');
                });
            }
        }
    </script>
</body>
</html>