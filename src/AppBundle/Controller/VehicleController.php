<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vehicle;
use AppBundle\Form\VehicleFormType;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        try {
            $vehicles = $this->get('vehicle_service')->getMyVehicles();
        } catch (\Exception $ex) {
            $vehicles = null;
        }

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

        try {
            $vehicle = $em->getRepository('AppBundle:Vehicle')->find($id);

            $form = $this->createForm(VehicleFormType::class, $vehicle);

            // only handles data on POST
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $vehicle = $form->getData();
                $vehicle->setModifiedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($vehicle);
                $em->flush();

                return $this->redirectToRoute('vehicle_list');
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicleForm' => $form->createView()
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
            $vehicle->setStatus('A');

            $em->persist($vehicle);
            $em->flush();

            return $this->redirectToRoute('vehicle_list');
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="vehicle_delete")
     * @param $request
     * @param $id
     * @Method("DELETE")
     * @return Response
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
                throw $this->createNotFoundException('Unable to find Vehicle entity.');
            }

            // Safe to remove
            $vehicle->setStatus(StatusEnums::Active);
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
