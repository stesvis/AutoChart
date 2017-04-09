<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vehicle;
use AppBundle\Form\VehicleFormType;
use AppBundle\Includes\Constants;
use AppBundle\Includes\RoleEnums;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


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
                'createdBy' => $this->getUser(),
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

            return $this->redirectToRoute('vehicle_list');
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicleForm' => $form->createView()
        ]);
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

        $em = $this->getDoctrine()->getManager();

        $vehicle = $em->getRepository('AppBundle:Vehicle')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);

        // Check if it exists
        if (!$vehicle) {
            throw $this->createNotFoundException(
                'No vehicle found for id ' . $id
            );
        }

        // Get all the vehicle info records
        $vehicleInfo = $em->getRepository('AppBundle:VehicleInfo')
            ->findByVehicle($id);

        return $this->render('vehicle/show.html.twig', [
            'vehicle' => $vehicle,
            'info' => $vehicleInfo,
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
                    'createdBy' => $this->getUser(),
                ]);

            if (!$vehicle) {
                throw $this->createNotFoundException(
                    'No vehicle found for id ' . $id
                );
            }

            // Safe to remove
            $vehicle->setStatus(StatusEnums::Deleted);
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
