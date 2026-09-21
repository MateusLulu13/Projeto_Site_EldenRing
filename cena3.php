<?php
session_start();

// verifica se o jogador veio da cena 1
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena3') {
    header("Location: index.php");
    exit;
}

// importa a classe Arma
require_once 'classes/Arma.php';


// armas
$veu_da_lua = new Arma(
    "Véu da Lua",
    120,
    240,
    15
);

$cajado_lusat = new Arma(
    "Espada Grande da Lua Sombria",
    140,
    310,
    25
);


// verifica se o jogador escolheu uma arma
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['escolha'])) {

        // véu da lua
        if ($_POST['escolha'] == 'veu') {

            $_SESSION['arma'] = [
                'nome' => $veu_da_lua->nome,
                'dano_basico' => $veu_da_lua->dano_basico,
                'ataque_especial' => $veu_da_lua->ataque_especial,
                'custo_mana' => $veu_da_lua->custo_mana
            ];

            $_SESSION['etapa'] = 'cena4';

            header("Location: cena4.php");
            exit;
        }


        // cajado de lusat
        if ($_POST['escolha'] == 'lusat') {

            $_SESSION['arma'] = [
                'nome' => $cajado_lusat->nome,
                'dano_basico' => $cajado_lusat->dano_basico,
                'ataque_especial' => $cajado_lusat->ataque_especial,
                'custo_mana' => $cajado_lusat->custo_mana
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
    <title>Cena 3 - O Legado dos Magos</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- imagem da cena -->
    <img src="imagens/cena3.1.png" width="100%">

    <h1>O Legado dos Magos</h1>

    <p>
        Ao cruzar os grandes portões da Academia Raya Lucaria, o silêncio
        dos salões de pedra é quebrado pelo brilho de artefatos mágicos.
        Sobre um altar de pedrilhante, duas relíquias lendárias aguardam
        um novo portador.
    </p>


    <form method="post">

        <!-- véu da lua -->
        <button type="submit" name="escolha" value="veu">

            <img
                src="imagens/veu_da_lua.png"
                width="200"
                height="200"
            >

            <br>

            A: Empunhar o Véu da Lua.

        </button>


        <br><br>


        <!-- cajado de lusat -->
        <button type="submit" name="escolha" value="lusat">

            <img
                src="imagens/Greatsword.webp"
                width="200"
                height="200"
            >

            <br>

            B: Empunhar a Espada Grande da Lua Sombria.

        </button>

    </form>

</body>

</html>