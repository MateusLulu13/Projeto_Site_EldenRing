<?php

session_start();

// proteção
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena11') {
    header("Location: index.php");
    exit;
}


// verifica de qual cena o jogador veio

if (!isset($_SESSION['cena_morte'])) {

    // caso não exista uma cena salva
    // volta para o início

    $_SESSION['cena_morte'] = 'index.php';
}

// voltar para a última graça

if (isset($_POST['voltar'])) {

    $cena_voltar =
        $_SESSION['cena_morte'];

    // define novamente a etapa correta
    // para a proteção da cena

    if ($cena_voltar == 'cena7.php') {
        $_SESSION['etapa'] = 'cena7';
    }

    elseif ($cena_voltar == 'cena8.php') {
        $_SESSION['etapa'] = 'cena8';
    }

    elseif ($cena_voltar == 'cena12.php') {
        $_SESSION['etapa'] = 'cena12';
    }

    // volta para a cena em que o jogador morreu

    header("Location: " . $cena_voltar);
    exit;
}


?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Morte</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <img
        src="imagens/morte.png"
        width="100%"
    >

    <!-- texto -->
    <h1>
        A visão do Maculado escurece enquanto suas forças se esvaem.
    </h1>


    <!-- botão -->

    <form method="post">

        <button
            type="submit"
            name="voltar"
        >
            Voltar à Última Graça Visitada
        </button>

    </form>


</body>

</html>
