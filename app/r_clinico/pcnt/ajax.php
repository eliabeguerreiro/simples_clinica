<?php
session_start();
include_once "paciente.class.php";

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['data_user'])) {
    echo json_encode(['sucesso' => false, 'erros' => ['Sessão expirada. Faça login novamente.']]);
    exit;
}

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$pacienteModel = new Paciente();

try {
    switch ($acao) {
        case 'cadastrar':
            $resultado = $pacienteModel->cadastrar($_POST);
            echo json_encode($resultado);
            break;
            
        case 'atualizar':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception("ID do paciente inválido.");
            }
            $resultado = $pacienteModel->atualizar($id, $_POST);
            echo json_encode($resultado);
            break;
            
        case 'excluir':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception("ID do paciente inválido.");
            }
            $resultado = $pacienteModel->excluir($id);
            echo json_encode($resultado);
            break;
            
        case 'excluir_multiplos':
            $ids = $_POST['ids'] ?? [];
            if (empty($ids) || !is_array($ids)) {
                throw new Exception("Nenhum paciente selecionado.");
            }
            $sucessos = 0;
            $erros = [];
            foreach ($ids as $id) {
                $id = (int)$id;
                if ($id > 0) {
                    $resultado = $pacienteModel->excluir($id);
                    if ($resultado['sucesso']) {
                        $sucessos++;
                    } else {
                        $erros[] = "Erro ao excluir paciente ID $id: " . implode(', ', $resultado['erros'] ?? []);
                    }
                }
            }
            if (empty($erros)) {
                echo json_encode(['sucesso' => true, 'mensagem' => "$sucessos paciente(s) excluído(s) com sucesso!"]);
            } else {
                echo json_encode(['sucesso' => $sucessos > 0, 'mensagem' => "$sucessos paciente(s) excluído(s).", 'erros' => $erros]);
            }
            break;
            
        default:
            echo json_encode(['sucesso' => false, 'erros' => ['Ação não reconhecida.']]);
    }
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erros' => [$e->getMessage()]]);
}
?>