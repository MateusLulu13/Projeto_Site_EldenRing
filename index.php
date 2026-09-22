<?php
session_start();

// começa um novo jogo
session_unset();

// define que o jogador começou pelo índice
$_SESSION['etapa'] = 'personagem';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Site Elden Ring</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <img src="imagens/inicio.jpg" width="100%" height="100%">

    <br><br>

    <a href="personagem.php"> 
        <button type="button">Novo Jogo</button> 
    </a>

    <br><br>

    <a href="ranking.php">
        <button type="button">Ver Ranking</button>
    </a>

</body>
</html>