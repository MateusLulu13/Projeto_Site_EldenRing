<?php
session_start();

// verifica se o jogador chegou até aqui pela cena 2 ou cena 3
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena4') {
    header("Location: index.php");
    exit;
}

// verifica se o jogador possui uma arma escolhida
if (!isset($_SESSION['arma'])) {
    header("Location: index.php");
    exit;
}


// verifica a escolha do jogador
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['escolha'])) {

        if ($_POST['escolha'] == 'A') {

            $_SESSION['etapa'] = 'cena6';

            header("Location: cena6.php");
            exit;
        }

        if ($_POST['escolha'] == 'B') {

            $_SESSION['etapa'] = 'cena5';

            header("Location: cena5.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cena 4 - A Chegada à Capital Real</title>
</head>

<body>

    <!-- imagem da cena -->
    <img src="imagens/cena4.jpg" width="100%">

    <h1>A Chegada à Capital Real</h1>

    <p>
        Acompanhado pela arma escolhida, o Maculado finalmente alcança os
        imponentes portões dourados de Leyndell, a Capital Real. As ruas
        estão cobertas de cinzas e memórias de uma era de ouro.
    </p>


    <form method="post">

        <!-- escolha A -->
        <button type="submit" name="escolha" value="A">
            A: Explorar as ruas e segredos de Leyndell.
        </button>

        <br><br>

        <!-- escolha B -->
        <button type="submit" name="escolha" value="B">
            B: Ir direto para o trono e desafiar o mestre do local.
        </button>

    </form>

</body>

</html>
