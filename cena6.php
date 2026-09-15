<?php

session_start();

require_once 'classes/Arma.php';



// proteção

if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena6') {
    header("Location: index.php");
    exit;
}



// CRIA AS ARMAS

$esmagador = new Arma(
    "Esmagador de Gigantes",
    180,
    350,
    40
);

$rios = new Arma(
    "Rios de Sangue",
    125,
    250,
    15
);



// SORTEIA A ARMA APENAS UMA VEZ
if (!isset($_SESSION['arma_cena6_sorteada'])) {

    $armas = [
        $esmagador,
        $rios
    ];

    $arma_escolhida = $armas[array_rand($armas)];


    // substitui a arma antiga
    $_SESSION['arma'] = [
        'nome' => $arma_escolhida->nome,
        'dano_basico' => $arma_escolhida->dano_basico,
        'ataque_especial' => $arma_escolhida->ataque_especial,
        'custo_mana' => $arma_escolhida->custo_mana
    ];


    // marca que a arma já foi sorteada
    $_SESSION['arma_cena6_sorteada'] = true;

} else {

    // Recupera a arma que já foi sorteada
    $arma_escolhida = new Arma(
        $_SESSION['arma']['nome'],
        $_SESSION['arma']['dano_basico'],
        $_SESSION['arma']['ataque_especial'],
        $_SESSION['arma']['custo_mana']
    );
}



// ESCOLHE A IMAGEM

if ($arma_escolhida->nome == "Esmagador de Gigantes") {

    $imagem_arma = "esmagador_de_gigantes.jpg";

} else {

    $imagem_arma = "rios_de_sangue.jpg";
}



// CONTINUAR

if (isset($_POST['continuar'])) {

    $_SESSION['etapa'] = 'cena5';

    header("Location: cena5.php");
    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>O Desfortúnio em Leyndell</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>



<!-- IMAGEM DA CENA -->
<img
    src="imagens/cena6.jpg"
    width="100%"
>

<h1>O Desfortúnio em Leyndell</h1>

<p>
    Enquanto explorava as vielas cobertas de cinzas da capital,
    o Maculado é surpreendido por uma armadilha secreta.
    Um colapso na estrutura do caminho o faz despencar nos
    esgotos da capital. Na queda, sua arma principal escapa
    de suas mãos e despenca em um abismo insondável, perdendo-se
    para sempre nas profundezas de Leyndell.
</p>

<p>
    Desarmado e sem opção de retorno, ele tateia a escuridão até
    encontrar um antigo altar em ruínas onde repousam restos de
    guerreiros tombados. Sem tempo a perder, ele é obrigado a
    pegar rapidamente a primeira arma que alcança.
</p>

<p>
    Com o novo e inesperado equipamento em mãos, o Maculado escala
    de volta à superfície e segue sem escolha direto para o
    trono central.
</p>



<!-- ARMA RECEBIDA -->

<h2>Arma encontrada</h2>

<?php

if ($arma_escolhida->nome == "Esmagador de Gigantes") {
    $imagem_arma = "esmagador_de_gigantes.png";
} else {
    $imagem_arma = "rios_de_sangue.png";
}

?>

<img
    src="imagens/<?php echo $imagem_arma; ?>"
    width="300"
>

<h2>
    <?php echo $arma_escolhida->nome; ?>
</h2>



<!-- CONTINUAR -->

<form method="post">

    <button
        type="submit"
        name="continuar"
    >
        Continuar para o trono
    </button>

</form>


</body>

</html>
