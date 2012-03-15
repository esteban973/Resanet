<?php

namespace Resanet\PaiementBundle\Classes;

/**
 * Description of Paiement
 *
 * @author user1
 */
class ConnexionPayPal {
    private $url;
    private $username;
    private $pwd;
    private $signature;
    private $version;
    private $reservation;
    
    
    
    
    public function setCommun($url, $version, $pwd, $username, $signature, $reservation ){
        $this->url=$url;
        $this->version=$version;
        $this->pwd=$pwd;
        $this->username=$username;
        $this->signature=$signature;
        $this->reservation=$reservation;

    }
    
    
    
   
    
       
    public function connectionApiPaypal($requete){
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $this->url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);
        $httpResponse = curl_exec($ch);
	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request");
	}
	return $httpParsedResponseAr;
    }
    /*
     * @todo ajouter la description des objets et le montant de la remise
     */
    
    
    public function getRequeteCommune(){
        $requete = "VERSION=".$this->version.
                    "&PWD=".$this->pwd.
                    "&USER=".$this->username.
                    "&SIGNATURE=".$this->signature.
                "&PAYMENTREQUEST_0_CURRENCYCODE=EUR".
                "&LOCALECODE=FR".
                "&PAYMENTREQUEST_0_AMT=".$this->reservation->getTotal();
        $arrivee=$this->reservation->getDateArrivee()->format('d-m-Y');
        $arrivee2=$this->reservation->getDateArrivee()->format('Y-m-d');
        $depart=$this->reservation->getDateDepart()->format('d-m-Y');
        $depart2=$this->reservation->getDateDepart()->format('Y-m-d');
        $duree=$this->reservation->getNbNuits().' nuits du '.$arrivee.' au '.$depart;
        $i=0;
        
        foreach ($this->reservation->getChambres() as $chambre) {
             $prixChbre=$chambre->getCategorie()->calculPrixPer($arrivee2, $depart2, null);       
            $requete=$requete.
                    "&L_PAYMENTREQUEST_0_NAME".$i."=Chambre ".$chambre->getCategorie()->getNom().
                    "&L_PAYMENTREQUEST_0_DESC".$i."=".urlencode($duree).
                    "&L_PAYMENTREQUEST_0_AMT".$i.'='.$prixChbre.
                    "&L_PAYMENTREQUEST_0_QTY".$i."=1";
            $i++;
        }
        foreach ($this->reservation->getReservationOptionRes() as $option) {
                   
            $requete=$requete.
                    "&L_PAYMENTREQUEST_0_NAME".$i."=".$option->getOptionRes()->getNom().
                    "&L_PAYMENTREQUEST_0_DESC".$i."=".$option->getOptionRes()->getDescription().
                    "&L_PAYMENTREQUEST_0_AMT".$i.'='.$option->getOptionRes()->getPrix().
                    "&L_PAYMENTREQUEST_0_QTY".$i."=".$option->getQuantite();
            $i++;
        }
        
       
        return $requete;
    }
    
    public function getRequeteToken($urlReturn, $urlCancel){
        $requete = $this->getRequeteCommune().
                "&METHOD=SetExpressCheckout".
               "&RETURNURL=".$urlReturn.
               "&CANCELURL=".$urlCancel;
        return $requete;
    }
    
     public function getPaiement($token, $payerId){
        $requete = $this->getRequeteCommune().
                "&METHOD=DoExpressCheckoutPayment".
               '&TOKEN='.$token.
               '&PAYERID='.$payerId.
               '&PAYMENTREQUEST_0_PAYMENTACTION=Sale';
       return $requete;
    }
    
    
    
}

?>
