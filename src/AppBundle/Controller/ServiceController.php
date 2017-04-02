<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Service;
use AppBundle\Form\ServiceFormType;
use AppBundle\Includes\Constants;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service controller.
 *
 * @Route("/services")
 */
class ServiceController extends Controller
{
    /**
     * Lists all service entities.
     *
     * @Route("/", name="service_list")
     * @Method("GET")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

//        $services = $em->getRepository('AppBundle:Service')
//            ->findBy([
//                'createdBy' => $this->getUser(),
//            ]);

        $dql = "SELECT j FROM AppBundle:Service j";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');

        $services = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), //page number
            Constants::ROWS_PER_PAGE //limit per page
        );

        return $this->render('service/index.html.twig', array(
            'services' => $services,
        ));
    }

    /**
     * Lists all services performed on a specific vehicle.
     *
     * @Route("/vehicle/{id}", name="vehicle_service_list")
     * @param $request
     * @param $id
     * @return Response
     */
    public function indexByVehicle(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

//        $vehicle = $em->getRepository('AppBundle:Vehicle')
//            ->find($id);

//        $services = $em->getRepository('AppBundle:Service')
//            ->findBy([
//                'vehicle' => $vehicle,
//            ]);

        $query = $em->getRepository('AppBundle:Service')->findByVehicle($id);

        $paginator = $this->get('knp_paginator');

        $services = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            Constants::ROWS_PER_PAGE
        );


        return $this->render('service/index.html.twig', array(
            'services' => $services,
        ));
    }

    /**
     * Creates a new service entity.
     *
     * @Route("/new", name="service_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $service = new Service();

        $em = $this->getDoctrine()->getManager();

        // Check if we need to pre-populate with a Task
        if (null !== $request->query->get('task')) {
            $task = $em->getRepository('AppBundle:Task')
                ->findOneBy([
                    'id' => $request->query->get('task'),
                    'createdBy' => $this->getUser(),
                ]);
            $service->setTask($task);
        }

        // Check if we need to pre-populate with a Vehicle
        if (null !== $request->query->get('vehicle')) {
            $vehicle = $em->getRepository('AppBundle:Vehicle')
                ->findOneBy([
                    'id' => $request->query->get('vehicle'),
                    'createdBy' => $this->getUser(),
                ]);
            $service->setVehicle($vehicle);
        }

        $form = $this->createForm('AppBundle\Form\ServiceFormType', $service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->setCreatedAt(new \DateTime('now'));
            $service->setModifiedAt(new \DateTime('now'));
            $service->setCreatedBy($this->getUser());
            $service->setModifiedBy($this->getUser());
            $service->setStatus(StatusEnums::Active);

            $em->persist($service);
            $em->flush($service);

            return $this->redirectToRoute('service_show', $service->getId());
        }

        return $this->render('service/new.html.twig', array(
            'serviceForm' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing service entity.
     *
     * @Route("/{id}/edit", name="service_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $service = $em->getRepository('AppBundle:Service')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);


        if (!$service) {
            throw $this->createNotFoundException(
                'No service found for id ' . $id
            );
        }

        $form = $this->createForm(ServiceFormType::class, $service);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service = $form->getData();
            $service->setModifiedAt(new \DateTime('now'));
            $service->setModifiedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();

            return $this->redirectToRoute('service_list');
        }

        return $this->render('service/edit.html.twig', [
            'serviceForm' => $form->createView()
        ]);
    }

    /**
     * Finds and displays a service entity.
     *
     * @Route("/{id}", name="service_show")
     * @param $id
     * @Method("GET")
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $service = $em->getRepository('AppBundle:Service')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);

        if (!$service) {
            throw $this->createNotFoundException(
                'No service found for id ' . $id
            );
        }

        return $this->render('service/show.html.twig', [
            'service' => $service
        ]);
    }

    /**
     * Deletes a service entity.
     *
     * @Route("/{id}", name="service_delete")
     * @param $request
     * @param $id
     * @Method("DELETE")
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $service = $em->getRepository('AppBundle:Service')
                ->findOneBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->getUser(),
                ]);

            if (!$service) {
                throw $this->createNotFoundException('Unable to find Service entity.');
            }

            // Safe to remove
            $service->setStatus(StatusEnums::Deleted);
            $em->persist($service);
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
