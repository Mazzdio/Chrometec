<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Geral | ceep.plg</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style>
    :root { color-scheme: dark; }
    body { background-color: #09090b; color: #f4f4f5; font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-[#09090b] text-zinc-100 flex flex-col selection:bg-violet-600 selection:text-white">

  <!-- Header Superior com Navegação Horizontal -->
  <header class="sticky top-0 z-30 flex h-[72px] items-center justify-between border-b border-white/8 bg-[#09090b]/95 px-6 backdrop-blur">
    <div class="flex items-center gap-8">
      <strong class="text-xl font-bold tracking-tight text-white">ceep.plg</strong>
      
      <!-- Menu Principal no Topo -->
      <nav class="hidden md:flex items-center gap-1 text-sm font-medium" aria-label="Navegação principal">
        <button type="button" class="px-3 py-2 rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-950/20 flex items-center gap-2 cursor-pointer">Empréstimos</button>
        <button type="button" onclick="window.location.href='alunos_adm.html'" class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">Alunos</button>
        <button type="button" onclick="window.location.href='professores_adm.html'" class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">Professores</button>
        <button type="button" onclick="window.location.href='responsaveis_adm.html'" class="px-3 py-2 rounded-xl text-zinc-400 hover:bg-white/5 hover:text-white transition cursor-pointer">Responsáveis</button>
      </nav>
    </div>

    <div class="flex items-center gap-4">
      <button class="relative rounded-xl p-2 text-zinc-400 hover:bg-white/5 hover:text-white cursor-pointer" aria-label="Notificações">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
        <span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-violet-500"></span>
      </button>

      <div class="flex items-center gap-3 pl-4 border-l border-white/8">
        <div id="adm-avatar" class="grid size-9 place-items-center rounded-xl bg-violet-600 text-xs font-bold text-white">AD</div>
        <div class="hidden sm:block text-left leading-tight">
          <p id="adm-nome" class="text-sm font-semibold text-white">Carregando...</p>
          <p id="adm-cargo" class="text-xs text-zinc-400">Administrador</p>
        </div>
      </div>

      <!-- Botão Sair do Administrador -->
      <button
        id="adm-logout-btn"
        title="Encerrar sessão"
        class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 cursor-pointer"
      >
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        <span class="hidden sm:inline">Sair</span>
      </button>
    </div>
  </header>

  <!-- Conteúdo Principal -->
  <main class="flex-1 p-6 lg:p-10 max-w-7xl w-full mx-auto">
    <section class="mb-8 rounded-2xl border border-violet-400/20 bg-[#121215] p-5 sm:p-6">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-xs font-bold tracking-widest text-violet-400">ANÁLISE DO ADMINISTRADOR</p>
          <h2 class="mt-1 text-xl font-bold text-white">Solicitações de devolução</h2>
          <p class="mt-1 text-sm text-zinc-400">Confirme a devolução somente depois de receber fisicamente o Chromebook.</p>
        </div>
        <button id="atualizar-solicitacoes" type="button" class="rounded-xl border border-white/10 px-4 py-2 text-xs font-semibold text-zinc-200 hover:bg-white/5">Atualizar lista</button>
      </div>
      <div id="lista-solicitacoes-devolucao" class="mt-5 space-y-3">
        <p class="py-6 text-center text-xs text-zinc-500">Carregando solicitações...</p>
      </div>
    </section>
    <!-- Título da página e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
      <div>
        <p class="text-xs font-bold tracking-widest text-violet-400 mb-1">MONITORAMENTO</p>
        <h1 class="text-3xl font-bold tracking-tight text-white">Painel Geral</h1>
        <p class="text-sm text-zinc-400 mt-1">Monitore a ocupação de recursos por setor e a escala mensal de devoluções.</p>
      </div>
      <div class="flex items-center gap-3 flex-shrink-0">
        <button class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-200 border border-white/10 bg-white/[0.03] rounded-xl hover:border-violet-500/40 hover:bg-violet-500/10 transition cursor-pointer">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Exportar Relatório
        </button>
        <button onclick="window.location.href='inventario_adm.html'" class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-violet-600 rounded-xl shadow-lg shadow-violet-950/40 hover:bg-violet-500 transition cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Chromebooks
        </button>
      </div>
    </div>

    <!-- Cards de KPI -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div class="bg-[#121215] rounded-2xl border border-white/8 p-5 shadow-lg shadow-black/20">
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs text-zinc-400 font-medium leading-tight">Total de Empréstimos Ativos</p>
          <div class="p-2 rounded-xl bg-violet-500/10 text-violet-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
        <p id="kpi-total-ativos" class="text-3xl font-bold text-white tracking-tight">...</p>
        <p class="text-xs mt-1.5 font-medium text-emerald-400">Dados do Banco de Dados</p>
      </div>

      <div class="bg-[#121215] rounded-2xl border border-white/8 p-5 shadow-lg shadow-black/20">
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs text-zinc-400 font-medium leading-tight">Retirados de Hoje</p>
          <div class="p-2 rounded-xl bg-amber-400/10 text-amber-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
              <path stroke-linecap="round" stroke-width="1.8" d="M12 6v6l4 2"/>
            </svg>
          </div>
        </div>
        <p id="kpi-retirados-hoje" class="text-3xl font-bold text-white tracking-tight">...</p>
        <span class="inline-block text-[10px] font-semibold px-2.5 py-1 rounded-full mt-2 bg-amber-400/10 text-amber-300 border border-amber-400/20">Em uso</span>
      </div>

      <div class="bg-[#121215] rounded-2xl border border-white/8 p-5 shadow-lg shadow-black/20">
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs text-zinc-400 font-medium leading-tight">Pendente de Devolução</p>
          <div class="p-2 rounded-xl bg-rose-400/10 text-rose-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
        </div>
        <p id="kpi-pendentes" class="text-3xl font-bold text-white tracking-tight">...</p>
        <span class="inline-block text-[10px] font-semibold px-2.5 py-1 rounded-full mt-2 bg-rose-400/10 text-rose-400 border border-rose-400/20">Atenção</span>
      </div>

      <div class="bg-[#121215] rounded-2xl border border-white/8 p-5 shadow-lg shadow-black/20">
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs text-zinc-400 font-medium leading-tight">Cota Total Ocupada</p>
          <div class="p-2 rounded-xl bg-violet-500/10 text-violet-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
        <p id="kpi-cota" class="text-3xl font-bold text-white tracking-tight">...</p>
        <p class="text-xs mt-1.5 font-medium text-violet-400">Inventário Geral</p>
      </div>
    </div>

    <!-- Setores + Agenda Mensal -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
      <!-- Tabela de Setores Dinâmica -->
      <div class="flex-1 bg-[#121215] rounded-2xl border border-white/8 overflow-hidden w-full shadow-lg shadow-black/20">
        <div class="px-6 py-5 border-b border-white/8 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-white">Setores e Cotas</span>
            <span class="text-[10px] font-semibold bg-violet-500/15 text-violet-300 border border-violet-400/20 px-2.5 py-0.5 rounded-full">Ativos</span>
          </div>
        </div>
        <div id="setores-container" class="divide-y divide-white/5">
          <!-- Preenchido via JS -->
          <div class="px-6 py-4 text-center text-xs text-zinc-500">Carregando setores...</div>
        </div>
      </div>

      <!-- Componente Agenda Mensal -->
      <div class="bg-[#121215] rounded-2xl border border-white/8 p-5 flex flex-col gap-4 items-center justify-start w-full lg:w-[320px] shrink-0 shadow-lg shadow-black/20">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
              <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.8"/>
              <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.8"/>
              <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.8"/>
            </svg>
            <span class="text-sm font-bold text-white">Agenda Mensal</span>
          </div>
          <div class="flex items-center gap-1">
            <button id="prev-mes-btn" type="button" class="p-1.5 rounded-lg hover:bg-white/5 text-zinc-400 text-sm cursor-pointer transition">&lt;</button>
            <span id="mes-ano-label" class="text-xs font-semibold text-zinc-200 whitespace-nowrap px-1">Carregando...</span>
            <button id="next-mes-btn" type="button" class="p-1.5 rounded-lg hover:bg-white/5 text-zinc-400 text-sm cursor-pointer transition">&gt;</button>
          </div>
        </div>

        <div class="w-full">
          <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-2">
            <div>Dom</div><div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sáb</div>
          </div>
          <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>
        </div>

        <div class="border-t border-white/8 pt-4 w-full">
          <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider mb-2.5">Eventos do Dia Selecionado</p>
          <div id="eventos-container" class="space-y-2">
            <p class="text-xs text-zinc-500 italic">Carregando eventos...</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    const MESES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    const hojeRef = new Date();
    let mesSelecionado = hojeRef.getMonth(); // 0-indexed
    let anoSelecionado = hojeRef.getFullYear();
    let diaSelecionado = hojeRef.getDate();
    let eventosMes = [];
    let ultimoSnapshotAgenda = "";

    const calendarGrid = document.getElementById("calendar-grid");
    const mesAnoLabel = document.getElementById("mes-ano-label");
    const eventosContainer = document.getElementById("eventos-container");
    const prevMesBtn = document.getElementById("prev-mes-btn");
    const nextMesBtn = document.getElementById("next-mes-btn");

    function isoData(ano, mesZeroIndexed, dia) {
      return `${ano}-${String(mesZeroIndexed + 1).padStart(2, "0")}-${String(dia).padStart(2, "0")}`;
    }

    function escapeHtml(valor) {
      return String(valor ?? "").replace(/[&<>"']/g, caractere => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[caractere]));
    }

    document.addEventListener("DOMContentLoaded", () => {
      const elAvatar = document.getElementById("adm-avatar");
      const elNome = document.getElementById("adm-nome");
      const elCargo = document.getElementById("adm-cargo");
      const admLogoutBtn = document.getElementById("adm-logout-btn");

      if (admLogoutBtn) {
        admLogoutBtn.addEventListener("click", async () => {
          try {
            localStorage.removeItem("adm_logado");
            await fetch("logout.php", { method: "GET", cache: "no-store" });
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

      carregarDadosPainel();
      carregarSolicitacoesDevolucao();
      carregarEventosAgenda(true);

      // Atualiza solicitações, indicadores e agenda automaticamente, sem F5.
      setInterval(() => {
        if (!document.hidden) {
          carregarSolicitacoesDevolucao(true);
          carregarDadosPainel();
          carregarEventosAgenda(false);
        }
      }, 10000);

      document.addEventListener("visibilitychange", () => {
        if (!document.hidden) {
          carregarSolicitacoesDevolucao(true);
          carregarDadosPainel();
          carregarEventosAgenda(false);
        }
      });

      document.getElementById("atualizar-solicitacoes")?.addEventListener("click", () => carregarSolicitacoesDevolucao(false));

      async function carregarSolicitacoesDevolucao(silencioso = false) {
        const container = document.getElementById("lista-solicitacoes-devolucao");
        if (!container) return;
        if (!silencioso && !container.dataset.carregado) {
          container.innerHTML = `<p class="py-6 text-center text-xs text-zinc-500">Carregando solicitações...</p>`;
        }
        try {
          const resposta = await fetch("api_solicitacoes_devolucao.php", { credentials: "same-origin", cache: "no-store" });
          const dados = await resposta.json();
          if (!resposta.ok || !dados.sucesso) throw new Error(dados.mensagem || "Não foi possível carregar as solicitações.");
          container.dataset.carregado = "1";
          if (!dados.solicitacoes.length) {
            container.innerHTML = `<p class="py-6 text-center text-xs text-zinc-500">Nenhuma solicitação de devolução pendente.</p>`;
            return;
          }
          container.innerHTML = dados.solicitacoes.map(item => `
            <article class="rounded-xl border border-white/[.07] bg-white/[.025] p-4">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-white">${escapeHtml(item.aluno)}</p>
                  <p class="mt-1 text-xs text-zinc-400">${escapeHtml(item.patrimonio)} · ${escapeHtml(item.modelo)}</p>
                  <p class="mt-1 text-[11px] text-zinc-500">Solicitado em: ${escapeHtml(item.data_emprestimo_formatada)}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <button type="button" data-id="${item.id_emprestimo}" data-acao="aceitar" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Aceitar devolução</button>
                  <button type="button" data-id="${item.id_emprestimo}" data-acao="recusar" class="rounded-lg border border-rose-400/20 bg-rose-400/10 px-3 py-2 text-xs font-semibold text-rose-300 hover:bg-rose-400/20">Recusar</button>
                </div>
              </div>
            </article>
          `).join("");
          container.querySelectorAll("button[data-acao]").forEach(botao => {
            botao.addEventListener("click", () => responderSolicitacao(botao.dataset.id, botao.dataset.acao));
          });
        } catch (erro) {
          console.error("Erro ao atualizar solicitações:", erro);
          if (!silencioso) container.innerHTML = `<p class="py-6 text-center text-xs text-rose-300">${escapeHtml(erro.message)}</p>`;
        }
      }

      async function responderSolicitacao(idEmprestimo, acao) {
        const aceitar = acao === "aceitar";
        const pergunta = aceitar
          ? "Confirmar que o Chromebook foi recebido e concluir a devolução?"
          : "Recusar a solicitação? O empréstimo continuará ativo.";
        if (!confirm(pergunta)) return;
        try {
          const resposta = await fetch("api_responder_devolucao.php", {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_emprestimo: Number(idEmprestimo), acao })
          });
          const dados = await resposta.json();
          if (!resposta.ok || !dados.sucesso) throw new Error(dados.mensagem || "Não foi possível atualizar a solicitação.");
          alert(dados.mensagem);
          await carregarSolicitacoesDevolucao();
          carregarDadosPainel();
        } catch (erro) {
          alert(erro.message || "Erro de conexão com o servidor.");
        }
      }
    });

    // Buscar dados reais do Banco de Dados via API
    function carregarDadosPainel() {
      fetch("api_painel_geral.php", { credentials: "same-origin", cache: "no-store" })
        .then(res => res.json())
        .then(data => {
          if (data.sucesso) {
            const d = data.dados;
            document.getElementById("kpi-total-ativos").textContent = d.total_ativos;
            document.getElementById("kpi-retirados-hoje").textContent = d.retirados_hoje;
            document.getElementById("kpi-pendentes").textContent = d.pendentes;
            document.getElementById("kpi-cota").textContent = d.cota_percentual + "%";

            // Renderizar Setores
            const containerSetores = document.getElementById("setores-container");
            containerSetores.innerHTML = "";

            if (d.setores.length === 0) {
              containerSetores.innerHTML = `<div class="px-6 py-4 text-center text-xs text-zinc-500">Nenhum setor cadastrado.</div>`;
              return;
            }

            d.setores.forEach(setor => {
              const corBarra = setor.porcentagem >= 100 ? "bg-rose-500" : setor.porcentagem > 50 ? "bg-amber-400" : "bg-emerald-400";
              const corDot = setor.porcentagem >= 100 ? "bg-rose-500 ring-rose-500/10" : "bg-emerald-400 ring-emerald-400/10";
              const badgeCls = setor.porcentagem >= 100 ? "bg-rose-400/10 text-rose-400 border-rose-400/20" : "bg-emerald-400/10 text-emerald-400 border-emerald-400/20";
              const statusTexto = setor.porcentagem >= 100 ? "Esgotado" : "Disponível";

              containerSetores.innerHTML += `
                <div class="px-6 py-4 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
                  <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 ${corDot} ring-4"></span>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">${escapeHtml(setor.nome)}</p>
                    <p class="text-xs text-zinc-500">Cota de empréstimos</p>
                  </div>
                  <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="flex-1 h-2 bg-white/5 rounded-full overflow-hidden">
                      <div class="h-full rounded-full transition-all ${corBarra}" style="width: ${setor.porcentagem}%"></div>
                    </div>
                    <span class="text-xs text-zinc-400 whitespace-nowrap">${setor.porcentagem}% utilizada</span>
                  </div>
                  <span class="text-xs font-mono font-semibold text-zinc-300 whitespace-nowrap">${setor.utilizados}/${setor.capacidade}</span>
                  <span class="text-[11px] font-semibold px-3 py-1 rounded-full border ${badgeCls}">${statusTexto}</span>
                </div>
              `;
            });
          }
        })
        .catch(err => console.error("Erro ao carregar painel geral:", err));
    }

    // Sincronização da Agenda Mensal com api_calendario.php
    async function carregarEventosAgenda(forcarRender = false) {
      try {
        const mesApi = mesSelecionado + 1;
        const res = await fetch(`api_calendario.php?mes=${mesApi}&ano=${anoSelecionado}`, {
          credentials: "same-origin",
          cache: "no-store"
        });
        if (!res.ok) return;
        const data = await res.json();
        if (!data.sucesso || !Array.isArray(data.eventos)) return;

        const snap = JSON.stringify(data.eventos);
        if (!forcarRender && snap === ultimoSnapshotAgenda) return;
        ultimoSnapshotAgenda = snap;

        eventosMes = data.eventos;
        renderCalendar();
      } catch (err) {
        console.error("Erro ao carregar agenda mensal:", err);
      }
    }

    function renderCalendar() {
      mesAnoLabel.textContent = `${MESES[mesSelecionado]} ${anoSelecionado}`;
      calendarGrid.innerHTML = "";

      const primeiroDia = new Date(anoSelecionado, mesSelecionado, 1).getDay();
      const totalDias = new Date(anoSelecionado, mesSelecionado + 1, 0).getDate();
      if (diaSelecionado > totalDias) diaSelecionado = totalDias;

      const diasComEvento = new Set(eventosMes.map(ev => ev.data_evento));

      const cells = Array(primeiroDia).fill(null);
      for (let d = 1; d <= totalDias; d++) cells.push(d);
      while (cells.length % 7 !== 0) cells.push(null);

      cells.forEach((d) => {
        const btn = document.createElement("button");
        btn.type = "button";
        const isoAtual = d !== null ? isoData(anoSelecionado, mesSelecionado, d) : null;
        const temEvento = isoAtual !== null && diasComEvento.has(isoAtual);
        const eSelecionado = d === diaSelecionado;

        let cls = "text-center text-xs py-2 rounded-xl transition-all relative w-full font-medium ";

        if (d === null) {
          cls += "cursor-default text-transparent";
          btn.disabled = true;
          btn.innerHTML = "&nbsp;";
        } else {
          cls += "cursor-pointer ";
          if (eSelecionado) {
            cls += "bg-violet-600 text-white font-bold shadow-md shadow-violet-950/40 ";
          } else {
            cls += "hover:bg-white/5 text-zinc-300 ";
          }
          btn.textContent = d;
          btn.addEventListener("click", () => {
            diaSelecionado = d;
            renderCalendar();
          });
        }

        btn.className = cls;

        if (temEvento && !eSelecionado) {
          const dot = document.createElement("span");
          dot.className = "absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-violet-400";
          btn.appendChild(dot);
        }

        calendarGrid.appendChild(btn);
      });

      renderEventos();
    }

    function renderEventos() {
      const isoSel = isoData(anoSelecionado, mesSelecionado, diaSelecionado);
      const eventos = eventosMes.filter(ev => ev.data_evento === isoSel);
      eventosContainer.innerHTML = "";

      if (eventos.length === 0) {
        eventosContainer.innerHTML = `<p class="text-xs text-zinc-500 italic">Nenhum evento para este dia.</p>`;
      } else {
        eventos.forEach(ev => {
          const detalhes = [
            ev.turma_alvo,
            ev.local_evento,
            ev.autor_nome ? `Por ${ev.autor_nome}` : null
          ].filter(Boolean).join(" • ");

          eventosContainer.innerHTML += `
            <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5">
              <div class="flex items-center justify-between text-xs gap-2">
                <span class="flex items-center gap-2 text-zinc-200 font-medium min-w-0">
                  <span class="w-1.5 h-1.5 rounded-full bg-violet-400 flex-shrink-0"></span>
                  <span class="truncate">${escapeHtml(ev.titulo)}</span>
                </span>
                <span class="text-zinc-500 font-mono text-[11px] whitespace-nowrap">${escapeHtml(ev.hora_evento || "")}</span>
              </div>
              ${detalhes ? `<p class="text-[11px] text-zinc-500 mt-1 pl-3.5 truncate">${escapeHtml(detalhes)}</p>` : ""}
            </div>
          `;
        });
      }
    }

    prevMesBtn.addEventListener("click", () => {
      if (mesSelecionado === 0) {
        mesSelecionado = 11;
        anoSelecionado--;
      } else {
        mesSelecionado--;
      }
      diaSelecionado = 1;
      ultimoSnapshotAgenda = "";
      renderCalendar();
      carregarEventosAgenda(true);
    });

    nextMesBtn.addEventListener("click", () => {
      if (mesSelecionado === 11) {
        mesSelecionado = 0;
        anoSelecionado++;
      } else {
        mesSelecionado++;
      }
      diaSelecionado = 1;
      ultimoSnapshotAgenda = "";
      renderCalendar();
      carregarEventosAgenda(true);
    });

    renderCalendar();
    carregarEventosAgenda(true);
  </script>
</body>
</html>