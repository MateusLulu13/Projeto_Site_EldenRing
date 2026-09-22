<?php

session_start();

require_once 'config.php';
require_once 'db.php';

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


// nome do jogador, para registrar no ranking
$nome_jogador =
    isset($_SESSION['nome_jogador']) ? $_SESSION['nome_jogador'] : 'Anônimo';

// garante que exista uma pontuação
if (!isset($_SESSION['pontos'])) {
    $_SESSION['pontos'] = PONTOS_INICIAIS;
}

// aplica a penalidade apenas uma vez por morte:
// sem essa verificação, um F5 (recarregar a página) nesta
// tela descontaria pontos de novo sem o jogador ter morrido de novo
if (
    !isset($_SESSION['penalidade_aplicada']) ||
    $_SESSION['penalidade_aplicada'] !== true
) {

    $_SESSION['pontos'] -= PONTOS_PENALIDADE_MORTE;

    if ($_SESSION['pontos'] < 0) {
        $_SESSION['pontos'] = 0;
    }

    $_SESSION['penalidade_aplicada'] = true;

    // salva/atualiza a pontuação do jogador no ranking
    salvar_pontuacao($nome_jogador, $_SESSION['pontos']);
}

$pontos_atuais = $_SESSION['pontos'];

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

    // libera a penalidade para a próxima morte
    $_SESSION['penalidade_aplicada'] = false;

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

    <!-- pontuação -->
    <p>
        Pontuação atual: <?php echo (int) $pontos_atuais; ?>
    </p>


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
