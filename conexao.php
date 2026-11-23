<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();

mysqli_report(MYSQLI_REPORT_OFF);

$host    = "localhost";
$usuario = "aluno";
$senha   = "ceep@1234";
$banco   = "Armazem_Ribeira";

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    error_log("Erro MySQL: " . $conexao->connect_error);

    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível conectar ao banco de dados."
    ]);

    exit;
}

if (!$conexao->set_charset("utf8mb4")) {
    error_log("Erro ao definir charset: " . $conexao->error);
    http_response_code(500);
    exit;
}
?>