<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Form\JobFormType;
use AppBundle\Includes\Constants;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Job controller.
 *
 * @Route("/jobs")
 */
class JobController extends Controller
{
    /**
     * Lists all job entities.
     *
     * @Route("/", name="job_list")
     * @Method("GET")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

//        $jobs = $em->getRepository('AppBundle:Job')
//            ->findBy([
//                'createdBy' => $this->getUser(),
//            ]);

        $dql = "SELECT j FROM AppBundle:Job j";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');

        $jobs = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), //page number
            Constants::ROWS_PER_PAGE //limit per page
        );

        return $this->render('job/index.html.twig', array(
            'jobs' => $jobs,
        ));
    }

    /**
     * Lists all jobs performed on a specific vehicle.
     *
     * @Route("/vehicle/{id}", name="vehicle_job_list")
     * @param $request
     * @param $id
     * @return Response
     */
    public function indexByVehicle(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

//        $vehicle = $em->getRepository('AppBundle:Vehicle')
//            ->find($id);

//        $jobs = $em->getRepository('AppBundle:Job')
//            ->findBy([
//                'vehicle' => $vehicle,
//            ]);

        $query = $em->getRepository('AppBundle:Job')->findByVehicle($id);

        $paginator = $this->get('knp_paginator');

        $jobs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            Constants::ROWS_PER_PAGE
        );


        return $this->render('job/index.html.twig', array(
            'jobs' => $jobs,
        ));
    }

    /**
     * Creates a new job entity.
     *
     * @Route("/new", name="job_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $job = new Job();

        $em = $this->getDoctrine()->getManager();

        // Check if we need to pre-populate with a Task
        if (null !== $request->query->get('task')) {
            $task = $em->getRepository('AppBundle:Task')
                ->findOneBy([
                    'id' => $request->query->get('task'),
                    'createdBy' => $this->getUser(),
                ]);
            $job->setTask($task);
        }

        // Check if we need to pre-populate with a Vehicle
        if (null !== $request->query->get('vehicle')) {
            $vehicle = $em->getRepository('AppBundle:Vehicle')
                ->findOneBy([
                    'id' => $request->query->get('vehicle'),
                    'createdBy' => $this->getUser(),
                ]);
            $job->setVehicle($vehicle);
        }

        $form = $this->createForm('AppBundle\Form\JobFormType', $job);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->setCreatedAt(new \DateTime('now'));
            $job->setModifiedAt(new \DateTime('now'));
            $job->setCreatedBy($this->getUser());
            $job->setModifiedBy($this->getUser());
            $job->setStatus(StatusEnums::Active);

            $em->persist($job);
            $em->flush($job);

            return $this->redirectToRoute('job_show', $job->getId());
        }

        return $this->render('job/new.html.twig', array(
            'jobForm' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing job entity.
     *
     * @Route("/{id}/edit", name="job_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $job = $em->getRepository('AppBundle:Job')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);


        if (!$job) {
            throw $this->createNotFoundException(
                'No job found for id ' . $id
            );
        }

        $form = $this->createForm(JobFormType::class, $job);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $form->getData();
            $job->setModifiedAt(new \DateTime('now'));
            $job->setModifiedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_list');
        }

        return $this->render('job/edit.html.twig', [
            'jobForm' => $form->createView()
        ]);
    }

    /**
     * Finds and displays a job entity.
     *
     * @Route("/{id}", name="job_show")
     * @param $id
     * @Method("GET")
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $job = $em->getRepository('AppBundle:Job')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);

        if (!$job) {
            throw $this->createNotFoundException(
                'No job found for id ' . $id
            );
        }

        return $this->render('job/show.html.twig', [
            'job' => $job
        ]);
    }

    /**
     * Deletes a job entity.
     *
     * @Route("/{id}", name="job_delete")
     * @param $request
     * @param $id
     * @Method("DELETE")
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $job = $em->getRepository('AppBundle:Job')
                ->findOneBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->getUser(),
                ]);

            if (!$job) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            // Safe to remove
            $job->setStatus(StatusEnums::Deleted);
            $em->persist($job);
            $em->flush();

            $response['success'] = true;
            $response['message'] = 'Task deleted.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

}
