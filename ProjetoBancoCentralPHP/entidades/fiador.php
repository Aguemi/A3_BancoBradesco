<?php
// Mostrar erros durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para validar CPF
function validaCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf); // Remove caracteres não numéricos

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false; // Verifica se tem 11 dígitos e se todos são iguais
    }

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ($cpf[$t] != $digito) {
            return false;
        }
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];

    if (!validaCPF($cpf)) {
        echo "❌ CPF inválido. Por favor, insira um CPF válido.<br><a href='javascript:history.back()'>Voltar</a>";
        exit;
    }

    // Montar dados para enviar no corpo JSON
    $data = [
        'name' => $nome,
        'cpf' => $cpf
    ];

    $jsonData = json_encode($data);

    // 🔁 🔧 ALTERE AQUI para o endpoint correto do seu Spring Boot para fiadores
    $url = 'http://localhost:8080/api/guarantors';

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

            echo "<h2>✅ Fiador cadastrado com sucesso!</h2>";
            echo "<strong>ID:</strong> " . ($responseData['id'] ?? 'N/A') . "<br>";
            echo "<strong>Nome:</strong> " . htmlspecialchars($responseData['name'] ?? '') . "<br>";
            echo "<strong>CPF:</strong> " . htmlspecialchars($responseData['cpf'] ?? '') . "<br><br>";
            echo "<a href='../index.php'>🔙 Voltar</a>";
        } else {
            echo "❌ Erro na resposta do Spring Boot (HTTP $httpCode):<br>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }

    curl_close($ch);
}
?>
