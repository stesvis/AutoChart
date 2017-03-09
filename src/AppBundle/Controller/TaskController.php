<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package AppBundle\Controller
 */
class TaskController extends Controller
{


    /**
     * @Route("/tasks", name="task_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $tasks = null;

        try
        {
            $em = $this->getDoctrine()->getManager();
            $tasks = $em->getRepository('AppBundle:Task')
                ->findAll();
        } catch (\Exception $ex)
        {
            die($ex->getMessage());
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);

    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        try
        {
            $task = $em->getRepository('AppBundle:Task')->find($id);

            $form = $this->createForm(TaskFormType::class, $task);

            // only handles data on POST
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $task = $form->getData();
                $task->setModifiedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->flush();

                return $this->redirectToRoute('task_list');
            }
        } catch (\Exception $ex)
        {
            die($ex->getMessage());
        }

        return $this->render('task/edit.html.twig', [
            'taskForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/tasks/new", name="task_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $task = $form->getData();
            
            $task->setCreatedAt(new \DateTime('now'));
            $task->setModifiedAt(new \DateTime('now'));
            $task->setCreatedBy($this->getUser());
            $task->setModifiedBy($this->getUser());
            $task->setStatus('A');
            $task->setType('Custom');
            $task->setCategory($em->getRepository('AppBundle:Category')->findOneBy([
                'name' => 'Brakes',
            ]));

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/new.html.twig', [
            'taskForm' => $form->createView()
        ]);
    }
}
