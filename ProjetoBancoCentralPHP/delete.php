<?php
// delete.php

include 'conexao.php'; // ajuste o caminho conforme sua estrutura

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("Parâmetros insuficientes.");
}

$type = $_GET['type'];
$id = intval($_GET['id']);

try {
    switch ($type) {
        case 'customer':
            // Excluir empréstimos e dependências dos empréstimos
            // Busca todos os empréstimos do cliente
            $loans = $pdo->prepare("SELECT id FROM loan WHERE customer_id = ?");
            $loans->execute([$id]);
            $loanIds = $loans->fetchAll(PDO::FETCH_COLUMN);

            foreach ($loanIds as $loanId) {
                // Excluir parcelas
                $pdo->prepare("DELETE FROM installment WHERE loan_id = ?")->execute([$loanId]);
                // Excluir contratos
                $pdo->prepare("DELETE FROM contract WHERE loan_id = ?")->execute([$loanId]);
                // Excluir empréstimo
                $pdo->prepare("DELETE FROM loan WHERE id = ?")->execute([$loanId]);
            }

            // Agora pode excluir o cliente
            $stmt = $pdo->prepare("DELETE FROM customer WHERE id = ?");
            $stmt->execute([$id]);
            break;

        case 'loan':
            // Excluir parcelas
            $pdo->prepare("DELETE FROM installment WHERE loan_id = ?")->execute([$id]);
            // Excluir contratos
            $pdo->prepare("DELETE FROM contract WHERE loan_id = ?")->execute([$id]);
            // Excluir empréstimo
            $pdo->prepare("DELETE FROM loan WHERE id = ?")->execute([$id]);
            break;

        case 'guarantor':
            // Excluir contratos que usam o fiador
            $pdo->prepare("DELETE FROM contract WHERE guarantor_id = ?")->execute([$id]);
            // Excluir fiador
            $pdo->prepare("DELETE FROM guarantor WHERE id = ?")->execute([$id]);
            break;

        case 'contract':
            $pdo->prepare("DELETE FROM contract WHERE id = ?")->execute([$id]);
            break;

        case 'installment':
            $pdo->prepare("DELETE FROM installment WHERE id = ?")->execute([$id]);
            break;

        default:
            die("Tipo inválido para exclusão.");
    }

    header("Location: relatorios/relatorio.php");
    exit;

} catch (PDOException $e) {
    die("Erro ao deletar: " . $e->getMessage());
}
