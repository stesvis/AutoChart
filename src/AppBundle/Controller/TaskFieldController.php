<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskField;
use AppBundle\Form\TaskFieldFormType;
use AppBundle\Includes\Constants;
use AppBundle\Includes\RoleEnums;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskFieldController
 *
 * @Route("/taskfields")
 * @package AppBundle\Controller
 */
class TaskFieldController extends Controller
{
    /**
     * @Route("/", name="taskfield_list")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

//        $taskFields = $em->getRepository('AppBundle:TaskField')
//            ->findBy([
//                'createdBy' => $this->get('user_service')->getEntitledUsers(),
//            ]);

        $queryBuilder = $em->getRepository('AppBundle:Task')->createQueryBuilder('t');

        if (in_array(RoleEnums::SuperAdmin, $this->getUser()->getRoles())) {
            $query = $queryBuilder
                ->orderBy('t.createdBy')
                ->getQuery();
        } else {
            $query = $queryBuilder
                ->Where('t.createdBy = :user_id')
                ->setParameter('user_id', $this->getUser()->getId())
                ->orderBy('t.name')
                ->getQuery();
        }

        $paginator = $this->get('knp_paginator');

        $taskFields = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), //page number
            Constants::ROWS_PER_PAGE //limit per page
        );

        return $this->render('taskField/index.html.twig', [
            'taskFields' => $taskFields
        ]);
    }

    /**
     * @Route("/new", name="taskfield_new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $field = new TaskField();

        // Check if we need to pre-populate with a Task
        if (null !== $request->query->get('task')) {
            $task = $em->getRepository('AppBundle:Task')
                ->findOneBy([
                    'id' => $request->query->get('task'),
                    'createdBy' => $this->getUser(),
                ]);
            $field->setTask($task);
        }

        $form = $this->createForm(TaskFieldFormType::class, $field);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $field = $form->getData();

            $field->setCreatedAt(new \DateTime('now'));
            $field->setModifiedAt(new \DateTime('now'));
            $field->setCreatedBy($this->getUser());
            $field->setModifiedBy($this->getUser());
            $field->setStatus(StatusEnums::Active);

            $em->persist($field);
            $em->flush();

            return $this->redirectToRoute('taskfield_list');
        }

        return $this->render('taskField/new.html.twig', [
            'taskfieldForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="taskfield_edit")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $field = $em->getRepository('AppBundle:TaskField')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);

        if (!$field) {
            throw $this->createNotFoundException(
                'No field found for id ' . $id
            );
        }

        $form = $this->createForm(TaskFieldFormType::class, $field);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $field = $form->getData();

            $field->setCreatedAt(new \DateTime('now'));
            $field->setModifiedAt(new \DateTime('now'));
            $field->setCreatedBy($this->getUser());
            $field->setModifiedBy($this->getUser());
            $field->setStatus(StatusEnums::Active);

            $em->persist($field);
            $em->flush();

            return $this->redirectToRoute('taskfield_list');
        }

        return $this->render('taskField/new.html.twig', [
            'taskfieldForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/show", name="taskfield_show")
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $field = null;

        $em = $this->getDoctrine()->getManager();

        $field = $em->getRepository('AppBundle:TaskField')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);

        if (!$field) {
            throw $this->createNotFoundException(
                'No field found for id ' . $id
            );
        }

        return $this->render('taskField/show.html.twig', [
            'field' => $field
        ]);
    }

    /**
     * @Route("/{id}", name="taskfield_delete")
     * @param Request $request
     * @param $id
     * @Method("DELETE")
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $field = $em->getRepository('AppBundle:TaskField')
                ->findOneBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->getUser(),
                ]);

            if (!$field) {
                throw $this->createNotFoundException('Unable to find TaskField entity.');
            }

            // Check if the task is in use on any task
            $servicesByTask = $em->getRepository('AppBundle:Service')
                ->findBy([
                    'task' => $field->getTask()
                ]);

            if (count($servicesByTask) == 0) {
                // Safe to remove
                $field->setStatus(StatusEnums::Deleted);
                $em->persist($field);
                $em->flush();

                $response['success'] = true;
                $response['message'] = 'Task deleted.';

                return new JsonResponse($response, 200);

            } else {
                $response['success'] = false;
                $response['message'] = 'This Field belongs to a Task that is in use on some Services. Delete its references first.';
                return new JsonResponse($response, 401);
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }


    }
}
