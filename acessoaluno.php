<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Aluno');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal do Estudante - CEEP</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script>
(function () {
  document.documentElement.style.visibility = 'hidden';
  const loginDestino = "login_aluno.html";
  async function conferirSessao() {
    try {
      const resposta = await fetch('validar_sessao.php', { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } });
      const dados = await resposta.json();
      if (!resposta.ok || !dados.autenticado || dados.perfil !== 'Aluno') {
        window.location.replace(loginDestino);
        return;
      }
      document.documentElement.style.visibility = 'visible';
    } catch (erro) {
      window.location.replace(loginDestino);
    }
  }
  window.addEventListener('pagehide', function () { document.documentElement.style.visibility = 'hidden'; });
  window.addEventListener('pageshow', conferirSessao);
})();
</script>
</head>

<body class="min-h-screen bg-[#101114] text-slate-100 selection:bg-violet-500 selection:text-white">

  <!-- Header / Barra Superior -->
  <header class="sticky top-0 z-30 flex h-[72px] items-center border-b border-white/8 bg-[#101114]/95 px-5 backdrop-blur md:px-8">
    <button
      id="menu-toggle-btn"
      aria-label="Abrir menu"
      class="mr-3 rounded-lg p-2 text-slate-300 hover:bg-white/5 lg:hidden cursor-pointer"
    >
      <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" id="menu-icon-svg">
        <path d="M4 7h16M4 12h16M4 17h16" />
      </svg>
    </button>

    <div class="flex items-center gap-3">
      <div class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 text-sm font-extrabold shadow-lg shadow-violet-900/30">
        CP
      </div>
      <div class="leading-tight">
        <p class="text-sm font-bold tracking-wide text-white">CEEP</p>
        <p class="text-[10px] uppercase tracking-[0.16em] text-slate-400">
          Portal do estudante
        </p>
      </div>
    </div>

    <!-- Lado Direito do Header -->
    <div class="ml-auto flex items-center gap-3">
      <button
        id="logout-btn"
        title="Encerrar sessão"
        class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 cursor-pointer"
      >
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        <span class="hidden sm:inline">Sair</span>
      </button>

      <div class="hidden h-8 w-px bg-white/10 sm:block"></div>

      <div class="hidden text-right sm:block">
        <p id="nome-aluno" class="text-sm font-semibold text-white">Carregando...</p>
        <p class="text-xs text-slate-500">Estudante</p>
      </div>

      <div class="grid size-10 place-items-center rounded-full border border-violet-400/20 bg-violet-500/15 text-xs font-bold text-violet-300">
        <span id="iniciais-aluno">--</span>
      </div>
    </div>
  </header>

  <div class="mx-auto flex max-w-[1500px]">

    <!-- Fundo do menu mobile -->
    <div
      id="sidebar-backdrop"
      aria-label="Fechar menu"
      class="fixed inset-0 z-30 bg-black/60 lg:hidden hidden transition-opacity"
    ></div>

    <!-- Menu Lateral -->
    <aside
      id="sidebar"
      class="fixed bottom-0 left-0 top-[72px] z-40 flex w-64 flex-col border-r border-white/8 bg-[#101114] p-5 transition-transform duration-300 -translate-x-full lg:sticky lg:translate-x-0"
    >
      <nav class="space-y-1.5" aria-label="Menu principal">
        <button class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3 text-left text-sm font-medium transition bg-violet-600 text-white shadow-lg shadow-violet-950/25 cursor-pointer">
          <svg aria-hidden="true" class="size-[19px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 10.8 9-7.3 9 7.3"/>
            <path d="M5.5 9.5V21h13V9.5M9 21v-6h6v6"/>
          </svg>
          Início
        </button>

        <a href="emprestimo_aluno.php" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3 text-left text-sm font-medium transition text-slate-400 hover:bg-white/5 hover:text-white cursor-pointer">
          <svg aria-hidden="true" class="size-[19px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="4" width="16" height="12" rx="1.5"/>
            <path d="M2 19h20M9 19h6"/>
          </svg>
          Meus empréstimos
        </a>
      </nav>
    </aside>

    <!-- Conteúdo Principal -->
    <main class="min-w-0 flex-1 px-5 py-8 md:px-9 md:py-10 xl:px-12">
      <div class="mx-auto max-w-6xl">

        <section class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
          <div>
            <p id="data-atual" class="mb-2 text-sm font-semibold text-violet-400">
              Portal do estudante
            </p>

            <h1 class="text-3xl font-bold tracking-tight text-white md:text-4xl">
              Olá, <span id="saudacao-aluno">Carregando...</span>!
            </h1>

            <p class="mt-2 text-sm text-slate-400 md:text-base">
              Veja o que precisa da sua atenção hoje.
            </p>
          </div>

          <a href="emprestimo_aluno.php" class="flex h-11 items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-sm font-semibold text-slate-200 transition hover:border-violet-500/40 hover:bg-violet-500/10 cursor-pointer">
            <svg aria-hidden="true" class="size-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="7"/>
              <path d="m20 20-4-4"/>
            </svg>
            Solicitar Chromebook
          </a>
        </section>

        <!-- Alerta de devolução -->
        <section id="notice-section" class="mb-6 hidden items-start gap-4 rounded-2xl border border-amber-400/15 bg-amber-400/[0.06] p-4 md:items-center md:p-5">
          <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-amber-400/10 text-amber-300">
            <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="9"/>
              <path d="M12 7v5l3 2"/>
            </svg>
          </div>

          <div class="min-w-0 flex-1">
            <p class="font-semibold text-white">
              Sua devolução está próxima
            </p>
            <p id="notice-text" class="mt-1 text-sm leading-relaxed text-slate-400">
              Seu Chromebook deve ser devolvido em breve.
            </p>
          </div>

          <button
            id="dismiss-notice-btn"
            class="rounded-lg px-3 py-2 text-xs font-semibold text-slate-400 transition hover:bg-white/5 hover:text-white cursor-pointer"
          >
            Entendi
          </button>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.45fr_0.85fr]">

          <!-- Empréstimo atual (Dinâmico do Banco) -->
          <section class="overflow-hidden rounded-2xl border border-white/10 bg-[#17181c]">
            <div class="flex items-center justify-between border-b border-white/8 px-5 py-5 md:px-6">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                  Empréstimo atual
                </p>
                <h2 id="titulo-qtd-emprestimo" class="mt-1 text-lg font-semibold text-white">
                  Carregando...
                </h2>
              </div>

              <span id="badge-status-emprestimo" class="hidden items-center gap-1.5 rounded-full bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold text-emerald-400">
                <span class="size-1.5 rounded-full bg-emerald-400"></span>
                Em dia
              </span>
            </div>

            <div id="conteudo-emprestimo-ativo" class="p-5 md:p-6 text-slate-400 text-sm">
              Buscando informações do seu empréstimo...
            </div>
          </section>

          <!-- Acesso rápido -->
          <section class="rounded-2xl border border-white/10 bg-[#17181c] p-5 md:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
              Acesso rápido
            </p>
            <h2 class="mt-1 text-lg font-semibold text-white">
              O que você quer fazer?
            </h2>

            <div class="mt-5 space-y-3">
              <a href="emprestimo_aluno.php" class="group flex w-full items-center gap-4 rounded-xl border border-white/8 bg-white/[0.025] p-4 text-left transition hover:border-violet-500/30 hover:bg-violet-500/[0.08] cursor-pointer">
                <span class="grid size-10 place-items-center rounded-lg bg-violet-500/10 text-violet-400">
                  <svg aria-hidden="true" class="size-[19px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                  </svg>
                </span>

                <span class="flex-1">
                  <span class="block text-sm font-semibold text-white">
                    Solicitar Chromebook
                  </span>
                  <span class="mt-0.5 block text-xs text-slate-500">
                    Consulte e faça sua solicitação
                  </span>
                </span>

                <svg aria-hidden="true" class="size-4 text-slate-600 transition group-hover:translate-x-0.5 group-hover:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18 6-6-6-6"/>
                </svg>
              </a>

              <a href="emprestimo_aluno.php" class="group flex w-full items-center gap-4 rounded-xl border border-white/8 bg-white/[0.025] p-4 text-left transition hover:border-violet-500/30 hover:bg-violet-500/[0.08] cursor-pointer">
                <span class="grid size-10 place-items-center rounded-lg bg-violet-500/10 text-violet-400">
                  <svg aria-hidden="true" class="size-[19px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="4" width="16" height="12" rx="1.5"/>
                    <path d="M2 19h20M9 19h6"/>
                  </svg>
                </span>

                <span class="flex-1">
                  <span class="block text-sm font-semibold text-white">
                    Meus empréstimos
                  </span>
                  <span class="mt-0.5 block text-xs text-slate-500">
                    Veja seu histórico
                  </span>
                </span>

                <svg aria-hidden="true" class="size-4 text-slate-600 transition group-hover:translate-x-0.5 group-hover:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18 6-6-6-6"/>
                </svg>
              </a>
            </div>
          </section>
        </div>

        <!-- Status de pendências -->
        <section class="mt-6 flex flex-col justify-between gap-4 rounded-2xl border border-white/8 bg-white/[0.025] p-5 sm:flex-row sm:items-center">
          <div class="flex items-center gap-3">
            <div class="grid size-9 place-items-center rounded-full bg-emerald-400/10 text-emerald-400">
              <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="m5 12 4 4L19 6"/>
              </svg>
            </div>

            <div>
              <p class="text-sm font-semibold text-white">Tudo certo por aqui</p>
              <p class="mt-0.5 text-xs text-slate-500">
                Você não possui pendências.
              </p>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Script Completo de Interatividade, Validação e Logout -->
  <script>
    const menuToggleBtn = document.getElementById("menu-toggle-btn");
    const sidebar = document.getElementById("sidebar");
    const sidebarBackdrop = document.getElementById("sidebar-backdrop");
    const menuIconSvg = document.getElementById("menu-icon-svg");
    const dismissNoticeBtn = document.getElementById("dismiss-notice-btn");
    const noticeSection = document.getElementById("notice-section");
    const logoutBtn = document.getElementById("logout-btn");

    let menuOpen = false;

    function toggleMenu() {
      menuOpen = !menuOpen;

      if (menuOpen) {
        sidebar.classList.remove("-translate-x-full");
        sidebarBackdrop.classList.remove("hidden");
        menuIconSvg.innerHTML = '<path d="m6 6 12 12M18 6 6 18" />';
        menuToggleBtn.setAttribute("aria-label", "Fechar menu");
      } else {
        sidebar.classList.add("-translate-x-full");
        sidebarBackdrop.classList.add("hidden");
        menuIconSvg.innerHTML = '<path d="M4 7h16M4 12h16M4 17h16" />';
        menuToggleBtn.setAttribute("aria-label", "Abrir menu");
      }
    }

    menuToggleBtn.addEventListener("click", toggleMenu);
    sidebarBackdrop.addEventListener("click", toggleMenu);

    if(dismissNoticeBtn) {
      dismissNoticeBtn.addEventListener("click", () => {
        noticeSection.style.display = "none";
      });
    }

    // Ação do Botão Sair (Logout Seguro)
    logoutBtn.addEventListener("click", async () => {
      try {
        localStorage.removeItem("aluno_logado");
        await fetch("logout.php?ajax=1", { method: "GET", cache: "no-store", credentials: "same-origin" });
      } catch (e) {
        console.error("Erro ao encerrar sessão:", e);
      }
      window.location.replace("login_aluno.html");
    });

    function gerarIniciais(nome) {
      const partes = nome.trim().split(/\s+/).filter(Boolean);
      if (partes.length === 0) return "--";
      if (partes.length === 1) return partes[0].substring(0, 2).toUpperCase();
      return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
    }

    async function carregarDadosAluno() {
      try {
        const resposta = await fetch("dados_aluno.php", {
          method: "GET",
          credentials: "same-origin",
          cache: "no-store"
        });

        const dados = await resposta.json();

        if (resposta.status === 401 || dados.sucesso !== true) {
          window.location.replace("login_aluno.html");
          return;
        }

        const nome = dados.nome.trim();
        localStorage.setItem("aluno_logado", JSON.stringify({ nome: nome, matricula: dados.matricula }));

        document.getElementById("nome-aluno").textContent = nome;
        document.getElementById("saudacao-aluno").textContent = nome;
        document.getElementById("iniciais-aluno").textContent = gerarIniciais(nome);

        const agora = new Date();
        const dataFormatada = agora.toLocaleDateString("pt-BR", {
          weekday: "long",
          day: "numeric",
          month: "long"
        });

        document.getElementById("data-atual").textContent = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);

      } catch (erro) {
        console.error("Erro ao carregar os dados do aluno:", erro);
        window.location.replace("login_aluno.html");
      }
    }

    async function carregarEmprestimoAtual() {
      try {
        const resposta = await fetch("api_meu_emprestimo.php", {
          method: "GET",
          cache: "no-store"
        });
        const dados = await resposta.json();

        const tituloQtd = document.getElementById("titulo-qtd-emprestimo");
        const badgeStatus = document.getElementById("badge-status-emprestimo");
        const containerEmprestimo = document.getElementById("conteudo-emprestimo-ativo");

        if (dados.sucesso && dados.emprestimo) {
          const emp = dados.emprestimo;

          tituloQtd.textContent = "1 Chromebook com você";
          badgeStatus.classList.remove("hidden");
          badgeStatus.classList.add("inline-flex");

          containerEmprestimo.innerHTML = `
            <div class="flex flex-col gap-5 sm:flex-row">
              <div class="relative flex h-44 w-full shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-violet-600 to-indigo-950 p-5 shadow-xl shadow-black/25 sm:w-40 border border-white/10">
                <div class="absolute -right-8 -top-8 size-24 rounded-full border border-white/15"></div>
                <div class="absolute -bottom-5 -left-5 size-20 rounded-full bg-white/5"></div>
                <div class="relative text-center">
                  <div class="grid h-[70px] w-[110px] place-items-center rounded-md border-2 border-violet-200/70 bg-[#121215] shadow-xl mx-auto">
                    <div class="grid size-7 place-items-center rounded-full bg-violet-500/20 text-[10px] font-extrabold text-violet-200">
                      CP
                    </div>
                  </div>
                  <div class="mx-auto h-2 w-[120px] rounded-b-md border-t border-violet-200/40 bg-violet-200/60 mt-0.5"></div>
                </div>
              </div>

              <div class="flex min-w-0 flex-1 flex-col justify-center">
                <p class="text-xs font-semibold uppercase tracking-wider text-violet-400">
                  Patrimônio: ${emp.patrimonio}
                </p>
                <h3 class="mt-1 text-xl font-bold text-white">
                  Chromebook ${emp.modelo}
                </h3>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div class="rounded-xl bg-white/[0.035] border border-white/5 p-3">
                    <p class="text-xs text-zinc-500">Retirado em</p>
                    <p class="mt-1 text-sm font-semibold text-zinc-200">${emp.retirado_em}</p>
                  </div>

                  <div class="rounded-xl bg-white/[0.035] border border-white/5 p-3">
                    <p class="text-xs text-zinc-500">Devolver até</p>
                    <p class="mt-1 text-sm font-semibold text-amber-300">
                      ${emp.devolver_ate} às 17:00
                    </p>
                  </div>
                </div>

                <a href="emprestimo_aluno.php" class="mt-4 flex items-center gap-1.5 self-start text-sm font-semibold text-violet-400 transition hover:text-violet-300 cursor-pointer">
                  Ver detalhes
                  <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                  </svg>
                </a>
              </div>
            </div>
          `;
        } else {
          tituloQtd.textContent = "Nenhum equipamento";
          badgeStatus.classList.add("hidden");

          containerEmprestimo.innerHTML = `
            <div class="py-6 text-center">
              <p class="text-zinc-400 text-sm">Você não possui nenhum Chromebook emprestado no momento.</p>
              <a href="emprestimo_aluno.php" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-violet-500 cursor-pointer">
                Solicitar Chromebook agora
              </a>
            </div>
          `;
        }
      } catch (erro) {
        console.error("Erro ao carregar empréstimo atual:", erro);
      }
    }

    carregarDadosAluno();
    carregarEmprestimoAtual();
  </script>
</body>
</html>