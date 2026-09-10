<?php
session_start();

// verifica se o jogador passou pelo index.php
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'personagem') {
    header("Location: index.php");
    exit;
}

// quando o jogador clicar em "pronto"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Verifica se escolheu um personagem
    if (isset($_POST['tipo'])) {
        $_SESSION['tipo_personagem'] = $_POST['tipo'];
    } else {
        $erro = "Escolha um personagem.";
    }

    // verifica se digitou um nome
    if (isset($_POST['nome']) && trim($_POST['nome']) != '') {
        $_SESSION['nome_jogador'] = trim($_POST['nome']);
    } else {
        $erro = "Digite um nome.";
    }

    // se tudo estiver preenchido, vai para a Cena 1
    if (!isset($erro)) {
        $_SESSION['etapa'] = 'cena1';

        header("Location: cena1.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Escolha seu Personagem</title>
</head>

<body>

    <h1>Escolha seu personagem</h1>

    <?php
    if (isset($erro)) {
        echo "<p>$erro</p>";
    }
    ?>

    <form method="post">

        <!-- tipo A -->
        <label>
            <input type="radio" name="tipo" value="tipo_a">

            <br>

            <img src="imagens/rapaiz.jpg" width="200" height="200">

            <br>

            Tipo A
        </label>

        <br><br>

        <!-- tipo B -->
        <label>
            <input type="radio" name="tipo" value="tipo_b">

            <br>

            <img src="imagens/muie.jpg" width="200" height="200">

            <br>

            Tipo B
        </label>

        <br><br>

        <!-- nome -->
        <label for="nome">Nome:</label>

        <input
            type="text"
            id="nome"
            name="nome"
            maxlength="50"
        >

        <br><br>

        <!-- botão Pronto -->
        <button type="submit">Pronto</button>

    </form>

</body>

</html>
