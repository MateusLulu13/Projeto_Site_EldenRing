<?php

session_start();

require_once 'classes/Arma.php';
require_once 'classes/Jogador.php';

// proteção
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena5') {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['arma'])) {
    header("Location: index.php");
    exit;
}

// iniciar batalha
if (isset($_POST['iniciar_batalha'])) {

    $_SESSION['jogador'] = [
        'vida' => 1000,
        'vida_maxima' => 1000,
        'mana' => 100,
        'mana_maxima' => 100,
        'usos_cura' => 4,
        'usos_mana' => 2
    ];

    $_SESSION['morgott_vida'] = 2000;
    $_SESSION['morgott_vida_maxima'] = 2000;

    $_SESSION['morgott_golpe_66'] = false;
    $_SESSION['morgott_golpe_33'] = false;

    // controle da chuva de espadas
    $_SESSION['chuva_de_espadas'] = false;
    $_SESSION['zona_segura'] = null;

    // selos de luz
    $_SESSION['selos_luz'] = [];

    $_SESSION['mensagem_batalha'] = "A batalha contra Morgott começou!";

    $_SESSION['batalha_morgott'] = true;

    header("Location: cena5.php");
    exit;
}



// verifica se a luta começou
$batalha_iniciada = isset($_SESSION['batalha_morgott']);

// processamento da chuva de espadas
if (
    $batalha_iniciada &&
    isset($_SESSION['chuva_de_espadas']) &&
    $_SESSION['chuva_de_espadas'] == true &&
    isset($_POST['zona'])
) {

    $zona_escolhida = intval($_POST['zona']);
    $zona_segura = intval($_SESSION['zona_segura']);

    // verifica a zona escolhida
    if ($zona_escolhida == $zona_segura) {

        $_SESSION['mensagem_batalha'] =
            "Você desviou da Chuva de Espadas! A Zona "
            . $zona_escolhida
            . " era a zona segura.";

    } else {

        $_SESSION['jogador']['vida'] -= 400;

        if ($_SESSION['jogador']['vida'] < 0) {
            $_SESSION['jogador']['vida'] = 0;
        }

        $_SESSION['mensagem_batalha'] =
            "Você foi atingido pela Chuva de Espadas! "
            . "A Zona segura era a Zona "
            . $zona_segura
            . ". Você recebeu 400 de dano.";
    }


    // a chuva terminou
    $_SESSION['chuva_de_espadas'] = false;
    $_SESSION['zona_segura'] = null;


    // verifica derrota
    if ($_SESSION['jogador']['vida'] <= 0) {

        $_SESSION['etapa'] = 'cena8';

        unset($_SESSION['batalha_morgott']);

        header("Location: cena8.php");
        exit;
    }


    // mostra o resultado antes do próximo turno
    header("Location: cena5.php");
    exit;
}



// processamento das ações normais
if (
    $batalha_iniciada &&
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['acao'])
) {

    // segurança extra:
    // se a chuva estiver ativa, não processa ação normal.
    if (
        isset($_SESSION['chuva_de_espadas']) &&
        $_SESSION['chuva_de_espadas'] == true
    ) {
        header("Location: cena5.php");
        exit;
    }


    $acao = $_POST['acao'];

    // cria jogador e recupera os dados da sessão
    $jogador = new Jogador();

    $jogador->vida = $_SESSION['jogador']['vida'];
    $jogador->vida_maxima = $_SESSION['jogador']['vida_maxima'];

    $jogador->mana = $_SESSION['jogador']['mana'];
    $jogador->mana_maxima = $_SESSION['jogador']['mana_maxima'];

    $jogador->usos_cura = $_SESSION['jogador']['usos_cura'];
    $jogador->usos_mana = $_SESSION['jogador']['usos_mana'];


    $mensagem = "";



// ATAQUE NORMAL
    if ($acao == 'ataque_normal') {

        $arma = $_SESSION['arma'];

        $dano = $arma['dano_basico'];

        $_SESSION['morgott_vida'] -= $dano;

        if ($_SESSION['morgott_vida'] < 0) {
            $_SESSION['morgott_vida'] = 0;
        }

        $mensagem =
            "Você realizou um ataque normal e causou "
            . $dano
            . " de dano em Morgott.";
    }



// ATAQUE ESPECIAL
    elseif ($acao == 'ataque_especial') {

        $arma = $_SESSION['arma'];

        $dano = $arma['ataque_especial'];
        $custo = $arma['custo_mana'];


        if ($jogador->mana < $custo) {

            $mensagem =
                "Mana insuficiente para usar o ataque especial.";

        } else {

            $jogador->gastar_mana($custo);

            $_SESSION['morgott_vida'] -= $dano;

            if ($_SESSION['morgott_vida'] < 0) {
                $_SESSION['morgott_vida'] = 0;
            }

            $mensagem =
                "Você usou o ataque especial da "
                . $arma['nome']
                . " e causou "
                . $dano
                . " de dano em Morgott.";
        }
    }



// CURAR
    elseif ($acao == 'curar') {

        if ($jogador->usos_cura <= 0) {

            $mensagem =
                "Você não possui mais usos de cura.";

        } else {

            $quantidade_cura = 300;

            // verifica se existe um selo de luz
            if (count($_SESSION['selos_luz']) > 0) {

                $quantidade_cura = 150;

                // remove um selo
                array_shift($_SESSION['selos_luz']);

                $mensagem =
                    "Um Selo de Luz explodiu! "
                    . "Sua cura foi reduzida pela metade.";

            } else {

                $mensagem =
                    "Você recuperou 300 de vida.";
            }

            $jogador->curar($quantidade_cura);

            $jogador->usos_cura--;
        }
    }



// RECUPERAR MANA
    elseif ($acao == 'recuperar_mana') {

        if ($jogador->usos_mana <= 0) {

            $mensagem =
                "Você não possui mais usos para recuperar mana.";

        } else {

            $jogador->recuperar_mana(60);

            $jogador->usos_mana--;

            $mensagem =
                "Você recuperou 60 de mana.";
        }
    }


// VERIFICA SE MORGOTT MORREU

    if ($_SESSION['morgott_vida'] <= 0) {

        $_SESSION['etapa'] = 'cena7';

        unset($_SESSION['batalha_morgott']);

        header("Location: cena7.php");
        exit;
    }


// SALVA OS DADOS DO JOGADOR

    $_SESSION['jogador']['vida'] = $jogador->vida;
    $_SESSION['jogador']['vida_maxima'] = $jogador->vida_maxima;

    $_SESSION['jogador']['mana'] = $jogador->mana;
    $_SESSION['jogador']['mana_maxima'] = $jogador->mana_maxima;

    $_SESSION['jogador']['usos_cura'] = $jogador->usos_cura;
    $_SESSION['jogador']['usos_mana'] = $jogador->usos_mana;




    $vida_morgott = $_SESSION['morgott_vida'];



// CHUVA DE ESPADAS EM 66%

    if (
        $vida_morgott <= 1320 &&
        $_SESSION['morgott_golpe_66'] == false
    ) {

        $_SESSION['morgott_golpe_66'] = true;

        $_SESSION['chuva_de_espadas'] = true;

        // sorteia uma zona de 1 a 3
        $_SESSION['zona_segura'] = rand(1, 3);

        $_SESSION['mensagem_batalha'] =
            $mensagem
            . " Morgott invocou a Chuva de Espadas! "
            . "Escolha uma zona para tentar desviar.";

        header("Location: cena5.php");
        exit;
    }



// CHUVA DE ESPADAS EM 33%

    if (
        $vida_morgott <= 660 &&
        $_SESSION['morgott_golpe_33'] == false
    ) {

        $_SESSION['morgott_golpe_33'] = true;

        $_SESSION['chuva_de_espadas'] = true;

        // sorteia uma nova zona
        $_SESSION['zona_segura'] = rand(1, 3);

        $_SESSION['mensagem_batalha'] =
            $mensagem
            . " Morgott invocou novamente a Chuva de Espadas! "
            . "Escolha uma zona para tentar desviar.";

        header("Location: cena5.php");
        exit;
    }


// ATAQUE NORMAL DE MORGOTT

    $chance_ataque = rand(1, 100);

    if ($chance_ataque <= 50) {

        $jogador->receber_dano(140);

        $mensagem .=
            " Morgott realizou um ataque.";

        // 20% de chance de criar um Selo
        $chance_selo = rand(1, 100);

        if ($chance_selo <= 20) {

            $_SESSION['selos_luz'][] = true;

            $mensagem .=
                " Morgott deixou um Selo de Luz no campo!";
        }

    } else {

        $mensagem .=
            " Morgott errou o ataque.";
    }



// SALVA NOVAMENTE O JOGADOR

    $_SESSION['jogador']['vida'] = $jogador->vida;
    $_SESSION['jogador']['mana'] = $jogador->mana;

    $_SESSION['jogador']['usos_cura'] = $jogador->usos_cura;
    $_SESSION['jogador']['usos_mana'] = $jogador->usos_mana;

    $_SESSION['mensagem_batalha'] = $mensagem;


// DERROTA

    if ($jogador->vida <= 0) {

        $_SESSION['etapa'] = 'cena8';

        unset($_SESSION['batalha_morgott']);

        header("Location: cena8.php");
        exit;
    }



// NOVO TURNO

    header("Location: cena5.php");
    exit;
}



// DADOS PARA EXIBIÇÃO

if ($batalha_iniciada) {

    $vida_jogador =
        $_SESSION['jogador']['vida'];

    $vida_jogador_maxima =
        $_SESSION['jogador']['vida_maxima'];

    $mana_jogador =
        $_SESSION['jogador']['mana'];

    $mana_jogador_maxima =
        $_SESSION['jogador']['mana_maxima'];

    $usos_cura =
        $_SESSION['jogador']['usos_cura'];

    $usos_mana =
        $_SESSION['jogador']['usos_mana'];

    $vida_morgott =
        $_SESSION['morgott_vida'];

    $vida_morgott_maxima =
        $_SESSION['morgott_vida_maxima'];

    $quantidade_selos =
        count($_SESSION['selos_luz']);

    $mensagem_batalha =
        $_SESSION['mensagem_batalha'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Cena 5 - O Guardião do Trono</title>

</head>

<body>


<?php if (!$batalha_iniciada): ?>

    
    <!-- INTRODUÇÃO -->
    <img src="imagens/morgott.jpg" width="100%">

    <h1>O Guardião do Trono</h1>

    <p>
        O caminho do Maculado o conduz inevitavelmente ao topo do
        santuário real. Das sombras que envolvem o trono deserto,
        surge Morgott, the Omen King, brandindo sua lâmina amaldiçoada
        para defender a santidade da Térvore até o último suspiro.
    </p>

    <form method="post">

        <button type="submit" name="iniciar_batalha">
            Iniciar Batalha
        </button>

    </form>


<?php else: ?>

    
    <!-- BATALHA -->

    <img src="imagens/morgott.jpg" width="100%">

    <h1>Morgott, the Omen King</h1>


    
    <!-- VIDA DE MORGOTT -->

    <h2>Vida de Morgott</h2>

    <progress
        value="<?php echo $vida_morgott; ?>"
        max="<?php echo $vida_morgott_maxima; ?>">
    </progress>

    <p>
        <?php echo $vida_morgott; ?>
        /
        <?php echo $vida_morgott_maxima; ?>
        HP
    </p>


    <hr>


    
    <!-- VIDA DO JOGADOR -->

    <h2>Maculado</h2>

    <p>
        Vida:
        <?php echo $vida_jogador; ?>
        /
        <?php echo $vida_jogador_maxima; ?>
        HP
    </p>

    <progress
        value="<?php echo $vida_jogador; ?>"
        max="<?php echo $vida_jogador_maxima; ?>">
    </progress>


    <p>
        Mana:
        <?php echo $mana_jogador; ?>
        /
        <?php echo $mana_jogador_maxima; ?>
        MP
    </p>

    <progress
        value="<?php echo $mana_jogador; ?>"
        max="<?php echo $mana_jogador_maxima; ?>">
    </progress>


    <p>
        Selos de Luz ativos:
        <?php echo $quantidade_selos; ?>
    </p>


    <hr>


    
    <!-- MENSAGEM -->

    <h2>
        <?php echo $mensagem_batalha; ?>
    </h2>


    
    <!-- CHUVA DE ESPADAS -->

    <?php if ($_SESSION['chuva_de_espadas'] == true): ?>

        <h2>Chuva de Espadas!</h2>

        <p>
            Morgott invocou uma chuva de espadas.
            Escolha uma zona para tentar desviar.
        </p>


        <form method="post">

            <button
                type="submit"
                name="zona"
                value="1">

                Zona 1

            </button>


            <button
                type="submit"
                name="zona"
                value="2">

                Zona 2

            </button>


            <button
                type="submit"
                name="zona"
                value="3">

                Zona 3

            </button>

        </form>


    <?php else: ?>


        
        <!-- AÇÕES -->

        <h2>Escolha sua ação</h2>


        <form method="post">

            <button
                type="submit"
                name="acao"
                value="ataque_normal">

                <img
                    src="imagens/ataque_normal.jpg"
                    width="100"
                    height="100"
                >

                <br>

                Ataque Normal

            </button>


            <button
                type="submit"
                name="acao"
                value="ataque_especial">

                <img
                    src="imagens/ataque_especial.jpg"
                    width="100"
                    height="100"
                >

                <br>

                Ataque Especial

            </button>


            <button
                type="submit"
                name="acao"
                value="curar">

                <img
                    src="imagens/cura.webp"
                    width="100"
                    height="100"
                >

                <br>

                Curar

                <br>

                Usos:
                <?php echo $usos_cura; ?>

            </button>


            <button
                type="submit"
                name="acao"
                value="recuperar_mana">

                <img
                    src="imagens/recuperar_mana.webp"
                    width="100"
                    height="100"
                >

                <br>

                Recuperar Mana

                <br>

                Usos:
                <?php echo $usos_mana; ?>

            </button>

        </form>


    <?php endif; ?>


<?php endif; ?>

</body>

</html>
