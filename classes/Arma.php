<?php

class Arma
{
    public $nome;
    public $dano_basico;
    public $ataque_especial;
    public $custo_mana;

    public function __construct($nome, $dano_basico, $ataque_especial, $custo_mana)
    {
        $this->nome = $nome;
        $this->dano_basico = $dano_basico;
        $this->ataque_especial = $ataque_especial;
        $this->custo_mana = $custo_mana;
    }
}
