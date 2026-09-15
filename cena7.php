<?php

session_start();

require_once 'classes/Arma.php';
require_once 'classes/Jogador.php';


// protecao

if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena7') {
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

    $_SESSION['maliketh_vida'] = 1900;
    $_SESSION['maliketh_vida_maxima'] = 1900;

    // salto sombrio
    $_SESSION['salto_sombrio'] = false;

    // turnos do corte do hp maximo
    $_SESSION['corte_hp_turnos'] = 0;

    // turnos da degradacao
    $_SESSION['degradacao_turnos'] = 0;

    // controle da batalha
    $_SESSION['batalha_maliketh'] = true;

    $_SESSION['mensagem_batalha'] =
        "A batalha contra Maliketh começou!";

    header("Location: cena7.php");
    exit;
}

// verifica se a batalha comecou

$batalha_iniciada = isset($_SESSION['batalha_maliketh']);


// processamento da batalha

if (
    $batalha_iniciada &&
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['acao'])
) {

    $acao = $_POST['acao'];

    $jogador = new Jogador();


// recupera dados do jogador

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

    $mensagem = "";


// degradacao

    if ($_SESSION['degradacao_turnos'] > 0) {

        $dano_degradacao =
            ceil($jogador->vida_maxima * 0.03);

        $jogador->receber_dano($dano_degradacao);

        $_SESSION['degradacao_turnos']--;

        $mensagem .=
            "A Degradação causou "
            . $dano_degradacao
            . " de dano. ";
    }

// ataque do jogador

    $arma = $_SESSION['arma'];


// salto sombrio

    if (
        $_SESSION['salto_sombrio'] == true &&
        $acao == 'ataque_normal'
    ) {

        $mensagem .=
            "Maliketh está no ar! "
            . "Seu ataque não causou dano.";

    }

    elseif (
        $_SESSION['salto_sombrio'] == true &&
        $acao == 'ataque_especial'
    ) {


        $custo = $arma['custo_mana'];

        if ($jogador->mana < $custo) {

            $mensagem .=
                "Mana insuficiente para usar o ataque especial.";

        } else {

            $jogador->gastar_mana($custo);

            $mensagem .=
                "Maliketh está no ar! "
                . "Seu ataque especial não causou dano.";
        }
    }


// ataque normal do jogador

    elseif ($acao == 'ataque_normal') {

        $dano = $arma['dano_basico'];

        $_SESSION['maliketh_vida'] -= $dano;

        if ($_SESSION['maliketh_vida'] < 0) {
            $_SESSION['maliketh_vida'] = 0;
        }

        $mensagem .=
            "Você realizou um ataque normal e causou "
            . $dano
            . " de dano em Maliketh.";
    }


// ataque especial do jogador
    elseif ($acao == 'ataque_especial') {

        $dano = $arma['ataque_especial'];

        $custo = $arma['custo_mana'];


        if ($jogador->mana < $custo) {

            $mensagem .=
                "Mana insuficiente para usar o ataque especial.";

        } else {

            $jogador->gastar_mana($custo);

            $_SESSION['maliketh_vida'] -= $dano;

            if ($_SESSION['maliketh_vida'] < 0) {
                $_SESSION['maliketh_vida'] = 0;
            }

            $mensagem .=
                "Você usou o ataque especial da "
                . $arma['nome']
                . " e causou "
                . $dano
                . " de dano em Maliketh.";
        }
    }


// curar

    elseif ($acao == 'curar') {

        if ($jogador->usos_cura <= 0) {

            $mensagem .=
                "Você não possui mais usos de cura.";

        } else {

            $cura = 300;

            $jogador->curar($cura);

            $jogador->usos_cura--;

            $mensagem .=
                "Você recuperou "
                . $cura
                . " de vida.";
        }
    }


// recuperar mana

    elseif ($acao == 'recuperar_mana') {

        if ($jogador->usos_mana <= 0) {

            $mensagem .=
                "Você não possui mais usos para recuperar mana.";

        } else {

            $jogador->recuperar_mana(50);

            $jogador->usos_mana--;

            $mensagem .=
                "Você recuperou 50 de mana.";
        }
    }


// verifica se maliketh morreu

    if ($_SESSION['maliketh_vida'] <= 0) {

        $_SESSION['etapa'] = 'cena9';

        unset($_SESSION['batalha_maliketh']);

        header("Location: cena9.php");
        exit;
    }


// ataque de maliketh

    if ($_SESSION['salto_sombrio'] == true) {



// final do salto sombrio

        $jogador->receber_dano(300);

        $mensagem .=
            " Maliketh desceu do Salto Sombrio "
            . "e causou 300 de dano!";

        $_SESSION['salto_sombrio'] = false;

    } else {

        // primeiro verifica se maliketh decidiu atacar
        $chance_atacar = rand(1, 100);

        if ($chance_atacar <= 70) {

            // maliketh decidiu atacar
            $tipo_ataque = rand(1, 100);


// ataque normal 60

            if ($tipo_ataque <= 60) {

                $jogador->receber_dano(180);

                $mensagem .=
                    " Maliketh realizou um ataque.";
            }


// salto sombrio 20

            elseif ($tipo_ataque <= 80) {

                $_SESSION['salto_sombrio'] = true;

                $mensagem .=
                    " Maliketh realizou o Salto Sombrio! "
                    . "Ele ficou inalcançável e descerá "
                    . "no próximo turno.";
            }


// lamina destinada 20

            else {

                $jogador->receber_dano(200);

                $mensagem .=
                    " Maliketh realizou a Lâmina Destinada! "
                    . "O corte causou 200 de dano.";


// corte do hp maximo

                if ($_SESSION['corte_hp_turnos'] <= 0) {

                    $novo_maximo =
                        floor($jogador->vida_maxima * 0.93);

                    $jogador->vida_maxima =
                        $novo_maximo;

                    if ($jogador->vida > $jogador->vida_maxima) {

                        $jogador->vida =
                            $jogador->vida_maxima;
                    }

                    $_SESSION['corte_hp_turnos'] = 3;

                    $mensagem .=
                        " Aplicou uma redução no HP Máximo  "
                        . "de 7% por 3 turnos.";
                }


// ativa a degradacao

                $_SESSION['degradacao_turnos'] = 3;

                $mensagem .=
                    " Aplicou uma degradação "
                    . "por 3 turnos.";
            }

        } else {

            // maliketh errou o ataque

            $mensagem .=
                " Maliketh errou o ataque.";
        }
    }



// reduz duracao do corte do hp maximo

    if ($_SESSION['corte_hp_turnos'] > 0) {

        $_SESSION['corte_hp_turnos']--;
    }


// salva os dados do jogador

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

    $_SESSION['cena_morte'] = 'cena7.php';  
    $_SESSION['etapa'] = 'cena11';

        unset($_SESSION['batalha_maliketh']);

        header("Location: cena11.php");
        exit;
    }

// proximo turno


    header("Location: cena7.php");
    exit;
}


// dados para exibicao

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

    $vida_maliketh =
        $_SESSION['maliketh_vida'];

    $vida_maliketh_maxima =
        $_SESSION['maliketh_vida_maxima'];

    $mensagem_batalha =
        $_SESSION['mensagem_batalha'];

    $salto_ativo =
        $_SESSION['salto_sombrio'];

    $corte_turnos =
        $_SESSION['corte_hp_turnos'];

    $degradacao_ativa =
        $_SESSION['degradacao_turnos'];
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Cena 7 - Maliketh</title>

</head>

<body>


<?php if (!$batalha_iniciada): ?>

    <!-- introducao -->

    <img
        src="imagens/maliketh.png"
        width="100%"
    >


    <h1>O Guardião da Morte</h1>


    <p>
        Vitorioso contra Morgott, o Maculado é subitamente envolvido
        por correntes de energia cinzenta e transportado contra sua
        vontade para o céu tempestuoso de Farum Azula em ruínas.
        Diante dele, entre escombros flutuantes, surge Maliketh,
        the Black Blade.
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

    <!-- batalha -->
    <img
        src="imagens/maliketh.png"
        width="100%"
    >


    <h1>Maliketh, the Black Blade</h1>


    <!-- vida de maliketh -->

    <h2>Vida de Maliketh</h2>

    <progress
        value="<?php echo $vida_maliketh; ?>"
        max="<?php echo $vida_maliketh_maxima; ?>"
    >
    </progress>

    <p>
        <?php echo $vida_maliketh; ?>
        /
        <?php echo $vida_maliketh_maxima; ?>
        HP
    </p>


    <hr>


    <!-- jogador -->

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
        max="<?php echo $vida_jogador_maxima; ?>"
    >
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
        max="<?php echo $mana_jogador_maxima; ?>"
    >
    </progress>


    <!-- efeitos -->

    <?php if ($salto_ativo): ?>

        <h3>
            Maliketh está realizando o Salto Sombrio!
        </h3>

        <p>
            Seus ataques não causarão dano neste turno.
            Você ainda pode curar ou recuperar mana.
        </p>

    <?php endif; ?>


    <?php if ($corte_turnos > 0): ?>

        <p>
            Corte do HP Máximo:
            <?php echo $corte_turnos; ?>
            turnos restantes.
        </p>

    <?php endif; ?>


    <?php if ($degradacao_ativa > 0): ?>

        <p>
            Degradação:
            <?php echo $degradacao_ativa; ?>
            turnos restantes.
        </p>

    <?php endif; ?>


    <hr>


    <h2>
        <?php echo $mensagem_batalha; ?>
    </h2>


    <!-- acoes -->

    <h2>Escolha sua ação</h2>


    <form method="post">


        <!-- ataque normal -->

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


        <!-- ataque especial -->

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


        <!-- cura -->

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


        <!-- recuperar mana -->

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


</body>

</html>
