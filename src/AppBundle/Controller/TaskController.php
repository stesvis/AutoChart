<?php

namespace AppBundle\Controller;

use AppBundle\Form\TaskFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
    /**
     * @Route("/", name="task_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $tasks = null;

        try {
            $em = $this->getDoctrine()->getManager();
            $tasks = $em->getRepository('AppBundle:Task')
                ->findAll();
        } catch (\Exception $ex) {

        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);

    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @param Request $request
     * @param int $id
     * @
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $task = $em->getRepository('AppBundle:Task')->find($id);

            $form = $this->createForm(TaskFormType::class, $task);

            // only handles data on POST
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();
                $task->setModifiedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->flush();

                return $this->redirectToRoute('task_list');
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }

        return $this->render('task/edit.html.twig', [
            'taskForm' => $form->createView()
        ]);
    }

}
