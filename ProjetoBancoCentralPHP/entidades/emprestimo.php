<?php
// Mostrar erros durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura e converte os dados do formulário
    $amount = floatval($_POST['amount']);
    $interestRate = $_POST['interest_rate'];
    $months = $_POST['months'];
    $startDate = $_POST['start_date'];
    $customerId = (int) $_POST['customer_id'];

    // Validação: valor do empréstimo deve ser maior que zero
    if ($amount <= 0) {
        echo "❌ O valor do empréstimo deve ser maior que zero.";
        exit;
    }

    $data = [
        'amount' => $amount,
        'interestRate' => $interestRate,
        'months' => $months,
        'startDate' => $startDate,
        'customer' => ['id' => $customerId]
    ];

    // Converte os dados para JSON
    $jsonData = json_encode($data);

    // URL do endpoint Spring Boot
    $url = 'http://localhost:8080/api/loans';

    // Inicia a requisição cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    // Executa e captura a resposta
    $response = curl_exec($ch);

    // Verifica erros na requisição
    if (curl_errno($ch)) {
        echo "❌ Erro ao enviar para o Spring Boot: " . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode === 200 || $httpCode === 201) {
            $responseData = json_decode($response, true);

            echo "<h2>✅ Empréstimo registrado com sucesso!</h2>";
            echo "<strong>ID:</strong> " . $responseData['id'] . "<br>";
            echo "<strong>Valor:</strong> R$ " . number_format($responseData['amount'], 2, ',', '.') . "<br>";
            echo "<strong>Taxa de Juros:</strong> " . $responseData['interestRate'] . "%<br>";
            echo "<strong>Parcelas:</strong> " . $responseData['months'] . "<br>";
            echo "<strong>Data de Início:</strong> " . $responseData['startDate'] . "<br><br>";
            echo "<a href='../index.php'>🔙 Voltar</a>";
        } else {
            echo "❌ Erro na resposta do Spring Boot (HTTP $httpCode):<br>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }

    curl_close($ch);
}
?>
