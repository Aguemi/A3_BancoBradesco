<?php
// Ativa erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $interestRate = floatval($_POST['interest_rate']) / 100; // Ex: 5% vira 0.05
    $months = intval($_POST['months']);
    $startDate = new DateTime($_POST['start_date']);
    $loanId = intval($_POST['loan_id']);

    // Calculo da parcela fixa PRICE
    if ($interestRate == 0) {
        $pmt = round($amount / $months, 2);
    } else {
        $pmt = round(($amount * $interestRate) / (1 - pow(1 + $interestRate, -$months)), 2);
    }

    $balance = $amount;

    for ($month = 1; $month <= $months; $month++) {
        $interest = round($balance * $interestRate, 2);
        $principal = round($pmt - $interest, 2);
        $balance = round($balance - $principal, 2);

        // Ajuste para última parcela evitar erros de arredondamento
        if ($month == $months && abs($balance) > 0.01) {
            $principal += $balance;
            $balance = 0;
        }

        $totalInstallment = round($principal + $interest, 2);

        $dueDate = (clone $startDate)->modify("+$month month")->format('Y-m-d');

        $data = [
            'monthNumber' => $month,
            'principal' => $principal,
            'interest' => $interest,
            'totalInstallment' => $totalInstallment, // NOVO campo
            'balance' => $balance,
            'dueDate' => $dueDate,
            'loan' => ['id' => $loanId]
        ];

        $jsonData = json_encode($data);

        $url = 'http://localhost:8080/api/installments';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "❌ Erro ao enviar parcela $month: " . curl_error($ch) . "<br>";
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200 || $httpCode === 201) {
                echo "✅ Parcela $month cadastrada com sucesso.<br>";
            } else {
                echo "❌ Erro na resposta para parcela $month (HTTP $httpCode): $response<br>";
            }
        }

        curl_close($ch);
    }

    echo '<a href="../index.php">🔙 Voltar</a>';
}
?>
