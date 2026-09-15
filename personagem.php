<?php
session_start();

// verifica se o jogador passou pelo index.php
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'personagem') {
    header("Location: index.php");
    exit;
}

// quando o jogador clicar em "pronto"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // salva o personagem escolhido
    $_SESSION['tipo_personagem'] = $_POST['tipo'];

    // salva o nome
    $_SESSION['nome_jogador'] = trim($_POST['nome']);

    // vai para a Cena 1
    $_SESSION['etapa'] = 'cena1';

    header("Location: cena1.php");
    exit;
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

    <form method="post">

        <!-- tipo A -->
        <label>
            <input type="radio" name="tipo" value="tipo_a" required>

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
            required
        >

        <br><br>

        <!-- botão pronto -->
        <button type="submit">Pronto</button>

    </form>

</body>

</html>
