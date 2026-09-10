<?php
session_start();

// verifica se o jogador passou pela criação do personagem
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena1') {
    header("Location: index.php");
    exit;
}

// verifica qual escolha o jogador fez
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['escolha'])) {

        if ($_POST['escolha'] == 'A') {

            $_SESSION['etapa'] = 'cena2';

            header("Location: cena2.php");
            exit;

        } elseif ($_POST['escolha'] == 'B') {

            $_SESSION['etapa'] = 'cena3';

            header("Location: cena3.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cena 1 - O Despertar nas Terras Intermédias</title>
</head>

<body>
    
    <img src="imagens/cena1.webp" width="100%">

    <h1>O Despertar nas Terras Intermédias</h1>

    <p>
        A névoa dourada se dissipa sobre as estepes de Limgrave.
        O Maculado ergue-se junto à Graça, com o olhar fixo no horizonte devastado.
        O caminho à frente divide-se entre as ruínas verdejantes da região ou a
        imponente silhueta da Academia que se ergue ao norte, cercada por brumas misteriosas.
    </p>

    <form method="post">

        <button type="submit" name="escolha" value="A">
            A: Explorar as terras selvagens de Limgrave.
        </button>

        <br><br>

        <button type="submit" name="escolha" value="B">
            B: Viagem direta para a Academia Raya Lucaria.
        </button>

    </form>

</body>

</html>