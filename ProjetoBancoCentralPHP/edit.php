<?php
include 'conexao.php';

// Salvar alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $id = $_POST['id'];

    switch ($type) {
        case 'customer':
            $sql = "UPDATE customer SET name = ?, email = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['name'], $_POST['email'], $id]);
            break;

        case 'guarantor':
            $sql = "UPDATE guarantor SET name = ?, cpf = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['name'], $_POST['cpf'], $id]);
            break;

        case 'loan':
            $sql = "UPDATE loan SET amount = ?, interest_rate = ?, months = ?, start_date = ?, customer_id = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['amount'], $_POST['interest_rate'], $_POST['months'], $_POST['start_date'], $_POST['customer_id'], $id]);
            break;

        case 'contract':
            $sql = "UPDATE contract SET signed_date = ?, terms = ?, guarantor_id = ?, loan_id = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['signed_date'], $_POST['terms'], $_POST['guarantor_id'], $_POST['loan_id'], $id]);
            break;

        case 'installment':
            $sql = "UPDATE installment SET month_number = ?, principal = ?, interest = ?, balance = ?, due_date = ?, loan_id = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['month_number'], $_POST['principal'], $_POST['interest'],
                $_POST['balance'], $_POST['due_date'], $_POST['loan_id'], $id
            ]);
            break;

        default:
            echo "Tipo de entidade não suportado para edição.";
            exit;
    }

    header("Location: relatorios/relatorio.php");
    exit;
}

// Se não for POST, mostrar formulário
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    echo "Parâmetros inválidos.";
    exit;
}

$type = $_GET['type'];
$id = $_GET['id'];

switch ($type) {
    case 'customer':
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        break;

    case 'guarantor':
        $stmt = $pdo->prepare("SELECT * FROM guarantor WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        break;

    case 'loan':
        $stmt = $pdo->prepare("SELECT * FROM loan WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        break;

    case 'contract':
        $stmt = $pdo->prepare("SELECT * FROM contract WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        break;

    case 'installment':
        $stmt = $pdo->prepare("SELECT * FROM installment WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        break;

    default:
        echo "Tipo não suportado para edição.";
        exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar <?= ucfirst($type) ?></title>
</head>
<body>
    <h1>Editar <?= ucfirst($type) ?></h1>
    <form method="post" action="edit.php">
        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <?php if ($type === 'customer'): ?>
            <label>Nome:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>"><br>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>"><br>

        <?php elseif ($type === 'guarantor'): ?>
            <label>Nome:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>"><br>
            <label>CPF:</label><br>
            <input type="text" name="cpf" value="<?= htmlspecialchars($data['cpf'] ?? '') ?>"><br>

        <?php elseif ($type === 'loan'): ?>
            <label>Valor:</label><br>
            <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars($data['amount'] ?? '0.00') ?>"><br>
            <label>Juros (%):</label><br>
            <input type="number" name="interest_rate" step="0.01" value="<?= htmlspecialchars($data['interest_rate'] ?? '0.00') ?>"><br>
            <label>Meses:</label><br>
            <input type="number" name="months" value="<?= htmlspecialchars($data['months'] ?? '') ?>"><br>
            <label>Data Início:</label><br>
            <input type="date" name="start_date" value="<?= htmlspecialchars($data['start_date'] ?? '') ?>"><br>
            <label>ID Cliente:</label><br>
            <input type="number" name="customer_id" value="<?= htmlspecialchars($data['customer_id'] ?? '') ?>"><br>

        <?php elseif ($type === 'contract'): ?>
            <label>Data de Assinatura:</label><br>
            <input type="date" name="signed_date" value="<?= htmlspecialchars($data['signed_date'] ?? '') ?>"><br>
            <label>Termos:</label><br>
            <textarea name="terms"><?= htmlspecialchars($data['terms'] ?? '') ?></textarea><br>
            <label>ID Fiador:</label><br>
            <input type="number" name="guarantor_id" value="<?= htmlspecialchars($data['guarantor_id'] ?? '') ?>"><br>
            <label>ID Empréstimo:</label><br>
            <input type="number" name="loan_id" value="<?= htmlspecialchars($data['loan_id'] ?? '') ?>"><br>

        <?php elseif ($type === 'installment'): ?>
            <label>Mês:</label><br>
            <input type="number" name="month_number" value="<?= htmlspecialchars($data['month_number'] ?? '') ?>"><br>
            <label>Principal:</label><br>
            <input type="number" step="0.01" name="principal" value="<?= htmlspecialchars($data['principal'] ?? '0.00') ?>"><br>
            <label>Juros (%):</label><br>
            <input type="number" step="0.01" name="interest" value="<?= htmlspecialchars($data['interest'] ?? '0.00') ?>"><br>
            <label>Saldo:</label><br>
            <input type="number" step="0.01" name="balance" value="<?= htmlspecialchars($data['balance'] ?? '0.00') ?>"><br>
            <label>Vencimento:</label><br>
            <input type="date" name="due_date" value="<?= htmlspecialchars($data['due_date'] ?? '') ?>"><br>
            <label>ID Empréstimo:</label><br>
            <input type="number" name="loan_id" value="<?= htmlspecialchars($data['loan_id'] ?? '') ?>"><br>
        <?php endif; ?>

        <br><button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="relatorios/relatorio.php">Voltar</a></p>
</body>
</html>
