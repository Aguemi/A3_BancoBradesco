<?php
// Mostrar erros durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para validar e-mail
function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Cadastrar Cliente (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    if (!validaEmail($email)) {
        echo "❌ E-mail inválido. Por favor, insira um e-mail válido.<br><a href='javascript:history.back()'>Voltar</a>";
        exit;
    }

    // Dados do cliente convertidos em JSON
    $data = [
        'name' => $nome,
        'email' => $email
    ];
    $jsonData = json_encode($data);

    // 🔁 🔧 ALTERE AQUI o endpoint para o seu endpoint real de clientes no Spring Boot
    $url = 'http://localhost:8080/api/customers'; // <-- Substitua esse caminho se o endpoint for diferente

    // Envia via cURL para o Spring Boot
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ Erro ao enviar para o Spring Boot: " . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode === 200 || $httpCode === 201) {
            $responseData = json_decode($response, true);

            echo "<h2>✅ Cliente cadastrado com sucesso!</h2>";
            echo "<strong>ID:</strong> " . $responseData['id'] . "<br>";
            echo "<strong>Nome:</strong> " . $responseData['name'] . "<br>";
            echo "<strong>E-mail:</strong> " . $responseData['email'] . "<br><br>";
            echo "<a href='../index.php'>🔙 Voltar</a>";
        } else {
            echo "❌ Erro na resposta do Spring Boot (HTTP $httpCode):<br>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }

    curl_close($ch);
}
?>
