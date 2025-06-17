<?php

include '../conexao.php';

function conectarBanco() {
    $host = 'localhost';
    $db   = 'bancocentral';
    $user = 'root';
    $pass = '123456';
    $charset = 'utf8mb4';



    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die('Erro de conexão: ' . $e->getMessage());
    }
}

// --- 2. Validação CPF ---
function validarCPF($cpf) {
    // Limpa o CPF
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica tamanho
    if (strlen($cpf) != 11) return false;

    // Elimina CPFs inválidos conhecidos
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;

    // Valida os dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }
    return true;
}

// --- 3. Funções CRUD para cada entidade ---

// --- CUSTOMER ---
function inserirCustomer($pdo, $name, $email) {
    $stmt = $pdo->prepare("INSERT INTO customer (name, email) VALUES (?, ?)");
    return $stmt->execute([$name, $email]);
}

function atualizarCustomer($pdo, $id, $name, $email) {
    $stmt = $pdo->prepare("UPDATE customer SET name = ?, email = ? WHERE id = ?");
    return $stmt->execute([$name, $email, $id]);
}

function deletarCustomer($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM customer WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarCustomerPorId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function listarCustomers($pdo) {
    $stmt = $pdo->query("SELECT * FROM customer");
    return $stmt->fetchAll();
}

// --- GUARANTOR ---
function inserirGuarantor($pdo, $name, $cpf) {
    if (!validarCPF($cpf)) {
        throw new Exception("CPF inválido.");
    }
    $stmt = $pdo->prepare("INSERT INTO guarantor (name, cpf) VALUES (?, ?)");
    return $stmt->execute([$name, $cpf]);
}

function atualizarGuarantor($pdo, $id, $name, $cpf) {
    if (!validarCPF($cpf)) {
        throw new Exception("CPF inválido.");
    }
    $stmt = $pdo->prepare("UPDATE guarantor SET name = ?, cpf = ? WHERE id = ?");
    return $stmt->execute([$name, $cpf, $id]);
}

function deletarGuarantor($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM guarantor WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarGuarantorPorId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM guarantor WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function listarGuarantors($pdo) {
    $stmt = $pdo->query("SELECT * FROM guarantor");
    return $stmt->fetchAll();
}

// --- LOAN ---
function inserirLoan($pdo, $amount, $interest_rate, $months, $start_date, $customer_id) {
    $stmt = $pdo->prepare("INSERT INTO loan (amount, interest_rate, months, start_date, customer_id) VALUES (?, ?, ?, ?, ?)");
    $sucesso = $stmt->execute([$amount, $interest_rate, $months, $start_date, $customer_id]);
    if ($sucesso) {
        $loanId = $pdo->lastInsertId();
        gerarParcelas($pdo, $loanId, $amount, $interest_rate, $months, $start_date);
        return $loanId;
    }
    return false;
}

function atualizarLoan($pdo, $id, $amount, $interest_rate, $months, $start_date, $customer_id) {
    $stmt = $pdo->prepare("UPDATE loan SET amount = ?, interest_rate = ?, months = ?, start_date = ?, customer_id = ? WHERE id = ?");
    $sucesso = $stmt->execute([$amount, $interest_rate, $months, $start_date, $customer_id, $id]);
    if ($sucesso) {
        // Delete todas as parcelas e gere novas
        deletarParcelasPorLoan($pdo, $id);
        gerarParcelas($pdo, $id, $amount, $interest_rate, $months, $start_date);
    }
    return $sucesso;
}

function deletarLoan($pdo, $id) {
    deletarParcelasPorLoan($pdo, $id); // Apaga parcelas associadas
    $stmt = $pdo->prepare("DELETE FROM loan WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarLoanPorId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM loan WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function listarLoans($pdo) {
    $stmt = $pdo->query("SELECT * FROM loan");
    return $stmt->fetchAll();
}

// --- CONTRACT ---
function inserirContract($pdo, $signed_date, $terms, $loan_id, $guarantor_id) {
    $stmt = $pdo->prepare("INSERT INTO contract (signed_date, terms, loan_id, guarantor_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$signed_date, $terms, $loan_id, $guarantor_id]);
}

function atualizarContract($pdo, $id, $signed_date, $terms, $loan_id, $guarantor_id) {
    $stmt = $pdo->prepare("UPDATE contract SET signed_date = ?, terms = ?, loan_id = ?, guarantor_id = ? WHERE id = ?");
    return $stmt->execute([$signed_date, $terms, $loan_id, $guarantor_id, $id]);
}

function deletarContract($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM contract WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarContractPorId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM contract WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function listarContracts($pdo) {
    $stmt = $pdo->query("SELECT * FROM contract");
    return $stmt->fetchAll();
}

// --- INSTALLMENT ---
function inserirInstallment($pdo, $month_number, $principal, $interest, $balance, $due_date, $loan_id) {
    $stmt = $pdo->prepare("INSERT INTO installment (month_number, principal, interest, balance, due_date, loan_id) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$month_number, $principal, $interest, $balance, $due_date, $loan_id]);
}

function atualizarInstallment($pdo, $id, $month_number, $principal, $interest, $balance, $due_date, $loan_id) {
    $stmt = $pdo->prepare("UPDATE installment SET month_number = ?, principal = ?, interest = ?, balance = ?, due_date = ?, loan_id = ? WHERE id = ?");
    return $stmt->execute([$month_number, $principal, $interest, $balance, $due_date, $loan_id, $id]);
}

function deletarInstallment($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM installment WHERE id = ?");
    return $stmt->execute([$id]);
}

function buscarInstallmentPorId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM installment WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function listarInstallments($pdo) {
    $stmt = $pdo->query("SELECT * FROM installment");
    return $stmt->fetchAll();
}

// Deletar todas as parcelas de um empréstimo
function deletarParcelasPorLoan($pdo, $loan_id) {
    $stmt = $pdo->prepare("DELETE FROM installment WHERE loan_id = ?");
    return $stmt->execute([$loan_id]);
}

// --- 4. Geração automática de parcelas ---
// Essa função divide o valor principal igualmente e calcula juros simples sobre o saldo devedor para cada parcela.
function gerarParcelas($pdo, $loan_id, $amount, $interest_rate, $months, $start_date) {
    // Limpa parcelas antigas só por garantia
    deletarParcelasPorLoan($pdo, $loan_id);

    $principalMensal = $amount / $months;
    $saldoDevedor = $amount;

    $date = new DateTime($start_date);

    for ($mes = 1; $mes <= $months; $mes++) {
        $jurosMensal = ($saldoDevedor * ($interest_rate / 100)) / 12; // juros simples mensal
        $balance = $saldoDevedor - $principalMensal;

        $due_date = clone $date;
        $due_date->modify("+$mes month");

        inserirInstallment(
            $pdo,
            $mes,
            round($principalMensal, 2),
            round($jurosMensal, 2),
            round($balance, 2),
            $due_date->format('Y-m-d'),
            $loan_id
        );

        $saldoDevedor = $balance;
    }
}
