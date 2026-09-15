<?php
session_start();

// verifica se o jogador veio da cena 1
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena2') {
    header("Location: index.php");
    exit;
}

// importa a classe Arma
require_once 'classes/Arma.php';


// criando as armas
$presa_cao_de_caca = new Arma(
    "Presa do Cão de Caça",
    140,
    280,
    20
);

$zweihander = new Arma(
    "Zweihander",
    160,
    310,
    30
);


// verifica se o jogador escolheu uma arma
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['escolha'])) {

        // cão de caça
        if ($_POST['escolha'] == 'presa') {

            $_SESSION['arma'] = [
                'nome' => $presa_cao_de_caca->nome,
                'dano_basico' => $presa_cao_de_caca->dano_basico,
                'ataque_especial' => $presa_cao_de_caca->ataque_especial,
                'custo_mana' => $presa_cao_de_caca->custo_mana
            ];

            $_SESSION['etapa'] = 'cena4';

            header("Location: cena4.php");
            exit;
        }


        // zweihander
        if ($_POST['escolha'] == 'zweihander') {

            $_SESSION['arma'] = [
                'nome' => $zweihander->nome,
                'dano_basico' => $zweihander->dano_basico,
                'ataque_especial' => $zweihander->ataque_especial,
                'custo_mana' => $zweihander->custo_mana
            ];

            $_SESSION['etapa'] = 'cena4';

            header("Location: cena4.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cena 2 - As Armas das Terras Selvagens</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- imagem da cena -->
    <img src="imagens/cena2.png" width="100%">

    <h1>As Armas das Terras Selvagens</h1>

    <p>
        Explorando os cantos esquecidos de Limgrave, o Maculado adentra uma
        cavidade rochosa iluminada por runas antigas. No centro da câmara,
        duas armas repousam sobre pedestais de pedra, emitindo uma aura de
        poder físico devastador.
    </p>


    <form method="post">

        <!-- cão de caça -->
        <button type="submit" name="escolha" value="presa">

            <img
                src="imagens/presa_cao_de_caca.png"
                width="200"
                height="200"
            >

            <br>

            A: Empunhar a Presa do Cão de Caça.

        </button>


        <br><br>


        <!-- zweihander -->
        <button type="submit" name="escolha" value="zweihander">

            <img
                src="imagens/zweihander.webp"
                width="200"
                height="200"
            >

            <br>

            B: Empunhar a Zweihander.

        </button>

    </form>

</body>

</html>
