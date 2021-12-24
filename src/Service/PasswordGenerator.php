<?php

namespace App\Service;

class PasswordGenerator
{
    /**
     * @param int $longueur
     * @return string
     */
    public function genererMDP ($longueur = 8): string
    {

        $mdp = "";

        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        $longueurMax = strlen($possible);

        if ($longueur > $longueurMax) {
            $longueur = $longueurMax;
        }

        $i = 0;

        while ($i < $longueur) {

            $caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);

            if (!strstr($mdp, $caractere)) {
                $mdp .= $caractere;
                $i++;
            }
        }

        return $mdp;
    }
}