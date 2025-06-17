<?php
// Mostrar erros durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signedDate = $_POST['signed_date'];
    $terms = $_POST['terms'];
    $loanId = (int)$_POST['loan_id'];
    $guarantorId = (int)$_POST['guarantor_id'];

    // Montar array para enviar no corpo JSON
    $data = [
        'signedDate' => $signedDate,
        'terms' => $terms,
        'loan' => ['id' => $loanId],         // Objeto loan com id
        'guarantor' => ['id' => $guarantorId] // Objeto guarantor com id
    ];

    $jsonData = json_encode($data);

    // 🔁 🔧 ALTERE AQUI para o endpoint correto do seu Spring Boot para contratos
    $url = 'http://localhost:8080/api/contracts';

    // Configurar cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    // Executar requisição
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ Erro ao enviar para o Spring Boot: " . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200 || $httpCode === 201) {
            $responseData = json_decode($response, true);

            echo "<h2>✅ Contrato cadastrado com sucesso!</h2>";
            echo "<strong>ID:</strong> " . ($responseData['id'] ?? 'N/A') . "<br>";
            echo "<strong>Data de Assinatura:</strong> " . ($responseData['signedDate'] ?? 'N/A') . "<br>";
            echo "<strong>Termos:</strong> " . htmlspecialchars($responseData['terms'] ?? '') . "<br>";
            echo "<strong>ID Empréstimo:</strong> " . ($responseData['loan']['id'] ?? 'N/A') . "<br>";
            echo "<strong>ID Fiador:</strong> " . ($responseData['guarantor']['id'] ?? 'N/A') . "<br><br>";
            echo "<a href='../index.php'>🔙 Voltar</a>";
        } else {
            echo "❌ Erro na resposta do Spring Boot (HTTP $httpCode):<br>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }

    curl_close($ch);
}
?>
