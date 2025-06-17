<?php
// estilo.php

header("Content-type: text/css; charset: UTF-8");
?>

/* Reset básico */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, sans-serif;
}

/* Corpo da página */
body {
  background-color: #f4f6f8;
  color: #333;
  padding: 20px;
}

/* Container geral */
.container {
  max-width: 900px;
  margin: 0 auto;
  background: #fff;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Títulos */
h1, h2, h3 {
  margin-bottom: 15px;
  color: #2c3e50;
}

/* Formulários */
form {
  margin-bottom: 30px;
}

label {
  display: block;
  margin-bottom: 6px;
  font-weight: bold;
}

input[type="text"],
input[type="email"],
input[type="number"],
input[type="date"],
textarea,
select {
  width: 100%;
  padding: 10px;
  margin-bottom: 12px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
}

button {
  background-color: #27ae60;
  border: none;
  color: white;
  padding: 12px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #219150;
}

/* Tabela de relatório */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table th, table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

table th {
  background-color: #3498db;
  color: white;
}

table tr:nth-child(even) {
  background-color: #f9f9f9;
}

/* Mensagens de erro e sucesso */
.mensagem-erro {
  color: #e74c3c;
  margin-bottom: 15px;
}

.mensagem-sucesso {
  color: #27ae60;
  margin-bottom: 15px;
}
