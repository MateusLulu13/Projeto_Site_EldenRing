<?php

session_start();

require_once 'classes/Arma.php';
require_once 'classes/Jogador.php';

// proteção

if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena8') {
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

    $_SESSION['malenia_vida'] = 2200;
    $_SESSION['malenia_vida_maxima'] = 2200;

    $_SESSION['malenia_fase2'] = false;

    $_SESSION['contador_podridao'] = 0;
    $_SESSION['infectado'] = false;

    // waterfowl
    $_SESSION['waterfowl_75'] = false;
    $_SESSION['waterfowl_25'] = false;
    $_SESSION['waterfowl_ativo'] = false;
    $_SESSION['waterfowl_ataque'] = 0;
    $_SESSION['waterfowl_direcao'] = '';

    $_SESSION['batalha_malenia'] = true;

    $_SESSION['mensagem_batalha'] =
        "A batalha contra Malenia começou!";

    header("Location: cena8.php");
    exit;
}

// verifica se a batalha começou

$batalha_iniciada =
    isset($_SESSION['batalha_malenia']);


// waterfowl dance

if (
    $batalha_iniciada &&
    $_SESSION['waterfowl_ativo'] &&
    isset($_POST['waterfowl_escolha'])
) {

    $jogador = new Jogador();

    // recupera os dados atuais do jogador
    $jogador->vida =
        $_SESSION['jogador']['vida'];

    $jogador->vida_maxima =
        $_SESSION['jogador']['vida_maxima'];

    $jogador->mana =
        $_SESSION['jogador']['mana'];

    $jogador->mana_maxima =
        $_SESSION['jogador']['mana_maxima'];

    $jogador->usos_cura =
        $_SESSION['jogador']['usos_cura'];

    $jogador->usos_mana =
        $_SESSION['jogador']['usos_mana'];


    // pega a escolha do jogador
    $escolha =
        $_POST['waterfowl_escolha'];

    // pega a direção correta sorteada pelo php
    $direcao_correta =
        $_SESSION['waterfowl_direcao'];

    // número do golpe atual
    $numero_golpe =
        $_SESSION['waterfowl_ataque'];


    $mensagem = "";


// defender

    if ($escolha == 'defender') {

        // defender evita o dano completamente

        // malenia cura 200
        $_SESSION['malenia_vida'] += 200;

        if (
            $_SESSION['malenia_vida']
            >
            $_SESSION['malenia_vida_maxima']
        ) {
            $_SESSION['malenia_vida'] =
                $_SESSION['malenia_vida_maxima'];
        }

        $mensagem =
            "Waterfowl Dance — Golpe "
            . $numero_golpe
            . ": Você defendeu o ataque! "
            . "Não sofreu dano. Malenia recuperou 200 HP.";
    }


// desvio correto

    elseif ($escolha == $direcao_correta) {

        // não recebe dano
        // malenia também não recupera vida

        $mensagem =
            "Waterfowl Dance — Golpe "
            . $numero_golpe
            . ": Você desviou para a "
            . $direcao_correta
            . " e evitou o ataque de Malenia!";
    }


// desvio errado

    else {

        // dano é aplicado imediatamente
        $jogador->receber_dano(200);

        // malenia recupera 50 hp
        $_SESSION['malenia_vida'] += 50;

        if (
            $_SESSION['malenia_vida']
            >
            $_SESSION['malenia_vida_maxima']
        ) {
            $_SESSION['malenia_vida'] =
                $_SESSION['malenia_vida_maxima'];
        }

        $mensagem =
            "Waterfowl Dance — Golpe "
            . $numero_golpe
            . ": Você escolheu "
            . $escolha
            . ", mas o ataque veio pela "
            . $direcao_correta
            . "! Malenia acertou você causando "
            . "200 de dano e recuperou 50 HP.";
    }


// verifica morte do jogador

    if ($jogador->vida <= 0) {

        $_SESSION['jogador']['vida'] =
            0;

            $_SESSION['cena_morte'] = 'cena8.php';
            $_SESSION['etapa'] =
            'cena11';

        unset($_SESSION['batalha_malenia']);

        header("Location: cena11.php");
        exit;
    }


// salva a vida do jogador imediatamente

    $_SESSION['jogador']['vida'] =
        $jogador->vida;


// verifica se foi o 4º golpe

    if ($numero_golpe == 4) {

        // o quarto golpe já foi processado
        // agora a dança termina

        $_SESSION['waterfowl_ativo'] =
            false;

        $_SESSION['waterfowl_ataque'] =
            0;

        $_SESSION['waterfowl_direcao'] =
            '';

        $_SESSION['mensagem_batalha'] =
            $mensagem
            . " A Waterfowl Dance terminou.";

    } else {

        // vai para o próximo golpe

        $proximo_golpe =
            $numero_golpe + 1;

        $_SESSION['waterfowl_ataque'] =
            $proximo_golpe;


        // sorteia uma nova direção
        $direcoes = [
            'esquerda',
            'direita'
        ];

        $_SESSION['waterfowl_direcao'] =
            $direcoes[array_rand($direcoes)];


        $_SESSION['mensagem_batalha'] =
            $mensagem
            . " Prepare-se para o próximo golpe.";
    }


    header("Location: cena8.php");
    exit;
}


// ações do jogador

if (
    $batalha_iniciada &&
    isset($_POST['acao']) &&
    !$_SESSION['waterfowl_ativo']
) {

    $jogador = new Jogador();

    $jogador->vida =
        $_SESSION['jogador']['vida'];

    $jogador->vida_maxima =
        $_SESSION['jogador']['vida_maxima'];

    $jogador->mana =
        $_SESSION['jogador']['mana'];

    $jogador->mana_maxima =
        $_SESSION['jogador']['mana_maxima'];

    $jogador->usos_cura =
        $_SESSION['jogador']['usos_cura'];

    $jogador->usos_mana =
        $_SESSION['jogador']['usos_mana'];


    $acao =
        $_POST['acao'];

    $arma =
        $_SESSION['arma'];

    $mensagem =
        "";

    $acao_valida =
        false;


// ataque normal

    if ($acao == 'ataque_normal') {

        $dano =
            $arma['dano_basico'];

        $_SESSION['malenia_vida'] -=
            $dano;

        if ($_SESSION['malenia_vida'] < 0) {
            $_SESSION['malenia_vida'] = 0;
        }

        $mensagem =
            "Você realizou um ataque normal e causou "
            . $dano
            . " de dano em Malenia.";

        $acao_valida = true;
    }


// ataque especial

    elseif ($acao == 'ataque_especial') {

        $dano =
            $arma['ataque_especial'];

        $custo =
            $arma['custo_mana'];


        if ($jogador->mana < $custo) {

            $mensagem =
                "Mana insuficiente para usar o ataque especial.";

        } else {

            $jogador->gastar_mana($custo);

            $_SESSION['malenia_vida'] -=
                $dano;

            if ($_SESSION['malenia_vida'] < 0) {
                $_SESSION['malenia_vida'] = 0;
            }

            $mensagem =
                "Você usou o ataque especial da "
                . $arma['nome']
                . " e causou "
                . $dano
                . " de dano em Malenia.";

            $acao_valida = true;
        }
    }


// cura

    elseif ($acao == 'curar') {

        if ($jogador->usos_cura <= 0) {

            $mensagem =
                "Você não possui mais usos de cura.";

        } else {

            $jogador->curar(300);

            $jogador->usos_cura--;

            $mensagem =
                "Você recuperou 300 de vida.";

            $acao_valida = true;
        }
    }


// recuperar mana

    elseif ($acao == 'recuperar_mana') {

        if ($jogador->usos_mana <= 0) {

            $mensagem =
                "Você não possui mais usos para recuperar mana.";

        } else {

            $jogador->recuperar_mana(60);

            $jogador->usos_mana--;

            $mensagem =
                "Você recuperou 50 de mana.";

            $acao_valida = true;
        }
    }


// contador da podridão

    if (
        $acao_valida &&
        $_SESSION['malenia_fase2']
    ) {

        $_SESSION['contador_podridao'] +=
            20;

        $mensagem .=
            " Contador da podridão: "
            . $_SESSION['contador_podridao']
            . "/100.";


        if (
            $_SESSION['contador_podridao'] >= 100 &&
            !$_SESSION['infectado']
        ) {

            $_SESSION['infectado'] =
                true;

            $mensagem .=
                " Você está Infectado pela Podridão Escarlate!";
        }
    }


// malenia derrotada

    if ($_SESSION['malenia_vida'] <= 0) {

        $_SESSION['etapa'] =
            'cena10';

        unset($_SESSION['batalha_malenia']);

        header("Location: cena10.php");
        exit;
    }

// entrada na fase 2

    $entrou_fase2 =
        false;


    if (
        !$_SESSION['malenia_fase2'] &&
        $_SESSION['malenia_vida']
        <=
        ($_SESSION['malenia_vida_maxima'] * 0.50)
    ) {

        $_SESSION['malenia_fase2'] =
            true;

        $entrou_fase2 =
            true;


        // escarlate aeonia
        $jogador->receber_dano(100);


        $mensagem .=
            " Malenia entrou na Fase 2! "
            . "Ela liberou a Escarlate Aeonia "
            . "e causou 100 de dano!";
    }


// waterfowl  75

    if (
        !$entrou_fase2 &&
        !$_SESSION['waterfowl_75'] &&
        $_SESSION['malenia_vida']
        <=
        ($_SESSION['malenia_vida_maxima'] * 0.75)
    ) {

        $_SESSION['waterfowl_75'] =
            true;

        $_SESSION['waterfowl_ativo'] =
            true;

        $_SESSION['waterfowl_ataque'] =
            1;


        $direcoes = [
            'esquerda',
            'direita'
        ];

        $_SESSION['waterfowl_direcao'] =
            $direcoes[array_rand($direcoes)];


        $mensagem .=
            " Malenia iniciou a Waterfowl Dance!";
    }


// waterfowl  25

    if (
        !$entrou_fase2 &&
        !$_SESSION['waterfowl_25'] &&
        $_SESSION['malenia_vida']
        <=
        ($_SESSION['malenia_vida_maxima'] * 0.25)
    ) {

        $_SESSION['waterfowl_25'] =
            true;

        $_SESSION['waterfowl_ativo'] =
            true;

        $_SESSION['waterfowl_ataque'] =
            1;


        $direcoes = [
            'esquerda',
            'direita'
        ];

        $_SESSION['waterfowl_direcao'] =
            $direcoes[array_rand($direcoes)];


        $mensagem .=
            " Malenia iniciou a Waterfowl Dance!";
    }

// ataque normal de malenia

    if (
        !$entrou_fase2 &&
        !$_SESSION['waterfowl_ativo']
    ) {

        $chance_ataque =
            rand(1, 100);


        if ($chance_ataque <= 60) {

                // acertou
            $jogador->receber_dano(180);

                // recupera 50 hp
            $_SESSION['malenia_vida'] +=
                    50;


            if (
                $_SESSION['malenia_vida']
                >
                $_SESSION['malenia_vida_maxima']
            ) {

                $_SESSION['malenia_vida'] =
                    $_SESSION['malenia_vida_maxima'];
            }


            $mensagem .=
                 " Malenia realizou um ataque, "
                . "acertou você causando 180 de dano "
                . "e recuperou 50 HP.";

        } else {

            $mensagem .=
                " Malenia errou o ataque.";
        }
    }


// podridão escarlate

    if ($_SESSION['infectado']) {

        $dano_podridao =
            ceil(
                $jogador->vida_maxima * 0.05
            );


        $jogador->receber_dano(
            $dano_podridao
        );


        $mensagem .=
            " A Podridão Escarlate causou "
            . $dano_podridao
            . " de dano.";
    }


// salva jogador

    $_SESSION['jogador']['vida'] =
        $jogador->vida;

    $_SESSION['jogador']['vida_maxima'] =
        $jogador->vida_maxima;

    $_SESSION['jogador']['mana'] =
        $jogador->mana;

    $_SESSION['jogador']['mana_maxima'] =
        $jogador->mana_maxima;

    $_SESSION['jogador']['usos_cura'] =
        $jogador->usos_cura;

    $_SESSION['jogador']['usos_mana'] =
        $jogador->usos_mana;


    $_SESSION['mensagem_batalha'] =
        $mensagem;


// derrota

    if ($jogador->vida <= 0) {

        $_SESSION['cena_morte'] = 'cena8.php';
        $_SESSION['etapa'] =
            'cena11';

        unset($_SESSION['batalha_malenia']);

        header("Location: cena11.php");
        exit;
    }


    header("Location: cena8.php");
    exit;
}


// dados para exibição

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

    $vida_malenia =
        $_SESSION['malenia_vida'];

    $vida_malenia_maxima =
        $_SESSION['malenia_vida_maxima'];

    $mensagem_batalha =
        $_SESSION['mensagem_batalha'];

    $fase2 =
        $_SESSION['malenia_fase2'];

    $contador_podridao =
        $_SESSION['contador_podridao'];

    $infectado =
        $_SESSION['infectado'];

    $waterfowl_ativo =
        $_SESSION['waterfowl_ativo'];

    $waterfowl_ataque =
        $_SESSION['waterfowl_ataque'];
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Malenia, Blade of Miquella</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<?php if (!$batalha_iniciada): ?>

    <!-- introdução -->

    <img
        src="imagens/malenia.png"
        width="100%"
    >


    <h1>O Refúgio Desolado</h1>


    <p>
        Derrotado por Morgott, o corpo do Maculado
        é arrastado pelas correntes do destino até
        as raízes podres da Árvore Sacra de Miquella.
        Ao recobrar a consciência, a figura lendária
        de Malenia, Blade of Miquella, desembainha
        sua espada.
    </p>


    <form method="post">

        <button
            type="submit"
            name="iniciar_batalha"
        >
            Iniciar Batalha
        </button>

    </form>


<?php else: ?>

    <!-- malenia -->

    <img
        src="imagens/malenia.png"
        width="100%"
    >


    <h1>Malenia, Blade of Miquella</h1>


    <h2>Vida de Malenia</h2>


    <progress
        value="<?php echo $vida_malenia; ?>"
        max="<?php echo $vida_malenia_maxima; ?>"
    >
    </progress>


    <p>
        <?php echo $vida_malenia; ?>
        /
        <?php echo $vida_malenia_maxima; ?>
        HP
    </p>


    <?php if ($fase2): ?>

        <h2>FASE 2 — Malenia, Goddess of Rot</h2>

        <p>
            Contador da podridão:
            <?php echo $contador_podridao; ?>
            / 100
        </p>

    <?php endif; ?>


    <hr>

    <!-- jogador -->

    <h2>Maculado</h2>


    <p>
        Vida:
        <?php echo $vida_jogador; ?>
        /
        <?php echo $vida_jogador_maxima; ?>
    </p>


    <progress
        value="<?php echo $vida_jogador; ?>"
        max="<?php echo $vida_jogador_maxima; ?>"
    >
    </progress>


    <p>
        Mana:
        <?php echo $mana_jogador; ?>
        /
        <?php echo $mana_jogador_maxima; ?>
    </p>


    <progress
        value="<?php echo $mana_jogador; ?>"
        max="<?php echo $mana_jogador_maxima; ?>"
    >
    </progress>


    <?php if ($infectado): ?>

        <h3>
            INFECTADO — PODRIDÃO ESCARLATE
        </h3>

        <p>
            Você perde 5% do seu HP máximo por turno.
        </p>

    <?php endif; ?>


    <hr>


    <!-- waterfowl dance -->

    <?php if ($waterfowl_ativo): ?>


        <h1>WATERFOWL DANCE</h1>


        <h2>
            Golpe
            <?php echo $waterfowl_ataque; ?>
            de 4
        </h2>


        <p>
            Malenia avança para realizar seu próximo golpe!
            Escolha como reagir.
        </p>


        <form method="post">


            <button
                type="submit"
                name="waterfowl_escolha"
                value="esquerda"
            >
                Desviar para a Esquerda
            </button>


            <button
                type="submit"
                name="waterfowl_escolha"
                value="direita"
            >
                Desviar para a Direita
            </button>


            <button
                type="submit"
                name="waterfowl_escolha"
                value="defender"
            >
                Defender
            </button>


        </form>


    <?php else: ?>


        <!-- mensagem -->

        <h2>
            <?php echo $mensagem_batalha; ?>
        </h2>


        <!-- ações -->

        <h2>Escolha sua ação</h2>


        <form method="post">


            <button
                type="submit"
                name="acao"
                value="ataque_normal"
            >

                <img
                    src="imagens/normal.webp"
                    width="100"
                    height="100"
                >

                <br>

                Ataque Normal

            </button>


            <button
                type="submit"
                name="acao"
                value="ataque_especial"
            >

                <img
                    src="imagens/ataque_especial.webp"
                    width="100"
                    height="100"
                >

                <br>

                Ataque Especial

            </button>


            <button
                type="submit"
                name="acao"
                value="curar"
            >

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
                value="recuperar_mana"
            >

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

