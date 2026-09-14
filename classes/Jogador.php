<?php

class Jogador
{
    public $vida;
    public $vida_maxima;

    public $mana;
    public $mana_maxima;

    public $usos_cura;
    public $usos_mana;


    public function __construct()
    {
        $this->vida_maxima = 1000;
        $this->vida = 1000;

        $this->mana_maxima = 100;
        $this->mana = 100;

        $this->usos_cura = 4;
        $this->usos_mana = 2;
    }


    // recebe dano
    public function receber_dano($dano)
    {
        $this->vida -= $dano;

        if ($this->vida < 0) {
            $this->vida = 0;
        }
    }


    // cura o jogador
    public function curar($quantidade)
    {
        $this->vida += $quantidade;

        if ($this->vida > $this->vida_maxima) {
            $this->vida = $this->vida_maxima;
        }
    }


    // recupera mana
    public function recuperar_mana($quantidade)
    {
        $this->mana += $quantidade;

        if ($this->mana > $this->mana_maxima) {
            $this->mana = $this->mana_maxima;
        }
    }


    // gasta mana
    public function gastar_mana($quantidade)
    {
        if ($this->mana >= $quantidade) {
            $this->mana -= $quantidade;
            return true;
        }

        return false;
    }
}
?>