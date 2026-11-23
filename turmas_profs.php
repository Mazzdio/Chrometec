<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();

if (!usuario_autenticado() || !in_array($_SESSION['status'] ?? '', ['Professor', 'Administrador'], true)) {
    header('Location: login_professor.html');
    exit;
}

$nomeProfessor = htmlspecialchars($_SESSION['nome'] ?? 'Professor', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ceep.plg — Alunos da Turma</title>
  <style>
    :root {
      color-scheme: dark;
      --bg: #09090b;
      --surface: #121217;
      --border: rgba(255, 255, 255, 0.08);
      --border-soft: rgba(255, 255, 255, 0.05);
      --text: #f4f4f5;
      --muted: #71717a;
      --accent: #7c3aed;
      --accent-hover: #8b5cf6;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
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

    .prof-pill {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 0.95rem;
      border-radius: 12px;
      border: 1px solid rgba(139, 92, 246, 0.25);
      background: rgba(124, 58, 237, 0.12);
      color: #ddd6fe;
      font-size: 0.8rem;
      font-weight: 600;
    }

    /* Container principal */
    .container {
      max-width: 1500px;
      margin: 0 auto;
      padding: 2.5rem 2rem 4rem;
    }

    .page-header {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-end;
      justify-content: space-between;
      gap: 1.25rem;
      margin-bottom: 1.75rem;
    }
    .page-header span {
      display: block;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      color: #a78bfa;
      margin-bottom: 0.35rem;
    }
    .page-header h1 { font-size: 1.85rem; font-weight: 600; letter-spacing: -0.03em; color: #fff; }

    .turma-select-wrap {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      min-width: 260px;
    }
    .turma-select-wrap label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      color: var(--muted);
    }
    .turma-select-wrap select {
      height: 42px;
      padding: 0 0.9rem;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: var(--surface);
      color: #fff;
      font-size: 0.85rem;
      font-family: inherit;
      outline: none;
      cursor: pointer;
    }
    .turma-select-wrap select:focus { border-color: #8b5cf6; }

    /* Painel de Alunos */
    .students-panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 1.5rem;
    }
    .students-toolbar {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding-bottom: 1.25rem;
      margin-bottom: 1.25rem;
      border-bottom: 1px solid var(--border-soft);
    }
    .students-toolbar h2 { font-size: 1.2rem; font-weight: 600; color: #fff; }
    .students-toolbar span { font-size: 0.78rem; color: var(--muted); }

    .toolbar-actions {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.75rem;
    }
    .search-box {
      position: relative;
      min-width: 260px;
    }
    .search-box svg {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
    }
    .search-box input {
      width: 100%;
      height: 40px;
      padding: 0 0.9rem 0 2.3rem;
      border-radius: 11px;
      border: 1px solid var(--border);
      background: #0d0d11;
      color: #fff;
      font-size: 0.83rem;
      outline: none;
    }
    .search-box input:focus { border-color: #8b5cf6; }

    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      height: 40px;
      padding: 0 1rem;
      border-radius: 11px;
      border: 1px solid rgba(139, 92, 246, 0.35);
      background: var(--accent);
      color: #fff;
      text-decoration: none;
      font-size: 0.8rem;
      font-weight: 600;
      transition: background 0.2s;
    }
    .btn-action:hover { background: var(--accent-hover); }

    /* Tabela de Alunos */
    .table-wrap { overflow-x: auto; }
    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }
    th {
      padding: 0.75rem 1rem;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      color: var(--muted);
      border-bottom: 1px solid var(--border-soft);
    }
    td {
      padding: 0.9rem 1rem;
      font-size: 0.85rem;
      color: #e4e4e7;
      border-bottom: 1px solid var(--border-soft);
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: rgba(255, 255, 255, 0.02); }

    .student-cell {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .avatar {
      display: grid;
      place-items: center;
      width: 34px;
      height: 34px;
      border-radius: 10px;
      background: rgba(124, 58, 237, 0.18);
      color: #c4b5fd;
      font-size: 0.75rem;
      font-weight: 700;
      flex-shrink: 0;
    }
    .student-cell strong { display: block; font-size: 0.86rem; color: #fff; text-transform: capitalize; }
    .student-cell small { display: block; font-size: 0.73rem; color: var(--muted); }

    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.8rem; color: #a1a1aa; }

    .status-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.22rem 0.6rem;
      border-radius: 999px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .status-tag.ok {
      background: rgba(52, 211, 153, 0.1);
      color: #6ee7b7;
      border: 1px solid rgba(52, 211, 153, 0.2);
    }
    .status-tag.in-use {
      background: rgba(251, 191, 36, 0.1);
      color: #fcd34d;
      border: 1px solid rgba(251, 191, 36, 0.2);
    }

    .empty-box {
      text-align: center;
      padding: 3rem 1rem;
      color: var(--muted);
      font-size: 0.85rem;
    }

    @media (max-width: 640px) {
      .topbar { padding: 0 1rem; }
      .topbar nav { display: none; }
      .container { padding: 1.5rem 1rem 3rem; }
    }
  </style>
</head>
<body>
  <header class="topbar">
    <a class="brand" href="acessoprofessor.php">
      <span class="brand-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </span>
      <span class="brand-copy">
        <strong>ceep.plg</strong>
        <small>Gestão escolar</small>
      </span>
    </a>
    <nav>
      <a href="acessoprofessor.php">Visão geral</a>
      <a class="active" href="turmas_profs.php">Minhas turmas</a>
      <a href="emprestimos_profs.html">Chromebooks</a>
    </nav>
    <div class="prof-pill">
      <span>Prof. <?= $nomeProfessor ?></span>
    </div>
  </header>

  <main class="container">
    <div class="page-header">
      <div>
        <span>Organização escolar</span>
        <h1>Alunos da Turma</h1>
      </div>

      <div class="turma-select-wrap">
        <label for="select-turma">Selecionar turma</label>
        <select id="select-turma">
          <option value="">Carregando turmas...</option>
        </select>
      </div>
    </div>

    <section class="students-panel">
      <div class="students-toolbar">
        <div>
          <h2 id="selected-turma-title">Carregando turma...</h2>
          <span id="selected-turma-subtitle">Buscando estudantes...</span>
        </div>
        <div class="toolbar-actions">
          <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <input id="student-search" type="text" placeholder="Buscar por nome ou matrícula..." />
          </div>
          <a class="btn-action" href="emprestimos_profs.html">
            Reservar Chromebooks
          </a>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Aluno</th>
              <th>Matrícula</th>
              <th>Turma</th>
              <th>Telefone</th>
              <th>Equipamento</th>
            </tr>
          </thead>
          <tbody id="students-tbody">
            <tr><td colspan="5" class="empty-box">Carregando alunos...</td></tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    let todasTurmas = [];
    let todosAlunos = [];
    let turmaSelecionadaId = null;
    let buscaAluno = "";

    const params = new URLSearchParams(window.location.search);
    const paramIdTurma = params.get("id_turma") ? Number(params.get("id_turma")) : null;
    const paramNomeTurma = (params.get("turma") || "").trim().toLowerCase();

    const selectTurma = document.getElementById("select-turma");
    const studentsTbody = document.getElementById("students-tbody");
    const selectedTurmaTitle = document.getElementById("selected-turma-title");
    const selectedTurmaSubtitle = document.getElementById("selected-turma-subtitle");
    const studentSearchInput = document.getElementById("student-search");

    function iniciais(nome) {
      const partes = String(nome || "").trim().split(/\s+/).filter(Boolean);
      if (partes.length === 0) return "AL";
      if (partes.length === 1) return partes[0].slice(0, 2).toUpperCase();
      return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
    }

    function preencherSelectTurmas() {
      selectTurma.innerHTML = todasTurmas.map(t =>
        `<option value="${t.id_turma}" ${t.id_turma === turmaSelecionadaId ? "selected" : ""}>${t.nome_turma}</option>`
      ).join("");
    }

    function renderAlunos() {
      const turmaObj = todasTurmas.find(t => t.id_turma === turmaSelecionadaId) || null;
      selectedTurmaTitle.textContent = turmaObj ? turmaObj.nome_turma : "Turma não encontrada";

      const termo = buscaAluno.trim().toLowerCase();
      const lista = todosAlunos.filter(a => {
        if (turmaSelecionadaId !== null && a.id_turma !== turmaSelecionadaId) return false;
        if (!termo) return true;
        return (
          a.nome.toLowerCase().includes(termo) ||
          String(a.matricula).toLowerCase().includes(termo) ||
          String(a.email).toLowerCase().includes(termo)
        );
      });

      selectedTurmaSubtitle.textContent = `${lista.length} ${lista.length === 1 ? "estudante na turma" : "estudantes na turma"}`;

      if (lista.length === 0) {
        studentsTbody.innerHTML = `<tr><td colspan="5" class="empty-box">Nenhum aluno encontrado nesta turma.</td></tr>`;
        return;
      }

      studentsTbody.innerHTML = lista.map(a => `
        <tr>
          <td>
            <div class="student-cell">
              <span class="avatar">${iniciais(a.nome)}</span>
              <div>
                <strong>${a.nome.toLowerCase()}</strong>
                <small>${a.email}</small>
              </div>
            </div>
          </td>
          <td class="mono">${a.matricula}</td>
          <td>${a.nome_turma}</td>
          <td class="mono">${a.telefone}</td>
          <td>
            ${a.com_chromebook
              ? `<span class="status-tag in-use">Em uso (${a.chromebook_patrimonio || "Chromebook"})</span>`
              : `<span class="status-tag ok">Sem pendência</span>`
            }
          </td>
        </tr>
      `).join("");
    }

    selectTurma.addEventListener("change", (e) => {
      turmaSelecionadaId = Number(e.target.value) || null;
      renderAlunos();
    });

    studentSearchInput.addEventListener("input", (e) => {
      buscaAluno = e.target.value;
      renderAlunos();
    });

    async function carregarDados() {
      try {
        const res = await fetch("api_turmas_alunos.php", {
          cache: "no-store",
          credentials: "same-origin"
        });
        const data = await res.json();
        if (!data.sucesso) {
          studentsTbody.innerHTML = `<tr><td colspan="5" class="empty-box">${data.mensagem || "Erro ao carregar dados."}</td></tr>`;
          return;
        }

        todasTurmas = data.turmas || [];
        todosAlunos = data.alunos || [];

        if (paramIdTurma && todasTurmas.some(t => t.id_turma === paramIdTurma)) {
          turmaSelecionadaId = paramIdTurma;
        } else if (paramNomeTurma) {
          const achada = todasTurmas.find(t => t.nome_turma.toLowerCase() === paramNomeTurma);
          if (achada) turmaSelecionadaId = achada.id_turma;
        }

        if (turmaSelecionadaId === null && todasTurmas.length > 0) {
          turmaSelecionadaId = todasTurmas[0].id_turma;
        }

        preencherSelectTurmas();
        renderAlunos();
      } catch (err) {
        console.error(err);
        studentsTbody.innerHTML = `<tr><td colspan="5" class="empty-box">Erro de conexão com o servidor.</td></tr>`;
      }
    }

    carregarDados();
  </script>
</body>
</html>