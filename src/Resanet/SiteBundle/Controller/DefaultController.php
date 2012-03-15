<?php

namespace Resanet\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Resanet\MainBundle\Entity\Reservation;
use Resanet\MainBundle\Entity\Client;
use Resanet\MainBundle\Entity\ReservationOptionRes;
use Resanet\MainBundle\Entity\OptionRes;
use Resanet\MainBundle\Entity\Coupon;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('ResanetSiteBundle:Default:index.html.twig');
    }
    
    

    
    
}
