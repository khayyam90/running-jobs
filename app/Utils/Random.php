<?php

namespace App\Utils;

class Random{
    public static function make($length, $allowNonAlpha = false, $onlyNumbers = false){
        if ($onlyNumbers){
            $characters = "0123456789";
        }else{
            $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            if ($allowNonAlpha){
                $characters .= "!?$%&*+-/.";
            }
        }
        
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}