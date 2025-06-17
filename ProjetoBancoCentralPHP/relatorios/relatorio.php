<?php
include '../conexao.php';

$nomePesquisado = isset($_GET['nome']) ? $_GET['nome'] : '';
$clientes = [];
if ($nomePesquisado) {
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE name LIKE ?");
    $stmt->execute(["%$nomePesquisado%"]);
    $clientes = $stmt->fetchAll();
} else {
    $clientes = $pdo->query("SELECT * FROM customer")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios do Sistema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 960px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            text-align: center;
            margin-bottom: 30px;
        }

        form input[type="text"] {
            padding: 8px;
            width: 250px;
            margin-right: 8px;
        }

        form button {
            padding: 8px 12px;
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }

        form a {
            margin-left: 10px;
            color: #555;
            text-decoration: none;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        a.excluir {
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        a.editar {
            color: green;
            font-weight: bold;
            text-decoration: none;
            margin-right: 10px;
        }

        a.excluir:hover, a.editar:hover {
            text-decoration: underline;
        }

        .back-link {
            display: block;
            margin-top: 40px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Relatórios do Simulador de Empréstimos</h1>

        <form method="get">
            <input type="text" name="nome" placeholder="Pesquisar cliente" value="<?= htmlspecialchars($nomePesquisado) ?>">
            <button type="submit">🔍 Buscar</button>
            <a href="relatorio.php">Limpar</a>
        </form>

        <!-- Clientes -->
        <h2>👤 Clientes</h2>
        <table>
            <tr><th>ID</th><th>Nome</th><th>Email</th><th>Ações</th></tr>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente['id'] ?></td>
                    <td><?= htmlspecialchars($cliente['name']) ?></td>
                    <td><?= htmlspecialchars($cliente['email']) ?></td>
                    <td>
                        <a href='/ProjetoBancoCentralPHP/edit.php?type=customer&id=<?= $cliente['id'] ?>' class='editar'>Editar</a>
                        <a href='/ProjetoBancoCentralPHP/delete.php?type=customer&id=<?= $cliente['id'] ?>' class='excluir' onclick="return confirm('Excluir cliente <?= htmlspecialchars($cliente['name']) ?>?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Empréstimos -->
        <h2>💰 Empréstimos</h2>
        <table>
            <tr><th>ID</th><th>Cliente</th><th>Valor</th><th>Juros (%)</th><th>Meses</th><th>Início</th><th>Ações</th></tr>
            <?php
            $emprestimos = [];
            if (!empty($clientes)) {
                $cliente_ids = array_column($clientes, 'id');
                $placeholders = implode(',', array_fill(0, count($cliente_ids), '?'));
                $stmt = $pdo->prepare("SELECT l.*, c.name AS cliente FROM loan l LEFT JOIN customer c ON c.id = l.customer_id WHERE l.customer_id IN ($placeholders)");
                $stmt->execute($cliente_ids);
                $emprestimos = $stmt->fetchAll();
            } else {
                $emprestimos = $pdo->query("SELECT l.*, c.name AS cliente FROM loan l LEFT JOIN customer c ON c.id = l.customer_id")->fetchAll();
            }
            $emprestimos_ids = [];
            foreach ($emprestimos as $e):
                $emprestimos_ids[] = $e['id'];
            ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['cliente']) ?></td>
                    <td>R$ <?= number_format($e['amount'], 2, ',', '.') ?></td>
                    <td><?= $e['interest_rate'] ?></td>
                    <td><?= $e['months'] ?></td>
                    <td><?= $e['start_date'] ?></td>
                    <td>
                        <a href='/ProjetoBancoCentralPHP/edit.php?type=loan&id=<?= $e['id'] ?>' class='editar'>Editar</a>
                        <a href='/ProjetoBancoCentralPHP/delete.php?type=loan&id=<?= $e['id'] ?>' class='excluir' onclick="return confirm('Excluir empréstimo do cliente <?= htmlspecialchars($e['cliente']) ?>?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Contratos -->
        <h2>📄 Contratos</h2>
        <table>
            <tr><th>ID</th><th>Data de Assinatura</th><th>Termos</th><th>Empréstimo</th><th>Fiador</th><th>Ações</th></tr>
            <?php
            $contratos = [];
            if (!empty($emprestimos_ids)) {
                $placeholders = implode(',', array_fill(0, count($emprestimos_ids), '?'));
                $stmt = $pdo->prepare("SELECT c.id, c.signed_date, c.terms, l.id AS loan_id, g.name AS guarantor FROM contract c LEFT JOIN loan l ON l.id = c.loan_id LEFT JOIN guarantor g ON g.id = c.guarantor_id WHERE c.loan_id IN ($placeholders)");
                $stmt->execute($emprestimos_ids);
                $contratos = $stmt->fetchAll();
            } else {
                $contratos = $pdo->query("SELECT c.id, c.signed_date, c.terms, l.id AS loan_id, g.name AS guarantor FROM contract c LEFT JOIN loan l ON l.id = c.loan_id LEFT JOIN guarantor g ON g.id = c.guarantor_id")->fetchAll();
            }
            foreach ($contratos as $c):
            ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= $c['signed_date'] ?></td>
                    <td><?= htmlspecialchars($c['terms']) ?></td>
                    <td><?= $c['loan_id'] ?></td>
                    <td><?= htmlspecialchars($c['guarantor']) ?></td>
                    <td>
                        <a href='/ProjetoBancoCentralPHP/edit.php?type=contract&id=<?= $c['id'] ?>' class='editar'>Editar</a>
                        <a href='/ProjetoBancoCentralPHP/delete.php?type=contract&id=<?= $c['id'] ?>' class='excluir' onclick="return confirm('Excluir contrato ID <?= $c['id'] ?>?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Parcelas -->
        <h2>📆 Parcelas</h2>
        <table>
            <tr><th>ID</th><th>Mês</th><th>Principal</th><th>Juros (R$ e %)</th><th>Saldo</th><th>Vencimento</th><th>Empréstimo</th><th>Parcela Total</th><th>Ações</th></tr>
            <?php
            $parcelas = [];
            if (!empty($emprestimos_ids)) {
                $placeholders = implode(',', array_fill(0, count($emprestimos_ids), '?'));
                $stmt = $pdo->prepare("SELECT i.*, l.interest_rate FROM installment i LEFT JOIN loan l ON l.id = i.loan_id WHERE i.loan_id IN ($placeholders)");
                $stmt->execute($emprestimos_ids);
                $parcelas = $stmt->fetchAll();
            } else {
                $parcelas = $pdo->query("SELECT i.*, l.interest_rate FROM installment i LEFT JOIN loan l ON l.id = i.loan_id")->fetchAll();
            }
            foreach ($parcelas as $p):
                $total = $p['principal'] + $p['interest'];
            ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['month_number'] ?></td>
                    <td>R$ <?= number_format($p['principal'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($p['interest'], 2, ',', '.') ?> (<?= number_format($p['interest_rate'], 2, ',', '.') ?> %)</td>
                    <td>R$ <?= number_format($p['balance'], 2, ',', '.') ?></td>
                    <td><?= $p['due_date'] ?></td>
                    <td><?= $p['loan_id'] ?></td>
                    <td>R$ <?= number_format($total, 2, ',', '.') ?></td>
                    <td>
                        <a href='/ProjetoBancoCentralPHP/edit.php?type=installment&id=<?= $p['id'] ?>' class='editar'>Editar</a>
                        <a href='/ProjetoBancoCentralPHP/delete.php?type=installment&id=<?= $p['id'] ?>' class='excluir' onclick="return confirm('Excluir parcela ID <?= $p['id'] ?>?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="back-link">
            <a href="../index.php">⬅ Voltar ao início</a>
        </div>
    </div>
</body>
</html>
