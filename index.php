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
    <title>RPG - Terras Intermédias</title>
</head>

<body>

    <img src="imagens/inicio.jpg" width="100%" height="100%">

    <br><br>

    <form action="personagem.php" method="post">
        <button type="submit">Novo Jogo</button>
    </form>

</body>
</html>