
<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Administrador');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão Acadêmica - Alunos | ceep.plg</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <style>
    :root { color-scheme: dark; }
    body {
      background-color: #09090b;
      color: #f4f4f5;
      font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
    }
  </style>

  <script>
  (function () {
    document.documentElement.style.visibility = 'hidden';
    const loginDestino = "login_adm.html";

    async function conferirSessao() {
      try {
        const resposta = await fetch('validar_sessao.php', {
          credentials: 'same-origin',
          cache: 'no-store',
          headers: { 'Accept': 'application/json' }
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.autenticado || dados.perfil !== 'Administrador') {
          window.location.replace(loginDestino);
          return;
        }

        document.documentElement.style.visibility = 'visible';

      } catch (erro) {
        window.location.replace(loginDestino);
      }
    }

    window.addEventListener('pagehide', function () {
      document.documentElement.style.visibility = 'hidden';
    });

    window.addEventListener('pageshow', conferirSessao);
  })();
  </script>
</head>

<body class="min-h-screen bg-[#09090b] text-zinc-100 flex flex-col">

  <!-- Header Superior -->
  <header class="sticky top-0 z-30 flex h-[72px] items-center justify-between border-b border-white/8 bg-[#09090b]/95 px-6 backdrop-blur">

    <div class="flex items-center gap-8">
      <strong class="text-xl font-bold tracking-tight text-white">ceep.plg</strong>

      <nav class="hidden md:flex items-center gap-1 text-sm font-medium" aria-label="Navegação principal">

        <button type="button"
          onclick="window.location.href='emprestimo_adm.php'"
          class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">
          Empréstimos
        </button>

        <button type="button"
          class="px-3 py-2 rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-950/20 flex items-center gap-2 cursor-pointer">
          Alunos
          <span id="nav-count" class="rounded-full bg-white/20 px-2 py-0.5 text-xs">0</span>
        </button>

        <button type="button"
          onclick="window.location.href='professores_adm.php'"
          class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">
          Professores
        </button>

        <button type="button"
          onclick="window.location.href='responsaveis_adm.php'"
          class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">
          Responsáveis
        </button>

      </nav>
    </div>

    <div class="flex items-center gap-4">

      <button class="relative rounded-xl p-2 text-zinc-400 hover:bg-white/5 hover:text-white cursor-pointer"
        aria-label="Notificações">

        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
        </svg>

        <span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-violet-500"></span>
      </button>

      <div class="flex items-center gap-3 pl-4 border-l border-white/8">

        <div id="adm-avatar"
          class="grid size-9 place-items-center rounded-xl bg-violet-600 text-xs font-bold text-white">
          AD
        </div>

        <div class="hidden sm:block text-left leading-tight">
          <p id="adm-nome" class="text-sm font-semibold text-white">Carregando...</p>
          <p id="adm-cargo" class="text-xs text-zinc-400">Administrador</p>
        </div>

      </div>

      <button
        id="adm-logout-btn"
        title="Encerrar sessão"
        class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 cursor-pointer">

        <svg class="size-4" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>

        <span class="hidden sm:inline">Sair</span>
      </button>

    </div>
  </header>

  <!-- Corpo da Página -->
  <main class="flex-1 p-6 lg:p-10 max-w-7xl w-full mx-auto">

    <div class="text-xs text-zinc-400 mb-6">
      Administração <span class="text-zinc-600">/</span>
      <strong class="text-white">Alunos</strong>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">

      <div>
        <p class="text-xs font-bold tracking-widest text-violet-400 mb-1">
          GESTÃO ACADÊMICA
        </p>

        <h1 class="text-3xl font-bold tracking-tight text-white">Alunos</h1>

        <p class="text-sm text-zinc-400 mt-1">
          Gerencie cadastros e informações dos estudantes vindos do banco de dados.
        </p>
      </div>

      <button id="btn-new-student"
        class="flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-950/20 hover:bg-violet-500 transition cursor-pointer">

        <svg class="size-4" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>

        Novo aluno
      </button>

    </div>

    <!-- Cartão de Conteúdo -->
    <div class="rounded-2xl border border-white/8 bg-[#121215] p-6 shadow-xl">

      <div class="flex flex-col sm:flex-row gap-4 mb-6">

        <div class="relative flex-1">

          <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-zinc-400"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>

          <input id="search-input" type="text"
            placeholder="Buscar por nome ou matrícula..."
            class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] pl-10 pr-10 text-sm text-white placeholder-zinc-500 outline-none focus:border-violet-500 transition">

          <button id="clear-search"
            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-white hidden cursor-pointer">

            <svg class="size-4" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2">
              <path d="m6 6 12 12M18 6 6 18"/>
            </svg>

          </button>
        </div>

        <select id="class-filter"
          class="h-11 rounded-xl border border-white/10 bg-[#18181b] px-4 text-sm text-white outline-none focus:border-violet-500 transition cursor-pointer">

          <option value="Todas as turmas">Todas as turmas</option>
          <option value="1º Ano A — Informática">1º Ano A — Informática</option>
          <option value="1º Ano B — Informática">1º Ano B — Informática</option>
          <option value="2º Ano A — Informática">2º Ano A — Informática</option>
          <option value="2º Ano B — Informática">2º Ano B — Informática</option>
          <option value="3º Ano A — Informática">3º Ano A — Informática</option>
          <option value="3º Ano B — Informática">3º Ano B — Informática</option>
          <option value="1º Ano A — Redes">1º Ano A — Redes</option>
          <option value="1º Ano B — Redes">1º Ano B — Redes</option>
          <option value="2º Ano A — Redes">2º Ano A — Redes</option>
          <option value="2º Ano B — Redes">2º Ano B — Redes</option>
          <option value="3º Ano A — Redes">3º Ano A — Redes</option>
          <option value="3º Ano B — Redes">3º Ano B — Redes</option>

        </select>
      </div>

      <div class="flex items-center justify-between text-xs text-zinc-400 mb-4 pb-4 border-b border-white/6">

        <span id="results-count">
          <strong>0</strong> alunos encontrados
        </span>

        <button id="reset-filters"
          class="hover:text-white transition hidden cursor-pointer">
          Limpar filtros
        </button>

      </div>

      <!-- Tabela Desktop -->
      <div class="overflow-x-auto hidden md:block">

        <table class="w-full text-left border-collapse">

          <thead>
            <tr class="border-b border-white/8 text-[11px] font-bold uppercase tracking-wider text-zinc-500">
              <th class="pb-3 font-semibold">ALUNO</th>
              <th class="pb-3 font-semibold">MATRÍCULA</th>
              <th class="pb-3 font-semibold">CONTATO</th>
              <th class="pb-3 font-semibold">TURMA</th>
              <th class="pb-3 font-semibold">STATUS</th>
              <th class="pb-3 font-semibold text-right">AÇÕES</th>
            </tr>
          </thead>

          <tbody id="students-table-body"
            class="divide-y divide-white/5 text-sm">
          </tbody>

        </table>
      </div>

      <!-- Lista Mobile -->
      <div id="students-mobile-list" class="space-y-3 md:hidden">
      </div>

      <!-- Estado Vazio -->
      <div id="empty-state" class="hidden py-12 text-center">

        <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-white/5 text-zinc-400 mb-3">

          <svg class="size-6" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>

        </div>

        <h3 class="font-semibold text-white">Nenhum aluno encontrado</h3>

        <p class="text-xs text-zinc-400 mt-1">
          Tente ajustar a busca ou os filtros selecionados.
        </p>

        <button id="empty-clear-btn"
          class="mt-4 rounded-xl bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/15 transition cursor-pointer">
          Limpar filtros
        </button>

      </div>

      <!-- Rodapé da Tabela -->
      <footer class="flex items-center justify-between pt-4 mt-4 border-t border-white/6 text-xs text-zinc-400">

        <span id="pagination-info">Exibindo 0 de 0</span>

        <div class="flex items-center gap-1">
          <button class="px-2.5 py-1 rounded-lg border border-white/10 bg-white/5 text-zinc-600 cursor-not-allowed">‹</button>
          <button class="px-3 py-1 rounded-lg bg-violet-600 text-white font-medium">1</button>
          <button class="px-2.5 py-1 rounded-lg border border-white/10 bg-white/5 text-zinc-600 cursor-not-allowed">›</button>
        </div>

      </footer>

    </div>
  </main>

  <!-- Modal Container -->
  <div id="modal-container"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">

    <div id="modal-card"
      class="relative w-full max-w-lg rounded-2xl border border-white/10 bg-[#121215] p-6 shadow-2xl">
    </div>

  </div>

  <!-- Toast Notification -->
  <div id="toast"
    class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300 shadow-xl transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">

    <span class="size-5 rounded-full bg-emerald-500/20 grid place-items-center text-xs">✓</span>

    <span id="toast-message">Operação realizada com sucesso.</span>
  </div>

  <script>
    let students = [];
    let query = "";
    let classFilter = "Todas as turmas";

    const classOptions = [
      "1º Ano A — Informática",
      "1º Ano B — Informática",
      "2º Ano A — Informática",
      "2º Ano B — Informática",
      "3º Ano A — Informática",
      "3º Ano B — Informática",
      "1º Ano A — Redes",
      "1º Ano B — Redes",
      "2º Ano A — Redes",
      "2º Ano B — Redes",
      "3º Ano A — Redes",
      "3º Ano B — Redes"
    ];

    const searchInput = document.getElementById("search-input");
    const clearSearchBtn = document.getElementById("clear-search");
    const classFilterSelect = document.getElementById("class-filter");
    const studentsTableBody = document.getElementById("students-table-body");
    const studentsMobileList = document.getElementById("students-mobile-list");
    const emptyState = document.getElementById("empty-state");
    const resultsCount = document.getElementById("results-count");
    const paginationInfo = document.getElementById("pagination-info");
    const resetFiltersBtn = document.getElementById("reset-filters");
    const emptyClearBtn = document.getElementById("empty-clear-btn");
    const navCount = document.getElementById("nav-count");
    const modalContainer = document.getElementById("modal-container");
    const modalContent = document.getElementById("modal-card");
    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toast-message");

    document.addEventListener("DOMContentLoaded", () => {

      const elAvatar = document.getElementById("adm-avatar");
      const elNome = document.getElementById("adm-nome");
      const elCargo = document.getElementById("adm-cargo");
      const admLogoutBtn = document.getElementById("adm-logout-btn");

      if (admLogoutBtn) {
        admLogoutBtn.addEventListener("click", async () => {

          try {
            localStorage.removeItem("adm_logado");

            await fetch("logout.php?ajax=1", {
              method: "GET",
              cache: "no-store",
              credentials: "same-origin"
            });

          } catch (e) {
            console.error("Erro ao encerrar sessão:", e);
          }

          window.location.replace("login_adm.html");
        });
      }

      try {
        const dadosSalvos = localStorage.getItem("adm_logado");

        if (dadosSalvos) {
          const admLogado = JSON.parse(dadosSalvos);

          if (admLogado && admLogado.nome) {

            const nomeCompleto = admLogado.nome.trim();
            const partes = nomeCompleto.split(/\s+/);

            let iniciais = partes[0][0];

            if (partes.length > 1) {
              iniciais += partes[partes.length - 1][0];
            }

            if (elAvatar) elAvatar.textContent = iniciais.toUpperCase();
            if (elNome) elNome.textContent = nomeCompleto;
            if (elCargo) elCargo.textContent = "Administrador";
          }
        }

      } catch (e) {
        console.error("Erro ao ler localStorage:", e);
      }

      carregarAlunos();
    });

    // Carregar alunos do banco de dados
    function carregarAlunos() {

      fetch("buscar_alunos.php", {
        credentials: "same-origin",
        cache: "no-store"
      })

      .then(res => res.json())

      .then(data => {

        if (data.sucesso) {
          students = data.alunos;
          render();

        } else {
          showToast(data.mensagem || "Erro ao carregar alunos.");
        }

      })

      .catch(err => {
        console.error("Erro na requisição:", err);
        showToast("Erro ao conectar com o servidor.");
      });
    }

    // Exibir mensagens
    function showToast(msg) {

      toastMessage.textContent = msg;

      toast.classList.remove("translate-y-20", "opacity-0");

      setTimeout(() => {
        toast.classList.add("translate-y-20", "opacity-0");
      }, 2800);
    }

    // Renderizar alunos
    function render() {

      const filtered = students.filter(s => {

        const nome = String(s.name || "").toLowerCase();
        const matricula = String(s.registration || "");

        const matchesQuery =
          !query ||
          nome.includes(query.toLowerCase()) ||
          matricula.includes(query);

        const matchesClass =
          classFilter === "Todas as turmas" ||
          s.className === classFilter;

        return matchesQuery && matchesClass;
      });

      navCount.textContent = students.length;

      resultsCount.innerHTML =
        `<strong>${filtered.length}</strong> ${filtered.length === 1 ? 'aluno encontrado' : 'alunos encontrados'}`;

      paginationInfo.textContent =
        `Exibindo 1–${filtered.length} de ${filtered.length}`;

      if (query || classFilter !== "Todas as turmas") {
        resetFiltersBtn.classList.remove("hidden");
      } else {
        resetFiltersBtn.classList.add("hidden");
      }

      if (filtered.length === 0) {

        emptyState.classList.remove("hidden");

        studentsTableBody.innerHTML = "";
        studentsMobileList.innerHTML = "";

        return;
      }

      emptyState.classList.add("hidden");

      // Tabela Desktop
      studentsTableBody.innerHTML = filtered.map(s => `

        <tr class="hover:bg-white/[0.02] transition">

          <td class="py-3.5 pr-4">
            <div class="font-semibold text-white">${s.name}</div>
            <div class="text-xs text-zinc-500">${s.email}</div>
          </td>

          <td class="py-3.5 pr-4 text-zinc-300 font-mono text-xs">
            ${s.registration}
          </td>

          <td class="py-3.5 pr-4">
            <div class="text-zinc-300">${s.phone}</div>
            <div class="text-xs text-zinc-500">${s.email}</div>
          </td>

          <td class="py-3.5 pr-4">
            <span class="inline-block px-3 py-1 rounded-full bg-violet-950/60 text-violet-300 text-xs font-medium border border-violet-800/40">
              ${s.className}
            </span>
          </td>

          <td class="py-3.5 pr-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Ativo
            </span>
          </td>

          <td class="py-3.5 text-right">

            <div class="flex items-center justify-end gap-1">

              <!-- Aprovar Empréstimo -->
              <button
                onclick="mudarStatusPermissao(${s.id}, 'Aprovado')"
                class="p-2 rounded-lg text-emerald-400 hover:bg-emerald-500/10 transition cursor-pointer"
                title="Permitir / Aprovar Empréstimo">

                <svg class="size-4" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <path d="M20 6L9 17l-5-5"/>
                </svg>
              </button>

              <!-- Recusar -->
              <button
                onclick="mudarStatusPermissao(${s.id}, 'Recusado')"
                class="p-2 rounded-lg text-amber-400 hover:bg-amber-500/10 transition cursor-pointer"
                title="Recusar Empréstimo">

                <svg class="size-4" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
              </button>

              <!-- Visualizar -->
              <button
                onclick="openView(${s.id})"
                class="p-2 rounded-lg text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer"
                title="Visualizar">

                <svg class="size-4" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.8">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>

              <!-- Editar -->
              <button
                onclick="openEdit(${s.id})"
                class="p-2 rounded-lg text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer"
                title="Editar">

                <svg class="size-4" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.8">
                  <path d="M12 20h9"/>
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                </svg>
              </button>

              <!-- Excluir -->
              <button
                onclick="openDelete(${s.id})"
                class="p-2 rounded-lg text-rose-400 hover:bg-rose-500/10 transition cursor-pointer"
                title="Excluir">

                <svg class="size-4" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.8">
                  <path d="M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6M10 11v5M14 11v5"/>
                </svg>
              </button>

            </div>
          </td>

        </tr>

      `).join("");

      // Lista Mobile
      studentsMobileList.innerHTML = filtered.map(s => `

        <article class="rounded-xl border border-white/8 bg-white/[0.02] p-4 space-y-3">

          <div class="flex items-start justify-between">

            <div>
              <strong class="text-white block">${s.name}</strong>

              <span class="text-xs text-zinc-500 block">
                ${s.email}
              </span>

              <span class="text-xs text-zinc-400 font-mono mt-1 block">
                Matrícula: ${s.registration}
              </span>
            </div>

            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Ativo
            </span>

          </div>

          <div class="grid grid-cols-2 gap-2 text-xs text-zinc-400 pt-2 border-t border-white/5">

            <div>
              <span>Turma:</span>
              <strong class="text-zinc-200 block">${s.className}</strong>
            </div>

            <div>
              <span>Telefone:</span>
              <strong class="text-zinc-200 block">${s.phone}</strong>
            </div>

          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/5">

            <button
              onclick="mudarStatusPermissao(${s.id}, 'Aprovado')"
              class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs font-semibold cursor-pointer">
              Aprovar
            </button>

            <button
              onclick="openView(${s.id})"
              class="px-3 py-1.5 rounded-lg bg-white/5 text-xs font-medium text-zinc-300 hover:bg-white/10 cursor-pointer">
              Ver dados
            </button>

            <button
              onclick="openEdit(${s.id})"
              class="p-2 rounded-lg bg-white/5 text-zinc-300 hover:bg-white/10 cursor-pointer">
              Editar
            </button>

            <!-- Novo botão Excluir Mobile -->
            <button
              onclick="openDelete(${s.id})"
              class="px-3 py-1.5 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-semibold hover:bg-rose-500/20 cursor-pointer">
              Excluir
            </button>

          </div>

        </article>

      `).join("");
    }

    // Busca
    searchInput.addEventListener("input", (e) => {

      query = e.target.value;

      clearSearchBtn.classList.toggle("hidden", !query);

      render();
    });

    clearSearchBtn.addEventListener("click", () => {

      searchInput.value = "";
      query = "";

      clearSearchBtn.classList.add("hidden");

      render();
    });

    // Filtro por turma
    classFilterSelect.addEventListener("change", (e) => {

      classFilter = e.target.value;

      render();
    });

    // Limpar filtros
    function resetAllFilters() {

      searchInput.value = "";
      query = "";

      clearSearchBtn.classList.add("hidden");

      classFilter = "Todas as turmas";
      classFilterSelect.value = "Todas as turmas";

      render();
    }

    resetFiltersBtn.addEventListener("click", resetAllFilters);
    emptyClearBtn.addEventListener("click", resetAllFilters);

    // Fechar modal
    function closeModal() {
      modalContainer.classList.add("hidden");
    }

    window.closeModal = closeModal;

    modalContainer.addEventListener("click", (e) => {
      if (e.target === modalContainer) closeModal();
    });

    // Alterar permissão do aluno
    window.mudarStatusPermissao = function(id, statusPermissao) {

      const acaoTexto =
        statusPermissao === 'Aprovado' ? 'permitir' : 'recusar';

      if (!confirm(`Deseja realmente ${acaoTexto} o empréstimo para este aluno?`)) {
        return;
      }

      const formData = new FormData();

      formData.append("id", id);
      formData.append("status_permissao", statusPermissao);

      fetch("atualizar_permissao_aluno.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin"
      })

      .then(res => res.json())

      .then(resData => {

        if (resData.sucesso) {

          showToast(`Permissão atualizada para: ${statusPermissao}`);

          carregarAlunos();

        } else {

          showToast(resData.mensagem || "Erro ao atualizar permissão.");
        }

      })

      .catch(err => {

        console.error("Erro na requisição:", err);

        showToast("Erro de conexão ao salvar permissão.");
      });
    };

    // Visualizar aluno
    window.openView = function(id) {

      const s = students.find(item => item.id == id);

      if (!s) return;

      modalContent.innerHTML = `

        <div class="flex items-start justify-between mb-6">

          <div>
            <p class="text-xs font-bold text-violet-400 uppercase tracking-widest">
              PERFIL DO ALUNO
            </p>

            <h2 class="text-xl font-bold text-white mt-0.5">
              Informações do cadastro
            </h2>
          </div>

          <button onclick="closeModal()"
            class="text-zinc-400 hover:text-white cursor-pointer">

            <svg class="size-5" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2">
              <path d="m6 6 12 12M18 6 6 18"/>
            </svg>

          </button>

        </div>

        <div class="mb-6 p-4 rounded-xl bg-white/[0.03] space-y-2">

          <h3 class="text-lg font-bold text-white">${s.name}</h3>

          <p class="text-sm text-zinc-400">${s.email}</p>

        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">

          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Matrícula</span>
            <strong class="text-white font-mono">${s.registration}</strong>
          </div>

          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Turma</span>
            <strong class="text-white">${s.className}</strong>
          </div>

          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Telefone</span>
            <strong class="text-white">${s.phone}</strong>
          </div>

        </div>

        <div class="flex items-center justify-end gap-3">

          <button onclick="closeModal()"
            class="px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 text-sm font-semibold text-zinc-300 hover:bg-white/10 cursor-pointer">
            Fechar
          </button>

          <button onclick="closeModal(); openEdit(${s.id})"
            class="px-4 py-2.5 rounded-xl bg-violet-600 text-sm font-semibold text-white hover:bg-violet-500 cursor-pointer">
            Editar cadastro
          </button>

        </div>
      `;

      modalContainer.classList.remove("hidden");
    };

    // Formulário de cadastro e edição
    function openFormModal(studentToEdit = null) {

      const isEditing = !!studentToEdit;

      const data = studentToEdit || {
        id: "",
        name: "",
        registration: "",
        phone: "",
        email: "",
        className: classOptions[0]
      };

      modalContent.innerHTML = `

        <form id="student-form" class="space-y-4">

          <input type="hidden" name="id" value="${data.id}">

          <div class="flex items-start justify-between">

            <div>
              <p class="text-xs font-bold text-violet-400 uppercase tracking-widest">
                ${isEditing ? 'ATUALIZAR CADASTRO' : 'NOVO CADASTRO'}
              </p>

              <h2 class="text-xl font-bold text-white mt-0.5">
                ${isEditing ? 'Editar aluno' : 'Cadastrar novo aluno'}
              </h2>
            </div>

            <button type="button" onclick="closeModal()"
              class="text-zinc-400 hover:text-white cursor-pointer">

              <svg class="size-5" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="m6 6 12 12M18 6 6 18"/>
              </svg>

            </button>

          </div>

          <div class="space-y-3 text-sm">

            <div>
              <label class="block text-xs font-medium text-zinc-400 mb-1">
                Nome completo
              </label>

              <input name="name" value="${data.name}" required
                placeholder="Nome do aluno"
                class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
            </div>

            <div class="grid grid-cols-2 gap-3">

              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">
                  Matrícula
                </label>

                <input name="registration" value="${data.registration}" required
                  placeholder="0000000000"
                  class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
              </div>

              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">
                  Telefone
                </label>

                <input name="phone" value="${data.phone}" required
                  placeholder="(00) 00000-0000"
                  class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
              </div>

            </div>

            <div>
              <label class="block text-xs font-medium text-zinc-400 mb-1">
                E-mail institucional
              </label>

              <input name="email" type="email" value="${data.email}" required
                placeholder="nome@aluno.edu.br"
                class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
            </div>

            <div>
              <label class="block text-xs font-medium text-zinc-400 mb-1">
                Turma
              </label>

              <select name="className"
                class="w-full h-11 rounded-xl border border-white/10 bg-[#18181b] px-3 text-white outline-none focus:border-violet-500">

                ${classOptions.map(c =>
                  `<option value="${c}" ${c === data.className ? 'selected' : ''}>${c}</option>`
                ).join('')}

              </select>
            </div>

          </div>

          <div class="flex items-center justify-end gap-3 pt-2">

            <button type="button" onclick="closeModal()"
              class="px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 text-sm font-semibold text-zinc-300 hover:bg-white/10 cursor-pointer">
              Cancelar
            </button>

            <button type="submit"
              class="px-4 py-2.5 rounded-xl bg-violet-600 text-sm font-semibold text-white hover:bg-violet-500 cursor-pointer">
              ${isEditing ? 'Salvar alterações' : 'Cadastrar aluno'}
            </button>

          </div>

        </form>
      `;

      document.getElementById("student-form").addEventListener("submit", (ev) => {

        ev.preventDefault();

        const formData = new FormData(ev.target);

        fetch("salvar_aluno.php", {
          method: "POST",
          body: formData,
          credentials: "same-origin"
        })

        .then(res => res.json())

        .then(resData => {

          if (resData.sucesso) {

            showToast(resData.mensagem);

            closeModal();

            carregarAlunos();

          } else {

            showToast(resData.mensagem || "Erro ao salvar.");
          }

        })

        .catch(() => {
          showToast("Erro de conexão ao salvar.");
        });

      });

      modalContainer.classList.remove("hidden");
    }

    // Novo aluno
    document.getElementById("btn-new-student").addEventListener("click", () => {
      openFormModal();
    });

    // Editar aluno
    window.openEdit = function(id) {

      const s = students.find(item => item.id == id);

      if (s) {
        openFormModal(s);
      }
    };

    // Excluir aluno
    window.openDelete = async function(id) {

      const aluno = students.find(item => item.id == id);

      if (!aluno) {
        showToast("Aluno não encontrado na lista.");
        return;
      }

      const confirmar = confirm(
        `Deseja realmente excluir o cadastro de ${aluno.name}?\n\n` +
        "Essa ação não poderá ser desfeita. Alunos com empréstimos registrados não poderão ser excluídos."
      );

      if (!confirmar) return;

      const formData = new FormData();

      formData.append("id", id);

      try {

        const resposta = await fetch("excluir_aluno.php", {
          method: "POST",
          body: formData,
          credentials: "same-origin",
          cache: "no-store"
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {

          showToast(
            dados.mensagem || "Não foi possível excluir o aluno."
          );

          return;
        }

        showToast(
          dados.mensagem || "Aluno excluído com sucesso!"
        );

        carregarAlunos();

      } catch (erro) {

        console.error("Erro ao excluir aluno:", erro);

        showToast("Erro de conexão ao tentar excluir o aluno.");
      }
    };

  </script>

</body>
</html>
