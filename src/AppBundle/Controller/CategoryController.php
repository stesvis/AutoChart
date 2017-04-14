<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryFormType;
use AppBundle\Includes\Constants;
use AppBundle\Includes\RoleEnums;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
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
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Category')->createQueryBuilder('c');

        if (in_array(RoleEnums::SuperAdmin, $this->getUser()->getRoles())) {
            $query = $queryBuilder
                ->orderBy('c.createdBy')
                ->getQuery();
        } else {
            $query = $queryBuilder
                ->Where('c.createdBy = :user_id')
                ->setParameter('user_id', $this->getUser()->getId())
                ->orderBy('c.name')
                ->getQuery();
        }

        $paginator = $this->get('knp_paginator');

        $categories = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), //page number
            Constants::ROWS_PER_PAGE //limit per page
        );

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

        $category = $em->getRepository('AppBundle:Category')
            ->findOneBy([
                'id' => $id,
                'status' => StatusEnums::Active,
                'createdBy' => $this->get('user_service')->getEntitledUsers(),
            ]);

        if (!$category) {
            throw $this->createNotFoundException(
                'No Category found for id ' . $id
            );
        }

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

        return $this->render('category/edit.html.twig', [
            'categoryForm' => $form->createView(),
            'category' => $category,
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
            $category->setStatus(StatusEnums::Active);

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/newAjax", name="category_new_ajax")
     *
     * @param $request Request
     *
     * @return Response
     */
    public function newAjaxAction(Request $request)
    {
        try {
            $form = $this->createForm(CategoryFormType::class, new Category(), [
                'hideSubmit' => true,
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();

                    $category = $form->getData();

                    $category->setCreatedAt(new \DateTime('now'));
                    $category->setModifiedAt(new \DateTime('now'));
                    $category->setCreatedBy($this->getUser());
                    $category->setModifiedBy($this->getUser());
                    $category->setStatus(StatusEnums::Active);

                    $em->persist($category);
                    $em->flush();

                    //all good, category saved
                    $response['success'] = true;
                    $response['message'] = 'Category added.';
                    $response['categoryId'] = $category->getId();
                    $response['categoryName'] = $category->getName();

                    return new JsonResponse($response, 200);

                } else {
                    //invalid form
                    $response['success'] = false;
                    $response['message'] = 'Form not valid.';

                    return new JsonResponse($response, 400);
                }
            }

            //return value of the GET request
            return $this->render('category/_form.html.twig', [
                'categoryForm' => $form->createView()
            ]);

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }
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
                'createdBy' => $this->get('user_service')->getEntitledUsers(),
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
        try {
            $em = $this->getDoctrine()->getManager();

            $category = $em->getRepository('AppBundle:Category')
                ->findOneBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->get('user_service')->getEntitledUsers(),
                ]);

            if (!$category) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $children = $em->getRepository('AppBundle:Category')
                ->findByParentCategory($id);
            if ($children != null) {
                throw new Exception('This category has children. What to do?');
            }

            // Check if the category is in use on any task
            $tasksByCategory = $em->getRepository('AppBundle:Task')
                ->findBy([
                    'category' => $category
                ]);

            if (count($tasksByCategory) == 0) {
                // Safe to remove
                $category->setStatus(StatusEnums::Deleted);
                $category->setModifiedBy($this->getUser());

                $em->persist($category);
                $em->flush();

                $response['success'] = true;
                $response['message'] = 'Category deleted.';
            } else {
                $response['success'] = false;
                $response['message'] = 'This Category is in use on some Tasks. Delete its references first.';
            }

            return new JsonResponse($response);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }
    }

}
