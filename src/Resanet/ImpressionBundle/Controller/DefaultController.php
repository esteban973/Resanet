<?php

namespace Resanet\ImpressionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Resanet\ImpressionBundle\Bibliotheques\FPDF;
use Resanet\ImpressionBundle\Classes\pdfFact;
use Resanet\ImpressionBundle\Classes\dateFr;
use Resanet\ImpressionBundle\Classes\ReservationToFacture;

class DefaultController extends Controller {

    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $res = $em->getRepository('ResanetMainBundle:Reservation')->find($this->getRequest()->get('id'));
        return $this->forward('ResanetImpressionBundle:Default:printFacture', array('resa' => $res));
    }

    public function testAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $res = $em->getRepository('ResanetMainBundle:Reservation')->find('44');
        $test = new ReservationToFacture($res);
        return new Response(var_dump($test->getOptions()));
    }

    public function printFactureAction($resa) {
        $resToFact = new ReservationToFacture($resa);
        
        ///////////////////////////EN TETE/////////////////////////////////////
        //Paramètres de l'en tête
        $resToFact->setNom($this->container->getParameter('nomHotel'));
        $resToFact->setAdresse( $this->container->getParameter('adresseHotel'));
        $resToFact->setCp( $this->container->getParameter('cpHotel'));
        $resToFact->setVille( $this->container->getParameter('villeHotel'));
        $resToFact->setSiret( $this->container->getParameter('siretHotel'));

        $pdf = $resToFact->getPDF();
        
        $response= new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

}
