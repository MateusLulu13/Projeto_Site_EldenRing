<?php

session_start();

// protecao

if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena9') {
    header("Location: index.php");
    exit;
}

// ir para a cena 12

if (isset($_POST['avancar'])) {

    $_SESSION['etapa'] = 'cena12';

    header("Location: cena12.php");
    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>A Armadura do Destino Sombrio</title>

</head>

<body>

    <h1>
        A Armadura do Destino Sombrio
    </h1>

    <img
        src="imagens/maliketh_set.jpg"
        width="100%"
    >

    <p>
        A lâmina da Morte Destinada tomba.
        Das cinzas do combate contra Maliketh,
        o Maculado clama o formidável
        <strong>Conjunto de Maliketh</strong>,
        sentindo o peso e o poder da morte
        fluírem por sua armadura.
    </p>

    <!-- botão -->

    <form method="post">

        <button
            type="submit"
            name="avancar"
        >
            Voltar para a Térvore
        </button>

    </form>


</body>

</html>
