<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vehicle;
use AppBundle\Form\VehicleFormType;
use AppBundle\Includes\Constants;
use AppBundle\Includes\RoleEnums;
use AppBundle\Includes\StaticFunctions;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class VehicleController
 *
 * @Route("/vehicles")
 * @package AppBundle\Controller
 */
class VehicleController extends Controller
{
    /**
     * @Route("/", name="vehicle_list")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Can't use the "/api/vehicles" because here i need only the query, for pagination

        $queryBuilder = $em->getRepository('AppBundle:Vehicle')->createQueryBuilder('v');

        if (in_array(RoleEnums::SuperAdmin, $this->getUser()->getRoles())) {
            $query = $queryBuilder
                ->orderBy('v.createdBy')
                ->getQuery();
        } else {
            $query = $queryBuilder
                ->Where('v.createdBy = :user_id')
                ->setParameter('user_id', $this->getUser()->getId())
                ->orderBy('v.year', 'DESC')
                ->getQuery();
        }

        $paginator = $this->get('knp_paginator');

        $vehicles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), //page number
            Constants::ROWS_PER_PAGE //limit per page
        );

        return $this->render('vehicle/index.html.twig', [
            'vehicles' => $vehicles
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vehicle_edit")
     * @param Request $request
     * @param int $id
     * @return string
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $vehicle = $em->getRepository('AppBundle:Vehicle')
            ->findOneBy([
                'id' => $id,
                'status' => StatusEnums::Active, // it's editable only if active, otherwise they are cheating
                'createdBy' => $this->get('user_service')->getEntitledUsers(),
            ]);

        if (!$vehicle) {
            throw $this->createNotFoundException(
                'No vehicle found for id ' . $id
            );
        }

        $form = $this->createForm(VehicleFormType::class, $vehicle);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();
            $vehicle->setModifiedAt(new \DateTime('now'));
            $vehicle->setModifiedBy($this->getUser());
            $vehicle->setName($vehicle->getYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel());

            $em = $this->getDoctrine()->getManager();
            $em->persist($vehicle);
            $em->flush();

            return $this->redirectToRoute('vehicle_list');
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicle' => $vehicle,
            'vehicleForm' => $form->createView(),
            'id' => $vehicle->getId(),
        ]);
    }

    /**
     * @Route("/new", name="vehicle_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleFormType::class, $vehicle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

//            $em = $this->getDoctrine()->getManager();
//
//            $vehicle->setCreatedAt(new \DateTime('now'));
//            $vehicle->setModifiedAt(new \DateTime('now'));
//            $vehicle->setCreatedBy($this->getUser());
//            $vehicle->setModifiedBy($this->getUser());
//            $vehicle->setStatus(StatusEnums::Active);
//            $vehicle->setName($vehicle->getYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel());
//
//            $em->persist($vehicle);
//            $em->flush();

            $serializer = $this->get('jms_serializer');
            $vehicleJson = $serializer->serialize($vehicle, 'json');
//            dump($serializer->toArray($serializer->serialize($request->getContent(), 'json')));
//            dump($vehicle);
//            dump(json_decode($vehicleJson, true));
//            dump(json_decode(json_encode($vehicle), true));
//            die();

            $apiVehicleController = new \AppApiBundle\Controller\VehicleController();
            $response = $apiVehicleController->newAction($request);
//            $response = $this->forward('AppApiBundle:Vehicle:new', json_decode($vehicleJson, true));

//            $content = $response->getContent();
//
//            if (!StaticFunctions::isJson($content)) {
//                throw $this->createNotFoundException('Did not return valid JSON');
//            }

//            $vehicle = $serializer->deserialize($content, Vehicle::class, 'json');


            return $this->redirectToRoute('vehicle_list');
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/newAjax", name="vehicle_new_ajax")
     *
     * @param $request Request
     *
     * @return Response
     */
    public function newAjaxAction(Request $request)
    {
        try {
            $form = $this->createForm(VehicleFormType::class, new Vehicle(), [
                'popup' => true,
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();

                    $vehicle = $form->getData();

                    $vehicle->setCreatedAt(new \DateTime('now'));
                    $vehicle->setModifiedAt(new \DateTime('now'));
                    $vehicle->setCreatedBy($this->getUser());
                    $vehicle->setModifiedBy($this->getUser());
                    $vehicle->setStatus(StatusEnums::Active);
                    $vehicle->setName($vehicle->getYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel());

                    $em->persist($vehicle);
                    $em->flush();

                    //all good, category saved
                    $response['success'] = true;
                    $response['message'] = 'Vehicle added.';
                    $response['vehicleId'] = $vehicle->getId();
                    $response['vehicleName'] = $vehicle->getName();

                    return new JsonResponse($response, 200);
                } else {
                    //invalid form
                    $response['success'] = false;
                    $response['message'] = 'Form not valid.';

                    return new JsonResponse($response, 400);
                }
            }

            return $this->render('vehicle/_form.html.twig', [
                'vehicleForm' => $form->createView()
            ]);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }
    }

    /**
     * @Route("/{id}", name="vehicle_show")
     * @param $id
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $vehicle = null;

        // Use the api that returns one vehicle, to avoid duplicating code
        $response = $this->forward('AppApiBundle:Vehicle:getOne', [
            'id' => $id
        ]);

        $serializer = $this->get('jms_serializer');
        $content = $response->getContent();

        if (!StaticFunctions::isJson($content)) {
            throw $this->createNotFoundException(
                'No vehicle found for id ' . $id
            );
        }

        $vehicle = $serializer->deserialize($content, Vehicle::class, 'json');

        // Check if it exists
        if (!$vehicle) {
            throw $this->createNotFoundException(
                'No vehicle found for id ' . $id
            );
        }

        return $this->render('vehicle/show.html.twig', [
            'vehicle' => $vehicle,
        ]);

    }

    /**
     * @Route("/{id}", name="vehicle_delete")
     * @param $request
     * @param $id
     * @Method("DELETE")
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $vehicle = $em->getRepository('AppBundle:Vehicle')
                ->findOneBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->get('user_service')->getEntitledUsers(),
                ]);

            if (!$vehicle) {
                throw $this->createNotFoundException(
                    'No vehicle found for id ' . $id
                );
            }

            // Safe to remove
            $vehicle->setStatus(StatusEnums::Deleted);
            $vehicle->setModifiedBy($this->getUser());

            $em->persist($vehicle);
            $em->flush();

            $response['success'] = true;
            $response['message'] = 'Vehicle deleted.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

}
