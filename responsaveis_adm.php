
<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Administrador');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão Acadêmica - Administradores | CEEP.PLG</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    :root { color-scheme: dark; }
    body {
      background-color: #09090b;
      color: #f4f4f5;
      font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
    }
  </style>
</head>

<body class="min-h-screen bg-[#09090b] text-zinc-100 flex flex-col">

<header class="sticky top-0 z-30 flex min-h-[72px] items-center justify-between border-b border-white/8 bg-[#09090b]/95 px-6 backdrop-blur">
  <div class="flex items-center gap-8">
    <strong class="text-xl font-bold tracking-tight text-white">ceep.plg</strong>

    <nav class="hidden md:flex items-center gap-1 text-sm font-medium" aria-label="Navegação principal">
      <button onclick="window.location.href='emprestimo_adm.html'"
        class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">
        Empréstimos
      </button>

      <button onclick="window.location.href='alunos_adm.html'"
        class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">
        Alunos
      </button>

      <button onclick="window.location.href='professores_adm.html'"
        class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">
        Professores
      </button>

      <button class="px-3 py-2 rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-950/20 cursor-pointer">
        Administradores
      </button>
    </nav>
  </div>

  <div class="flex items-center gap-4">
    <button onclick="window.location.href='emprestimo_adm.html'"
      class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-3.5 py-2 text-xs font-semibold text-zinc-300 hover:bg-white/10 hover:text-white transition cursor-pointer">
      <svg class="size-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      <span class="hidden sm:inline">Painel Geral</span>
    </button>

    <div class="flex items-center gap-3 pl-4 border-l border-white/8">
      <div id="adm-avatar" class="grid size-9 place-items-center rounded-xl bg-violet-600 text-xs font-bold text-white">AD</div>
      <div class="hidden sm:block text-left leading-tight">
        <p id="adm-nome" class="text-sm font-semibold text-white">Administrador</p>
        <p class="text-xs text-zinc-400">Administrador</p>
      </div>
    </div>

    <button id="adm-logout-btn"
      class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 cursor-pointer">
      <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
      </svg>
      <span class="hidden sm:inline">Sair</span>
    </button>
  </div>
</header>

<main class="flex-1 p-6 lg:p-10 max-w-7xl w-full mx-auto">

  <div class="text-xs text-zinc-400 mb-6">
    Administração <span class="text-zinc-600">/</span>
    <strong class="text-white">Administradores</strong>
  </div>

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <p class="text-xs font-bold tracking-widest text-violet-400 mb-1">GESTÃO ACADÊMICA</p>
      <h1 class="text-3xl font-bold tracking-tight text-white">Administradores</h1>
      <p class="text-sm text-zinc-400 mt-1">
        Gerencie os usuários que possuem acesso administrativo ao sistema.
      </p>
    </div>
  </div>

  <div class="rounded-2xl border border-white/8 bg-[#121215] p-6 shadow-xl">

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
      <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-zinc-400"
          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="7"/>
          <path d="m20 20-4-4"/>
        </svg>

        <input id="search-input" type="text"
          placeholder="Buscar por nome, matrícula, e-mail ou telefone..."
          class="w-full h-11 rounded-xl border border-white/10 bg-white/[0.03] pl-10 pr-10 text-sm text-white placeholder-zinc-500 outline-none focus:border-violet-500 transition">

        <button id="clear-search"
          class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-white cursor-pointer"
          aria-label="Limpar pesquisa">
          ✕
        </button>
      </div>

      <button id="refresh-btn"
        class="h-11 rounded-xl border border-white/10 bg-white/[0.03] px-4 text-sm font-semibold text-zinc-300 hover:bg-white/10 hover:text-white transition cursor-pointer">
        ↻ Atualizar lista
      </button>
    </div>

    <div class="flex items-center justify-between text-xs text-zinc-400 mb-4 pb-4 border-b border-white/6">
      <span id="results-count">Carregando administradores...</span>
      <span id="loading-indicator" class="text-violet-400 hidden">Carregando...</span>
    </div>

    <!-- Tabela para computador -->
    <div class="overflow-x-auto hidden md:block">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-white/8 text-[11px] font-bold uppercase tracking-wider text-zinc-500">
            <th class="pb-3 pr-4 font-semibold">ADMINISTRADOR</th>
            <th class="pb-3 pr-4 font-semibold">MATRÍCULA</th>
            <th class="pb-3 pr-4 font-semibold">E-MAIL</th>
            <th class="pb-3 pr-4 font-semibold">TELEFONE</th>
            <th class="pb-3 pr-4 font-semibold">STATUS</th>
            <th class="pb-3 font-semibold text-right">AÇÕES</th>
          </tr>
        </thead>
        <tbody id="admins-table-body" class="divide-y divide-white/5 text-sm">
          <tr>
            <td colspan="6" class="py-10 text-center text-zinc-500">
              Carregando administradores...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Lista para celular -->
    <div id="admins-mobile-list" class="space-y-3 md:hidden">
      <p class="py-8 text-center text-sm text-zinc-500">Carregando administradores...</p>
    </div>

    <!-- Estado vazio -->
    <div id="empty-state" class="hidden py-12 text-center">
      <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-white/5 text-zinc-400 mb-3">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="7"/>
          <path d="m20 20-4-4"/>
        </svg>
      </div>
      <h3 class="font-semibold text-white">Nenhum administrador encontrado</h3>
      <p class="text-xs text-zinc-400 mt-1">Tente alterar os termos da pesquisa.</p>
      <button id="empty-clear-btn"
        class="mt-4 rounded-xl bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/15 transition cursor-pointer">
        Limpar pesquisa
      </button>
    </div>

    <footer class="flex items-center justify-between pt-4 mt-4 border-t border-white/6 text-xs text-zinc-400">
      <span id="pagination-info">Exibindo 0 de 0</span>
      <span class="text-zinc-600">Portal CEEP</span>
    </footer>

  </div>
</main>

<!-- Modal de visualização -->
<div id="view-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
  <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#121215] p-6 shadow-2xl">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-lg font-bold text-white">Dados do administrador</h2>
      <button id="close-view-modal" class="text-zinc-400 hover:text-white text-xl cursor-pointer">✕</button>
    </div>

    <div class="space-y-4">
      <div>
        <p class="text-xs text-zinc-500 mb-1">Nome completo</p>
        <p id="view-name" class="text-sm font-semibold text-white">—</p>
      </div>
      <div>
        <p class="text-xs text-zinc-500 mb-1">Matrícula</p>
        <p id="view-registration" class="text-sm text-white">—</p>
      </div>
      <div>
        <p class="text-xs text-zinc-500 mb-1">E-mail</p>
        <p id="view-email" class="text-sm text-white break-all">—</p>
      </div>
      <div>
        <p class="text-xs text-zinc-500 mb-1">Telefone</p>
        <p id="view-phone" class="text-sm text-white">—</p>
      </div>
      <div>
        <p class="text-xs text-zinc-500 mb-1">Perfil</p>
        <p class="text-sm text-violet-400">Administrador</p>
      </div>
    </div>

    <button id="close-view-button"
      class="w-full mt-7 rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-zinc-300 hover:bg-white/10 transition cursor-pointer">
      Fechar
    </button>
  </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
  <div class="w-full max-w-md rounded-2xl border border-red-500/20 bg-[#121215] p-6 shadow-2xl">
    <div class="flex items-center gap-3 mb-5">
      <div class="grid size-11 place-items-center rounded-xl bg-red-500/10 text-red-400">
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18.5A2 2 0 0 0 3.55 21.5h16.9a2 2 0 0 0 1.73-3L13.7 3.86a2 2 0 0 0-3.46 0Z"/>
        </svg>
      </div>
      <div>
        <h2 class="text-lg font-bold text-white">Excluir administrador?</h2>
        <p class="text-xs text-zinc-400">Esta ação não poderá ser desfeita.</p>
      </div>
    </div>

    <p class="text-sm text-zinc-300 leading-relaxed mb-2">
      Você está prestes a excluir a conta de:
    </p>

    <p id="delete-admin-name" class="font-bold text-white mb-5 break-words">—</p>

    <p class="text-xs text-zinc-500 mb-6">
      O sistema impedirá a exclusão caso essa seja sua própria conta, o último administrador ou uma conta com empréstimos registrados.
    </p>

    <div class="flex gap-3">
      <button id="cancel-delete"
        class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-zinc-300 hover:bg-white/10 transition cursor-pointer">
        Cancelar
      </button>

      <button id="confirm-delete"
        class="flex-1 rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-500 transition cursor-pointer">
        Excluir conta
      </button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast"
  class="fixed bottom-6 right-6 z-[70] flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-[#17201c] px-4 py-3 text-sm font-medium text-emerald-300 shadow-xl transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">
  <span id="toast-icon" class="size-5 rounded-full bg-emerald-500/20 grid place-items-center text-xs">✓</span>
  <span id="toast-message">Operação realizada.</span>
</div>

<script>
  let admins = [];
  let filteredAdmins = [];
  let selectedAdminId = null;
  let toastTimeout = null;

  const searchInput = document.getElementById("search-input");
  const clearSearch = document.getElementById("clear-search");
  const tableBody = document.getElementById("admins-table-body");
  const mobileList = document.getElementById("admins-mobile-list");
  const resultsCount = document.getElementById("results-count");
  const paginationInfo = document.getElementById("pagination-info");
  const emptyState = document.getElementById("empty-state");
  const loadingIndicator = document.getElementById("loading-indicator");

  const viewModal = document.getElementById("view-modal");
  const deleteModal = document.getElementById("delete-modal");

  const confirmDeleteButton = document.getElementById("confirm-delete");

  function escapeHTML(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;"
    })[char]);
  }

  function showToast(message, success = true) {
    const toast = document.getElementById("toast");
    const icon = document.getElementById("toast-icon");
    const text = document.getElementById("toast-message");

    text.textContent = message;
    icon.textContent = success ? "✓" : "!";
    icon.className = success
      ? "size-5 rounded-full bg-emerald-500/20 grid place-items-center text-xs"
      : "size-5 rounded-full bg-red-500/20 grid place-items-center text-xs";

    toast.classList.toggle("border-emerald-500/20", success);
    toast.classList.toggle("bg-[#17201c]", success);
    toast.classList.toggle("text-emerald-300", success);

    toast.classList.toggle("border-red-500/20", !success);
    toast.classList.toggle("bg-[#211617]", !success);
    toast.classList.toggle("text-red-300", !success);

    toast.classList.remove("translate-y-20", "opacity-0");

    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      toast.classList.add("translate-y-20", "opacity-0");
    }, 3500);
  }

  function obterNome(admin) {
    return admin.name || admin.nome_responsavel || admin.nome || "Nome não informado";
  }

  function obterMatricula(admin) {
    return admin.registration || admin.matricula || "Não informada";
  }

  function obterEmail(admin) {
    return admin.email || "E-mail não cadastrado";
  }

  function obterTelefone(admin) {
    return admin.phone || admin.telefone || "Telefone não informado";
  }

  async function carregarResponsaveis() {
    loadingIndicator.classList.remove("hidden");

    try {
      const response = await fetch("buscar_responsaveis.php", {
        method: "GET",
        cache: "no-store",
        credentials: "same-origin"
      });

      const data = await response.json();

      if (response.status === 401 || response.status === 403) {
        showToast(data.mensagem || "Acesso não autorizado.", false);
        return;
      }

      if (!response.ok || data.sucesso === false) {
        throw new Error(data.mensagem || "Não foi possível carregar os administradores.");
      }

      admins = Array.isArray(data.responsaveis)
        ? data.responsaveis
        : Array.isArray(data.administradores)
          ? data.administradores
          : [];

      renderizarAdministradores();

    } catch (error) {
      console.error("Erro ao carregar administradores:", error);

      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="py-10 text-center text-red-400">
            ${escapeHTML(error.message)}
          </td>
        </tr>`;

      mobileList.innerHTML = `
        <p class="py-8 text-center text-sm text-red-400">
          ${escapeHTML(error.message)}
        </p>`;

      resultsCount.textContent = "Erro ao carregar dados";
      paginationInfo.textContent = "Não foi possível carregar a lista.";

    } finally {
      loadingIndicator.classList.add("hidden");
    }
  }

  function renderizarAdministradores() {
    const termo = searchInput.value.trim().toLowerCase();

    filteredAdmins = admins.filter(admin => {
      const dados = [
        obterNome(admin),
        obterMatricula(admin),
        obterEmail(admin),
        obterTelefone(admin)
      ].join(" ").toLowerCase();

      return dados.includes(termo);
    });

    resultsCount.textContent =
      `${filteredAdmins.length} administrador(es) encontrado(s)`;

    paginationInfo.textContent =
      `Exibindo ${filteredAdmins.length} de ${admins.length}`;

    const vazio = filteredAdmins.length === 0;

    emptyState.classList.toggle("hidden", !vazio);
    tableBody.parentElement.parentElement.classList.toggle("hidden", vazio);
    mobileList.classList.toggle("hidden", vazio);

    if (vazio) {
      tableBody.innerHTML = "";
      mobileList.innerHTML = "";
      return;
    }

    tableBody.innerHTML = filteredAdmins.map(admin => {
      const id = Number(admin.id);
      const nome = escapeHTML(obterNome(admin));
      const matricula = escapeHTML(obterMatricula(admin));
      const email = escapeHTML(obterEmail(admin));
      const telefone = escapeHTML(obterTelefone(admin));

      return `
        <tr class="hover:bg-white/[0.02] transition">
          <td class="py-4 pr-4">
            <div class="flex items-center gap-3">
              <div class="grid size-9 shrink-0 place-items-center rounded-xl bg-violet-500/10 text-xs font-bold text-violet-300">
                ${escapeHTML(iniciais(obterNome(admin)))}
              </div>
              <span class="font-semibold text-white">${nome}</span>
            </div>
          </td>

          <td class="py-4 pr-4 text-zinc-300">${matricula}</td>
          <td class="py-4 pr-4 text-zinc-400">${email}</td>
          <td class="py-4 pr-4 text-zinc-400">${telefone}</td>

          <td class="py-4 pr-4">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">
              <span class="size-1.5 rounded-full bg-emerald-400"></span>
              Administrador
            </span>
          </td>

          <td class="py-4">
            <div class="flex items-center justify-end gap-1">
              <button onclick="openView(${id})"
                class="rounded-lg p-2 text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer"
                title="Visualizar dados">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>

              <button onclick="openDelete(${id})"
                class="rounded-lg p-2 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition cursor-pointer"
                title="Excluir administrador">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 11v5m4-5v5"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join("");

    mobileList.innerHTML = filteredAdmins.map(admin => {
      const id = Number(admin.id);

      return `
        <article class="rounded-xl border border-white/8 bg-white/[0.02] p-4 space-y-3">
          <div class="flex items-start gap-3">
            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-500/10 text-xs font-bold text-violet-300">
              ${escapeHTML(iniciais(obterNome(admin)))}
            </div>

            <div class="min-w-0 flex-1">
              <strong class="block text-white break-words">${escapeHTML(obterNome(admin))}</strong>
              <span class="text-xs text-violet-400 font-medium">Administrador</span>
            </div>

            <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-[10px] font-semibold text-emerald-400">
              Ativo
            </span>
          </div>

          <div class="space-y-2 border-t border-white/5 pt-3 text-xs">
            <div class="flex justify-between gap-3">
              <span class="text-zinc-500">Matrícula</span>
              <strong class="text-zinc-300">${escapeHTML(obterMatricula(admin))}</strong>
            </div>

            <div class="flex justify-between gap-3">
              <span class="text-zinc-500">E-mail</span>
              <span class="text-zinc-300 text-right break-all">${escapeHTML(obterEmail(admin))}</span>
            </div>

            <div class="flex justify-between gap-3">
              <span class="text-zinc-500">Telefone</span>
              <span class="text-zinc-300">${escapeHTML(obterTelefone(admin))}</span>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-white/5 pt-3">
            <button onclick="openView(${id})"
              class="rounded-lg bg-white/5 px-3 py-2 text-xs font-semibold text-zinc-300 hover:bg-white/10 cursor-pointer">
              Ver dados
            </button>

            <button onclick="openDelete(${id})"
              class="rounded-lg bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-400 hover:bg-red-500/20 cursor-pointer">
              Excluir
            </button>
          </div>
        </article>
      `;
    }).join("");
  }

  function iniciais(nome) {
    const partes = String(nome || "").trim().split(/\s+/).filter(Boolean);

    if (!partes.length) return "AD";

    let resultado = partes[0][0];

    if (partes.length > 1) {
      resultado += partes[partes.length - 1][0];
    }

    return resultado.toUpperCase();
  }

  window.openView = function(id) {
    const admin = admins.find(item => Number(item.id) === Number(id));

    if (!admin) {
      showToast("Administrador não encontrado.", false);
      return;
    }

    document.getElementById("view-name").textContent = obterNome(admin);
    document.getElementById("view-registration").textContent = obterMatricula(admin);
    document.getElementById("view-email").textContent = obterEmail(admin);
    document.getElementById("view-phone").textContent = obterTelefone(admin);

    viewModal.classList.remove("hidden");
    viewModal.classList.add("flex");
  };

  window.openDelete = function(id) {
    const admin = admins.find(item => Number(item.id) === Number(id));

    if (!admin) {
      showToast("Administrador não encontrado.", false);
      return;
    }

    selectedAdminId = Number(id);

    document.getElementById("delete-admin-name").textContent = obterNome(admin);

    deleteModal.classList.remove("hidden");
    deleteModal.classList.add("flex");
  };

  function fecharModal(modal) {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  }

  async function excluirAdministrador() {
    if (!selectedAdminId) {
      showToast("Nenhum administrador selecionado.", false);
      return;
    }

    confirmDeleteButton.disabled = true;
    confirmDeleteButton.textContent = "Excluindo...";
    confirmDeleteButton.classList.add("opacity-50", "cursor-not-allowed");

    try {
      const formData = new FormData();
      formData.append("id", String(selectedAdminId));

      const response = await fetch("excluir_responsavel.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin",
        cache: "no-store"
      });

      const data = await response.json();

      if (!response.ok || !data.sucesso) {
        throw new Error(data.mensagem || "Não foi possível excluir o administrador.");
      }

      fecharModal(deleteModal);
      selectedAdminId = null;

      showToast(data.mensagem || "Administrador excluído com sucesso.");

      await carregarResponsaveis();

    } catch (error) {
      console.error("Erro ao excluir administrador:", error);
      showToast(error.message || "Erro ao conectar com o servidor.", false);

    } finally {
      confirmDeleteButton.disabled = false;
      confirmDeleteButton.textContent = "Excluir conta";
      confirmDeleteButton.classList.remove("opacity-50", "cursor-not-allowed");
    }
  }

  searchInput.addEventListener("input", () => {
    clearSearch.classList.toggle("hidden", !searchInput.value);
    renderizarAdministradores();
  });

  clearSearch.addEventListener("click", () => {
    searchInput.value = "";
    clearSearch.classList.add("hidden");
    renderizarAdministradores();
  });

  document.getElementById("empty-clear-btn").addEventListener("click", () => {
    searchInput.value = "";
    clearSearch.classList.add("hidden");
    renderizarAdministradores();
  });

  document.getElementById("refresh-btn").addEventListener("click", carregarResponsaveis);

  confirmDeleteButton.addEventListener("click", excluirAdministrador);

  document.getElementById("cancel-delete").addEventListener("click", () => {
    fecharModal(deleteModal);
    selectedAdminId = null;
  });

  document.getElementById("close-view-modal").addEventListener("click", () => fecharModal(viewModal));
  document.getElementById("close-view-button").addEventListener("click", () => fecharModal(viewModal));

  viewModal.addEventListener("click", event => {
    if (event.target === viewModal) fecharModal(viewModal);
  });

  deleteModal.addEventListener("click", event => {
    if (event.target === deleteModal) {
      fecharModal(deleteModal);
      selectedAdminId = null;
    }
  });

  document.addEventListener("keydown", event => {
    if (event.key === "Escape") {
      fecharModal(viewModal);
      fecharModal(deleteModal);
      selectedAdminId = null;
    }
  });

  document.addEventListener("DOMContentLoaded", () => {
    try {
      const dadosSalvos = localStorage.getItem("adm_logado");

      if (dadosSalvos) {
        const admLogado = JSON.parse(dadosSalvos);

        if (admLogado && admLogado.nome) {
          const nome = admLogado.nome.trim();

          document.getElementById("adm-nome").textContent = nome;
          document.getElementById("adm-avatar").textContent = iniciais(nome);
        }
      }
    } catch (error) {
      console.error("Erro ao carregar identificação:", error);
    }

    carregarResponsaveis();

    document.getElementById("adm-logout-btn").addEventListener("click", async () => {
      try {
        localStorage.removeItem("adm_logado");
        await fetch("logout.php", {
          method: "GET",
          cache: "no-store",
          credentials: "same-origin"
        });
      } catch (error) {
        console.error("Erro ao encerrar sessão:", error);
      }

      window.location.replace("login_adm.html");
    });
  });
</script>

</body>
</html>
