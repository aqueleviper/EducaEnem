// Validação do formulário de cadastro
document.addEventListener('DOMContentLoaded', function() {
    const cadastroForm = document.getElementById('cadastroForm');
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', function(e) {
            const senha = document.getElementById('senha');
            const confirmarSenha = document.getElementById('confirmar_senha');
            
            if (senha.value !== confirmarSenha.value) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                confirmarSenha.focus();
                return false;
            }
            
            if (senha.value.length < 6) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 6 caracteres!');
                senha.focus();
                return false;
            }
        });
    }
    
    // Validação do formulário de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const senha = document.getElementById('senha');
            
            if (!email.value || !senha.value) {
                e.preventDefault();
                alert('Preencha todos os campos!');
                return false;
            }
        });
    }
});

// Função para mostrar/esconder senha (opcional)
function toggleSenha(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}
