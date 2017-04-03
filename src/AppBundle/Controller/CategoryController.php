<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryFormType;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Category controller.
 *
 * @Route("categories")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="category_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $categories = null;

        try {
            $em = $this->getDoctrine()->getManager();
            $categories = $em->getRepository('AppBundle:Category')
                ->findBy([
                    'createdBy' => $this->get('user_service')->getEntitledUsers(),
                ]);
        } catch (\Exception $ex) {

        }

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit")
     * @param Request $request
     * @param int $id
     * @
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $category = $em->getRepository('AppBundle:Category')
                ->findBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->getUser(),
                ]);

            $form = $this->createForm(CategoryFormType::class, $category);

            // only handles data on POST
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $category = $form->getData();
                $category->setModifiedAt(new \DateTime('now'));

                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute('category_list');
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }

        return $this->render('category/edit.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="category_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * @Route("/{id}", name="category_show")
     * @param $id
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $category = null;

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);

        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete")
     * @param $request
     * @param $id
     * @Method("DELETE")
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')
            ->findOneBy([
                'id' => $id,
                'status' => StatusEnums::Active,
                'createdBy' => $this->getUser(),
            ]);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        // Check if the category is in use on any task
        $tasksByCategory = $em->getRepository('AppBundle:Task')
            ->findBy([
                'category' => $category
            ]);

        if (count($tasksByCategory) == 0) {
            // Safe to remove
            $category->setStatus(StatusEnums::Deleted);
            $em->persist($category);
            $em->flush();

            $response['success'] = true;
            $response['message'] = 'Category deleted.';
        } else {
            $response['success'] = false;
            $response['message'] = 'This Category is in use on some Tasks. Delete its references first.';
        }

        return new JsonResponse($response);
    }

}
