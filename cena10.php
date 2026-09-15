<?php

session_start();

// protecao

if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena10') {
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

    <title>O Triunfo sobre a Imparável</title>

</head>

<body>

    <h1>
       O Triunfo sobre a Imparável
    </h1>

    <img
        src="imagens/malenia_set.jpg"
        width="100%"
    >

    <p>
        A flor de podridão se fecha e o silêncio retorna à Árvore Sacra. 
        Ao superar Malenia, o Maculado recebe o lendário
        <strong>Conjunto de Malenia</strong>,
        trajando o vestuário da guerreira 
        que nunca havia conhecido a derrota.
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
