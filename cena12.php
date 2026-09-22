<?php
session_start();

require_once 'classes/Arma.php';
require_once 'classes/Jogador.php';

// Proteçao
if (!isset($_SESSION['etapa']) || $_SESSION['etapa'] != 'cena12') {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['arma'])) {
    header("Location: index.php");
    exit;
}

// inicializaçao da batalha ou escolha da aliança
if (isset($_POST['aceitar_alianca'])) {
    $_SESSION['etapa'] = 'cena13';
    header("Location: cena13.php");
    exit;
}

if (isset($_POST['iniciar_batalha'])) {
    $_SESSION['jogador'] = [
        'vida' => 1000,
        'vida_maxima' => 1000,
        'mana' => 100,
        'mana_maxima' => 100,
        'usos_cura' => 4,
        'usos_mana' => 2
    ];

    $_SESSION['ederson_vida'] = 2700;
    $_SESSION['ederson_vida_maxima'] = 2700;

    // estados e modificadores
    $_SESSION['batalha_ederson'] = true;
    $_SESSION['fase2_bsod'] = false;
    $_SESSION['countdown_bsod'] = 0;
    $_SESSION['turno_contador'] = 0;
    $_SESSION['acao_bloqueada'] = null; // 'ataque_normal', 'ataque_especial', 'curar', 'recuperar_mana'
    $_SESSION['loop_infinito_acao'] = null;
    $_SESSION['variavel_corrompida'] = null; // 'ataque', 'cura', 'mana'
    $_SESSION['ederson_vulneravel'] = false;
    $_SESSION['ederson_overflow'] = false;
    $_SESSION['ultima_acao_jogador'] = null;

    $_SESSION['mensagem_batalha'] = "O combate definitivo contra Ederson, Lord of the Web começou!";

    header("Location: cena12.php");
    exit;
}

$batalha_iniciada = isset($_SESSION['batalha_ederson']);

// processamento do turno da batalha
if ($batalha_iniciada && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $jogador = new Jogador();

    // carrega dados do jogador
    $jogador->vida = $_SESSION['jogador']['vida'];
    $jogador->vida_maxima = $_SESSION['jogador']['vida_maxima'];
    $jogador->mana = $_SESSION['jogador']['mana'];
    $jogador->mana_maxima = $_SESSION['jogador']['mana_maxima'];
    $jogador->usos_cura = $_SESSION['jogador']['usos_cura'];
    $jogador->usos_mana = $_SESSION['jogador']['usos_mana'];

    $arma = $_SESSION['arma'];
    $mensagem = "";

    // restauraçao/reset de modificadores temporarios por turno
    $mult_dano_jogador = 1.0;
    $mult_cura_jogador = 1.0;
    $mult_custo_mana = 1.0;

    if ($_SESSION['variavel_corrompida'] == 'ataque') {
        $mult_dano_jogador = 0.7; // -30%
    } elseif ($_SESSION['variavel_corrompida'] == 'cura') {
        $mult_cura_jogador = 1.5; // +50%
    } elseif ($_SESSION['variavel_corrompida'] == 'mana') {
        $mult_custo_mana = 2.0; // Dobrado
    }

    // validaçao das restriçoes de açao do jogador
    if ($acao === $_SESSION['acao_bloqueada']) {
        $mensagem .= "Sua ação foi BLOQUEADA devido ao Syntax Error! Você perdeu o turno. ";
    } elseif ($acao === $_SESSION['loop_infinito_acao']) {
        $mensagem .= "Loop Infinito ativo! Você não pode repetição do tipo de ação anterior. Perdeu a ação. ";
    } else {
        // açao do Jogador
        if ($acao == 'ataque_normal') {
            $dano_base = $arma['dano_basico'] * $mult_dano_jogador;
            if ($_SESSION['ederson_vulneravel']) {
                $dano_base *= 2; // dobro de dano se Ederson estiver vulneravel
            }
            $dano = ceil($dano_base);
            $_SESSION['ederson_vida'] -= $dano;
            $mensagem .= "Você realizou um ataque normal e causou {$dano} de dano em Ederson. ";

        } elseif ($acao == 'ataque_especial') {
            $custo = ceil($arma['custo_mana'] * $mult_custo_mana);

            if ($jogador->mana < $custo) {
                $mensagem .= "Mana insuficiente para usar o ataque especial. ";
            } else {
                $jogador->gastar_mana($custo);
                $dano_base = $arma['ataque_especial'] * $mult_dano_jogador;
                if ($_SESSION['ederson_vulneravel']) {
                    $dano_base *= 2;
                }
                $dano = ceil($dano_base);
                $_SESSION['ederson_vida'] -= $dano;
                $mensagem .= "Você usou {$arma['nome']} e causou {$dano} de dano em Ederson. ";
            }

        } elseif ($acao == 'curar') {
            if ($jogador->usos_cura <= 0) {
                $mensagem .= "Sem usos de cura restantes. ";
            } else {
                $cura = ceil(300 * $mult_cura_jogador);
                $jogador->curar($cura);
                $jogador->usos_cura--;
                $mensagem .= "Você recuperou {$cura} de vida. ";
            }

        } elseif ($acao == 'recuperar_mana') {
            if ($jogador->usos_mana <= 0) {
                $mensagem .= "Sem usos de recuperar mana restantes. ";
            } else {
                $jogador->recuperar_mana(60);
                $jogador->usos_mana--;
                $mensagem .= "Você recuperou 60 de mana. ";
            }
        }
    }

    // Registra ultima açao para o loop infinito
    $_SESSION['loop_infinito_acao'] = null; // limpa apos verificar
    $_SESSION['ultima_acao_jogador'] = $acao;

    if ($_SESSION['ederson_vida'] < 0) {
        $_SESSION['ederson_vida'] = 0;
    }

    // 2. Verificaçao de transiçao para fase 2
    if ($_SESSION['ederson_vida'] <= 0) {
        if (!$_SESSION['fase2_bsod']) {
            $_SESSION['fase2_bsod'] = true;
            $_SESSION['ederson_vida'] = 1000;
            $_SESSION['ederson_vida_maxima'] = 1000;
            $_SESSION['countdown_bsod'] = 5;
            $mensagem .= " ERRO CRÍTICO! PROCESSO ENCERRADO... REINICIANDO PROCESSO... EDESON.EXE REINICIADO COM TELA AZUL DA MORTE!";
        } else {
            // vitoria
            $_SESSION['etapa'] = 'cena13.1';
            unset($_SESSION['batalha_ederson']);
            header("Location: cena13.1.php");
            exit;
        }
    }

    $_SESSION['ederson_vulneravel'] = false;

    // turno de ederson
    if ($_SESSION['ederson_vida'] > 0) {
        $_SESSION['turno_contador']++;
        $_SESSION['acao_bloqueada'] = null; // reseta bloqueio no turno seguinte

        // reduz contador da fase 2
        if ($_SESSION['fase2_bsod']) {
            $_SESSION['countdown_bsod']--;
            if ($_SESSION['countdown_bsod'] <= 0) {
                // Derrota por limite de tempo esgotado
                $_SESSION['cena_morte'] = 'cena12.php';
                $_SESSION['etapa'] = 'cena11';
                unset($_SESSION['batalha_ederson']);
                header("Location: cena11.php");
                exit;
            }
        }

        // a cada 3 turnos ocorre "COMPILANDO NOVA REGRA..."
        if ($_SESSION['turno_contador'] % 3 == 0) {
            $regra = rand(1, 3);
            $mensagem .= " [COMPILANDO NOVA REGRA...] ";

            if ($regra == 1) {
                // Loop Infinito
                $_SESSION['loop_infinito_acao'] = $_SESSION['ultima_acao_jogador'];
                $mensagem .= "LOOP INFINITO: Você não poderá usar a ação '{$_SESSION['ultima_acao_jogador']}' no próximo turno! ";
            } elseif ($regra == 2) {
                // Variável Corrompida
                $vars = ['ataque', 'cura', 'mana'];
                $_SESSION['variavel_corrompida'] = $vars[array_rand($vars)];
                if ($_SESSION['variavel_corrompida'] == 'ataque') {
                    $mensagem .= "VARIÁVEL CORROMPIDA: Seu ataque foi reduzido em 30%! ";
                } elseif ($_SESSION['variavel_corrompida'] == 'cura') {
                    $mensagem .= "VARIÁVEL CORROMPIDA: Sua cura foi aumentada em 50%! ";
                } else {
                    $mensagem .= "VARIÁVEL CORROMPIDA: O consumo de Mana dos seus ataques foi dobrado! ";
                }
            } else {
                // Overflow
                $_SESSION['ederson_overflow'] = true;
                $mensagem .= "OVERFLOW DETECTADO: Ederson prepara um ataque massivo! ";
            }
        } else {
            $_SESSION['variavel_corrompida'] = null;
        }

        // ataques de ederson
        if ($_SESSION['ederson_overflow']) {
            // Ataque especial de Overflow
            $jogador->receber_dano(500);
            $_SESSION['ederson_vulneravel'] = true; // Fica vulnerável no próximo turno
            $_SESSION['ederson_overflow'] = false;
            $mensagem .= " OVERFLOW! Ederson causou 500 de dano crítico, mas ficou extremamente vulnerável!";
        } else {
            // chance de ataque (60% de chance)
            $chance_atacar = rand(1, 100);

            if ($chance_atacar <= 60) {
                $tipo_ataque = rand(1, 100);

                if ($tipo_ataque <= 70) {
                    // ataque normal (70%)
                    $jogador->receber_dano(160);
                    $mensagem .= " Ederson realizou um ataque e causou 160 de dano.";
                } elseif ($tipo_ataque <= 80) {
                    // Segmentation Fault (10%)
                    $jogador->receber_dano(300);
                    $mensagem .= " SEGMENTATION FAULT: Ederson acessou uma área inválida da memória. e causou 300 de dano.";
                } else {
                    // Syntax Error (20%)
                    $jogador->receber_dano(100);
                    $acoes_possiveis = ['ataque_normal', 'ataque_especial', 'curar', 'recuperar_mana'];
                    $_SESSION['acao_bloqueada'] = $acoes_possiveis[array_rand($acoes_possiveis)];
                    $mensagem .= " SYNTAX ERROR: Ederson causa 100 de dano e bloqueou sua ação '{$_SESSION['acao_bloqueada']}' para o próximo turno!";
                }
            } else {
                // falha de ataque
                $mensagem .= " 404: Attack Not Found";
            }
        }
    }

    // salva estado do jogador na sessao
    $_SESSION['jogador']['vida'] = $jogador->vida;
    $_SESSION['jogador']['vida_maxima'] = $jogador->vida_maxima;
    $_SESSION['jogador']['mana'] = $jogador->mana;
    $_SESSION['jogador']['mana_maxima'] = $jogador->mana_maxima;
    $_SESSION['jogador']['usos_cura'] = $jogador->usos_cura;
    $_SESSION['jogador']['usos_mana'] = $jogador->usos_mana;
    $_SESSION['mensagem_batalha'] = $mensagem;

    // morte do jogador
    if ($jogador->vida <= 0) {
        $_SESSION['cena_morte'] = 'cena12.php';
        $_SESSION['etapa'] = 'cena11';
        unset($_SESSION['batalha_ederson']);
        header("Location: cena11.php");
        exit;
    }

    header("Location: cena12.php");
    exit;
}

// dados para interface
if ($batalha_iniciada) {
    $vida_jogador = $_SESSION['jogador']['vida'];
    $vida_jogador_maxima = $_SESSION['jogador']['vida_maxima'];
    $mana_jogador = $_SESSION['jogador']['mana'];
    $mana_jogador_maxima = $_SESSION['jogador']['mana_maxima'];
    $usos_cura = $_SESSION['jogador']['usos_cura'];
    $usos_mana = $_SESSION['jogador']['usos_mana'];

    $vida_ederson = $_SESSION['ederson_vida'];
    $vida_ederson_maxima = $_SESSION['ederson_vida_maxima'];
    $mensagem_batalha = $_SESSION['mensagem_batalha'];
    $fase2 = $_SESSION['fase2_bsod'];
    $countdown = $_SESSION['countdown_bsod'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cena 12 - Ederson, Lord of the Web</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .bsod-box {
            border: 2px solid #000;
            background-color: #0000aa;
            color: #ffffff;
            font-family: monospace;
            padding: 15px;
            white-space: pre;
            margin: 15px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<?php if (!$batalha_iniciada): ?>

    <!-- escolha -->
    <img src="imagens/ederson.png" width="100%" alt="Ederson Lord of the Web">

    <h1>A Térvore</h1>

    <p>
        Com todo o poder acumulado e seu equipamento lendário trajado, o Maculado rompe os espinhos e adentra o interior sagrado e brilhante da Térvore. O chão espelhado reflete a luz dourada imensurável do local e, no centro dessa câmara divina, ergue-se o soberano supremo desta era: <strong>Ederson, Lord of the Web</strong>. Ao notar a chegada do invasor, Ederson se vira lentamente, empunhando o poder do destino para o combate definitivo.
    </p>
    <p>
        Contudo, ao observarem a Térvore prestes a ruir pelo desgaste das eras, ambos percebem que um confronto direto apenas destruiria o que resta do mundo. Diante do risco iminente de ruína total das Terras Intermédias, Ederson compreende o valor do guerreiro à sua frente e abaixa a sua guarda. Em vez de atacar, ele estende sua mão envolta em fios dourados de energia e faz uma proposta solene ao Maculado: unirem suas forças para governar e reconstruir o reino juntos.
    </p>
    <p>
        A escolha do destino agora repousa inteiramente nas mãos do Maculado.
    </p>

    <form method="post">
        <button type="submit" name="aceitar_alianca">
            Aceitar a aliança com Ederson.
        </button>
        <button type="submit" name="iniciar_batalha">
            Rejeitar a proposta.
        </button>
    </form>

<?php else: ?>

    <!-- interface de batalha -->
    <img src="imagens/ederson.png" width="100%" alt="Ederson Lord of the Web">

    <h1>Ederson, Lord of the Web</h1>

    <?php if ($fase2): ?>
        <div class="bsod-box">
╔══════════════════════════╗
║       SYSTEM FAILURE     ║
║                          ║
║   EDESON.EXE REINICIADO  ║
║                          ║
║   TURNOS RESTANTES: <?php echo $countdown; ?>    ║
╚══════════════════════════╝
        </div>
    <?php endif; ?>

    <!-- hp boss -->
    <h2>Vida de Ederson</h2>
    <progress value="<?php echo $vida_ederson; ?>" max="<?php echo $vida_ederson_maxima; ?>"></progress>
    <p><?php echo $vida_ederson; ?> / <?php echo $vida_ederson_maxima; ?> HP</p>

    <hr>

    <!-- hp e mana jogador -->
    <h2>Maculado</h2>
    <p>Vida: <?php echo $vida_jogador; ?> / <?php echo $vida_jogador_maxima; ?> HP</p>
    <progress value="<?php echo $vida_jogador; ?>" max="<?php echo $vida_jogador_maxima; ?>"></progress>

    <p>Mana: <?php echo $mana_jogador; ?> / <?php echo $mana_jogador_maxima; ?> MP</p>
    <progress value="<?php echo $mana_jogador; ?>" max="<?php echo $mana_jogador_maxima; ?>"></progress>

    <hr>

    <h2><?php echo $mensagem_batalha; ?></h2>

    <!-- açoes do jogador -->
    <h2>Escolha sua ação</h2>

    <form method="post">
        <!-- ataque normal -->
        <button type="submit" name="acao" value="ataque_normal">
            <img src="imagens/normal.webp" width="100" height="100"><br>
            Ataque Normal
        </button>

        <!-- Ataque Especial -->
        <button type="submit" name="acao" value="ataque_especial">
            <img src="imagens/ataque_especial.webp" width="100" height="100"><br>
            Ataque Especial
        </button>

        <!-- curar -->
        <button type="submit" name="acao" value="curar">
            <img src="imagens/cura.webp" width="100" height="100"><br>
            Curar<br>
            Usos: <?php echo $usos_cura; ?>
        </button>

        <!-- Recuperar Mana -->
        <button type="submit" name="acao" value="recuperar_mana">
            <img src="imagens/recuperar_mana.webp" width="100" height="100"><br>
            Recuperar Mana<br>
            Usos: <?php echo $usos_mana; ?>
        </button>
    </form>

<?php endif; ?>

</body>
</html>
