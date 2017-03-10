<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="category_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $categories = null;

        try
        {
            $em = $this->getDoctrine()->getManager();
            $categories = $em->getRepository('AppBundle:Category')
                ->findAll();
        } catch (\Exception $ex)
        {

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

        try
        {
            $category = $em->getRepository('AppBundle:Category')->find($id);

            $form = $this->createForm(CategoryFormType::class, $category);

            // only handles data on POST
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $category = $form->getData();
                $category->setModifiedAt(new \DateTime('now'));

                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute('category_list');
            }
        } catch (\Exception $ex)
        {
            die($ex->getMessage());
        }

        return $this->render('category/edit.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/new", name="category_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $category = $form->getData();

            $category->setCreatedAt(new \DateTime('now'));
            $category->setModifiedAt(new \DateTime('now'));
            $category->setCreatedBy($this->getUser());
            $category->setModifiedBy($this->getUser());
            $category->setStatus('A');

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }
}
