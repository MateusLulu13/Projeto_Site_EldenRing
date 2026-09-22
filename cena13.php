<?php
session_start();

// proteçao
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena13') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cena 13 - O Novo Pacto da Teia </title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img src="imagens/finalb.png" width="100%" alt="Elden Lord">

    <h1>O Novo Pacto da Teia </h1>

    <p>
        Ao apertar a mão de Ederson, uma nova ordem se estabelece nas Terras Intermédias. A luz dourada da Térvore entrelaça-se com a vasta rede digital do Lord of the Web, reescrevendo as leis da realidade. 

    <p>
        Sem a necessidade de mais sangue derramado, o Maculado ascende não como um Lorde solitário, mas como o Consorte e Co-regente de uma era de conhecimento e conexão infinitos.

    <p>
        <strong>(Fim do Jogo - Final Alternativo: A Era da Rede Compartilhada)</strong>
    </p>

    <a href="ranking.php">
        <button type="button">Ver Ranking</button>
    </a>

</body>
</html>