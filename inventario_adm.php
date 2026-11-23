<?php
require_once __DIR__ . '/auth.php';
exigir_perfil('Administrador');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ceep.plg — Gestão escolar & Inventário de Chromebooks</title>
  <style>
    :root {
      color-scheme: dark;
      --bg: #09090b;
      --surface: #121217;
      --surface-hover: #17171f;
      --border: rgba(255, 255, 255, 0.08);
      --border-soft: rgba(255, 255, 255, 0.05);
      --text: #f4f4f5;
      --muted: #71717a;
      --accent: #7c3aed;
      --accent-hover: #8b5cf6;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      background-color: var(--bg);
      color: var(--text);
      font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      -webkit-font-smoothing: antialiased;
      line-height: 1.5;
    }

    /* Topbar */
    .topbar {
      position: sticky;
      top: 0;
      z-index: 40;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 74px;
      padding: 0 2rem;
      background: rgba(9, 9, 11, 0.85);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border-soft);
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: inherit;
    }
    .brand-icon {
      display: grid;
      place-items: center;
      width: 38px;
      height: 38px;
      border-radius: 11px;
      background: var(--accent);
      color: #fff;
      box-shadow: 0 0 24px rgba(124, 58, 237, 0.35);
    }
    .brand-copy strong { display: block; font-size: 0.95rem; letter-spacing: 0.04em; color: #fff; }
    .brand-copy small { display: block; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.18em; color: var(--muted); }

    .topbar nav { display: flex; gap: 1.75rem; align-items: center; }
    .topbar nav a {
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--muted);
      transition: color 0.2s;
    }
    .topbar nav a:hover, .topbar nav a.active { color: #fff; }

    /* Overview / Stats */
    .overview {
      max-width: 1500px;
      margin: 0 auto;
      padding: 3rem 2rem 1.5rem;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 1rem;
    }
    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 1.35rem;
      position: relative;
      overflow: hidden;
    }
    .stat-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: var(--muted);
      margin-bottom: 0.75rem;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      font-weight: 600;
    }
    .stat-card strong {
      display: block;
      font-size: 2.25rem;
      font-weight: 700;
      color: #fff;
      line-height: 1.1;
      letter-spacing: -0.03em;
    }
    .stat-card p {
      font-size: 0.78rem;
      color: var(--muted);
      margin-top: 0.35rem;
    }

    /* Catalog / Inventário */
    .catalog {
      max-width: 1500px;
      margin: 0 auto;
      padding: 1.5rem 2rem 4rem;
    }
    .catalog-bar {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-end;
      justify-content: space-between;
      gap: 1.25rem;
      margin-bottom: 1.5rem;
    }
    .catalog-title span {
      display: block;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      color: #a78bfa;
      margin-bottom: 0.35rem;
    }
    .catalog-title h1 { font-size: 1.75rem; font-weight: 600; letter-spacing: -0.03em; color: #fff; }

    .catalog-controls {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.75rem;
    }
    .search {
      position: relative;
      display: flex;
      align-items: center;
      min-width: 260px;
    }
    .search svg:first-child { position: absolute; left: 13px; color: var(--muted); pointer-events: none; }
    .search input {
      width: 100%;
      height: 42px;
      padding: 0 2.25rem 0 2.5rem;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: var(--surface);
      color: #fff;
      font-size: 0.85rem;
      outline: none;
      transition: border-color 0.2s;
    }
    .search input:focus { border-color: rgba(139, 92, 246, 0.6); }
    .search button {
      position: absolute;
      right: 10px;
      background: none;
      border: none;
      color: var(--muted);
      cursor: pointer;
      display: grid;
      place-items: center;
    }
    .search button:hover { color: #fff; }

    .filters {
      display: flex;
      gap: 0.25rem;
      padding: 0.25rem;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #0e0e12;
      overflow-x: auto;
    }
    .filters button {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.45rem 0.8rem;
      border-radius: 9px;
      border: none;
      background: transparent;
      color: var(--muted);
      font-size: 0.78rem;
      font-weight: 500;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.2s;
    }
    .filters button:hover { color: #e4e4e7; background: rgba(255, 255, 255, 0.04); }
    .filters button.active { background: var(--accent); color: #fff; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3); }
    .filters button b { font-weight: 600; opacity: 0.7; }

    .catalog-meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding-bottom: 1.1rem;
      margin-bottom: 1.5rem;
      border-bottom: 1px solid var(--border-soft);
      font-size: 0.8rem;
      color: var(--muted);
    }
    .add-device-button {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.6rem 1rem;
      border-radius: 12px;
      border: 1px solid rgba(139, 92, 246, 0.35);
      background: var(--accent);
      color: #fff;
      font-size: 0.8rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .add-device-button:hover { background: var(--accent-hover); transform: translateY(-1px); }

    /* Device Grid */
    .device-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 1rem;
    }
    .device-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 1.25rem;
      position: relative;
      transition: all 0.25s;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .device-card:hover { border-color: rgba(139, 92, 246, 0.3); background: var(--surface-hover); transform: translateY(-3px); }

    .asset-number {
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      color: #a78bfa;
      margin-bottom: 0.5rem;
    }

    .device-visual {
      padding: 0.5rem 0 0.5rem;
      border-radius: 12px;
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.02), transparent);
    }
    .device-art { width: 100%; height: auto; display: block; }

    .device-info { margin-top: 0.65rem; }
    .device-title-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.5rem;
    }
    .device-title-row h2 { font-size: 0.95rem; font-weight: 600; color: #f4f4f5; }
    .device-info p { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); margin-top: 0.2rem; }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.2rem 0.55rem;
      border-radius: 999px;
      font-size: 0.65rem;
      font-weight: 600;
      white-space: nowrap;
    }
    .status-pill i { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .status-pill.available { background: rgba(52, 211, 153, 0.1); color: #6ee7b7; border: 1px solid rgba(52, 211, 153, 0.2); }
    .status-pill.available i { background: #34d399; }
    .status-pill.in-use { background: rgba(251, 191, 36, 0.1); color: #fcd34d; border: 1px solid rgba(251, 191, 36, 0.2); }
    .status-pill.in-use i { background: #fbbf24; }
    .status-pill.unavailable { background: rgba(244, 63, 94, 0.1); color: #fda4af; border: 1px solid rgba(244, 63, 94, 0.2); }
    .status-pill.unavailable i { background: #f43f5e; }

    .request-button {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.4rem;
      width: 100%;
      height: 38px;
      margin-top: 0.85rem;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.03);
      color: #e4e4e7;
      font-size: 0.78rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }
    .request-button:hover { background: rgba(139, 92, 246, 0.15); border-color: rgba(139, 92, 246, 0.4); color: #fff; }

    /* Empty State */
    .empty-state {
      display: grid;
      place-items: center;
      text-align: center;
      padding: 4rem 1.5rem;
      border-radius: 18px;
      border: 1px dashed var(--border);
      background: rgba(255, 255, 255, 0.01);
      color: var(--muted);
    }
    .empty-state h2 { font-size: 1rem; color: #e4e4e7; margin-top: 0.75rem; }
    .empty-state p { font-size: 0.8rem; margin-top: 0.25rem; }
    .empty-state button {
      margin-top: 1rem;
      padding: 0.5rem 1rem;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.05);
      color: #fff;
      font-size: 0.78rem;
      cursor: pointer;
    }

    /* Footer */
    footer {
      display: flex;
      justify-content: space-between;
      max-width: 1500px;
      margin: 0 auto;
      padding: 1.75rem 2rem;
      border-top: 1px solid var(--border-soft);
      font-size: 0.75rem;
      color: #52525b;
    }

    /* Modals */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      z-index: 50;
      display: grid;
      place-items: center;
      padding: 1.25rem;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(6px);
    }
    .modal {
      position: relative;
      width: 100%;
      max-width: 480px;
      max-height: 90vh;
      overflow-y: auto;
      border-radius: 24px;
      border: 1px solid var(--border);
      background: #141419;
      padding: 1.75rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
    }
    .modal-close {
      position: absolute;
      top: 1.25rem;
      right: 1.25rem;
      display: grid;
      place-items: center;
      width: 34px;
      height: 34px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.03);
      color: var(--muted);
      cursor: pointer;
    }
    .modal-close:hover { color: #fff; }
    .modal-kicker {
      display: block;
      font-size: 0.65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.18em;
      color: #a78bfa;
    }
    .modal h2 { font-size: 1.4rem; font-weight: 600; color: #fff; margin-top: 0.35rem; }
    .modal > p { font-size: 0.82rem; color: var(--muted); margin-top: 0.25rem; }

    .modal form { display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem; }
    .modal label { display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.75rem; font-weight: 500; color: #a1a1aa; }
    .modal input, .modal select {
      width: 100%;
      padding: 0.65rem 0.85rem;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #0d0d11;
      color: #fff;
      font-size: 0.85rem;
      font-family: inherit;
      outline: none;
    }
    .modal input:focus, .modal select:focus { border-color: #8b5cf6; }

    .status-options { border: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem; }
    .status-options legend { font-size: 0.75rem; font-weight: 500; color: #a1a1aa; margin-bottom: 0.35rem; }
    .status-options label {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      padding: 0.65rem 0.85rem;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #0d0d11;
      cursor: pointer;
    }
    .status-options input[type="radio"] { display: none; }
    .status-options span { display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: #e4e4e7; width: 100%; }
    .status-options span i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .status-options label.available i { background: #34d399; }
    .status-options label.in-use i { background: #fbbf24; }
    .status-options label.unavailable i { background: #f43f5e; }
    .status-options span svg { margin-left: auto; color: transparent; transition: color 0.2s; }
    .status-options input[type="radio"]:checked + span svg { color: var(--accent); }
    .status-options input[type="radio"]:checked ~ span, .status-options input[type="radio"]:checked + span { color: #fff; font-weight: 600; }
    .status-options label:has(input:checked) { border-color: rgba(139, 92, 246, 0.5); background: rgba(124, 58, 237, 0.08); }

    .modal form button[type="submit"] {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      height: 46px;
      margin-top: 0.5rem;
      border-radius: 12px;
      border: none;
      background: var(--accent);
      color: #fff;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
    }
    .modal form button[type="submit"]:hover { background: var(--accent-hover); }

    /* Botão de Excluir no Modal */
    .delete-device-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      height: 42px;
      margin-top: 0.25rem;
      border-radius: 12px;
      border: 1px solid rgba(244, 63, 94, 0.3);
      background: rgba(244, 63, 94, 0.1);
      color: #fda4af;
      font-size: 0.82rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .delete-device-btn:hover { background: rgba(244, 63, 94, 0.2); border-color: rgba(244, 63, 94, 0.5); color: #fff; }

    .modal form small { text-align: center; font-size: 0.7rem; color: var(--muted); }

    /* Toast */
    .toast {
      position: fixed;
      bottom: 1.5rem;
      left: 50%;
      transform: translateX(-50%);
      z-index: 60;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.75rem 1.15rem;
      border-radius: 14px;
      border: 1px solid rgba(52, 211, 153, 0.25);
      background: #14221c;
      color: #a7f3d0;
      font-size: 0.82rem;
      font-weight: 500;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
    }
    .toast span:first-child { display: grid; place-items: center; color: #34d399; }

    .hidden { display: none !important; }

    @media (max-width: 640px) {
      .topbar { padding: 0 1rem; }
      .topbar nav { display: none; }
      .overview, .catalog { padding-left: 1rem; padding-right: 1rem; }
      footer { flex-direction: column; gap: 0.5rem; text-align: center; }
    }
  </style>
<script>
(function () {
  document.documentElement.style.visibility = 'hidden';
  const loginDestino = "login_adm.html";
  async function conferirSessao() {
    try {
      const resposta = await fetch('validar_sessao.php', { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } });
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
  window.addEventListener('pagehide', function () { document.documentElement.style.visibility = 'hidden'; });
  window.addEventListener('pageshow', conferirSessao);
})();
</script>
</head>
<body>
  <div class="app-shell">
    <!-- Topbar -->
    <header class="topbar">
      <a class="brand" href="#overview" aria-label="ceep.plg — visão geral">
        <span class="brand-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="11" rx="1.5" /><path d="M3 19h18M8 19l1-4M16 19l-1-4" /></svg>
        </span>
        <span class="brand-copy">
          <strong>ceep.plg</strong>
          <small>Gestão escolar</small>
        </span>
      </a>

      <!-- Menu superior ajustado -->
      <nav aria-label="Navegação principal">
        <a class="active" href="#overview">Visão geral</a>
        <a href="emprestimo_adm.php" style="color: #a78bfa;">Painel de Empréstimos</a>
      </nav>

      <!-- Perfil do Administrador e Botão Sair -->
      <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding-left: 1rem; border-left: 1px solid var(--border);">
          <div id="adm-avatar" style="display: grid; place-items: center; width: 36px; height: 36px; border-radius: 10px; background: var(--accent); font-size: 0.75rem; font-weight: 700; color: #fff;">AD</div>
          <div style="text-align: left; line-height: 1.2;">
            <p id="adm-nome" style="font-size: 0.85rem; font-weight: 600; color: #fff; margin: 0;">Carregando...</p>
            <p id="adm-cargo" style="font-size: 0.7rem; color: var(--muted); margin: 0;">Administrador</p>
          </div>
        </div>

        <button
          id="adm-logout-btn"
          title="Encerrar sessão"
          style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.85rem; border-radius: 12px; border: 1px solid rgba(244, 63, 94, 0.25); background: rgba(244, 63, 94, 0.1); color: #fda4af; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: background 0.2s;"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
          </svg>
          <span>Sair</span>
        </button>
      </div>
    </header>

    <main>
      <!-- Visão Geral / Estatísticas -->
      <section class="overview" id="overview" aria-label="Visão geral do inventário">
        <div class="stats-grid">
          <article class="stat-card">
            <div class="stat-card-head">
              <span>Total</span>
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" /><rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" /></svg>
            </div>
            <strong id="stat-total">0</strong>
            <p>equipamentos cadastrados</p>
          </article>
          <article class="stat-card">
            <div class="stat-card-head">
              <span>Disponíveis</span>
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="11" rx="1.5" /><path d="M3 19h18M8 19l1-4M16 19l-1-4" /></svg>
            </div>
            <strong id="stat-available">0</strong>
            <p id="stat-available-detail">0% do inventário</p>
          </article>
          <article class="stat-card">
            <div class="stat-card-head">
              <span>Em uso</span>
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
            </div>
            <strong id="stat-inuse">0</strong>
            <p>em empréstimo agora</p>
          </article>
          <article class="stat-card">
            <div class="stat-card-head">
              <span>Indisponíveis</span>
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.6 2.6 18a2 2 0 0 0 1.8 3h15.2a2 2 0 0 0 1.8-3L13.7 3.6a2 2 0 0 0-3.4 0Z" /><path d="M12 9v4M12 17h.01" /></svg>
            </div>
            <strong id="stat-unavailable">0</strong>
            <p>manutenção ou bloqueio</p>
          </article>
        </div>
      </section>

      <!-- Catálogo / Inventário -->
      <section class="catalog" id="inventory">
        <div class="catalog-bar">
          <div class="catalog-title">
            <span>Inventário</span>
            <h1>Todos os equipamentos</h1>
          </div>
          <div class="catalog-controls">
            <label class="search">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></svg>
              <input id="search-input" placeholder="Buscar Chromebook..." />
              <button id="clear-search-btn" class="hidden" aria-label="Limpar busca">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m6 6 12 12M18 6 6 18" /></svg>
              </button>
            </label>
            <div id="filter-group" class="filters" role="group" aria-label="Filtrar por status">
              <button data-filter="all" class="active"><span>Todos</span><b id="count-all">0</b></button>
              <button data-filter="available"><span>Disponíveis</span><b id="count-available">0</b></button>
              <button data-filter="in-use"><span>Em uso</span><b id="count-inuse">0</b></button>
              <button data-filter="unavailable"><span>Indisponíveis</span><b id="count-unavailable">0</b></button>
            </div>
          </div>
        </div>

        <div class="catalog-meta">
          <span id="filtered-count-label">0 equipamentos encontrados</span>
          <button id="add-device-btn" class="add-device-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
            Adicionar Chromebook
          </button>
        </div>

        <div id="device-grid" class="device-grid"></div>

        <div id="empty-state" class="empty-state hidden">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></svg>
          <h2>Nenhum equipamento encontrado</h2>
          <p>Tente outro número ou altere o filtro selecionado.</p>
          <button id="reset-filters-btn">Limpar filtros</button>
        </div>
      </section>
    </main>

    <footer>
      <span>ceep.plg · Gestão escolar</span>
      <span id="footer-catalog-count">0 ativos catalogados</span>
    </footer>

    <!-- Modal de Edição & Exclusão -->
    <div id="edit-modal" class="modal-backdrop hidden">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="edit-title">
        <button id="close-edit-modal" class="modal-close" aria-label="Fechar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m6 6 12 12M18 6 6 18" /></svg>
        </button>
        <span class="modal-kicker">Gerenciar equipamento</span>
        <h2 id="edit-title">Chromebook</h2>
        <p id="edit-subtitle">Altere o status atual ou remova este Chromebook do inventário.</p>
        <form id="edit-form">
          <fieldset class="status-options">
            <legend>Novo status</legend>
            <label class="available">
              <input type="radio" name="status" value="available" />
              <span><i></i>Disponível <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span>
            </label>
            <label class="in-use">
              <input type="radio" name="status" value="in-use" />
              <span><i></i>Em uso <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span>
            </label>
            <label class="unavailable">
              <input type="radio" name="status" value="unavailable" />
              <span><i></i>Indisponível <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span>
            </label>
          </fieldset>
          <button type="submit">
            Salvar alterações 
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6" /></svg>
          </button>
        </form>

        <!-- Botão de Excluir / Remover com Confirmação -->
        <button id="delete-device-btn" class="delete-device-btn" style="width: 100%; margin-top: 0.75rem;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
          Remover Chromebook do inventário
        </button>
      </div>
    </div>

    <!-- Modal de Adição -->
    <div id="add-modal" class="modal-backdrop hidden">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="add-title">
        <button id="close-add-modal" class="modal-close" aria-label="Fechar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m6 6 12 12M18 6 6 18" /></svg>
        </button>
        <span class="modal-kicker">Novo patrimônio</span>
        <h2 id="add-title">Adicionar Chromebook</h2>
        <p>Cadastre um novo equipamento no inventário da escola.</p>
        <form id="add-form">
          <label>
            Patrimônio / Nome do equipamento
            <input id="add-name-input" name="patrimonio" placeholder="Ex: CEEP-0111" required />
          </label>
          <label>
            Modelo
            <input name="modelo" placeholder="Ex: Samsung Chromebook 4" value="Padrão" />
          </label>
          <label>
            Status inicial
            <select name="status" required>
              <option value="available">Disponível</option>
              <option value="in-use">Em uso</option>
              <option value="unavailable">Indisponível</option>
            </select>
          </label>
          <button type="submit">
            Adicionar ao inventário 
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
          </button>
          <small>O equipamento será salvo diretamente no banco de dados.</small>
        </form>
      </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast hidden" role="status">
      <span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6" /></svg>
      </span>
      <span id="toast-text"></span>
    </div>
  </div>

  <script>
    let inventory = [];
    let query = "";
    let filter = "all";
    let editingDevice = null;

    const searchInput = document.getElementById("search-input");
    const clearSearchBtn = document.getElementById("clear-search-btn");
    const filterButtons = document.querySelectorAll("#filter-group button");
    const deviceGrid = document.getElementById("device-grid");
    const emptyState = document.getElementById("empty-state");
    const filteredCountLabel = document.getElementById("filtered-count-label");

    const statTotal = document.getElementById("stat-total");
    const statAvailable = document.getElementById("stat-available");
    const statAvailableDetail = document.getElementById("stat-available-detail");
    const statInuse = document.getElementById("stat-inuse");
    const statUnavailable = document.getElementById("stat-unavailable");
    const footerCatalogCount = document.getElementById("footer-catalog-count");

    const editModal = document.getElementById("edit-modal");
    const closeEditModalBtn = document.getElementById("close-edit-modal");
    const editTitle = document.getElementById("edit-title");
    const editSubtitle = document.getElementById("edit-subtitle");
    const editForm = document.getElementById("edit-form");
    const deleteDeviceBtn = document.getElementById("delete-device-btn");

    const addModal = document.getElementById("add-modal");
    const addDeviceBtn = document.getElementById("add-device-btn");
    const closeAddModalBtn = document.getElementById("close-add-modal");
    const addForm = document.getElementById("add-form");

    const toastEl = document.getElementById("toast");
    const toastText = document.getElementById("toast-text");

    function showToast(msg) {
      toastText.textContent = msg;
      toastEl.classList.remove("hidden");
      setTimeout(() => toastEl.classList.add("hidden"), 4500);
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
            await fetch("logout.php?ajax=1", { method: "GET", cache: "no-store", credentials: "same-origin" });
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

      carregarInventarioDoBanco();
      // Atualiza os status do inventário a cada 10 segundos.
      setInterval(() => {
        if (!document.hidden) carregarInventarioDoBanco(true);
      }, 10000);
      document.addEventListener("visibilitychange", () => {
        if (!document.hidden) carregarInventarioDoBanco(true);
      });
    });

    function carregarInventarioDoBanco(silencioso = false) {
      fetch("api_chromebooks.php", { credentials: "same-origin", cache: "no-store" })
        .then(res => res.json())
        .then(data => {
          if (data.sucesso) {
            inventory = data.dados || [];
            render();
          } else if (!silencioso) {
            showToast("Erro ao carregar dados do inventário.");
          }
        })
        .catch(err => {
          console.error("Erro na requisição:", err);
          if (!silencioso) showToast("Erro ao conectar com o servidor.");
        });
    }

    function createChromebookArt(id) {
      const padded = String(id).padStart(3, "0");
      return `
        <svg class="device-art" viewBox="0 0 340 190" role="img" aria-label="Ilustração do Chromebook ${id}">
          <defs>
            <radialGradient id="glow-${id}" cx="65%" cy="32%" r="75%">
              <stop offset="0" stop-color="#3c1766" stop-opacity=".8" />
              <stop offset=".48" stop-color="#170d27" stop-opacity=".75" />
              <stop offset="1" stop-color="#09090d" />
            </radialGradient>
            <linearGradient id="base-${id}" x1="0" x2="0" y1="0" y2="1">
              <stop stop-color="#35323d" />
              <stop offset="1" stop-color="#121116" />
            </linearGradient>
          </defs>
          <ellipse cx="170" cy="170" rx="120" ry="9" fill="#000" opacity=".6" />
          <rect x="73" y="22" width="194" height="123" rx="10" fill="#111016" stroke="#1f1d27" stroke-width="5" />
          <rect x="82" y="31" width="176" height="104" rx="3" fill="url(#glow-${id})" />
          <circle cx="215" cy="67" r="50" fill="none" stroke="#a855f7" stroke-opacity=".13" />
          <circle cx="215" cy="67" r="34" fill="none" stroke="#a855f7" stroke-opacity=".07" />
          <circle cx="160" cy="26.5" r="1.7" fill="#5d5866" />
          <circle cx="101" cy="113" r="2.2" fill="#a855f7" />
          <text x="109" y="116" fill="#a98ec9" font-size="6" font-family="monospace" letter-spacing="1.2">CEEPLG / ${padded}</text>
          <path d="M51 145h238l18 18c3 4 0 8-7 8H40c-7 0-10-4-7-8Z" fill="url(#base-${id})" />
          <path d="M51 145h238l10 11H41Z" fill="#24222b" />
          <path d="M145 158h50l7 7h-64Z" fill="#0b0a0d" />
        </svg>
      `;
    }

    window.openEditModal = function(id) {
      editingDevice = inventory.find(item => item.id === id);
      if (!editingDevice) return;
      editTitle.textContent = editingDevice.patrimonio || editingDevice.name;
      editSubtitle.textContent = editingDevice.aluno_atual ? `Em uso por: ${editingDevice.aluno_atual}` : "Altere o status atual ou remova este Chromebook.";
      editForm.querySelectorAll("input[name='status']").forEach(radio => {
        radio.checked = radio.value === editingDevice.status;
      });
      editModal.classList.remove("hidden");
    };

    closeEditModalBtn.addEventListener("click", () => editModal.classList.add("hidden"));
    addDeviceBtn.addEventListener("click", () => addModal.classList.remove("hidden"));
    closeAddModalBtn.addEventListener("click", () => addModal.classList.add("hidden"));

    // Ação de Excluir / Remover Chromebook com Confirmação
    deleteDeviceBtn.addEventListener("click", () => {
      if (!editingDevice) return;
      
      const nomeEquipamento = editingDevice.patrimonio || editingDevice.name || `Chromebook #${editingDevice.id}`;
      const confirmado = confirm(`Tem certeza que deseja remover permanentemente o equipamento "${nomeEquipamento}" do inventário?`);
      
      if (!confirmado) return;

      const payload = {
        acao: 'remover',
        id_chromebook: editingDevice.id
      };

      fetch("api_chromebooks.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso) {
          showToast(data.mensagem || "Equipamento removido com sucesso!");
          editModal.classList.add("hidden");
          carregarInventarioDoBanco();
        } else {
          showToast(data.mensagem || "Erro ao remover equipamento.");
        }
      })
      .catch(() => showToast("Erro de conexão ao tentar remover."));
    });

    editForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const selectedStatus = editForm.querySelector("input[name='status']:checked").value;
      if (!editingDevice) return;

      const payload = {
        id_chromebook: editingDevice.id,
        status: selectedStatus
      };

      fetch("api_chromebooks.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso) {
          showToast(data.mensagem || "Status atualizado com sucesso!");
          editModal.classList.add("hidden");
          carregarInventarioDoBanco();
        } else {
          showToast(data.mensagem || "Erro ao atualizar status.");
        }
      })
      .catch(() => showToast("Erro de conexão com o servidor."));
    });

    addForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const formData = new FormData(addForm);
      const payload = {
        acao: 'cadastrar',
        patrimonio: formData.get("patrimonio"),
        modelo: formData.get("modelo"),
        status: formData.get("status")
      };

      fetch("api_chromebooks.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso) {
          showToast(data.mensagem || "Chromebook cadastrado com sucesso!");
          addModal.classList.add("hidden");
          addForm.reset();
          carregarInventarioDoBanco();
        } else {
          showToast(data.mensagem || "Erro ao cadastrar.");
        }
      })
      .catch(() => showToast("Erro de conexão ao cadastrar."));
    });

    filterButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        filterButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        filter = btn.getAttribute("data-filter");
        render();
      });
    });

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

    function render() {
      const counts = {
        available: inventory.filter(i => i.status === "available").length,
        "in-use": inventory.filter(i => i.status === "in-use").length,
        unavailable: inventory.filter(i => i.status === "unavailable").length,
      };

      statTotal.textContent = inventory.length;
      statAvailable.textContent = counts.available;
      statAvailableDetail.textContent = `${Math.round((counts.available / (inventory.length || 1)) * 100)}% do inventário`;
      statInuse.textContent = counts["in-use"];
      statUnavailable.textContent = counts.unavailable;
      footerCatalogCount.textContent = `${inventory.length} ativos catalogados`;

      document.getElementById("count-all").textContent = inventory.length;
      document.getElementById("count-available").textContent = counts.available;
      document.getElementById("count-inuse").textContent = counts["in-use"];
      document.getElementById("count-unavailable").textContent = counts.unavailable;

      const normalized = query.trim().toLowerCase();
      const filtered = inventory.filter(item => {
        const matches = (item.patrimonio && item.patrimonio.toLowerCase().includes(normalized)) || 
                        (item.name && item.name.toLowerCase().includes(normalized)) || 
                        String(item.id).includes(normalized);
        return matches && (filter === "all" || item.status === filter);
      });

      filteredCountLabel.textContent = `${filtered.length} equipamentos encontrados`;

      if (filtered.length === 0) {
        deviceGrid.innerHTML = "";
        emptyState.classList.remove("hidden");
        return;
      }

      emptyState.classList.add("hidden");

      deviceGrid.innerHTML = filtered.map(item => {
        const statusClass = item.status;
        const statusLabel = item.status === 'available' ? 'Disponível' : item.status === 'in-use' ? 'Em uso' : 'Indisponível';
        const patrimonioText = item.patrimonio || `CEEP-${String(item.id).padStart(4, '0')}`;
        const alunoInfo = item.aluno_atual ? `<p style="font-size: 0.7rem; color: #a78bfa; margin-top: 0.2rem;">Aluno: ${item.aluno_atual}</p>` : '';

        return `
          <article class="device-card">
            <div>
              <div class="asset-number">${patrimonioText}</div>
              <div class="device-visual">
                ${createChromebookArt(item.id)}
              </div>
              <div class="device-info">
                <div class="device-title-row">
                  <h2>${item.name || patrimonioText}</h2>
                  <span class="status-pill ${statusClass}">
                    <i></i>${statusLabel}
                  </span>
                </div>
                <p>Modelo: ${item.modelo || 'Padrão'}</p>
                ${alunoInfo}
              </div>
            </div>
            <button class="request-button" onclick="openEditModal(${item.id})">
              Gerenciar status
            </button>
          </article>
        `;
      }).join("");
    }
  </script>
</body>
</html>