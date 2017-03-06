<?php

namespace AppBundle\Controller;

use AppBundle\Form\CategoryFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categories_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $categories = null;

        try {
            $em = $this->getDoctrine()->getManager();
            $categories = $em->getRepository('AppBundle:Category')
                ->findAll();
        } catch (\Exception $ex) {

        }

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categories/{id}/edit", name="category_edit")
     * @param Request $request
     * @param int $id
     * @
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $category = $em->getRepository('AppBundle:Category')->find($id);

            $form = $this->createForm(CategoryFormType::class, $category);

            // only handles data on POST
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $category = $form->getData();
                $category->setModifiedAt(new \DateTime('now'));

                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute('categories_list');
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }

        return $this->render('category/edit.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

}
