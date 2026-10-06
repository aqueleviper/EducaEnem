<?php
session_start();
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dados da etapa 1
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'aluno'; // Padrão: aluno
    
    // Validar
    if(empty($nome) || empty($email) || empty($senha)) {
        $_SESSION['erro'] = "Dados incompletos!";
        header("Location: cadastro.php");
        exit();
    }
    
    try {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            $_SESSION['erro'] = "Email já cadastrado!";
            header("Location: cadastro.php");
            exit();
        }
        
        // Iniciar transação
        $pdo->beginTransaction();
        
        // Inserir usuário
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha_hash, $tipo]);
        $usuario_id = $pdo->lastInsertId();
        
        // Se for professor, inserir dados extras
        if($tipo === 'professor') {
            $especialidade = $_POST['especialidade'] ?? '';
            $departamento = $_POST['departamento'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $formacao = $_POST['formacao'] ?? '';
            
            if(empty($especialidade) || empty($departamento)) {
                throw new Exception("Dados do professor incompletos!");
            }
            
            $stmt = $pdo->prepare("INSERT INTO professores (usuario_id, especialidade, departamento, telefone, formacao) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $especialidade, $departamento, $telefone, $formacao]);
        }
        
        $pdo->commit();
        
        // Limpar dados da sessão
        unset($_SESSION['cadastro_dados']);
        
        $_SESSION['sucesso'] = "Cadastro realizado com sucesso! Faça login.";
        header("Location: index.php");
        exit();
        
    } catch(Exception $e) {
        $pdo->rollBack();
        $_SESSION['erro'] = "Erro ao cadastrar: " . $e->getMessage();
        header("Location: cadastro.php");
        exit();
    }
}
?>
