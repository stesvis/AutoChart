<?php

namespace AppBundle\Controller\NavMenu;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends Controller
{
    /**
     * @Route("/contact", name="contact_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction()
    {
        return $this->render('menu/contact.html.twig');
    }

    /**
     * @Route("/about", name="about_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('menu/about.html.twig');
    }

}
