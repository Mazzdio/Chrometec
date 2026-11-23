<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Chromebooks - Aluno | ceep.plg</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    :root { color-scheme: dark; }
    body { background-color: #09090b; color: #f4f4f5; font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; }
    .form-input {
      display: flex;
      height: 48px;
      width: 100%;
      align-items: center;
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background-color: #131318;
      padding: 0 16px;
      font-size: 14px;
      color: #ffffff;
      outline: none;
      transition: all 0.2s;
    }
    .form-input:focus {
      border-color: rgba(139, 92, 246, 0.6);
      box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.1);
    }
    select.form-input { appearance: none; }
  </style>
</head>
<body class="min-h-screen bg-[#09090b] text-zinc-100 selection:bg-violet-500/30 flex flex-col">

  <!-- Header Superior -->
  <header class="sticky top-0 z-40 border-b border-white/[.06] bg-[#09090b]/85 backdrop-blur-xl">
    <div class="mx-auto flex h-[72px] max-w-[1500px] items-center justify-between px-5 sm:px-8">
      <div class="flex items-center gap-4">
        <button type="button" onclick="window.location.href='acessoaluno.html'" class="flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-white transition cursor-pointer">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
          Voltar ao Portal
        </button>
        <span class="h-4 w-px bg-white/10"></span>
        <span class="text-sm font-bold tracking-tight text-white">ceep.plg · Chromebooks</span>
      </div>

      <div class="flex items-center gap-4">
        <button id="open-reservations-btn" class="flex items-center gap-2 rounded-xl border border-violet-400/20 bg-violet-500/10 px-3.5 py-2 text-xs font-semibold text-violet-200 transition-colors hover:bg-violet-500/20 cursor-pointer">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
          <span>Minhas reservas</span>
          <span id="reservation-badge" class="hidden grid h-5 min-w-5 place-items-center rounded-md bg-violet-500 px-1 text-[10px] text-white">0</span>
        </button>
        <div class="flex items-center gap-3 pl-4 border-l border-white/8">
          <div id="aluno-avatar" class="grid size-9 place-items-center rounded-xl bg-violet-600 text-xs font-bold text-white">AL</div>
          <div class="hidden sm:block text-left leading-tight">
            <p id="aluno-nome" class="text-sm font-semibold text-white">Carregando...</p>
            <p class="text-xs text-zinc-400">Estudante</p>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="flex-1">
    <!-- Dashboard / Métricas -->
    <section class="relative overflow-hidden border-b border-white/[.05]">
      <div class="pointer-events-none absolute left-1/2 top-0 h-80 w-[700px] -translate-x-1/2 bg-violet-700/[.08] blur-[100px]"></div>
      <div class="relative mx-auto max-w-[1500px] px-5 py-11 sm:px-8 sm:py-14">
        <div class="mb-8 flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
          <div>
            <div class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[.22em] text-violet-400">
              <span class="h-px w-7 bg-violet-500"></span> Reserva de Equipamentos
            </div>
            <h1 class="max-w-3xl text-4xl font-semibold leading-[1.05] tracking-[-.045em] text-white sm:text-5xl">
              Chromebooks, <span class="text-zinc-500">disponíveis para aula.</span>
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-6 text-zinc-500">
              Consulte a disponibilidade dos carrinhos e solicite seu empréstimo integrado ao banco de dados.
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
          <article class="relative overflow-hidden rounded-2xl border border-white/[.07] bg-[#131318] p-5">
            <div class="relative flex items-start justify-between">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-zinc-500">Total</p>
                <p id="count-total" class="mt-3 text-4xl font-semibold tracking-[-.05em] text-white">0</p>
                <p class="mt-1 text-xs text-zinc-500">equipamentos</p>
              </div>
            </div>
          </article>
          <article class="relative overflow-hidden rounded-2xl border border-white/[.07] bg-[#131318] p-5">
            <div class="relative flex items-start justify-between">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-zinc-500">Disponíveis</p>
                <p id="count-available" class="mt-3 text-4xl font-semibold tracking-[-.05em] text-white">0</p>
                <p id="detail-available" class="mt-1 text-xs text-zinc-500">Livres para uso</p>
              </div>
            </div>
          </article>
          <article class="relative overflow-hidden rounded-2xl border border-white/[.07] bg-[#131318] p-5">
            <div class="relative flex items-start justify-between">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-zinc-500">Em uso</p>
                <p id="count-inuse" class="mt-3 text-4xl font-semibold tracking-[-.05em] text-white">0</p>
                <p class="mt-1 text-xs text-zinc-500">emprestados</p>
              </div>
            </div>
          </article>
          <article class="relative overflow-hidden rounded-2xl border border-white/[.07] bg-[#131318] p-5">
            <div class="relative flex items-start justify-between">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[.16em] text-zinc-500">Indisponíveis</p>
                <p id="count-unavailable" class="mt-3 text-4xl font-semibold tracking-[-.05em] text-white">0</p>
                <p class="mt-1 text-xs text-zinc-500">manutenção</p>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Inventário -->
    <section id="inventario" class="mx-auto max-w-[1500px] px-5 py-10 sm:px-8 sm:py-12">
      <div class="mb-7 flex flex-col justify-between gap-5 xl:flex-row xl:items-end">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[.2em] text-violet-400">Inventário</p>
          <h2 class="mt-2 text-2xl font-semibold tracking-[-.03em] text-white">Escolha um Chromebook</h2>
        </div>
        <div class="flex flex-col gap-3 lg:flex-row">
          <label class="relative block min-w-0 sm:min-w-[280px]">
            <span class="sr-only">Buscar por identificação</span>
            <input id="search-input" placeholder="Buscar Chromebook..." class="h-11 w-full rounded-xl border border-white/[.08] bg-[#131318] pl-4 pr-10 text-sm text-white outline-none transition focus:border-violet-500/60 placeholder:text-zinc-600">
          </label>
        </div>
      </div>

      <!-- Grade de Chromebooks -->
      <div id="devices-grid" class="grid grid-cols-1 gap-3 min-[500px]:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5"></div>
    </section>
  </main>

  <!-- Modal de Empréstimo -->
  <div id="reservation-modal" class="fixed inset-0 z-50 hidden place-items-end bg-black/75 p-0 backdrop-blur-sm sm:place-items-center sm:p-5">
    <div class="w-full max-w-md rounded-t-3xl border border-white/10 bg-[#15151b] p-6 shadow-2xl sm:rounded-3xl sm:p-7">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[.2em] text-violet-400">Solicitar equipamento</p>
          <h2 id="modal-device-name" class="mt-2 text-2xl font-semibold text-white">Chromebook 01</h2>
        </div>
        <button id="close-modal-btn" class="rounded-lg border border-white/[.07] p-2 text-zinc-500 hover:text-white cursor-pointer">✕</button>
      </div>
      <form id="reservation-form" class="space-y-4 mt-6">
        <label class="block">
          <span class="mb-1.5 block text-[11px] font-medium text-zinc-400">Estudante (Sessão Ativa)</span>
          <input id="form-student-name" name="student" readonly class="form-input opacity-75 cursor-not-allowed">
        </label>
        <label class="block">
          <span class="mb-1.5 block text-[11px] font-medium text-zinc-400">Período de uso</span>
          <select name="periodo" required class="form-input">
            <option value="">Selecione um período</option>
            <option>1 aula</option>
            <option>2 aulas</option>
            <option>Período da manhã</option>
            <option>Período da tarde</option>
          </select>
        </label>
        <button type="submit" class="mt-2 flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-violet-600 text-sm font-semibold text-white transition hover:bg-violet-500 cursor-pointer">
          Confirmar empréstimo
        </button>
      </form>
    </div>
  </div>

  <!-- Drawer de Minhas Reservas -->
  <div id="reservations-drawer" class="fixed inset-0 z-50 hidden justify-end bg-black/70 backdrop-blur-sm">
    <div id="drawer-backdrop" class="flex-1"></div>
    <aside class="h-full w-full max-w-md border-l border-white/10 bg-[#111115] p-6 shadow-2xl sm:p-8 overflow-y-auto">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[.2em] text-violet-400">Área do estudante</p>
          <h2 class="mt-2 text-2xl font-semibold text-white">Minhas reservas</h2>
        </div>
        <button id="close-drawer-btn" class="rounded-lg border border-white/[.07] p-2 text-zinc-500 hover:text-white cursor-pointer">✕</button>
      </div>
      <div id="reservations-list" class="mt-8 space-y-3"></div>
    </aside>
  </div>

  <!-- Toast -->
  <div id="toast" class="fixed bottom-5 left-1/2 z-[60] hidden -translate-x-1/2 items-center gap-3 rounded-xl border border-emerald-400/20 bg-[#17201c] px-4 py-3 text-xs font-medium text-emerald-200 shadow-2xl">
    <span id="toast-text"></span>
  </div>

  <script>
    let inventory = [];
    let searchQuery = "";
    let selectedDevice = null;
    let alunoLogado = null;

    const statusConfig = {
      available: { label: "Disponível", dot: "bg-emerald-400", text: "text-emerald-300", panel: "border-emerald-400/20 bg-emerald-400/10" },
      "in-use": { label: "Em uso", dot: "bg-amber-400", text: "text-amber-300", panel: "border-amber-400/20 bg-amber-400/10" },
      unavailable: { label: "Indisponível", dot: "bg-rose-500", text: "text-rose-300", panel: "border-rose-400/20 bg-rose-400/10" }
    };

    const devicesGrid = document.getElementById("devices-grid");
    const searchInput = document.getElementById("search-input");
    const reservationModal = document.getElementById("reservation-modal");
    const closeModalBtn = document.getElementById("close-modal-btn");
    const modalDeviceName = document.getElementById("modal-device-name");
    const reservationForm = document.getElementById("reservation-form");

    const reservationsDrawer = document.getElementById("reservations-drawer");
    const openReservationsBtn = document.getElementById("open-reservations-btn");
    const closeDrawerBtn = document.getElementById("close-drawer-btn");
    const drawerBackdrop = document.getElementById("drawer-backdrop");
    const reservationsList = document.getElementById("reservations-list");
    const reservationBadge = document.getElementById("reservation-badge");
    const toast = document.getElementById("toast");
    const toastText = document.getElementById("toast-text");

    function showToast(msg) {
      toastText.textContent = msg;
      toast.classList.remove("hidden");
      toast.classList.add("flex");
      setTimeout(() => {
        toast.classList.add("hidden");
        toast.classList.remove("flex");
      }, 3500);
    }

    document.addEventListener("DOMContentLoaded", async () => {
      await carregarInventarioDoBanco();
      // Atualiza a disponibilidade sem recarregar a página.
      setInterval(() => {
        if (!document.hidden) carregarInventarioDoBanco(true);
      }, 10000);
    });

    function atualizarIdentificacaoAluno(usuario) {
      alunoLogado = usuario || null;
      const nome = alunoLogado && alunoLogado.nome ? alunoLogado.nome.trim() : "Estudante";
      document.getElementById("aluno-nome").textContent = nome;
      document.getElementById("form-student-name").value = nome;
      const partes = nome.split(/\s+/).filter(Boolean);
      let iniciais = partes.length ? partes[0][0] : "A";
      if (partes.length > 1) iniciais += partes[partes.length - 1][0];
      document.getElementById("aluno-avatar").textContent = iniciais.toUpperCase();
    }

    async function carregarInventarioDoBanco(silencioso = false) {
      try {
        const res = await fetch("api_chromebooks.php", { credentials: "same-origin", cache: "no-store" });
        const resultado = await res.json();
        if (res.status === 401) {
          alert(resultado.mensagem || "Entre novamente no portal para continuar.");
          window.location.href = "login_aluno.html";
          return;
        }
        if (!resultado.sucesso) throw new Error(resultado.mensagem || "Não foi possível carregar o inventário.");
        atualizarIdentificacaoAluno(resultado.usuario);
        if (!alunoLogado || alunoLogado.status !== "Aluno") {
          alert("Esta área é exclusiva para estudantes.");
          window.location.href = "acessoaluno.html";
          return;
        }
        inventory = resultado.dados || [];
        render();
        if (!reservationsDrawer.classList.contains("hidden")) renderReservationsList();
      } catch (err) {
        console.error("Erro ao carregar inventário:", err);
        if (!silencioso) alert(err.message || "Erro ao conectar com o servidor.");
      }
    }

    function render() {
      const total = inventory.length;
      const available = inventory.filter(d => d.status === "available").length;
      const inUse = inventory.filter(d => d.status === "in-use").length;
      const unavailable = inventory.filter(d => d.status === "unavailable").length;

      document.getElementById("count-total").textContent = total;
      document.getElementById("count-available").textContent = available;
      document.getElementById("count-inuse").textContent = inUse;
      document.getElementById("count-unavailable").textContent = unavailable;

      const meusEmprestimos = inventory.filter(d => d.status === "in-use" && Number(d.id_usuario) === Number(alunoLogado?.id_usuario));
      if (meusEmprestimos.length > 0) {
        reservationBadge.textContent = meusEmprestimos.length;
        reservationBadge.classList.remove("hidden");
        reservationBadge.classList.add("grid");
      } else {
        reservationBadge.classList.add("hidden");
        reservationBadge.classList.remove("grid");
      }

      const normalizedQuery = searchQuery.trim().toLowerCase().replace(/^0+/, "");
      const filtered = inventory.filter(dev => {
        return !normalizedQuery || dev.name.toLowerCase().includes(normalizedQuery) || String(dev.id).includes(normalizedQuery);
      });

      devicesGrid.innerHTML = filtered.map(dev => {
        const info = statusConfig[dev.status];
        return `
          <article class="group rounded-2xl border border-white/[.07] bg-[#121217] p-4 transition-all duration-300 hover:border-violet-400/25">
            <div class="relative overflow-hidden rounded-xl bg-black/20 pt-2 h-28 flex items-center justify-center">
              <span class="absolute left-2 top-2 text-[10px] font-mono text-zinc-500">#${String(dev.id).padStart(3, "0")}</span>
              <div class="text-xs font-bold text-violet-300 tracking-wider">CEEP CHROMEBOOK</div>
            </div>
            <div class="mt-3 flex items-start justify-between gap-3">
              <div>
                <h3 class="text-sm font-semibold text-zinc-100">${dev.name}</h3>
                <p class="mt-0.5 text-[10px] text-zinc-500">Patrimônio ${dev.patrimonio}</p>
              </div>
              <span class="rounded-full border px-2 py-1 text-[9px] font-semibold ${info.panel} ${info.text}">${info.label}</span>
            </div>
            ${dev.status === "available" ? `
              <button onclick="openModal(${dev.id})" class="mt-4 flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-violet-600 text-xs font-semibold text-white transition hover:bg-violet-500 cursor-pointer">
                Pedir empréstimo
              </button>
            ` : `
              <div class="mt-4 flex h-10 items-center justify-center rounded-xl border border-white/[.05] bg-black/10 text-[11px] text-zinc-600">
                ${dev.status === "in-use" ? "Em uso" : "Indisponível"}
              </div>
            `}
          </article>
        `;
      }).join("");
    }

    function renderReservationsList() {
      const meusEmprestimos = inventory.filter(d =>
        Number(d.id_usuario) === Number(alunoLogado?.id_usuario) &&
        d.status === "in-use" &&
        ["Ativo", "Devolução solicitada"].includes(d.status_emprestimo)
      );
      if (meusEmprestimos.length === 0) {
        reservationsList.innerHTML = `<p class="text-xs text-zinc-500 text-center py-10">Nenhum empréstimo ativo no momento.</p>`;
        return;
      }
      reservationsList.innerHTML = meusEmprestimos.map(res => {
        const solicitada = res.status_emprestimo === "Devolução solicitada";
        return `
          <article class="rounded-2xl border border-white/[.07] bg-white/[.025] p-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-sm font-semibold text-white">${res.name}</p>
                <p class="mt-1 text-[11px] text-zinc-400">Patrimônio: ${res.patrimonio}</p>
              </div>
              <span class="rounded-full border ${solicitada ? 'border-violet-400/20 bg-violet-400/10 text-violet-300' : 'border-amber-400/15 bg-amber-400/10 text-amber-300'} px-2 py-1 text-[9px] font-semibold">
                ${solicitada ? 'Devolução solicitada' : 'Em uso'}
              </span>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-white/[.06] pt-3 text-xs">
              <span class="text-zinc-400">${solicitada ? 'Aguardando análise do administrador' : 'Empréstimo ativo'}</span>
              ${solicitada ? '' : `<button onclick="devolverEquipamento(${res.id})" class="rounded-xl bg-violet-600 px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-500 cursor-pointer">Solicitar devolução</button>`}
            </div>
          </article>
        `;
      }).join("");
    }

    window.devolverEquipamento = async function(idChromebook) {
      if (!confirm("Enviar uma solicitação de devolução ao administrador? O Chromebook continuará em uso até a confirmação.")) return;
      try {
        const res = await fetch("api_solicitar_devolucao.php", {
          method: "POST",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id_chromebook: idChromebook })
        });
        const data = await res.json();
        if (!res.ok || !data.sucesso) {
          alert(data.mensagem || "Não foi possível solicitar a devolução.");
          return;
        }
        showToast("Solicitação enviada ao administrador!");
        await carregarInventarioDoBanco();
        renderReservationsList();
      } catch (err) {
        console.error("Erro ao solicitar devolução:", err);
        alert("Erro de conexão com o servidor.");
      }
    };

    window.openModal = function(id) {
      selectedDevice = inventory.find(d => d.id === id);
      modalDeviceName.textContent = selectedDevice.name;
      reservationModal.classList.remove("hidden");
      reservationModal.classList.add("grid");
    };

    closeModalBtn.addEventListener("click", () => {
      reservationModal.classList.add("hidden");
      reservationModal.classList.remove("grid");
    });

    reservationForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (!selectedDevice) return;

      if (!alunoLogado || !alunoLogado.id_usuario) {
        alert("Não foi possível identificar sua sessão. Saia e entre novamente no portal.");
        return;
      }

      const periodo = reservationForm.querySelector("[name='periodo']").value;
      try {
        const res = await fetch("api_chromebooks.php", {
          method: "POST",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            acao: "solicitar_emprestimo",
            id_chromebook: selectedDevice.id,
            periodo: periodo
          })
        });
        const data = await res.json();

        if (data.sucesso) {
          reservationModal.classList.add("hidden");
          reservationModal.classList.remove("grid");
          reservationForm.reset();
          if (alunoLogado) document.getElementById("form-student-name").value = alunoLogado.nome;
          showToast(`${selectedDevice.name} reservado com sucesso!`);
          await carregarInventarioDoBanco();
          renderReservationsList();
        } else {
          alert(data.mensagem || "Erro ao realizar empréstimo.");
        }
      } catch (err) {
        console.error("Erro:", err);
        alert("Erro de conexão com o servidor.");
      }
    });

    openReservationsBtn.addEventListener("click", () => {
      renderReservationsList();
      reservationsDrawer.classList.remove("hidden");
      reservationsDrawer.classList.add("flex");
    });

    closeDrawerBtn.addEventListener("click", () => {
      reservationsDrawer.classList.add("hidden");
      reservationsDrawer.classList.remove("flex");
    });

    drawerBackdrop.addEventListener("click", () => {
      reservationsDrawer.classList.add("hidden");
      reservationsDrawer.classList.remove("flex");
    });

    searchInput.addEventListener("input", (e) => {
      searchQuery = e.target.value;
      render();
    });
  </script>
</body>
</html>