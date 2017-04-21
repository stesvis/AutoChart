<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Vehicle;
use AppBundle\Form\VehicleFormType;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends Controller
{
    /**
     * @Route("/api/vehicles", name="api_vehicle_new")
     * @Method("POST")
     * @param $request Request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = json_decode($request->getContent(), true);

        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleFormType::class, $vehicle);
        $form->submit($data);

        $vehicle->setCreatedAt(new \DateTime('now'));
        $vehicle->setModifiedAt(new \DateTime('now'));
        $vehicle->setCreatedBy($this->getUser());
        $vehicle->setModifiedBy($this->getUser());
        $vehicle->setStatus(StatusEnums::Active);
        $vehicle->setName($vehicle->getYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel());


        $em = $this->getDoctrine()->getManager();
        $em->persist($vehicle);
        $em->flush();

        $response = new Response('Vehicle created', 201);
        $response->headers->set('Location', 'vehicles/' . $vehicle->getId());
        return $response;
    }

    /**
     * @Route("/api/vehicles")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new Response('indexAction');
    }
}
