<?php
// 1. Inicia ou recupera a sessão ativa no servidor
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();

// 2. SISTEMA DE SEGURANÇA: Se a variável de sessão do usuário não existir, expulsa para o login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login_aluno.html");
    exit; // Interrompe o processamento da página imediatamente
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Principal - CEEP.PLG</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    :root { color-scheme: dark; }
    body { background-color: #0c0d10; color: #f1f5f9; font-family: ui-sans-serif, system-ui, sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-[#0c0d10] text-slate-100 flex flex-col justify-center items-center p-6">

  <div class="max-w-md w-full bg-[#141518] border border-white/10 rounded-3xl p-8 text-center shadow-2xl">
    <div class="mx-auto size-16 grid place-items-center rounded-2xl bg-violet-600/20 text-violet-400 mb-6">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>
    </div>

    <p class="text-xs font-semibold uppercase tracking-widest text-violet-400">Área Restrita</p>

    <!-- Proteção XSS com htmlspecialchars() ao exibir o nome vindo da sessão -->
    <h1 class="mt-2 text-3xl font-extrabold text-white">
      Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!
    </h1>

    <p class="mt-4 text-sm text-slate-400 leading-relaxed">
      Você foi autenticado com sucesso e está na área protegida do relatório de Chromebooks do CEEP.
    </p>

    <!-- Botão de Logout -->
    <a href="sair.php" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 border border-rose-500/30 py-3.5 text-sm font-semibold text-rose-300 transition cursor-pointer">
      <span>Desconectar / Sair</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
    </a>
  </div>

</body>
</html>
