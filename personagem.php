<?php
session_start();

require_once 'config.php';

// verifica se o jogador passou pelo index.php
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'personagem') {
    header("Location: index.php");
    exit;
}

$erro_nome = null;

// quando o jogador clicar em "pronto"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';

    // o "required" do HTML não impede um nome só com espaços,
    // então validamos de novo aqui depois do trim()
    if ($nome === '' || !isset($_POST['tipo'])) {

        $erro_nome = "Por favor, escolha um personagem e digite um nome válido.";

    } else {

        // salva o personagem escolhido
        $_SESSION['tipo_personagem'] = $_POST['tipo'];

        // salva o nome
        $_SESSION['nome_jogador'] = $nome;

        // inicia a pontuação do jogador
        $_SESSION['pontos'] = PONTOS_INICIAIS;
        $_SESSION['penalidade_aplicada'] = false;
        unset($_SESSION['bonus_cena13_1']);

        // vai para a Cena 1
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
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Escolha seu personagem</h1>

    <?php if ($erro_nome): ?>
        <p style="color: red;"><?php echo htmlspecialchars($erro_nome); ?></p>
    <?php endif; ?>

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
