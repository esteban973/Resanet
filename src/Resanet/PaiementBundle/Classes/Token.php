<?php
namespace Resanet\PaiementBundle\Classes;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Token
 *
 * @author user1
 */
class Token  {
   
    //put your code here
    public static function generateToken($str) {
        return sha1($str);   
    }
    
    public static function isCsrfTokenValid($str, $token) {
        if (Token::generateToken($str)==$token) return true;
        else return false;
            
    }
}

?>
