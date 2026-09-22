<?php

require_once 'db.php';

$jogadores = [];
$erro_bd = null;

try {

    $pdo = conectar_bd();

    $stmt = $pdo->query(
        "SELECT nome, pontos FROM ranking ORDER BY pontos DESC, nome ASC"
    );

    $jogadores = $stmt->fetchAll();

} catch (PDOException $e) {

    $erro_bd = "Não foi possível carregar o ranking agora. Tente novamente mais tarde.";
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Ranking dos Maculados</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Ranking dos Maculados</h1>

    <?php if ($erro_bd): ?>

        <p><?php echo htmlspecialchars($erro_bd); ?></p>

    <?php elseif (count($jogadores) === 0): ?>

        <p>Nenhuma pontuação registrada ainda.</p>

    <?php else: ?>

        <table border="1" cellpadding="8" cellspacing="0">

            <tr>
                <th>Posição</th>
                <th>Nome</th>
                <th>Pontos</th>
            </tr>

            <?php $posicao = 1; ?>

            <?php foreach ($jogadores as $jogador): ?>

                <tr>
                    <td><?php echo $posicao; ?></td>
                    <td><?php echo htmlspecialchars($jogador['nome']); ?></td>
                    <td><?php echo (int) $jogador['pontos']; ?></td>
                </tr>

                <?php $posicao++; ?>

            <?php endforeach; ?>

        </table>

    <?php endif; ?>

    <br>

    <a href="index.php">
        <button type="button">Voltar ao Início</button>
    </a>

</body>

</html>
