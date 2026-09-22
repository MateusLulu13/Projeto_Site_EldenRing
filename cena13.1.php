<?php
session_start();

// proteçao
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena13.1') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cena 13.1 - Elden Lord</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img src="imagens/elden_lord.png" width="100%" alt="Elden Lord">

    <h1>A Queda do Senhor da Rede</h1>

    <p>
        Com um golpe final devastador, o Maculado rompe a defesa de Ederson, Lord of the Web. O tirano sucumbe, desfazendo-se em uma poeira de luz dourada que se dispersa por toda a câmara da Térvore. O silêncio finalmente retorna ao santuário sagrado.
    </p>

    <p>
        Caminhando entre os fragmentos brilhantes que restaram da batalha, o Maculado aproxima-se do trono do Elden Ring. Ao reivindicar o poder supremo para si, a luz da Térvore volta a brilhar com intensidade renovada sobre todas as Terras Intermédias, anunciando o início de um novo reinado.
    </p>

    <p>
        <strong>(Fim do Jogo - Final Principal: O Novo Lorde Prístino)</strong>
    </p>

    <a href="ranking.php">
        <button type="button">Ver Ranking</button>
    </a>

</body>
</html>