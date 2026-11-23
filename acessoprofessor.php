<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Professor');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal do Professor - CEEP</title>
  <!-- Tailwind CSS via CDN -->
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
          <!-- Book Icon -->
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
        <button type="button" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-semibold transition bg-violet-600 text-white shadow-lg shadow-violet-950/30 cursor-pointer">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Visão geral
        </button>
        <a href="turmas_profs.php" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition text-slate-400 hover:bg-white/5 hover:text-white cursor-pointer">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Minhas turmas
        </a>
        <a href="emprestimos_profs.html" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition text-slate-400 hover:bg-white/5 hover:text-white cursor-pointer">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="11" rx="2"/><path d="M2 19h20M8 19v1h8v-1"/></svg>
          Chromebooks
        </a>
        <a href="calendario_profs.php" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm transition text-slate-400 hover:bg-white/5 hover:text-white cursor-pointer">
          <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
          Calendário
        </a>
      </nav>

      <div class="mt-auto rounded-2xl border border-violet-500/20 bg-violet-500/10 p-4">
        <div class="flex items-center gap-3">
          <div id="sidebar-avatar" class="grid size-10 place-items-center rounded-full bg-violet-500 text-sm font-bold text-white">
            PR
          </div>
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
        <div class="flex items-center gap-3 lg:hidden">
          <div class="grid size-9 place-items-center rounded-xl bg-violet-600 text-white">
            <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
          </div>
          <div class="text-sm font-bold tracking-wide text-white">CEEP</div>
        </div>
        <div class="hidden text-sm font-medium text-slate-400 lg:block">
          Portal do professor
        </div>
        <div class="flex items-center gap-3">
          <button type="button" aria-label="Notificações" class="relative grid size-10 place-items-center rounded-xl border border-white/10 text-slate-400 transition hover:bg-white/5 hover:text-white cursor-pointer">
            <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
            <span class="absolute right-2 top-2 size-2 rounded-full bg-violet-500 ring-2 ring-[#101114]"></span>
          </button>
          <div class="hidden text-right sm:block">
            <div id="header-name" class="text-sm font-semibold text-white">Carregando...</div>
            <div class="text-xs text-slate-400">Professor</div>
          </div>
          <div id="header-avatar" class="grid size-10 place-items-center rounded-xl border border-violet-400/20 bg-violet-500/15 text-xs font-bold text-violet-300">
            PR
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="mx-auto max-w-screen-2xl px-5 pt-7 pb-24 md:px-8 md:pt-9 lg:px-10 lg:pb-9">
        <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
          <div>
            <div id="current-date-label" class="text-sm font-semibold text-violet-400">Carregando data...</div>
            <div id="welcome-title" class="mt-1 text-3xl font-bold tracking-tight text-white md:text-4xl">
              Bom dia, Professor.
            </div>
            <div class="mt-2 text-sm text-slate-400">
              Aqui está o resumo das suas atividades de hoje.
            </div>
          </div>

          <!-- Seletor de Curso -->
          <div class="inline-flex self-start rounded-xl border border-white/10 bg-[#17181c] p-1 shadow-sm">
            <button type="button" data-course="Informática" class="course-btn rounded-lg px-4 py-2 text-xs font-bold transition bg-violet-600 text-white cursor-pointer">
              Informática
            </button>
            <button type="button" data-course="Redes" class="course-btn rounded-lg px-4 py-2 text-xs font-bold transition text-slate-400 hover:text-white cursor-pointer">
              Redes
            </button>
          </div>
        </div>

        <div class="mt-8 grid gap-7 xl:grid-cols-[minmax(0,1fr)_22rem]">
          <div class="min-w-0">
            <!-- Suas Turmas -->
            <section>
              <div class="mb-4 flex items-end justify-between">
                <div>
                  <div class="text-lg font-bold tracking-tight text-white">Suas turmas</div>
                  <div id="course-subtitle" class="mt-1 text-xs text-slate-400">Curso técnico em informática</div>
                </div>
                <a href="turmas_profs.php" class="text-xs font-bold text-violet-400 hover:text-violet-300 cursor-pointer">
                  Ver todas
                </a>
              </div>
              <!-- Grid de Turmas dinâmicas -->
              <div id="classes-grid" class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3">
                <!-- Preenchido via JS -->
              </div>
            </section>

            <!-- Chromebooks (Conectado ao Banco + Atualização Automática) -->
            <section class="mt-8">
              <div class="mb-4 flex items-end justify-between">
                <div>
                  <div class="flex items-center gap-2.5">
                    <div class="text-lg font-bold tracking-tight text-white">Chromebooks</div>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[0.65rem] font-semibold text-emerald-400">
                      <span class="size-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                      Ao vivo
                    </span>
                  </div>
                  <div class="mt-1 text-xs text-slate-400">Disponibilidade em tempo real</div>
                </div>
                <a href="emprestimos_profs.html" class="text-xs font-bold text-violet-400 hover:text-violet-300 cursor-pointer">
                  Gerenciar reservas
                </a>
              </div>
              <div id="chromebooks-grid" class="grid gap-3 md:grid-cols-3">
                <div class="col-span-full rounded-2xl border border-white/10 bg-[#17181c] p-6 text-center text-xs text-slate-400">
                  Carregando disponibilidade do banco de dados...
                </div>
              </div>
            </section>

            <!-- Banner Reserva -->
            <section class="mt-8 rounded-3xl border border-white/10 bg-[#17181c] p-5 text-white md:p-6">
              <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                  <div class="grid size-12 shrink-0 place-items-center rounded-2xl bg-violet-600">
                    <svg aria-hidden="true" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="11" rx="2"/><path d="M2 19h20M8 19v1h8v-1"/></svg>
                  </div>
                  <div>
                    <div class="text-sm font-bold">Precisa de Chromebooks para uma aula?</div>
                    <div class="mt-1 text-xs text-slate-400">Faça uma reserva antecipada e garanta os equipamentos.</div>
                  </div>
                </div>
                <a href="emprestimos_profs.html" class="rounded-xl bg-white px-5 py-3 text-xs font-bold text-zinc-950 transition hover:bg-violet-100 cursor-pointer text-center">
                  Nova reserva
                </a>
              </div>
            </section>
          </div>

          <!-- Coluna Direita (Calendário e Próxima Aula) -->
          <aside class="space-y-5">
            <!-- Calendário Escolar -->
            <section class="rounded-3xl border border-white/10 bg-[#17181c] p-5 shadow-sm md:p-6">
              <div class="flex items-center justify-between">
                <div>
                  <a href="calendario_profs.php" class="text-lg font-bold tracking-tight text-white hover:text-violet-300 transition">Calendário escolar</a>
                  <div id="widget-month-label" class="mt-1 text-xs text-slate-400 capitalize">Carregando...</div>
                </div>
                <div class="flex gap-2">
                  <button id="widget-prev-month" type="button" aria-label="Mês anterior" class="grid size-8 place-items-center rounded-lg border border-white/10 text-slate-400 transition hover:border-violet-500/40 hover:text-white cursor-pointer">
                    <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                  </button>
                  <button id="widget-next-month" type="button" aria-label="Próximo mês" class="grid size-8 place-items-center rounded-lg border border-white/10 text-slate-400 transition hover:border-violet-500/40 hover:text-white cursor-pointer">
                    <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                  </button>
                </div>
              </div>

              <!-- Dias da Semana -->
              <div class="mt-5 grid grid-cols-7 gap-y-2 text-center" id="calendar-grid">
                <div class="text-[0.65rem] font-bold text-slate-500">D</div>
                <div class="text-[0.65rem] font-bold text-slate-500">S</div>
                <div class="text-[0.65rem] font-bold text-slate-500">T</div>
                <div class="text-[0.65rem] font-bold text-slate-500">Q</div>
                <div class="text-[0.65rem] font-bold text-slate-500">Q</div>
                <div class="text-[0.65rem] font-bold text-slate-500">S</div>
                <div class="text-[0.65rem] font-bold text-slate-500">S</div>
              </div>

              <!-- Próximos eventos -->
              <div class="mt-5 border-t border-white/8 pt-4">
                <div class="mb-3 flex items-center justify-between">
                  <div id="widget-events-heading" class="text-[0.65rem] font-bold uppercase tracking-widest text-slate-500">Próximos eventos</div>
                  <a href="calendario_profs.php" class="text-[0.65rem] font-bold text-violet-400 hover:text-violet-300">Abrir agenda</a>
                </div>
                <div id="widget-events-list" class="space-y-3">
                  <div class="text-xs text-slate-400">Carregando eventos...</div>
                </div>
              </div>
            </section>

            <!-- Próxima Aula Card -->
            <section class="rounded-3xl bg-violet-600 p-5 text-white shadow-xl shadow-violet-950/40 md:p-6">
              <div class="flex items-start justify-between">
                <div>
                  <div class="text-[0.65rem] font-bold uppercase tracking-widest text-violet-200">Próxima aula</div>
                  <div id="next-class-title" class="mt-3 text-xl font-bold">Lógica de programação</div>
                  <div id="next-class-details" class="mt-1 text-sm text-violet-100">1º ano A • Sala 12</div>
                </div>
                <div id="next-class-time" class="rounded-xl bg-white/15 px-3 py-2 text-sm font-bold">08:00</div>
              </div>
              <button type="button" class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-[#101114] py-3 text-xs font-bold text-white transition hover:bg-black cursor-pointer">
                Abrir plano de aula
                <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              </button>
            </section>
          </aside>
        </div>
      </main>
    </div>
  </div>

  <!-- Barra de Navegação Mobile Inferior -->
  <nav class="fixed inset-x-3 bottom-3 z-20 grid grid-cols-4 rounded-2xl border border-white/10 bg-[#101114] p-1.5 shadow-2xl shadow-black/50 lg:hidden">
    <button type="button" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold transition bg-violet-600 text-white cursor-pointer">
      <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span>Visão</span>
    </button>
    <a href="turmas_profs.php" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold transition text-slate-400 hover:text-white cursor-pointer">
      <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span>Turmas</span>
    </a>
    <a href="emprestimos_profs.html" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold transition text-slate-400 hover:text-white cursor-pointer">
      <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="11" rx="2"/><path d="M2 19h20M8 19v1h8v-1"/></svg>
      <span>Books</span>
    </a>
    <a href="calendario_profs.php" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[0.6rem] font-semibold transition text-slate-400 hover:text-white cursor-pointer">
      <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
      <span>Agenda</span>
    </a>
  </nav>

  <!-- Script JavaScript para interatividade, dados do usuário e atualização automática -->
  <script>
    // Recuperar dados do usuário logado via localStorage
    document.addEventListener("DOMContentLoaded", () => {
      const agora = new Date();
      const dataFormatada = agora.toLocaleDateString("pt-BR", {
        weekday: "long",
        day: "numeric",
        month: "long"
      });
      const dateLabel = document.getElementById("current-date-label");
      if (dateLabel) {
        dateLabel.textContent = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);
      }

      try {
        const dadosSalvos = localStorage.getItem("professor_logado");
        if (dadosSalvos) {
          const professor = JSON.parse(dadosSalvos);
          if (professor && professor.nome) {
            const nomeCompleto = professor.nome.trim();

            document.getElementById("sidebar-name").textContent = nomeCompleto;
            document.getElementById("header-name").textContent = nomeCompleto;
            document.getElementById("welcome-title").textContent = `Bom dia, ${nomeCompleto.split(' ')[0]}.`;

            const partes = nomeCompleto.split(/\s+/);
            let iniciais = partes[0][0];
            if (partes.length > 1) {
              iniciais += partes[partes.length - 1][0];
            }
            const avatarTexto = iniciais.toUpperCase();
            document.getElementById("sidebar-avatar").textContent = avatarTexto;
            document.getElementById("header-avatar").textContent = avatarTexto;
          }
        }
      } catch (e) {
        console.error("Erro ao carregar dados do professor:", e);
      }

      document.querySelectorAll(".btn-logout").forEach(btn => {
        btn.addEventListener("click", async () => {
          localStorage.removeItem("professor_logado");
          try {
            await fetch("logout.php?ajax=1", { method: "GET", cache: "no-store", credentials: "same-origin" });
          } catch (e) {
            console.error("Erro ao encerrar sessão:", e);
          }
          window.location.replace("login_professor.html");
        });
      });
    });

    const classesData = {
      Informática: [
        { year: "1º", group: "A", students: 0, next: "Lógica de programação", time: "08:00" },
        { year: "1º", group: "B", students: 0, next: "Fundamentos de TI", time: "10:20" },
        { year: "2º", group: "A", students: 0, next: "Banco de dados", time: "13:30" },
        { year: "2º", group: "B", students: 0, next: "Desenvolvimento web", time: "15:10" },
        { year: "3º", group: "A", students: 0, next: "Projeto integrador", time: "08:50" },
        { year: "3º", group: "B", students: 0, next: "Segurança digital", time: "14:20" }
      ],
      Redes: [
        { year: "1º", group: "A", students: 0, next: "Introdução a redes", time: "07:10" },
        { year: "1º", group: "B", students: 0, next: "Sistemas operacionais", time: "09:40" },
        { year: "2º", group: "A", students: 0, next: "Cabeamento estruturado", time: "13:30" },
        { year: "2º", group: "B", students: 0, next: "Protocolos de rede", time: "16:00" },
        { year: "3º", group: "A", students: 0, next: "Administração de redes", time: "10:20" },
        { year: "3º", group: "B", students: 0, next: "Segurança de redes", time: "14:20" }
      ]
    };

    const hojeRef = new Date();
    let widgetAno = hojeRef.getFullYear();
    let widgetMes = hojeRef.getMonth() + 1;
    let widgetDiaSelecionadoIso = `${widgetAno}-${String(widgetMes).padStart(2, "0")}-${String(hojeRef.getDate()).padStart(2, "0")}`;
    let eventosWidget = [];
    let ultimoSnapshotCalendarioJson = "";

    let currentCourse = "Informática";

    const courseBtns = document.querySelectorAll(".course-btn");
    const classesGrid = document.getElementById("classes-grid");
    const courseSubtitle = document.getElementById("course-subtitle");
    const nextClassTitle = document.getElementById("next-class-title");
    const nextClassDetails = document.getElementById("next-class-details");
    const nextClassTime = document.getElementById("next-class-time");
    const calendarGrid = document.getElementById("calendar-grid");
    const widgetMonthLabel = document.getElementById("widget-month-label");
    const widgetEventsHeading = document.getElementById("widget-events-heading");
    const widgetEventsList = document.getElementById("widget-events-list");

    function normalizarTexto(txt) {
      return String(txt || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[—–-]/g, " ")
        .replace(/\s+/g, " ")
        .trim();
    }

    function renderClasses(courseName) {
      const items = classesData[courseName];
      classesGrid.innerHTML = items.map(item => {
        const nomeTurmaBanco = `${item.year} Ano ${item.group} — ${courseName}`;
        const destino = `turmas_profs.php?turma=${encodeURIComponent(nomeTurmaBanco)}`;
        const qtd = Number(item.students) || 0;
        const labelAlunos = `${qtd} ${qtd === 1 ? "aluno" : "alunos"}`;

        return `
        <a href="${destino}" class="group flex min-w-56 flex-1 items-center gap-4 rounded-2xl border border-white/10 bg-[#17181c] p-4 text-left transition hover:-translate-y-0.5 hover:border-violet-500/40 hover:bg-violet-500/[0.08] cursor-pointer">
          <div class="grid size-14 shrink-0 place-items-center rounded-2xl bg-[#101114] text-white transition group-hover:bg-violet-600">
            <div class="text-center leading-none">
              <div class="text-xl font-bold">${item.year}</div>
              <div class="mt-1 text-[0.65rem] font-semibold text-slate-400 group-hover:text-violet-100">${item.group}</div>
            </div>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
              <div class="truncate text-sm font-bold text-white">${item.year} ano ${item.group}</div>
              <svg aria-hidden="true" class="size-4 text-slate-500 transition group-hover:translate-x-0.5 group-hover:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </div>
            <div class="mt-1 truncate text-xs text-slate-400">${courseName}</div>
            <div class="mt-2 flex items-center gap-1.5 text-[0.7rem] font-medium text-slate-400">
              <svg aria-hidden="true" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              ${labelAlunos}
            </div>
          </div>
        </a>
      `;
      }).join('');

      courseSubtitle.textContent = `Curso técnico em ${courseName.toLowerCase()}`;

      const next = items[0];
      nextClassTitle.textContent = next.next;
      nextClassDetails.textContent = `${next.year} ano ${next.group} • Sala 12`;
      nextClassTime.textContent = next.time;
    }

    function formatarIso(ano, mes, dia) {
      return `${ano}-${String(mes).padStart(2, "0")}-${String(dia).padStart(2, "0")}`;
    }

    function renderCalendar() {
      const dataRef = new Date(widgetAno, widgetMes - 1, 1);
      if (widgetMonthLabel) {
        widgetMonthLabel.textContent = dataRef.toLocaleDateString("pt-BR", { month: "long", year: "numeric" });
      }

      const primeiroDiaSemana = dataRef.getDay();
      const totalDiasMes = new Date(widgetAno, widgetMes, 0).getDate();
      const totalDiasMesAnterior = new Date(widgetAno, widgetMes - 1, 0).getDate();

      const diasComEvento = new Set(eventosWidget.map(ev => ev.data_evento));

      const weekHeaderHtml = `
        <div class="text-[0.65rem] font-bold text-slate-500">D</div>
        <div class="text-[0.65rem] font-bold text-slate-500">S</div>
        <div class="text-[0.65rem] font-bold text-slate-500">T</div>
        <div class="text-[0.65rem] font-bold text-slate-500">Q</div>
        <div class="text-[0.65rem] font-bold text-slate-500">Q</div>
        <div class="text-[0.65rem] font-bold text-slate-500">S</div>
        <div class="text-[0.65rem] font-bold text-slate-500">S</div>
      `;

      let html = "";

      for (let i = primeiroDiaSemana - 1; i >= 0; i--) {
        const diaAnt = totalDiasMesAnterior - i;
        html += `<div class="mx-auto grid size-8 place-items-center rounded-lg text-xs font-semibold text-slate-600">${diaAnt}</div>`;
      }

      for (let dia = 1; dia <= totalDiasMes; dia++) {
        const iso = formatarIso(widgetAno, widgetMes, dia);
        const selected = iso === widgetDiaSelecionadoIso;
        const hasEvent = diasComEvento.has(iso);

        let cls = "relative mx-auto grid size-8 place-items-center rounded-lg text-xs font-semibold transition cursor-pointer ";
        if (selected) {
          cls += "bg-violet-600 text-white shadow-md shadow-violet-950/40";
        } else {
          cls += "text-slate-300 hover:bg-white/5 hover:text-white";
        }

        html += `<button type="button" onclick="selecionarDiaWidget('${iso}')" class="${cls}">${dia}${hasEvent ? '<span class="absolute bottom-0.5 size-1 rounded-full bg-violet-400"></span>' : ''}</button>`;
      }

      const totalCelulasUsadas = primeiroDiaSemana + totalDiasMes;
      const restante = (7 - (totalCelulasUsadas % 7)) % 7;
      for (let dProx = 1; dProx <= restante; dProx++) {
        html += `<div class="mx-auto grid size-8 place-items-center rounded-lg text-xs font-semibold text-slate-600">${dProx}</div>`;
      }

      calendarGrid.innerHTML = weekHeaderHtml + html;
      renderEventosWidget();
    }

    function renderEventosWidget() {
      if (!widgetEventsList) return;

      const doDia = eventosWidget.filter(ev => ev.data_evento === widgetDiaSelecionadoIso);
      const [y, m, d] = widgetDiaSelecionadoIso.split("-").map(Number);
      const dtSel = new Date(y, m - 1, d);
      const labelDiaCurto = dtSel.toLocaleDateString("pt-BR", { day: "2-digit", month: "short" }).replace(".", "");

      let listaExibir = doDia;
      if (doDia.length > 0) {
        if (widgetEventsHeading) widgetEventsHeading.textContent = `Eventos em ${labelDiaCurto}`;
      } else {
        if (widgetEventsHeading) widgetEventsHeading.textContent = "Próximos eventos";
        const futuros = eventosWidget.filter(ev => ev.data_evento >= widgetDiaSelecionadoIso);
        listaExibir = (futuros.length > 0 ? futuros : eventosWidget).slice(0, 3);
      }

      if (listaExibir.length === 0) {
        widgetEventsList.innerHTML = `<div class="text-xs text-slate-500">Nenhum evento neste mês.</div>`;
        return;
      }

      widgetEventsList.innerHTML = listaExibir.slice(0, 3).map((ev, idx) => {
        const [, evM, evD] = String(ev.data_evento || "").split("-");
        const mesAbrev = evM
          ? new Date(2026, Number(evM) - 1, 1).toLocaleDateString("pt-BR", { month: "short" }).replace(".", "")
          : "---";
        const destaque = idx === 0 || ev.data_evento === widgetDiaSelecionadoIso;
        const subinfo = [
          ev.hora_evento,
          ev.local_evento,
          ev.turma_alvo,
          ev.autor_nome ? `Por ${ev.autor_nome}` : null
        ].filter(Boolean).join(" • ");

        return `
          <a href="calendario_profs.php?data=${encodeURIComponent(ev.data_evento)}" class="flex items-center gap-3 rounded-xl p-1 -m-1 transition hover:bg-white/[0.03]">
            <div class="w-10 shrink-0 rounded-lg ${destaque ? "bg-violet-500/10" : "bg-white/5"} py-1.5 text-center">
              <div class="text-[0.6rem] font-bold uppercase ${destaque ? "text-violet-400" : "text-slate-500"}">${mesAbrev}</div>
              <div class="text-sm font-bold ${destaque ? "text-violet-300" : "text-slate-300"}">${evD || "--"}</div>
            </div>
            <div class="min-w-0">
              <div class="truncate text-xs font-bold text-white">${ev.titulo}</div>
              <div class="mt-0.5 truncate text-[0.65rem] text-slate-400">${subinfo || ev.categoria}</div>
            </div>
          </a>
        `;
      }).join("");
    }

    window.selecionarDiaWidget = function(iso) {
      widgetDiaSelecionadoIso = iso;
      renderCalendar();
    };

    async function carregarCalendarioWidget(forcarRender = false) {
      try {
        const res = await fetch(`api_calendario.php?mes=${widgetMes}&ano=${widgetAno}`, {
          cache: "no-store",
          credentials: "same-origin"
        });
        const data = await res.json();
        if (!data.sucesso || !Array.isArray(data.eventos)) return;

        const novoSnapshot = JSON.stringify(data.eventos);
        if (!forcarRender && novoSnapshot === ultimoSnapshotCalendarioJson) return;
        ultimoSnapshotCalendarioJson = novoSnapshot;

        eventosWidget = data.eventos;
        renderCalendar();
      } catch (erro) {
        console.error("Erro ao carregar eventos do calendário:", erro);
      }
    }

    let ultimoSnapshotTurmasJson = "";

    async function carregarQuantidadeAlunosTurmas() {
      try {
        const res = await fetch("api_turmas_alunos.php", {
          cache: "no-store",
          credentials: "same-origin"
        });
        const data = await res.json();

        if (!data.sucesso || !Array.isArray(data.turmas)) return;

        const novoSnapshot = JSON.stringify(data.turmas);
        if (novoSnapshot === ultimoSnapshotTurmasJson) return;
        ultimoSnapshotTurmasJson = novoSnapshot;

        const mapaContagens = new Map();
        data.turmas.forEach(t => {
          mapaContagens.set(normalizarTexto(t.nome_turma), Number(t.total_alunos) || 0);
        });

        Object.keys(classesData).forEach(curso => {
          classesData[curso].forEach(item => {
            const chaveEsperada = normalizarTexto(`${item.year} Ano ${item.group} — ${curso}`);
            if (mapaContagens.has(chaveEsperada)) {
              item.students = mapaContagens.get(chaveEsperada);
            }
          });
        });

        renderClasses(currentCourse);
      } catch (erro) {
        console.error("Erro ao atualizar contagem de alunos por turma:", erro);
      }
    }

    let ultimoSnapshotJson = "";

    async function carregarChromebooks() {
      const grid = document.getElementById("chromebooks-grid");
      if (!grid) return;

      try {
        const res = await fetch("api_painel_geral.php", { cache: "no-store" });
        const data = await res.json();

        if (!data.sucesso || !data.dados || !Array.isArray(data.dados.setores)) {
          if (!ultimoSnapshotJson) {
            grid.innerHTML = `<div class="col-span-full rounded-2xl border border-white/10 bg-[#17181c] p-5 text-center text-xs text-rose-400">Erro ao carregar disponibilidade dos Chromebooks.</div>`;
          }
          return;
        }

        const setores = data.dados.setores;
        const novoSnapshot = JSON.stringify(setores);

        if (novoSnapshot === ultimoSnapshotJson) return;
        ultimoSnapshotJson = novoSnapshot;

        if (setores.length === 0) {
          grid.innerHTML = `<div class="col-span-full rounded-2xl border border-white/10 bg-[#17181c] p-5 text-center text-xs text-slate-400">Nenhum modelo ou lote de Chromebook cadastrado no banco de dados.</div>`;
          return;
        }

        grid.innerHTML = setores.map(setor => {
          const total = Number(setor.capacidade) || 0;
          const utilizados = Number(setor.utilizados) || 0;
          const disponiveis = Math.max(0, total - utilizados);
          const pctLivre = total > 0 ? Math.round((disponiveis / total) * 100) : 0;

          let badgeColor = "text-emerald-400 bg-emerald-400/10";
          if (pctLivre <= 25) {
            badgeColor = "text-rose-400 bg-rose-400/10";
          } else if (pctLivre <= 50) {
            badgeColor = "text-amber-300 bg-amber-400/10";
          }

          const nomeEscapado = String(setor.nome).replace(/'/g, "\\'");

          return `
            <div onclick="window.location.href='emprestimos_profs.html?modelo=' + encodeURIComponent('${nomeEscapado}')" class="cursor-pointer rounded-2xl border border-white/10 bg-[#17181c] p-4 transition hover:border-violet-500/40 hover:bg-violet-500/[0.06]">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate text-sm font-bold text-white">${setor.nome}</div>
                  <div class="mt-0.5 text-xs text-slate-400">${utilizados} em uso no momento</div>
                </div>
                <div class="shrink-0 rounded-full px-2.5 py-1 text-[0.65rem] font-bold ${badgeColor}">
                  ${pctLivre}% livre
                </div>
              </div>
              <div class="mt-5 flex items-end justify-between">
                <div>
                  <span class="text-3xl font-bold tracking-tight text-white">${disponiveis}</span>
                  <span class="text-sm font-medium text-slate-400"> / ${total}</span>
                </div>
                <div class="mb-1 text-xs font-medium text-slate-400">disponíveis</div>
              </div>
              <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/10">
                <div class="h-full rounded-full bg-violet-500 transition-all duration-500" style="width: ${pctLivre}%"></div>
              </div>
            </div>
          `;
        }).join('');
      } catch (erro) {
        console.error("Erro ao buscar Chromebooks:", erro);
        if (!ultimoSnapshotJson) {
          grid.innerHTML = `<div class="col-span-full rounded-2xl border border-white/10 bg-[#17181c] p-5 text-center text-xs text-rose-400">Falha de conexão com o servidor.</div>`;
        }
      }
    }

    document.getElementById("widget-prev-month").addEventListener("click", () => {
      widgetMes--;
      if (widgetMes < 1) { widgetMes = 12; widgetAno--; }
      widgetDiaSelecionadoIso = formatarIso(widgetAno, widgetMes, 1);
      ultimoSnapshotCalendarioJson = "";
      carregarCalendarioWidget(true);
    });

    document.getElementById("widget-next-month").addEventListener("click", () => {
      widgetMes++;
      if (widgetMes > 12) { widgetMes = 1; widgetAno++; }
      widgetDiaSelecionadoIso = formatarIso(widgetAno, widgetMes, 1);
      ultimoSnapshotCalendarioJson = "";
      carregarCalendarioWidget(true);
    });

    courseBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        courseBtns.forEach(b => {
          b.classList.remove("bg-violet-600", "text-white");
          b.classList.add("text-slate-400", "hover:text-white");
        });
        btn.classList.add("bg-violet-600", "text-white");
        btn.classList.remove("text-slate-400", "hover:text-white");

        currentCourse = btn.getAttribute("data-course");
        renderClasses(currentCourse);
      });
    });

    renderClasses("Informática");
    renderCalendar();

    carregarChromebooks();
    carregarQuantidadeAlunosTurmas();
    carregarCalendarioWidget(true);

    setInterval(() => {
      if (!document.hidden) {
        carregarChromebooks();
        carregarQuantidadeAlunosTurmas();
        carregarCalendarioWidget(false);
      }
    }, 10000);

    document.addEventListener("visibilitychange", () => {
      if (!document.hidden) {
        carregarChromebooks();
        carregarQuantidadeAlunosTurmas();
        carregarCalendarioWidget(false);
      }
    });
  </script>
</body>
</html>