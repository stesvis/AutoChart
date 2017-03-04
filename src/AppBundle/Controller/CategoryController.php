<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categories_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category');
        return $this->render('category/index.html.twig', array('categories' => $categories));
    }
}
