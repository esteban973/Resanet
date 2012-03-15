<?php

namespace Resanet\ImpressionBundle\Classes;
use Resanet\ImpressionBundle\Bibliotheques\FPDF;

class pdfFact {
    
    
   function __construct($ResToFact) {
        
        $this->AddPage();
        $carEuro = ' ' . chr(128);

        //hotel
        $this->Image('logo.png', 10, 6, 30);
        $this->Ln();
        $this->SetXY(10, 15);
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(80, 10, $nom . "\n" . $ResToFact->getAdresse() . "\n" . $ResToFact->getcp() . " " . $ResToFact->getville() . "\nSiret : " . $ResToFact->getsiret(), 1);
        
        //'FACTURE'
        $this->SetXY(130, 15);
        $this->SetFont('Arial', 'B', 14); //on passe en gras
        $this->Cell(40, 10, 'FACTURE', 0, 1);
        
        //la date
        $mois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
        $datefr = date("d") . " " . $mois[date("n")] . " " . date("Y");
        $this->SetXY(130, 30);
        $this->SetFont('Arial', '', 11); //on revient en normal
        $this->Cell(40, 10, $datefr, 0, 1);
        
        //coordonnees du client
        $this->SetXY(130, 45);
        $this->SetFont('Arial', 'U', 11); //souligné
        $this->Cell(30, 8, utf8_decode('Vos coordonnées :'), 0, 2);
        $this->SetFont('Arial', '', 11); //normal
        $this->SetXY(130, 55);
        $this->MultiCell(80, 7, $ResToFact->getClient()->getNom() . "\n" . $ResToFact->getClient()->getAdresse() . "\n" . $ResToFact->getClient()->getCp() . " " . $ResToFact->getClient()->getVille());
        
        ////////////////////////CORPS DE LA FACTURE////////////////////////////

        $this->SetXY(10, 85);

        //les dates
        $this->Cell(70, 5, utf8_decode('Votre séjour du ') . $ResToFact->getDateArrivee()->format('d/m/y') . ' au ' . $ResToFact->getDateDepart()->format('d/m/y'), 0, 2);
        $this->cell(50, 5, utf8_decode('Réf : ') . $ResToFact->getNumRes(), 0, 2);

        //les chambres
        $this->SetFont('Arial', 'U', 13); //souligné
        $this->Cell(30, 10, "Chambres :", 0, 2);
        $this->SetFont('Arial', '', 11); //normal
   }
    
    
}
?>
