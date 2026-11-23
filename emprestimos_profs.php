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

    .topbar nav { display: flex; gap: 1.75rem; }
    .topbar nav a {
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--muted);
      transition: color 0.2s;
    }
    .topbar nav a:hover, .topbar nav a.active { color: #fff; }

    .reservations-button {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.55rem 0.95rem;
      border-radius: 12px;
      border: 1px solid rgba(139, 92, 246, 0.25);
      background: rgba(124, 58, 237, 0.12);
      color: #ddd6fe;
      font-size: 0.8125rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
    }
    .reservations-button:hover { background: rgba(124, 58, 237, 0.22); }
    .reservations-button b {
      display: inline-grid;
      place-items: center;
      min-width: 20px;
      height: 20px;
      padding: 0 5px;
      border-radius: 6px;
      background: var(--accent);
      color: #fff;
      font-size: 0.7rem;
    }

    /* Overview / Turmas */
    .overview {
      max-width: 1500px;
      margin: 0 auto;
      padding: 3rem 2rem 2.5rem;
      border-bottom: 1px solid var(--border-soft);
    }
    .years-heading span {
      display: block;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      color: #a78bfa;
      margin-bottom: 0.4rem;
    }
    .years-heading h1 { font-size: 1.85rem; font-weight: 600; letter-spacing: -0.03em; color: #fff; }

    .years-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1rem;
      margin-top: 1.75rem;
    }
    .year-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 1.35rem;
      transition: border-color 0.2s, transform 0.2s;
    }
    .year-card:hover { border-color: rgba(139, 92, 246, 0.3); transform: translateY(-2px); }
    .year-card-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 1.1rem;
    }
    .year-card-head span { display: block; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--muted); }
    .year-card-head strong { font-size: 2rem; font-weight: 700; line-height: 1.1; color: #fff; }
    .year-card-head small { font-size: 0.85rem; color: #a1a1aa; margin-left: 0.25rem; }
    .year-icon {
      display: grid;
      place-items: center;
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--border-soft);
      color: #a78bfa;
    }
    .class-list {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.5rem;
    }
    .class-list a {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.55rem 0.75rem;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.025);
      border: 1px solid var(--border-soft);
      font-size: 0.8rem;
      color: #d4d4d8;
      text-decoration: none;
      transition: border-color 0.2s, background 0.2s, color 0.2s;
    }
    .class-list a:hover {
      border-color: rgba(139, 92, 246, 0.45);
      background: rgba(124, 58, 237, 0.12);
      color: #fff;
    }
    .class-list b {
      padding: 0.1rem 0.45rem;
      border-radius: 6px;
      background: rgba(124, 58, 237, 0.18);
      color: #c4b5fd;
      font-size: 0.72rem;
    }

    /* Catalog / Inventário */
    .catalog {
      max-width: 1500px;
      margin: 0 auto;
      padding: 2.75rem 2rem 4rem;
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
    .bulk-request-button {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      padding: 0.6rem 1rem;
      border-radius: 12px;
      border: 1px solid rgba(139, 92, 246, 0.35);
      background: var(--accent);
      color: #fff;
      font-size: 0.8rem;
      cursor: pointer;
      transition: all 0.2s;
    }
    .bulk-request-button:hover:not(:disabled) { background: var(--accent-hover); transform: translateY(-1px); }
    .bulk-request-button:disabled {
      background: rgba(255, 255, 255, 0.03);
      border-color: var(--border-soft);
      color: var(--muted);
      cursor: not-allowed;
    }
    .bulk-request-button span { opacity: 0.8; font-size: 0.75rem; }

    /* Device Grid */
    .device-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 0.9rem;
    }
    .device-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 1rem;
      transition: all 0.25s;
    }
    .device-card:hover { border-color: rgba(139, 92, 246, 0.3); background: var(--surface-hover); transform: translateY(-3px); }
    .device-card.selected { border-color: #8b5cf6; background: rgba(124, 58, 237, 0.08); }

    .card-selection-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 0.35rem;
    }
    .asset-number { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; color: #52525b; }

    .device-checkbox { position: relative; display: inline-flex; cursor: pointer; }
    .device-checkbox.disabled { opacity: 0.3; cursor: not-allowed; }
    .device-checkbox input { position: absolute; opacity: 0; width: 0; height: 0; }
    .device-checkbox span {
      display: grid;
      place-items: center;
      width: 24px;
      height: 24px;
      border-radius: 8px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      background: rgba(255, 255, 255, 0.03);
      color: transparent;
      transition: all 0.15s;
    }
    .device-checkbox input:checked + span {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .device-visual {
      padding: 0.25rem 0;
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
    .device-title-row h2 { font-size: 0.92rem; font-weight: 600; color: #f4f4f5; }
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

    .card-note {
      margin-top: 0.85rem;
      padding: 0.5rem 0.7rem;
      border-radius: 10px;
      font-size: 0.72rem;
      font-weight: 500;
      text-align: center;
      background: rgba(255, 255, 255, 0.025);
      border: 1px solid var(--border-soft);
      color: #a1a1aa;
    }
    .card-note.selected { background: rgba(124, 58, 237, 0.2); border-color: rgba(139, 92, 246, 0.4); color: #ddd6fe; }

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

    .selected-device-list {
      display: flex;
      flex-wrap: wrap;
      gap: 0.4rem;
      margin: 1rem 0 1.25rem;
    }
    .selected-device-list span {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.25rem 0.6rem;
      border-radius: 8px;
      background: rgba(124, 58, 237, 0.16);
      border: 1px solid rgba(139, 92, 246, 0.3);
      color: #ddd6fe;
      font-size: 0.72rem;
      font-weight: 600;
    }
    .selected-device-list button {
      background: none;
      border: none;
      color: inherit;
      cursor: pointer;
      display: grid;
      place-items: center;
    }

    .modal form { display: flex; flex-direction: column; gap: 0.9rem; }
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
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
    .modal form button[type="submit"] {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      height: 46px;
      margin-top: 0.35rem;
      border-radius: 12px;
      border: none;
      background: var(--accent);
      color: #fff;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
    }
    .modal form button[type="submit"]:hover { background: var(--accent-hover); }
    .modal form small { text-align: center; font-size: 0.7rem; color: var(--muted); }

    /* Reservation List in Modal */
    .reservation-list { display: flex; flex-direction: column; gap: 0.65rem; margin-top: 1.25rem; }
    .reservation-item {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      padding: 0.85rem;
      border-radius: 14px;
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.02);
    }
    .mini-device {
      display: grid;
      place-items: center;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: rgba(124, 58, 237, 0.15);
      color: #a78bfa;
    }
    .reservation-item div { flex: 1; }
    .reservation-item strong { display: block; font-size: 0.88rem; color: #fff; }
    .reservation-item small { font-size: 0.72rem; color: #fbbf24; }
    .reservation-item button {
      padding: 0.4rem 0.75rem;
      border-radius: 8px;
      border: 1px solid rgba(244, 63, 94, 0.25);
      background: rgba(244, 63, 94, 0.1);
      color: #fda4af;
      font-size: 0.72rem;
      font-weight: 600;
      cursor: pointer;
    }
    .no-reservations {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 2.5rem 1rem;
      margin-top: 1rem;
      border-radius: 16px;
      border: 1px dashed var(--border);
      color: var(--muted);
      gap: 0.4rem;
    }
    .no-reservations strong { color: #e4e4e7; font-size: 0.92rem; margin-top: 0.35rem; }
    .no-reservations span { font-size: 0.78rem; }

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

    .hidden { display: none !important; }

    @media (max-width: 640px) {
      .topbar { padding: 0 1rem; }
      .topbar nav { display: none; }
      .overview, .catalog { padding-left: 1rem; padding-right: 1rem; }
      footer { flex-direction: column; gap: 0.5rem; text-align: center; }
    }
  </style>
</head>
<body>
  <div class="app-shell">
    <!-- Topbar -->
    <header class="topbar">
      <a class="brand" href="acessoprofessor.php" aria-label="ceep.plg — portal do professor">
        <span class="brand-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="11" rx="1.5" /><path d="M3 19h18M8 19l1-4M16 19l-1-4" /></svg>
        </span>
        <span class="brand-copy">
          <strong>ceep.plg</strong>
          <small>Gestão escolar</small>
        </span>
      </a>
      <nav aria-label="Navegação principal">
        <a href="acessoprofessor.php">Visão geral</a>
        <a class="active" href="#inventory">Inventário</a>
      </nav>
      <button id="open-reservations-btn" class="reservations-button">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4" /><path d="M5 21a7 7 0 0 1 14 0" /></svg>
        <span>Minhas reservas</span>
        <b id="reserved-count-badge" class="hidden">0</b>
      </button>
    </header>

    <main>
      <!-- Organização Escolar -->
      <section class="overview" id="overview" aria-label="Turmas da escola">
        <div class="years-heading">
          <span>Organização escolar</span>
          <h1>Turmas por série e curso</h1>
        </div>
        <div class="years-grid">
          <article class="year-card">
            <div class="year-card-head">
              <div><span>Turmas</span><strong>1º</strong><small>ano</small></div>
              <span class="year-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4" /><path d="M5 21a7 7 0 0 1 14 0" /></svg></span>
            </div>
            <div class="class-list">
              <a href="turmas_profs.php?turma=1%C2%BA%20Ano%20A%20%E2%80%94%20Inform%C3%A1tica">Informática <b>A</b></a>
              <a href="turmas_profs.php?turma=1%C2%BA%20Ano%20B%20%E2%80%94%20Inform%C3%A1tica">Informática <b>B</b></a>
              <a href="turmas_profs.php?turma=1%C2%BA%20Ano%20A%20%E2%80%94%20Redes">Redes <b>A</b></a>
              <a href="turmas_profs.php?turma=1%C2%BA%20Ano%20B%20%E2%80%94%20Redes">Redes <b>B</b></a>
            </div>
          </article>
          <article class="year-card">
            <div class="year-card-head">
              <div><span>Turmas</span><strong>2º</strong><small>ano</small></div>
              <span class="year-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4" /><path d="M5 21a7 7 0 0 1 14 0" /></svg></span>
            </div>
            <div class="class-list">
              <a href="turmas_profs.php?turma=2%C2%BA%20Ano%20A%20%E2%80%94%20Inform%C3%A1tica">Informática <b>A</b></a>
              <a href="turmas_profs.php?turma=2%C2%BA%20Ano%20B%20%E2%80%94%20Inform%C3%A1tica">Informática <b>B</b></a>
              <a href="turmas_profs.php?turma=2%C2%BA%20Ano%20A%20%E2%80%94%20Redes">Redes <b>A</b></a>
              <a href="turmas_profs.php?turma=2%C2%BA%20Ano%20B%20%E2%80%94%20Redes">Redes <b>B</b></a>
            </div>
          </article>
          <article class="year-card">
            <div class="year-card-head">
              <div><span>Turmas</span><strong>3º</strong><small>ano</small></div>
              <span class="year-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4" /><path d="M5 21a7 7 0 0 1 14 0" /></svg></span>
            </div>
            <div class="class-list">
              <a href="turmas_profs.php?turma=3%C2%BA%20Ano%20A%20%E2%80%94%20Inform%C3%A1tica">Informática <b>A</b></a>
              <a href="turmas_profs.php?turma=3%C2%BA%20Ano%20B%20%E2%80%94%20Inform%C3%A1tica">Informática <b>B</b></a>
              <a href="turmas_profs.php?turma=3%C2%BA%20Ano%20A%20%E2%80%94%20Redes">Redes <b>A</b></a>
              <a href="turmas_profs.php?turma=3%C2%BA%20Ano%20B%20%E2%80%94%20Redes">Redes <b>B</b></a>
            </div>
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
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></svg>
              <input id="search-input" placeholder="Buscar Chromebook ou modelo..." />
              <button id="clear-search-btn" class="hidden" aria-label="Limpar busca">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m6 6 12 12M18 6 6 18" /></svg>
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
          <span id="filtered-count-label">Carregando equipamentos...</span>
          <button id="bulk-request-btn" class="bulk-request-button" disabled>
            <span id="selected-count-label">Selecione os equipamentos</span>
            <strong>Pedir empréstimos</strong>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 12h14M14 7l5 5-5 5" /></svg>
          </button>
        </div>

        <div id="device-grid" class="device-grid"></div>

        <div id="empty-state" class="empty-state hidden">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></svg>
          <h2>Nenhum equipamento encontrado</h2>
          <p>Tente outro patrimônio, modelo ou altere o filtro selecionado.</p>
          <button id="reset-filters-btn">Limpar filtros</button>
        </div>
      </section>
    </main>

    <footer>
      <span>ceep.plg · Gestão escolar</span>
      <span id="footer-total-label">Sincronizado com o banco de dados</span>
    </footer>

    <!-- Modal de Solicitação em Lote -->
    <div id="loan-modal" class="modal-backdrop hidden">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="loan-title">
        <button id="close-loan-modal" class="modal-close" aria-label="Fechar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m6 6 12 12M18 6 6 18" /></svg>
        </button>
        <span class="modal-kicker">Solicitação em lote</span>
        <h2 id="loan-title">0 Chromebooks selecionados</h2>
        <p>Confira os equipamentos e preencha os dados do professor responsável.</p>
        <div id="selected-device-chips" class="selected-device-list"></div>
        <form id="loan-form">
          <label>
            Professor responsável
            <input id="loan-professor-name" name="name" required placeholder="Digite o nome do professor" />
          </label>
          <div class="form-row">
            <label>
              Turma
              <select name="class" required>
                <option value="" disabled selected>Selecione a turma</option>
                <optgroup label="1º Ano">
                  <option value="1º Ano A — Informática">1º Ano A — Informática</option>
                  <option value="1º Ano B — Informática">1º Ano B — Informática</option>
                  <option value="1º Ano A — Redes">1º Ano A — Redes</option>
                  <option value="1º Ano B — Redes">1º Ano B — Redes</option>
                </optgroup>
                <optgroup label="2º Ano">
                  <option value="2º Ano A — Informática">2º Ano A — Informática</option>
                  <option value="2º Ano B — Informática">2º Ano B — Informática</option>
                  <option value="2º Ano A — Redes">2º Ano A — Redes</option>
                  <option value="2º Ano B — Redes">2º Ano B — Redes</option>
                </optgroup>
                <optgroup label="3º Ano">
                  <option value="3º Ano A — Informática">3º Ano A — Informática</option>
                  <option value="3º Ano B — Informática">3º Ano B — Informática</option>
                  <option value="3º Ano A — Redes">3º Ano A — Redes</option>
                  <option value="3º Ano B — Redes">3º Ano B — Redes</option>
                </optgroup>
              </select>
            </label>
            <label>
              Turno
              <select name="shift" required>
                <option value="" disabled selected>Selecione</option>
                <option value="1 horario">1 horario</option>
                <option value="2 horarios">2 horarios</option>
                <option value="manhã">manhã</option>
                <option value="tarde">tarde</option>
              </select>
            </label>
          </div>
          <button type="submit">
            Confirmar solicitação 
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 12h14M14 7l5 5-5 5" /></svg>
          </button>
          <small>O empréstimo será gravado no banco e o status mudará para Em uso.</small>
        </form>
      </div>
    </div>

    <!-- Modal de Minhas Reservas -->
    <div id="reservations-modal" class="modal-backdrop hidden">
      <div class="modal reservations-modal" role="dialog" aria-modal="true" aria-labelledby="reservations-title">
        <button id="close-reservations-modal" class="modal-close" aria-label="Fechar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m6 6 12 12M18 6 6 18" /></svg>
        </button>
        <span class="modal-kicker">Área do professor</span>
        <h2 id="reservations-title">Minhas reservas</h2>
        <p>Acompanhe os equipamentos solicitados neste dispositivo.</p>
        <div id="reservations-list-container"></div>
      </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast hidden" role="status">
      <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m5 12 4 4L19 6" /></svg></span>
      <span id="toast-text"></span>
    </div>
  </div>

  <script>
    const statusMeta = {
      available: { label: "Disponível", plural: "Disponíveis" },
      "in-use": { label: "Em uso", plural: "Em uso" },
      unavailable: { label: "Indisponível", plural: "Indisponíveis" },
    };

    let inventory = [];
    let lastSnapshot = "";
    let reservedIds = [];

    try {
      const stored = JSON.parse(localStorage.getItem("ceep-reservations") || "[]");
      if (Array.isArray(stored)) reservedIds = stored.map(Number).filter(Number.isFinite);
    } catch {
      localStorage.removeItem("ceep-reservations");
    }

    const params = new URLSearchParams(window.location.search);
    const modeloInicial = params.get("modelo") ? params.get("modelo").replace(/^Modelo\s+/i, "").trim() : "";

    let query = modeloInicial;
    let filter = "all";
    let selectedIds = [];

    const searchInput = document.getElementById("search-input");
    const clearSearchBtn = document.getElementById("clear-search-btn");
    const filterButtons = document.querySelectorAll("#filter-group button");
    const deviceGrid = document.getElementById("device-grid");
    const emptyState = document.getElementById("empty-state");
    const resetFiltersBtn = document.getElementById("reset-filters-btn");
    const bulkRequestBtn = document.getElementById("bulk-request-btn");
    const selectedCountLabel = document.getElementById("selected-count-label");
    const filteredCountLabel = document.getElementById("filtered-count-label");
    const reservedCountBadge = document.getElementById("reserved-count-badge");
    const footerTotalLabel = document.getElementById("footer-total-label");

    const loanModal = document.getElementById("loan-modal");
    const closeLoanModalBtn = document.getElementById("close-loan-modal");
    const loanTitle = document.getElementById("loan-title");
    const selectedDeviceChips = document.getElementById("selected-device-chips");
    const loanForm = document.getElementById("loan-form");
    const professorNameInput = document.getElementById("loan-professor-name");

    const reservationsModal = document.getElementById("reservations-modal");
    const openReservationsBtn = document.getElementById("open-reservations-btn");
    const closeReservationsModalBtn = document.getElementById("close-reservations-modal");
    const reservationsListContainer = document.getElementById("reservations-list-container");

    const toastEl = document.getElementById("toast");
    const toastText = document.getElementById("toast-text");

    if (query) {
      searchInput.value = query;
      clearSearchBtn.classList.remove("hidden");
    }

    function preencherProfessorLogado() {
      try {
        const profSalvo = JSON.parse(localStorage.getItem("professor_logado") || "null");
        if (profSalvo && profSalvo.nome && professorNameInput) {
          professorNameInput.value = profSalvo.nome.trim();
        }
      } catch {}
    }

    preencherProfessorLogado();

    function normalizarStatus(rawStatus) {
      const s = String(rawStatus || "available").toLowerCase().trim();
      if (s === "in-use" || s === "em uso" || s === "em_uso" || s === "ocupado" || s === "emprestado") {
        return "in-use";
      }
      if (s === "unavailable" || s === "indisponivel" || s === "indisponível" || s === "manutencao" || s === "manutenção" || s === "bloqueado") {
        return "unavailable";
      }
      return "available";
    }

    function createChromebookArt(id) {
      const safeKey = String(id).replace(/[^a-zA-Z0-9_-]/g, "");
      const padded = String(id).padStart(3, "0");
      return `
        <svg class="device-art" viewBox="0 0 340 190" role="img" aria-label="Ilustração do Chromebook ${id}">
          <defs>
            <radialGradient id="glow-${safeKey}" cx="65%" cy="32%" r="75%">
              <stop offset="0" stop-color="#3c1766" stop-opacity=".8" />
              <stop offset=".48" stop-color="#170d27" stop-opacity=".75" />
              <stop offset="1" stop-color="#09090d" />
            </radialGradient>
            <linearGradient id="base-${safeKey}" x1="0" x2="0" y1="0" y2="1">
              <stop stop-color="#35323d" />
              <stop offset="1" stop-color="#121116" />
            </linearGradient>
          </defs>
          <ellipse cx="170" cy="170" rx="120" ry="9" fill="#000" opacity=".6" />
          <rect x="73" y="22" width="194" height="123" rx="10" fill="#111016" stroke="#1f1d27" stroke-width="5" />
          <rect x="82" y="31" width="176" height="104" rx="3" fill="url(#glow-${safeKey})" />
          <circle cx="215" cy="67" r="50" fill="none" stroke="#a855f7" stroke-opacity=".13" />
          <circle cx="215" cy="67" r="34" fill="none" stroke="#a855f7" stroke-opacity=".07" />
          <circle cx="160" cy="26.5" r="1.7" fill="#5d5866" />
          <circle cx="101" cy="113" r="2.2" fill="#a855f7" />
          <text x="109" y="116" fill="#a98ec9" font-size="6" font-family="monospace" letter-spacing="1.2">CEEPLG / ${padded}</text>
          <path d="M51 145h238l18 18c3 4 0 8-7 8H40c-7 0-10-4-7-8Z" fill="url(#base-${safeKey})" />
          <path d="M51 145h238l10 11H41Z" fill="#24222b" />
          <path d="M145 158h50l7 7h-64Z" fill="#0b0a0d" />
        </svg>
      `;
    }

    function showToast(msg) {
      toastText.textContent = msg;
      toastEl.classList.remove("hidden");
      clearTimeout(window.__toastTimer);
      window.__toastTimer = setTimeout(() => toastEl.classList.add("hidden"), 4500);
    }

    function updateSelectionUI() {
      bulkRequestBtn.disabled = selectedIds.length === 0;
      selectedCountLabel.textContent = selectedIds.length > 0
        ? `${selectedIds.length} ${selectedIds.length === 1 ? "selecionado" : "selecionados"}`
        : "Selecione os equipamentos";
    }

    async function atualizarStatusNoBanco(id, novoStatus) {
      const res = await fetch("api_chromebooks.php", {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id, id_chromebook: id, status: novoStatus })
      });
      return await res.json();
    }

    window.toggleSelection = function(id) {
      if (selectedIds.includes(id)) {
        selectedIds = selectedIds.filter(item => item !== id);
      } else {
        selectedIds.push(id);
      }
      render();
      if (!loanModal.classList.contains("hidden")) {
        renderLoanModal();
      }
    };

    window.cancelReservation = async function(id) {
      try {
        const resp = await atualizarStatusNoBanco(id, "available");
        if (!resp.sucesso) {
          showToast(resp.mensagem || "Não foi possível cancelar a reserva.");
          return;
        }
        reservedIds = reservedIds.filter(item => item !== id);
        localStorage.setItem("ceep-reservations", JSON.stringify(reservedIds));
        showToast("Reserva cancelada e equipamento liberado.");
        await carregarInventario(true);
        renderReservationsModal();
      } catch (e) {
        console.error(e);
        showToast("Erro de conexão ao cancelar reserva.");
      }
    };

    function render() {
      const counts = {
        available: inventory.filter(i => i.status === "available").length,
        "in-use": inventory.filter(i => i.status === "in-use").length,
        unavailable: inventory.filter(i => i.status === "unavailable").length,
      };

      document.getElementById("count-all").textContent = inventory.length;
      document.getElementById("count-available").textContent = counts.available;
      document.getElementById("count-inuse").textContent = counts["in-use"];
      document.getElementById("count-unavailable").textContent = counts.unavailable;
      footerTotalLabel.textContent = `${inventory.length} ${inventory.length === 1 ? "ativo catalogado" : "ativos catalogados"} · Banco de dados`;

      if (reservedIds.length > 0) {
        reservedCountBadge.textContent = reservedIds.length;
        reservedCountBadge.classList.remove("hidden");
      } else {
        reservedCountBadge.classList.add("hidden");
      }

      const normalized = query.trim().toLowerCase();
      const filtered = inventory.filter(item => {
        const matches =
          item.name.toLowerCase().includes(normalized) ||
          item.patrimonio.toLowerCase().includes(normalized) ||
          item.modelo.toLowerCase().includes(normalized) ||
          String(item.id).includes(normalized);
        return matches && (filter === "all" || item.status === filter);
      });

      filteredCountLabel.textContent = `${filtered.length} ${filtered.length === 1 ? "equipamento encontrado" : "equipamentos encontrados"}`;
      updateSelectionUI();

      if (filtered.length === 0) {
        deviceGrid.innerHTML = "";
        emptyState.classList.remove("hidden");
        return;
      }

      emptyState.classList.add("hidden");
      deviceGrid.innerHTML = filtered.map(item => {
        const isSelected = selectedIds.includes(item.id);
        const isAvailable = item.status === "available";
        const noteText = isSelected
          ? "Selecionado para empréstimo"
          : item.status === "available"
            ? `Disponível (${item.modelo})`
            : item.status === "in-use"
              ? "Equipamento em uso"
              : "Manutenção ou bloqueio";

        return `
          <article class="device-card ${isSelected ? "selected" : ""}">
            <div class="card-selection-row">
              <span class="asset-number">#${String(item.id).padStart(3, "0")}</span>
              <label class="device-checkbox ${!isAvailable ? "disabled" : ""}" title="${isAvailable ? `Selecionar ${item.name}` : "Equipamento indisponível para seleção"}">
                <input type="checkbox" ${isSelected ? "checked" : ""} ${!isAvailable ? "disabled" : ""} onchange="toggleSelection(${item.id})" aria-label="Selecionar ${item.name}" />
                <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" /></svg></span>
              </label>
            </div>
            <div class="device-visual">
              ${createChromebookArt(item.id)}
            </div>
            <div class="device-info">
              <div class="device-title-row">
                <h2>${item.name}</h2>
                <span class="status-pill ${item.status}"><i></i>${statusMeta[item.status].label}</span>
              </div>
              <p>${item.patrimonio} · ${item.modelo}</p>
              <div class="card-note ${item.status} ${isSelected ? "selected" : ""}">${noteText}</div>
            </div>
          </article>
        `;
      }).join("");
    }

    function renderLoanModal() {
      const selectedDevices = inventory.filter(i => selectedIds.includes(i.id));
      if (selectedDevices.length === 0) {
        loanModal.classList.add("hidden");
        return;
      }
      loanTitle.textContent = `${selectedDevices.length} ${selectedDevices.length === 1 ? "Chromebook selecionado" : "Chromebooks selecionados"}`;
      selectedDeviceChips.innerHTML = selectedDevices.map(item => `
        <span>
          ${item.patrimonio}
          <button type="button" onclick="toggleSelection(${item.id})" aria-label="Remover ${item.name}">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18" /></svg>
          </button>
        </span>
      `).join("");
    }

    function renderReservationsModal() {
      const reservations = inventory.filter(i => reservedIds.includes(i.id));
      if (reservations.length === 0) {
        reservationsListContainer.innerHTML = `
          <div class="no-reservations">
            <svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="5" y="4" width="14" height="11" rx="1.5" /><path d="M3 19h18M8 19l1-4M16 19l-1-4" /></svg>
            <strong>Nenhuma reserva</strong>
            <span>Seus pedidos aparecerão aqui.</span>
          </div>
        `;
        return;
      }
      reservationsListContainer.innerHTML = `
        <div class="reservation-list">
          ${reservations.map(item => `
            <div class="reservation-item">
              <span class="mini-device"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="5" y="4" width="14" height="11" rx="1.5" /><path d="M3 19h18M8 19l1-4M16 19l-1-4" /></svg></span>
              <div>
                <strong>${item.name}</strong>
                <small>${item.patrimonio} · Em uso</small>
              </div>
              <button type="button" onclick="cancelReservation(${item.id})">Cancelar</button>
            </div>
          `).join("")}
        </div>
      `;
    }

    async function carregarInventario(forcarRender = false) {
      try {
        const res = await fetch("api_chromebooks.php", {
          cache: "no-store",
          credentials: "same-origin"
        });
        const data = await res.json();
        if (!data.sucesso || !Array.isArray(data.dados)) return;

        const novoSnapshot = JSON.stringify(data.dados);
        if (!forcarRender && novoSnapshot === lastSnapshot) return;
        lastSnapshot = novoSnapshot;

        inventory = data.dados.map(row => ({
          id: Number(row.id),
          name: row.name || row.patrimonio || `Chromebook ${row.id}`,
          patrimonio: row.patrimonio || `CEEP-${row.id}`,
          modelo: row.modelo || "Padrão",
          status: normalizarStatus(row.status)
        }));

        const idsDisponiveis = new Set(inventory.filter(i => i.status === "available").map(i => i.id));
        selectedIds = selectedIds.filter(id => idsDisponiveis.has(id));

        render();
        if (!reservationsModal.classList.contains("hidden")) {
          renderReservationsModal();
        }
      } catch (erro) {
        console.error("Erro ao sincronizar inventário:", erro);
      }
    }

    // Event Listeners
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

    filterButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        filterButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        filter = btn.getAttribute("data-filter");
        render();
      });
    });

    resetFiltersBtn.addEventListener("click", () => {
      searchInput.value = "";
      query = "";
      clearSearchBtn.classList.add("hidden");
      filter = "all";
      filterButtons.forEach(b => b.classList.toggle("active", b.getAttribute("data-filter") === "all"));
      render();
    });

    bulkRequestBtn.addEventListener("click", () => {
      if (selectedIds.length === 0) return;
      renderLoanModal();
      loanModal.classList.remove("hidden");
    });

    closeLoanModalBtn.addEventListener("click", () => loanModal.classList.add("hidden"));
    loanModal.addEventListener("mousedown", (e) => { if (e.target === loanModal) loanModal.classList.add("hidden"); });

    loanForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (!selectedIds.length) return;

      const formData = new FormData(loanForm);
      const idsParaReservar = [...selectedIds];
      const payload = {
        ids: idsParaReservar,
        chromebooks: idsParaReservar,
        professor: String(formData.get("name") || "").trim(),
        turma: String(formData.get("class") || "").trim(),
        turno: String(formData.get("shift") || "").trim()
      };

      const submitBtn = loanForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;

      try {
        const res = await fetch("api_emprestimos.php", {
          method: "POST",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const resp = await res.json();

        if (!res.ok || resp.sucesso === false) {
          showToast(resp.mensagem || "Não foi possível registrar o empréstimo.");
          return;
        }

        // Atualiza imediatamente para "in-use" e persiste seleção em localStorage
        const reservadosSet = new Set(idsParaReservar);
        inventory = inventory.map(item =>
          reservadosSet.has(item.id) ? { ...item, status: "in-use" } : item
        );

        reservedIds = [...new Set([...reservedIds, ...idsParaReservar])];
        localStorage.setItem("ceep-reservations", JSON.stringify(reservedIds));

        showToast(`${idsParaReservar.length} ${idsParaReservar.length === 1 ? "empréstimo registrado" : "empréstimos registrados"} para ${payload.turma}`);
        selectedIds = [];
        loanForm.reset();
        preencherProfessorLogado();
        loanModal.classList.add("hidden");

        lastSnapshot = "";
        render();
        await carregarInventario(true);
      } catch (err) {
        console.error(err);
        showToast("Erro de conexão ao registrar empréstimo.");
      } finally {
        submitBtn.disabled = false;
      }
    });

    openReservationsBtn.addEventListener("click", () => {
      renderReservationsModal();
      reservationsModal.classList.remove("hidden");
    });

    closeReservationsModalBtn.addEventListener("click", () => reservationsModal.classList.add("hidden"));
    reservationsModal.addEventListener("mousedown", (e) => { if (e.target === reservationsModal) reservationsModal.classList.add("hidden"); });

    // Inicialização + Atualização automática sem F5
    carregarInventario(true);
    setInterval(() => {
      if (!document.hidden) carregarInventario(false);
    }, 10000);
    document.addEventListener("visibilitychange", () => {
      if (!document.hidden) carregarInventario(false);
    });
  </script>
</body>
</html>