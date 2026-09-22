<?php

// ---------------------------------------------------------
// Configuração de conexão com o banco de dados
// Ajuste estas informações conforme o seu ambiente
// (ex.: XAMPP/WAMP normalmente usam usuário "root" e senha vazia)
// ---------------------------------------------------------

define('DB_HOST', 'localhost');
define('DB_NOME', 'elden_ring_site');
define('DB_USUARIO', 'root');
define('DB_SENHA', '');


// abre (ou reaproveita) a conexão com o banco
function conectar_bd()
{
    static $pdo = null;

    // evita abrir uma conexão nova em toda chamada
    if ($pdo !== null) {
        return $pdo;
    }

    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NOME . ";charset=utf8mb4",
        DB_USUARIO,
        DB_SENHA,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    return $pdo;
}


// grava ou atualiza a pontuação de um jogador no ranking
// (um jogador com o mesmo nome tem sua pontuação atualizada,
// em vez de gerar uma linha nova a cada morte)
function salvar_pontuacao($nome, $pontos)
{
    try {

        $pdo = conectar_bd();

        $sql = "INSERT INTO ranking (nome, pontos)
                VALUES (:nome, :pontos)
                ON DUPLICATE KEY UPDATE pontos = :pontos2";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'nome'    => $nome,
            'pontos'  => $pontos,
            'pontos2' => $pontos
        ]);

        return true;

    } catch (PDOException $e) {

        // se o banco estiver fora do ar, o jogo continua
        // funcionando normalmente (só o ranking não é atualizado)
        error_log('Erro ao salvar pontuação: ' . $e->getMessage());

        return false;
    }
}
