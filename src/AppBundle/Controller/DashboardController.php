<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Class DashboardController
 * @package AppBundle\Controller
 * @Route("/")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('dashboard/index.html.twig');
    }
}
