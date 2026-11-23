
<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Administrador');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão Acadêmica - Professores | ceep.plg</title>
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

  <header class="sticky top-0 z-30 flex h-[72px] items-center justify-between border-b border-white/8 bg-[#09090b]/95 px-6 backdrop-blur">
    <div class="flex items-center gap-8">
      <strong class="text-xl font-bold tracking-tight text-white">ceep.plg</strong>

      <nav class="hidden md:flex items-center gap-1 text-sm font-medium" aria-label="Navegação principal">
        <button type="button" onclick="window.location.href='emprestimo_adm.php'" class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">Empréstimos</button>
        <button type="button" onclick="window.location.href='alunos_adm.php'" class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">Alunos</button>
        <button type="button" class="px-3 py-2 rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-950/20 flex items-center gap-2 cursor-pointer">
          Professores <span id="nav-count" class="rounded-full bg-white/20 px-2 py-0.5 text-xs">0</span>
        </button>
        <button type="button" onclick="window.location.href='responsaveis_adm.php'" class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">Responsáveis</button>
      </nav>
    </div>

    <div class="flex items-center gap-4">
      <button class="relative rounded-xl p-2 text-zinc-400 hover:bg-white/5 hover:text-white cursor-pointer" aria-label="Notificações">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
        </svg>
        <span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-violet-500"></span>
      </button>

      <div class="flex items-center gap-3 pl-4 border-l border-white/8">
        <div id="adm-avatar" class="grid size-9 place-items-center rounded-xl bg-violet-600 text-xs font-bold text-white">AD</div>
        <div class="hidden sm:block text-left leading-tight">
          <p id="adm-nome" class="text-sm font-semibold text-white">Carregando...</p>
          <p id="adm-cargo" class="text-xs text-zinc-400">Administrador</p>
        </div>
      </div>

      <button id="adm-logout-btn" title="Encerrar sessão"
        class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 cursor-pointer">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        <span class="hidden sm:inline">Sair</span>
      </button>
    </div>
  </header>

  <main class="flex-1 p-6 lg:p-10 max-w-7xl w-full mx-auto">

    <div class="text-xs text-zinc-400 mb-6">
      Administração <span class="text-zinc-600">/</span> <strong class="text-white">Professores</strong>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <p class="text-xs font-bold tracking-widest text-violet-400 mb-1">GESTÃO ACADÊMICA</p>
        <h1 class="text-3xl font-bold tracking-tight text-white">Professores</h1>
        <p class="text-sm text-zinc-400 mt-1">Gerencie cadastros e informações do corpo docente vindos do banco de dados.</p>
      </div>

      <button id="btn-new-teacher" class="flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-950/20 hover:bg-violet-500 transition cursor-pointer">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        Novo professor
      </button>
    </div>

    <div class="rounded-2xl border border-white/8 bg-[#121215] p-6 shadow-xl">

      <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="relative flex-1">
          <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>

          <input id="search-input" type="text" placeholder="Buscar por nome ou disciplina..."
            class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] pl-10 pr-10 text-sm text-white placeholder-zinc-500 outline-none focus:border-violet-500 transition">

          <button id="clear-search" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-white hidden cursor-pointer">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m6 6 12 12M18 6 6 18"/>
            </svg>
          </button>
        </div>

        <select id="department-filter" class="h-11 rounded-xl border border-white/10 bg-[#18181b] px-4 text-sm text-white outline-none focus:border-violet-500 transition cursor-pointer">
          <option value="Todos os departamentos">Todos os departamentos</option>
          <option value="Informática">Informática</option>
          <option value="Redes">Redes</option>
          <option value="Base Nacional Comum Curricular">Base Nacional Comum Curricular</option>
        </select>
      </div>

      <div class="flex items-center justify-between text-xs text-zinc-400 mb-4 pb-4 border-b border-white/6">
        <span id="results-count"><strong>0</strong> professores encontrados</span>
        <button id="reset-filters" class="hover:text-white transition hidden cursor-pointer">Limpar filtros</button>
      </div>

      <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-white/8 text-[11px] font-bold uppercase tracking-wider text-zinc-500">
              <th class="pb-3 font-semibold">PROFESSOR</th>
              <th class="pb-3 font-semibold">REGISTRO / SIAPE</th>
              <th class="pb-3 font-semibold">CONTATO</th>
              <th class="pb-3 font-semibold">DISCIPLINA / ÁREA</th>
              <th class="pb-3 font-semibold">STATUS</th>
              <th class="pb-3 font-semibold text-right">AÇÕES</th>
            </tr>
          </thead>

          <tbody id="teachers-table-body" class="divide-y divide-white/5 text-sm"></tbody>
        </table>
      </div>

      <div id="teachers-mobile-list" class="space-y-3 md:hidden"></div>

      <div id="empty-state" class="hidden py-12 text-center">
        <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-white/5 text-zinc-400 mb-3">
          <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>
        </div>

        <h3 class="font-semibold text-white">Nenhum professor encontrado</h3>
        <p class="text-xs text-zinc-400 mt-1">Tente ajustar a busca ou os filtros selecionados.</p>
        <button id="empty-clear-btn" class="mt-4 rounded-xl bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/15 transition cursor-pointer">Limpar filtros</button>
      </div>

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

  <div id="modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm hidden">
    <div id="modal-card" class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 bg-[#121215] p-6 shadow-2xl"></div>
  </div>

  <div id="toast" class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300 shadow-xl transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">
    <span class="size-5 rounded-full bg-emerald-500/20 grid place-items-center text-xs">✓</span>
    <span id="toast-message">Operação realizada com sucesso.</span>
  </div>

  <script>
    let teachers = [];
    let query = "";
    let departmentFilter = "Todos os departamentos";

    const departmentOptions = [
      "Informática",
      "Redes",
      "Base Nacional Comum Curricular"
    ];

    const searchInput = document.getElementById("search-input");
    const clearSearchBtn = document.getElementById("clear-search");
    const departmentFilterSelect = document.getElementById("department-filter");
    const teachersTableBody = document.getElementById("teachers-table-body");
    const teachersMobileList = document.getElementById("teachers-mobile-list");
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

      carregarProfessores();
    });

    function carregarProfessores() {
      fetch("buscar_professores.php", {
        credentials: "same-origin",
        cache: "no-store"
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso) {
          teachers = data.professores;
          render();
        } else {
          showToast(data.mensagem || "Erro ao carregar professores.");
        }
      })
      .catch(err => {
        console.error("Erro na requisição:", err);
        showToast("Erro ao conectar com o servidor.");
      });
    }

    function showToast(msg) {
      toastMessage.textContent = msg;
      toast.classList.remove("translate-y-20", "opacity-0");

      setTimeout(() => {
        toast.classList.add("translate-y-20", "opacity-0");
      }, 2800);
    }

    function render() {
      const filtered = teachers.filter(t => {
        const nome = (t.name || "").toLowerCase();
        const disciplina = (t.subject || "").toLowerCase();

        const matchesQuery = !query ||
          nome.includes(query.toLowerCase()) ||
          disciplina.includes(query.toLowerCase());

        const matchesDepartment =
          departmentFilter === "Todos os departamentos" ||
          t.department === departmentFilter;

        return matchesQuery && matchesDepartment;
      });

      navCount.textContent = teachers.length;

      resultsCount.innerHTML =
        `<strong>${filtered.length}</strong> ${filtered.length === 1 ? 'professor encontrado' : 'professores encontrados'}`;

      paginationInfo.textContent =
        `Exibindo ${filtered.length} de ${filtered.length}`;

      resetFiltersBtn.classList.toggle(
        "hidden",
        !query && departmentFilter === "Todos os departamentos"
      );

      if (filtered.length === 0) {
        emptyState.classList.remove("hidden");
        teachersTableBody.innerHTML = "";
        teachersMobileList.innerHTML = "";
        return;
      }

      emptyState.classList.add("hidden");

      teachersTableBody.innerHTML = filtered.map(t => `
        <tr class="hover:bg-white/[0.02] transition">
          <td class="py-3.5 pr-4">
            <div class="font-semibold text-white">${t.name}</div>
            <div class="text-xs text-zinc-500">${t.email}</div>
          </td>

          <td class="py-3.5 pr-4 text-zinc-300 font-mono text-xs">
            ${t.registration || t.siape || '—'}
          </td>

          <td class="py-3.5 pr-4">
            <div class="text-zinc-300">${t.phone || '—'}</div>
            <div class="text-xs text-zinc-500">${t.email}</div>
          </td>

          <td class="py-3.5 pr-4">
            <div class="text-zinc-300 text-xs">${t.subject || 'Não informada'}</div>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full bg-violet-950/60 text-violet-300 text-[11px] font-medium border border-violet-800/40">${t.department}</span>
          </td>

          <td class="py-3.5 pr-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Ativo
            </span>
          </td>

          <td class="py-3.5 text-right">
            <div class="flex items-center justify-end gap-2">
              <button onclick="openView(${t.id})" class="p-2 rounded-lg text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer" title="Visualizar">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>

              <button onclick="openEdit(${t.id})" class="p-2 rounded-lg text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer" title="Editar">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M12 20h9" />
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                </svg>
              </button>

              <button onclick="openDelete(${t.id})" class="p-2 rounded-lg text-rose-400 hover:bg-rose-500/10 transition cursor-pointer" title="Excluir">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6M10 11v5M14 11v5" />
                </svg>
              </button>
            </div>
          </td>
        </tr>
      `).join("");

      teachersMobileList.innerHTML = filtered.map(t => `
        <article class="rounded-xl border border-white/8 bg-white/[0.02] p-4 space-y-3">
          <div class="flex items-start justify-between">
            <div>
              <strong class="text-white block">${t.name}</strong>
              <span class="text-xs text-zinc-500 block">${t.email}</span>
              <span class="text-xs text-zinc-400 font-mono mt-1 block">SIAPE/Registro: ${t.registration || t.siape || '—'}</span>
            </div>

            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Ativo
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs text-zinc-400 pt-2 border-t border-white/5">
            <div>
              <span>Departamento:</span>
              <strong class="text-zinc-200 block">${t.department}</strong>
            </div>

            <div>
              <span>Telefone:</span>
              <strong class="text-zinc-200 block">${t.phone || '—'}</strong>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/5">
            <button onclick="openView(${t.id})" class="px-3 py-1.5 rounded-lg bg-white/5 text-xs font-medium text-zinc-300 hover:bg-white/10 cursor-pointer">Ver dados</button>
            <button onclick="openEdit(${t.id})" class="p-2 rounded-lg bg-white/5 text-zinc-300 hover:bg-white/10 cursor-pointer">Editar</button>
            <button onclick="openDelete(${t.id})" class="p-2 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 cursor-pointer" title="Excluir">
              <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6M10 11v5M14 11v5" />
              </svg>
            </button>
          </div>
        </article>
      `).join("");
    }

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

    departmentFilterSelect.addEventListener("change", (e) => {
      departmentFilter = e.target.value;
      render();
    });

    function resetAllFilters() {
      searchInput.value = "";
      query = "";
      clearSearchBtn.classList.add("hidden");
      departmentFilter = "Todos os departamentos";
      departmentFilterSelect.value = "Todos os departamentos";
      render();
    }

    resetFiltersBtn.addEventListener("click", resetAllFilters);
    emptyClearBtn.addEventListener("click", resetAllFilters);

    function closeModal() {
      modalContainer.classList.add("hidden");
    }

    window.closeModal = closeModal;

    modalContainer.addEventListener("click", (e) => {
      if (e.target === modalContainer) closeModal();
    });

    window.openView = function(id) {
      const t = teachers.find(item => item.id == id);
      if (!t) return;

      modalContent.innerHTML = `
        <div class="flex items-start justify-between mb-6">
          <div>
            <p class="text-xs font-bold text-violet-400 uppercase tracking-widest">PERFIL DO PROFESSOR</p>
            <h2 class="text-xl font-bold text-white mt-0.5">Informações do cadastro</h2>
          </div>

          <button onclick="closeModal()" class="text-zinc-400 hover:text-white cursor-pointer">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m6 6 12 12M18 6 6 18"/>
            </svg>
          </button>
        </div>

        <div class="mb-6 p-4 rounded-xl bg-white/[0.03] space-y-2">
          <h3 class="text-lg font-bold text-white">${t.name}</h3>
          <p class="text-sm text-zinc-400">${t.email}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Registro / SIAPE</span>
            <strong class="text-white font-mono">${t.registration || t.siape || '—'}</strong>
          </div>

          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Departamento</span>
            <strong class="text-white">${t.department}</strong>
          </div>

          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Disciplina</span>
            <strong class="text-white">${t.subject || 'Não informada'}</strong>
          </div>

          <div class="p-3 rounded-xl bg-white/[0.02]">
            <span class="text-xs text-zinc-500 block">Telefone</span>
            <strong class="text-white">${t.phone || '—'}</strong>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3">
          <button onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 text-sm font-semibold text-zinc-300 hover:bg-white/10 cursor-pointer">Fechar</button>
          <button onclick="closeModal(); openEdit(${t.id})" class="px-4 py-2.5 rounded-xl bg-violet-600 text-sm font-semibold text-white hover:bg-violet-500 cursor-pointer">Editar cadastro</button>
        </div>
      `;

      modalContainer.classList.remove("hidden");
    };

    function alternarSenha(id) {
      const campo = document.getElementById(id);
      const botao = document.querySelector(`[data-toggle="${id}"]`);

      if (!campo || !botao) return;

      const mostrar = campo.type === "password";
      campo.type = mostrar ? "text" : "password";
      botao.textContent = mostrar ? "Ocultar" : "Mostrar";
    }

    window.alternarSenha = alternarSenha;

    function openFormModal(teacherToEdit = null) {
      const isEditing = !!teacherToEdit;

      const data = teacherToEdit || {
        id: "",
        name: "",
        registration: "",
        phone: "",
        email: "",
        department: departmentOptions[0],
        subject: ""
      };

      modalContent.innerHTML = `
        <form id="teacher-form" class="space-y-4">
          <input type="hidden" name="id" value="${data.id}">

          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs font-bold text-violet-400 uppercase tracking-widest">
                ${isEditing ? 'ATUALIZAR CADASTRO' : 'NOVO CADASTRO'}
              </p>

              <h2 class="text-xl font-bold text-white mt-0.5">
                ${isEditing ? 'Editar professor' : 'Cadastrar novo professor'}
              </h2>
            </div>

            <button type="button" onclick="closeModal()" class="text-zinc-400 hover:text-white cursor-pointer">
              <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m6 6 12 12M18 6 6 18"/>
              </svg>
            </button>
          </div>

          <div class="space-y-3 text-sm">

            <div>
              <label class="block text-xs font-medium text-zinc-400 mb-1">Nome completo</label>
              <input name="name" value="${data.name || ''}" required placeholder="Nome do professor"
                class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Registro / SIAPE</label>
                <input name="registration" value="${data.registration || data.siape || ''}" required placeholder="SIAPE ou Registro"
                  class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
              </div>

              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Telefone</label>
                <input name="phone" value="${data.phone || ''}" required placeholder="(00) 00000-0000"
                  class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-zinc-400 mb-1">E-mail institucional</label>
              <input name="email" type="email" value="${data.email || ''}" required placeholder="nome@professor.edu.br"
                class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Departamento</label>
                <select name="department" class="w-full h-11 rounded-xl border border-white/10 bg-[#18181b] px-3 text-white outline-none focus:border-violet-500">
                  ${departmentOptions.map(d =>
                    `<option value="${d}" ${d === data.department ? 'selected' : ''}>${d}</option>`
                  ).join('')}
                </select>
              </div>

              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Disciplina principal</label>
                <input name="subject" value="${data.subject || ''}" placeholder="Ex: Programação Web"
                  class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">
              </div>
            </div>

            <div class="border-t border-white/10 pt-4 space-y-3">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">
                  Senha ${isEditing ? '(opcional)' : ''}
                </label>

                <div class="flex gap-2">
                  <input id="senha-professor" name="senha" type="password"
                    ${isEditing ? '' : 'required'}
                    autocomplete="new-password"
                    placeholder="${isEditing ? 'Deixe vazio para manter a atual' : 'Defina a senha inicial'}"
                    class="flex-1 min-w-0 h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">

                  <button type="button" data-toggle="senha-professor"
                    onclick="alternarSenha('senha-professor')"
                    class="px-3 rounded-xl border border-white/10 bg-white/5 text-xs text-zinc-300 hover:bg-white/10">
                    Mostrar
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">
                  Confirmar senha ${isEditing ? '(opcional)' : ''}
                </label>

                <div class="flex gap-2">
                  <input id="confirmar-senha-professor" name="confirmar_senha" type="password"
                    ${isEditing ? '' : 'required'}
                    autocomplete="new-password"
                    placeholder="Digite a senha novamente"
                    class="flex-1 min-w-0 h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-white outline-none focus:border-violet-500">

                  <button type="button" data-toggle="confirmar-senha-professor"
                    onclick="alternarSenha('confirmar-senha-professor')"
                    class="px-3 rounded-xl border border-white/10 bg-white/5 text-xs text-zinc-300 hover:bg-white/10">
                    Mostrar
                  </button>
                </div>
              </div>

              <p class="text-xs text-zinc-500">
                ${isEditing
                  ? 'Para alterar a senha, preencha os dois campos. Se deixar ambos vazios, a senha atual será mantida.'
                  : 'A senha será utilizada pelo professor para acessar o sistema.'}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" onclick="closeModal()"
              class="px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 text-sm font-semibold text-zinc-300 hover:bg-white/10 cursor-pointer">
              Cancelar
            </button>

            <button type="submit"
              class="px-4 py-2.5 rounded-xl bg-violet-600 text-sm font-semibold text-white hover:bg-violet-500 cursor-pointer">
              ${isEditing ? 'Salvar alterações' : 'Cadastrar professor'}
            </button>
          </div>
        </form>
      `;

      document.getElementById("teacher-form").addEventListener("submit", (ev) => {
        ev.preventDefault();

        const form = ev.target;
        const senha = form.elements["senha"].value;
        const confirmarSenha = form.elements["confirmar_senha"].value;

        if (!isEditing && !senha) {
          showToast("Informe uma senha para o professor.");
          return;
        }

        if (senha !== confirmarSenha) {
          showToast("As senhas não coincidem.");
          return;
        }

        const formData = new FormData(form);

        fetch("salvar_professor.php", {
          method: "POST",
          body: formData,
          credentials: "same-origin"
        })
        .then(res => res.json())
        .then(resData => {
          if (resData.sucesso) {
            showToast(resData.mensagem);
            closeModal();
            carregarProfessores();
          } else {
            showToast(resData.mensagem || "Erro ao salvar.");
          }
        })
        .catch(err => {
          console.error("Erro ao salvar professor:", err);
          showToast("Erro de conexão ao salvar.");
        });
      });

      modalContainer.classList.remove("hidden");
    }

    document.getElementById("btn-new-teacher").addEventListener("click", () => openFormModal());

    window.openEdit = function(id) {
      const t = teachers.find(item => item.id == id);
      if (t) openFormModal(t);
    };

    // EXCLUSÃO DE PROFESSORES
    window.openDelete = async function(id) {
      const professor = teachers.find(item => item.id == id);

      if (!professor) {
        showToast("Professor não encontrado na lista.");
        return;
      }

      const confirmar = confirm(
        `Tem certeza de que deseja excluir o cadastro de ${professor.name}?\n\nEssa ação não poderá ser desfeita.`
      );

      if (!confirmar) return;

      try {
        const formData = new FormData();
        formData.append("id", id);

        const resposta = await fetch("excluir_professor.php", {
          method: "POST",
          body: formData,
          credentials: "same-origin",
          cache: "no-store"
        });

        const resultado = await resposta.json();

        if (resposta.ok && resultado.sucesso) {
          showToast(resultado.mensagem || "Professor excluído com sucesso!");
          carregarProfessores();
        } else {
          showToast(resultado.mensagem || "Não foi possível excluir o professor.");
        }

      } catch (erro) {
        console.error("Erro ao excluir professor:", erro);
        showToast("Erro de comunicação com o servidor ao excluir.");
      }
    };
  </script>
</body>
</html>
