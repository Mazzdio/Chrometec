<?php
/** Utilitários centrais de sessão e autorização do Portal CEEP. */
function iniciar_sessao_segura(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
}

function usuario_autenticado(): bool {
    return !empty($_SESSION['id_usuario']) && !empty($_SESSION['status']);
}

function exigir_login(string $formato = 'json'): void {
    if (!usuario_autenticado()) {
        http_response_code(401);
        if ($formato === 'pagina') {
            header('Location: login_aluno.html');
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'autenticado' => false, 'mensagem' => 'Sessão encerrada. Faça login novamente.'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}

function exigir_perfil(string $perfil): void {
    iniciar_sessao_segura();
    if (!usuario_autenticado()) {
        header('Location: login_aluno.html', true, 302);
        exit;
    }
    if ($_SESSION['status'] !== $perfil) {
        $destinos = [
            'Aluno' => 'acessoaluno.php',
            'Professor' => 'acessoprofessor.php',
            'Administrador' => 'emprestimo_adm.php'
        ];
        header('Location: ' . ($destinos[$_SESSION['status']] ?? 'login_aluno.html'), true, 302);
        exit;
    }
}

function exigir_administrador(): void {
    iniciar_sessao_segura();
    if (!usuario_autenticado() || $_SESSION['status'] !== 'Administrador') {
        http_response_code(empty($_SESSION['id_usuario']) ? 401 : 403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso restrito a administradores.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>