<?php

namespace Digitix\FrameworkBundle\Utils;

class Tools
{
	public static function genPassword($char = 10)
    {
        $password = "";
        $tab      = array_merge(range('a','z'), range('A','Z'), range('0','9'));
        $nb       = count($tab);

        for($i = 0; $i <= $char; $i++)
            $password .= $tab[rand(0,$nb-1)];

        return $password;
    }
}
