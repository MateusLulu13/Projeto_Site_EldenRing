-- Cria o banco de dados do site (caso ainda não exista)
CREATE DATABASE IF NOT EXISTS elden_ring_site
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE elden_ring_site;

-- Tabela com o nome do jogador e sua pontuação atual
-- "nome" é único: se o mesmo nome jogar de novo, a pontuação é atualizada
CREATE TABLE IF NOT EXISTS ranking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    pontos INT NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
