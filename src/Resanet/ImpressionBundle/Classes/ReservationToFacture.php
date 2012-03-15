<?php

namespace Resanet\ImpressionBundle\Classes;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReservationToFacture extends \Resanet\ImpressionBundle\Bibliotheques\FPDF{

    private $nom;
    private $adresse;
    private $cp;
    private $ville;
    private $siret;
    private $numRes;
    private $chambres;
    private $totalChambres;
    private $options;
    private $totalOptions;
    private $reductions;
    private $paiements;
    private $client;
    private $dateArr;
    private $dateDep;
    private $solde;
    private $total;
    private $annulation;

    public function __construct($resa) {
        parent::__construct();
        
        //le client
        $this->client = $resa->getClient();
        
        //date début et date fin
        $this->dateArr = $resa->getDateArrivee();
        $this->dateDep = $resa->getDateDepart();
        
        //n° de réservation
        $this->numRes = $resa->getId();
        
        //les chambres
        $chambres = array();
        foreach ($resa->getChambres() as $ch) {
            $cat = $ch->getCategorie();
            if (isset($chambres[$cat->getId()])) {
                $chambres[$cat->getId()]['nbChambres']++;
            } else {
                $tabCh['prixChambres'] = $cat->calculPrixPer($resa->getDateArrivee()->format('Y-m-d'), $resa->getDateDepart()->format('Y-m-d'), $resa->getDateCreation()->format('Y-m-d'));
                $tabCh['nbChambres'] = 1;
                $tabCh['nomCat'] = $cat->getNom();
                $chambres[$cat->getId()] = $tabCh;
            }
            
        }
        $this->chambres=$chambres;
        
        
        //les options
        $options = array();
        foreach ($resa->getReservationOptionRes() as $resOpt) {
                $tabOpt['nomOption'] = $resOpt->getOptionRes()->getNom();
                $tabOpt['nbOption'] = $resOpt->getQuantite();
                $tabOpt['prixUOption'] = $resOpt->getOptionRes()->getPrix();
                $options[$resOpt->getOptionRes()->getId()] = $tabOpt;
        }
        $this->options = $options;
        
        //les reductions
        $this->reductions = $resa->getTotalReductions();
        
        //les paiements
        $this->paiements = $resa->getPaiements();
        
        //annulation
        $this->annulation = $resa->getAnnulation();
        
        //le solde
        $this->solde = $resa->getSolde();
        
        //total de la reservation
        $this->total = $resa->getTotal();
    }
    
    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    public function setCp($cp) {
        $this->cp = $cp;
    }

    public function setVille($ville) {
        $this->ville = $ville;
    }

    public function setSiret($siret) {
        $this->siret = $siret;
    }

    
    public function getPDF(){
        $this->AddPage();
        $carEuro = ' ' . chr(128);

        //hotel
        $this->Image('logo.png', 10, 6, 30);
        $this->Ln();
        $this->SetXY(10, 15);
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(80, 10, $this->nom . "\n" . $this->adresse . "\n" . $this->cp . " " . $this->ville . "\nSiret : " . $this->siret, 1);
        
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
        $this->MultiCell(80, 7, $this->client->getNom() . "\n" . $this->client->getAdresse() . "\n" . $this->client->getCp() . " " . $this->client->getVille());
        
        ////////////////////////CORPS DE LA FACTURE////////////////////////////

        $this->SetXY(10, 85);

        //les dates
        $this->Cell(70, 5, utf8_decode('Votre séjour du ') . $this->dateArr->format('d/m/y') . ' au ' . $this->dateDep->format('d/m/y'), 0, 2);
        $this->cell(50, 5, utf8_decode('Réf : ') . $this->numRes, 0, 2);

        //les chambres
        $this->SetFont('Arial', 'U', 13); //souligné
        $this->Cell(30, 10, "Chambres :", 0, 2);
        $this->SetFont('Arial', '', 11); //normal
        foreach($this->chambres as $ch) {
            $this->Cell(40, 10, $ch['nomCat'], 0, 0);
            $this->Cell(5, 10, 'x' . $ch['nbChambres']);
            $this->Cell(30, 10, number_format($ch['prixChambres'], 2) . $carEuro, 0, 0, 'R');
            $this->Cell(30, 10, number_format($ch['prixChambres']*$ch['nbChambres'], 2) . $carEuro, 0, 0, 'R');
            $this->Ln();
            $this->totalChambres += $ch['prixChambres']*$ch['nbChambres'];
        }
        $this->SetFont('Arial', 'B', 11); //gras
        $this->Cell(75, 10, "Total Chambres : ", 0, 0, 'R');
        $this->Cell(30, 10, number_format($this->totalChambres, 2) . $carEuro, 0, 1, 'R');
        
        //les options
        $this->SetFont('Arial', 'U', 13); //souligné
        $this->Cell(30, 10, "Options :", 0, 2);
        $this->SetFont('Arial', '', 11); //normal
        foreach ($this->options as $opt) {
            $this->Cell(40, 10, utf8_decode($opt['nomOption']));
            $this->Cell(5, 10, 'x' . $opt['nbOption']);
            $this->Cell(30, 10, number_format($opt['prixUOption'], 2) . $carEuro, 0, 0, 'R');
            $this->Cell(30, 10, number_format($opt['nbOption']*$opt['prixUOption'], 2) . $carEuro, 0, 0, 'R');
            $this->Ln();
            $this->totalOptions += $opt['nbOption']*$opt['prixUOption'];
        }
        $this->SetFont('Arial', 'B', 11); //gras
        $this->Cell(75, 10, "Total Options : ", 0, 0, 'R');
        $this->Cell(30, 10, number_format($this->totalOptions, 2) . $carEuro, 0, 1, 0, 0, 'R');
        
        //les réductions
        $this->SetFont('Arial', 'U', 13); //souligné
        $this->Cell(40, 10, utf8_decode("Réductions :"));
        $this->SetFont('Arial', 'B', 11); //gras
        $this->Cell(65, 10,number_format($this->reductions, 2) . $carEuro, 0, 0, 'R');
        $this->Ln();
        $this->Ln();
        $this->Line($this->GetX(), $this->GetY(), $this->GetX()+185, $this->GetY());
        
        //total
        
        $totalHT = ($this->total)/1.196;
        //$totalHT = ($this->totalChambres + $this->totalOptions - $this->reductions) / 1.196;
        $this->SetFont('Arial', 'UB', 13); //gras souligné
        $this->Cell(75, 10, "Total TTC :", 0, 0, 'R');
        $this->SetFont('Arial', 'B', 13); //gras
        $this->cell(30, 10, number_format($totalHT * 1.196, 2) . $carEuro, 0, 0, 'R');
        $this->Cell(30, 10, " dont TVA :", 0, 0, 'R');
        $this->cell(20, 10, round($totalHT * 0.196, 2) . $carEuro, 0, 0, 'L');
        $this->Ln();
        $this->Line($this->GetX(), $this->GetY(), $this->GetX()+185, $this->GetY());
        $this->Ln();
        
        //les paiements
        $this->SetFont('Arial', 'U', 13); //souligné
        $this->Cell(40, 10, utf8_decode("Vos règlements :"));
        $this->SetFont('Arial', '', 11); //normal
        foreach ($this->paiements as $pm) {
            $this->cell(35, 10, $pm->getMoyen(), 0, 0, 'R');
            $this->cell(30, 10, $pm->getMontant(). $carEuro, 0, 0, 'R');
        }
        $this->Ln();
        $this->Ln();
        
        
        $this->SetFont('Arial', 'U', 13); //souligné
        //annulation - remboursement d'arrhes - solde
        if ($this->annulation) {
            $texte = "ARRHES A REMBOURSER :";
            $this->solde = abs($this->solde);
        }else {
           $texte = "RESTE A PAYER :";
        }
        $this->Cell(40, 10, utf8_decode($texte));
        $this->cell(65, 10, number_format($this->solde,2). $carEuro, 0, 0, 'R');
        
        return ($this->Output()); 
    }

}

?>
