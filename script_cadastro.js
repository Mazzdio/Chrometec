document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registration-form');
  const formContainer = document.getElementById('form-container');
  const successCard = document.getElementById('success-card');
  const errorMessage = document.getElementById('error-message');
  const resetBtn = document.getElementById('reset-btn');

  // Alternar visualização da Senha
  const togglePassword = document.getElementById('toggle-password');
  const passwordInput = document.getElementById('password');
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
    });
  }

  // Alternar visualização da Confirmação de Senha
  const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
  const confirmPasswordInput = document.getElementById('confirm-password');
  if (toggleConfirmPassword && confirmPasswordInput) {
    toggleConfirmPassword.addEventListener('click', () => {
      const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordInput.setAttribute('type', type);
    });
  }

  // Envio assíncrono do formulário via Fetch API
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      const pass = passwordInput.value;
      const confirmPass = confirmPasswordInput.value;

      if (pass !== confirmPass) {
        errorMessage.textContent = 'As senhas não coincidem. Digite novamente.';
        errorMessage.classList.remove('hidden');
        return;
      }

      errorMessage.classList.add('hidden');

      const formData = new FormData();
      formData.append('name', document.getElementById('name').value);
      formData.append('registration', document.getElementById('registration').value);
      formData.append('phone', document.getElementById('phone').value);
      formData.append('password', pass);
      formData.append('confirm_password', confirmPass);
      formData.append('school_class', document.getElementById('school-class').value);

      fetch('cadastro_usuario.php', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if (!res.ok) throw new Error('Falha de conexão com o servidor.');
        return res.json();
      })
      .then(data => {
        if (data.sucesso) {
          formContainer.classList.add('hidden');
          successCard.classList.remove('hidden');
        } else {
          errorMessage.textContent = data.mensagem;
          errorMessage.classList.remove('hidden');
        }
      })
      .catch(err => {
        errorMessage.textContent = 'Erro ao processar cadastro: ' + err.message;
        errorMessage.classList.remove('hidden');
      });
    });
  }

  // Novo Cadastro
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      form.reset();
      successCard.classList.add('hidden');
      formContainer.classList.remove('hidden');
    });
  }
});
