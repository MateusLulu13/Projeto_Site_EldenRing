<?php

session_start();

// PROTEÇÃO
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena11') {
    header("Location: index.php");
    exit;
}


// VERIFICA DE QUAL CENA O JOGADOR VEIO

if (!isset($_SESSION['cena_morte'])) {

    // Caso não exista uma cena salva,
    // volta para o início.

    $_SESSION['cena_morte'] = 'index.php';
}

// VOLTAR PARA A ÚLTIMA GRAÇA

if (isset($_POST['voltar'])) {

    $cena_voltar =
        $_SESSION['cena_morte'];

    // Define novamente a etapa correta
    // para a proteção da cena.

    if ($cena_voltar == 'cena7.php') {
        $_SESSION['etapa'] = 'cena7';
    }

    elseif ($cena_voltar == 'cena8.php') {
        $_SESSION['etapa'] = 'cena8';
    }

    elseif ($cena_voltar == 'cena12.php') {
        $_SESSION['etapa'] = 'cena12';
    }

    // Volta para a cena em que o jogador morreu.

    header("Location: " . $cena_voltar);
    exit;
}


?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Morte</title>

</head>

<body>

    <img
        src="imagens/morte.png"
        width="100%"
    >

    <!-- TEXTO -->
    <h1>
        A visão do Maculado escurece enquanto suas forças se esvaem.
    </h1>


    <!-- BOTÃO -->

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
