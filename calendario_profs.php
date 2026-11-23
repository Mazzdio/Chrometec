<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Professor');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendário Escolar - CEEP</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script>
  (function () {
    document.documentElement.style.visibility = 'hidden';
    const loginDestino = "login_professor.html";
    async function conferirSessao() {
      try {
        const resposta = await fetch('validar_sessao.php', { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } });
        const dados = await resposta.json();
        if (!resposta.ok || !dados.autenticado || dados.perfil !== 'Professor') {
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
<body class="min-h-screen bg-[#101114] text-slate-100 selection:bg-violet-600 selection:text-white pb-20 lg:pb-0">

  <div class="flex min-h-screen">
    <!-- Sidebar Desktop -->
    <aside class="hidden min-h-screen w-64 shrink-0 flex-col border-r border-white/8 bg-[#101114] px-5 py-7 text-white lg:flex">
      <div class="flex items-center gap-3 px-2">
        <div class="grid size-10 place-items-center rounded-xl bg-violet-600 shadow-lg shadow-violet-950/40">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
        </div>
        <div>
          <div class="text-sm font-semibold tracking-wide text-white">CEEP</div>
          <div class="text-xs text-slate-400">Portal Educacional</div>
        </div>
      </div>

      <div class="mt-10 px-3 text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-500">
        Menu principal
      </div>
      <nav class="mt-3 space-y-1.5">
        <a href="acessoprofessor.php" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition text-slate-400 hover:bg-white/5 hover:text-white">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Visão geral
        </a>
        <a href="turmas_profs.php" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition text-slate-400 hover:bg-white/5 hover:text-white">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Minhas turmas
        </a>
        <a href="emprestimos_profs.html" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition text-slate-400 hover:bg-white/5 hover:text-white">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="11" rx="2"/><path d="M2 19h20M8 19v1h8v-1"/></svg>
          Chromebooks
        </a>
        <a href="calendario_profs.php" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-semibold transition bg-violet-600 text-white shadow-lg shadow-violet-950/30">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
          Calendário
        </a>
      </nav>

      <div class="mt-auto rounded-2xl border border-violet-500/20 bg-violet-500/10 p-4">
        <div class="flex items-center gap-3">
          <div id="sidebar-avatar" class="grid size-10 place-items-center rounded-full bg-violet-500 text-sm font-bold text-white">PR</div>
          <div class="min-w-0">
            <div id="sidebar-name" class="truncate text-sm font-semibold text-white">Carregando...</div>
            <div class="text-xs text-slate-400">Professor</div>
          </div>
        </div>
        <button type="button" class="btn-logout mt-4 flex w-full items-center gap-2 border-t border-white/10 pt-3 text-xs font-medium text-slate-400 transition hover:text-white cursor-pointer">
          <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5M15 12H3M21 19V5a2 2 0 0 0-2-2h-6"/></svg>
          Sair da conta
        </button>
      </div>
    </aside>

    <div class="min-w-0 flex-1">
      <!-- Header -->
      <header class="flex items-center justify-between border-b border-white/8 bg-[#101114]/95 px-5 py-4 backdrop-blur md:px-8 lg:px-10">
        <div class="text-sm font-medium text-slate-400">Calendário escolar</div>
        <div class="flex items-center gap-3">
          <div class="hidden text-right sm:block">
            <div id="header-name" class="text-sm font-semibold text-white">Carregando...</div>
            <div class="text-xs text-slate-400">Professor</div>
          </div>
          <div id="header-avatar" class="grid size-10 place-items-center rounded-xl border border-violet-400/20 bg-violet-500/15 text-xs font-bold text-violet-300">PR</div>
        </div>
      </header>

      <!-- Main -->
      <main class="mx-auto max-w-screen-2xl px-5 pt-7 pb-24 md:px-8 md:pt-9 lg:px-10 lg:pb-10">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
          <div>
            <div class="text-xs font-bold uppercase tracking-[0.2em] text-violet-400">Agenda acadêmica</div>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Calendário escolar</h1>
            <p class="mt-1 text-sm text-slate-400">Acompanhe reuniões, avaliações, aulas e eventos da escola.</p>
          </div>

          <div class="flex items-center gap-2">
            <button id="btn-mes-anterior" type="button" class="grid size-10 place-items-center rounded-xl border border-white/10 bg-[#17181c] text-slate-300 transition hover:border-violet-500/40 hover:text-white cursor-pointer" aria-label="Mês anterior">
              <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <div id="mes-ano-label" class="min-w-40 rounded-xl border border-white/10 bg-[#17181c] px-4 py-2.5 text-center text-sm font-bold text-white capitalize">
              —
            </div>
            <button id="btn-mes-proximo" type="button" class="grid size-10 place-items-center rounded-xl border border-white/10 bg-[#17181c] text-slate-300 transition hover:border-violet-500/40 hover:text-white cursor-pointer" aria-label="Próximo mês">
              <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"/></svg>
            </button>
          </div>
        </div>

        <div class="mt-7 grid gap-7 xl:grid-cols-[minmax(0,1.4fr)_24rem]">
          <!-- Grade mensal -->
          <section class="rounded-3xl border border-white/10 bg-[#17181c] p-5 md:p-6">
            <div class="grid grid-cols-7 gap-2 text-center text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">
              <div>Dom</div><div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sáb</div>
            </div>
            <div id="calendar-days-grid" class="mt-3 grid grid-cols-7 gap-2"></div>
          </section>

          <!-- Painel lateral: Eventos do dia + Novo evento -->
          <aside class="space-y-6">
            <section class="rounded-3xl border border-white/10 bg-[#17181c] p-5 md:p-6">
              <div class="text-xs font-bold uppercase tracking-widest text-violet-400">Eventos do dia</div>
              <h2 id="dia-selecionado-titulo" class="mt-1 text-lg font-bold text-white">Selecione um dia</h2>
              <div id="lista-eventos-dia" class="mt-4 space-y-3 text-sm text-slate-400">
                Carregando...
              </div>
            </section>

            <section class="rounded-3xl border border-white/10 bg-[#17181c] p-5 md:p-6">
              <div class="text-xs font-bold uppercase tracking-widest text-violet-400">Novo registro</div>
              <h2 class="mt-1 text-lg font-bold text-white">Adicionar evento</h2>

              <form id="form-evento" class="mt-4 space-y-3">
                <div>
                  <label class="block text-xs font-medium text-slate-400 mb-1">Título</label>
                  <input name="titulo" required placeholder="Ex.: Entrega de projeto" class="w-full rounded-xl border border-white/10 bg-[#101114] px-3.5 py-2.5 text-sm text-white outline-none focus:border-violet-500" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Data</label>
                    <input id="input-data-evento" type="date" name="data_evento" required class="w-full rounded-xl border border-white/10 bg-[#101114] px-3 py-2.5 text-sm text-white outline-none focus:border-violet-500" />
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Hora</label>
                    <input type="time" name="hora_evento" class="w-full rounded-xl border border-white/10 bg-[#101114] px-3 py-2.5 text-sm text-white outline-none focus:border-violet-500" />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Categoria</label>
                    <select name="categoria" class="w-full rounded-xl border border-white/10 bg-[#101114] px-3 py-2.5 text-sm text-white outline-none focus:border-violet-500">
                      <option value="Escolar">Escolar</option>
                      <option value="Aula">Aula</option>
                      <option value="Avaliacao">Avaliação</option>
                      <option value="Reserva">Reserva</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Local</label>
                    <input name="local_evento" placeholder="Ex.: Lab 02" class="w-full rounded-xl border border-white/10 bg-[#101114] px-3 py-2.5 text-sm text-white outline-none focus:border-violet-500" />
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-medium text-slate-400 mb-1">Turma (opcional)</label>
                  <select name="turma_alvo" class="w-full rounded-xl border border-white/10 bg-[#101114] px-3 py-2.5 text-sm text-white outline-none focus:border-violet-500">
                    <option value="">Geral / Todas</option>
                    <option value="Todos os 1º Anos">Todos os 1º Anos</option>
                    <option value="Todos os 2º Anos">Todos os 2º Anos</option>
                    <option value="Todos os 3º Anos">Todos os 3º Anos</option>
                    <option value="1º Ano A — Informática">1º Ano A — Informática</option>
                    <option value="1º Ano B — Informática">1º Ano B — Informática</option>
                    <option value="1º Ano A — Redes">1º Ano A — Redes</option>
                    <option value="1º Ano B — Redes">1º Ano B — Redes</option>
                    <option value="2º Ano A — Informática">2º Ano A — Informática</option>
                    <option value="2º Ano B — Informática">2º Ano B — Informática</option>
                    <option value="2º Ano A — Redes">2º Ano A — Redes</option>
                    <option value="2º Ano B — Redes">2º Ano B — Redes</option>
                    <option value="3º Ano A — Informática">3º Ano A — Informática</option>
                    <option value="3º Ano B — Informática">3º Ano B — Informática</option>
                    <option value="3º Ano A — Redes">3º Ano A — Redes</option>
                    <option value="3º Ano B — Redes">3º Ano B — Redes</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-slate-400 mb-1">Observações</label>
                  <textarea name="descricao" rows="2" placeholder="Detalhes adicionais..." class="w-full rounded-xl border border-white/10 bg-[#101114] px-3.5 py-2 text-sm text-white outline-none focus:border-violet-500"></textarea>
                </div>

                <button type="submit" class="w-full rounded-xl bg-violet-600 py-3 text-xs font-bold text-white transition hover:bg-violet-500 cursor-pointer">
                  Salvar evento
                </button>
              </form>
            </section>
          </aside>
        </div>
      </main>
    </div>
  </div>

  <!-- Barra Mobile -->
  <nav class="fixed inset-x-3 bottom-3 z-20 grid grid-cols-4 rounded-2xl border border-white/10 bg-[#101114] p-1.5 shadow-2xl shadow-black/50 lg:hidden">
    <a href="acessoprofessor.php" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold text-slate-400 hover:text-white">
      <span>Visão</span>
    </a>
    <a href="turmas_profs.php" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold text-slate-400 hover:text-white">
      <span>Turmas</span>
    </a>
    <a href="emprestimos_profs.html" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold text-slate-400 hover:text-white">
      <span>Books</span>
    </a>
    <a href="calendario_profs.php" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold bg-violet-600 text-white">
      <span>Agenda</span>
    </a>
  </nav>

  <script>
    const hoje = new Date();
    const paramsUrl = new URLSearchParams(window.location.search);
    const dataParam = (paramsUrl.get("data") || "").trim();

    let anoAtual = hoje.getFullYear();
    let mesAtual = hoje.getMonth() + 1;
    let diaSelecionadoIso = `${anoAtual}-${String(mesAtual).padStart(2, "0")}-${String(hoje.getDate()).padStart(2, "0")}`;

    if (/^\d{4}-\d{2}-\d{2}$/.test(dataParam)) {
      const [py, pm] = dataParam.split("-").map(Number);
      anoAtual = py;
      mesAtual = pm;
      diaSelecionadoIso = dataParam;
    }

    let eventosMes = [];

    const mesAnoLabel = document.getElementById("mes-ano-label");
    const gridDias = document.getElementById("calendar-days-grid");
    const diaTitulo = document.getElementById("dia-selecionado-titulo");
    const listaEventosDia = document.getElementById("lista-eventos-dia");
    const formEvento = document.getElementById("form-evento");
    const inputDataEvento = document.getElementById("input-data-evento");

    document.addEventListener("DOMContentLoaded", () => {
      try {
        const prof = JSON.parse(localStorage.getItem("professor_logado") || "null");
        if (prof && prof.nome) {
          const nome = prof.nome.trim();
          document.getElementById("sidebar-name").textContent = nome;
          document.getElementById("header-name").textContent = nome;
          const partes = nome.split(/\s+/);
          const ini = (partes[0][0] + (partes.length > 1 ? partes[partes.length - 1][0] : "")).toUpperCase();
          document.getElementById("sidebar-avatar").textContent = ini;
          document.getElementById("header-avatar").textContent = ini;
        }
      } catch {}

      document.querySelectorAll(".btn-logout").forEach(btn => {
        btn.addEventListener("click", async () => {
          localStorage.removeItem("professor_logado");
          try { await fetch("logout.php?ajax=1", { cache: "no-store", credentials: "same-origin" }); } catch {}
          window.location.replace("login_professor.html");
        });
      });

      inputDataEvento.value = diaSelecionadoIso;
      carregarEventos();
    });

    function formatarIso(ano, mes, dia) {
      return `${ano}-${String(mes).padStart(2, "0")}-${String(dia).padStart(2, "0")}`;
    }

    function renderCalendario() {
      const dataRef = new Date(anoAtual, mesAtual - 1, 1);
      mesAnoLabel.textContent = dataRef.toLocaleDateString("pt-BR", { month: "long", year: "numeric" });

      const primeiroDiaSemana = dataRef.getDay();
      const totalDiasMes = new Date(anoAtual, mesAtual, 0).getDate();

      const porDia = new Map();
      eventosMes.forEach(ev => {
        if (!porDia.has(ev.data_evento)) porDia.set(ev.data_evento, []);
        porDia.get(ev.data_evento).push(ev);
      });

      let html = "";
      for (let i = 0; i < primeiroDiaSemana; i++) {
        html += `<div class="h-20 rounded-xl bg-white/[0.01]"></div>`;
      }

      for (let dia = 1; dia <= totalDiasMes; dia++) {
        const iso = formatarIso(anoAtual, mesAtual, dia);
        const selecionada = iso === diaSelecionadoIso;
        const evs = porDia.get(iso) || [];

        html += `
          <button type="button" onclick="selecionarDia('${iso}')" class="flex h-20 flex-col justify-between rounded-xl border p-2 text-left transition cursor-pointer ${
            selecionada
              ? "border-violet-500 bg-violet-600/20 text-white"
              : "border-white/8 bg-[#101114] text-slate-300 hover:border-violet-500/30 hover:bg-white/[0.03]"
          }">
            <span class="text-xs font-bold">${dia}</span>
            <div class="space-y-1 overflow-hidden">
              ${evs.slice(0, 2).map(e => `
                <div class="truncate rounded bg-violet-500/20 px-1.5 py-0.5 text-[0.62rem] font-semibold text-violet-200">
                  ${e.hora_evento ? e.hora_evento + " · " : ""}${e.titulo}
                </div>
              `).join("")}
              ${evs.length > 2 ? `<div class="text-[0.6rem] text-slate-400">+${evs.length - 2} mais</div>` : ""}
            </div>
          </button>
        `;
      }

      gridDias.innerHTML = html;
      renderEventosDoDia();
    }

    function renderEventosDoDia() {
      const [y, m, d] = diaSelecionadoIso.split("-").map(Number);
      const dt = new Date(y, m - 1, d);
      diaTitulo.textContent = dt.toLocaleDateString("pt-BR", { weekday: "long", day: "2-digit", month: "long" });

      const doDia = eventosMes.filter(e => e.data_evento === diaSelecionadoIso);
      if (doDia.length === 0) {
        listaEventosDia.innerHTML = `<div class="rounded-xl border border-dashed border-white/10 p-4 text-center text-xs text-slate-500">Nenhum evento cadastrado neste dia.</div>`;
        return;
      }

      listaEventosDia.innerHTML = doDia.map(ev => {
        const autorTexto = ev.autor_nome ? `Por ${ev.autor_nome}` : "Por Coordenação / Sistema";
        return `
        <div class="rounded-2xl border border-white/8 bg-[#101114] p-3.5">
          <div class="flex items-start justify-between gap-2">
            <div>
              <div class="flex flex-wrap items-center gap-2">
                <span class="inline-block rounded-md bg-violet-500/15 px-2 py-0.5 text-[0.65rem] font-bold text-violet-300">${ev.categoria}</span>
                <span class="text-[0.68rem] font-medium text-slate-400">• ${autorTexto}</span>
              </div>
              <div class="mt-1.5 text-sm font-bold text-white">${ev.titulo}</div>
            </div>
            <button type="button" onclick="excluirEvento(${ev.id_evento})" class="rounded-lg px-2 py-1 text-xs text-rose-400 hover:bg-rose-500/10 cursor-pointer">Excluir</button>
          </div>
          ${(ev.hora_evento || ev.local_evento || ev.turma_alvo) ? `
            <div class="mt-2 flex flex-wrap gap-2 text-[0.7rem] text-slate-400">
              ${ev.hora_evento ? `<span>${ev.hora_evento}</span>` : ""}
              ${ev.local_evento ? `<span>• ${ev.local_evento}</span>` : ""}
              ${ev.turma_alvo ? `<span>• ${ev.turma_alvo}</span>` : ""}
            </div>
          ` : ""}
          ${ev.descricao ? `<p class="mt-2 text-xs text-slate-400">${ev.descricao}</p>` : ""}
        </div>
      `;
      }).join("");
    }

    window.selecionarDia = function(iso) {
      diaSelecionadoIso = iso;
      inputDataEvento.value = iso;
      renderCalendario();
    };

    window.excluirEvento = async function(idEvento) {
      try {
        const res = await fetch("api_calendario.php", {
          method: "POST",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ acao: "excluir", id_evento: idEvento })
        });
        const data = await res.json();
        if (data.sucesso) {
          await carregarEventos();
        }
      } catch (e) {
        console.error(e);
      }
    };

    async function carregarEventos() {
      try {
        const res = await fetch(`api_calendario.php?mes=${mesAtual}&ano=${anoAtual}`, {
          cache: "no-store",
          credentials: "same-origin"
        });
        const data = await res.json();
        eventosMes = data.sucesso && Array.isArray(data.eventos) ? data.eventos : [];
        renderCalendario();
      } catch (e) {
        console.error(e);
        listaEventosDia.innerHTML = `<div class="text-xs text-rose-400">Erro ao carregar eventos.</div>`;
      }
    }

    document.getElementById("btn-mes-anterior").addEventListener("click", () => {
      mesAtual--;
      if (mesAtual < 1) { mesAtual = 12; anoAtual--; }
      diaSelecionadoIso = formatarIso(anoAtual, mesAtual, 1);
      inputDataEvento.value = diaSelecionadoIso;
      carregarEventos();
    });

    document.getElementById("btn-mes-proximo").addEventListener("click", () => {
      mesAtual++;
      if (mesAtual > 12) { mesAtual = 1; anoAtual++; }
      diaSelecionadoIso = formatarIso(anoAtual, mesAtual, 1);
      inputDataEvento.value = diaSelecionadoIso;
      carregarEventos();
    });

    formEvento.addEventListener("submit", async (e) => {
      e.preventDefault();
      const fd = new FormData(formEvento);
      const payload = {
        acao: "criar",
        titulo: String(fd.get("titulo") || "").trim(),
        data_evento: String(fd.get("data_evento") || "").trim(),
        hora_evento: String(fd.get("hora_evento") || "").trim(),
        categoria: String(fd.get("categoria") || "Escolar").trim(),
        local_evento: String(fd.get("local_evento") || "").trim(),
        turma_alvo: String(fd.get("turma_alvo") || "").trim(),
        descricao: String(fd.get("descricao") || "").trim()
      };

      try {
        const res = await fetch("api_calendario.php", {
          method: "POST",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.sucesso) {
          formEvento.reset();
          diaSelecionadoIso = payload.data_evento;
          inputDataEvento.value = diaSelecionadoIso;
          const [y, m] = payload.data_evento.split("-").map(Number);
          anoAtual = y;
          mesAtual = m;
          await carregarEventos();
        }
      } catch (err) {
        console.error(err);
      }
    });
  </script>
</body>
</html>