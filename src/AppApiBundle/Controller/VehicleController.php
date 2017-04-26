<?php

namespace AppApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends Controller
{
    /**
     * @Route("/api/vehicles", name="api_vehicle_list")
     * @Method("GET")
     * @return Response
     */
    public function getAction()
    {
        $em = $this->getDoctrine()->getManager();

//        $vehicles = $em->getRepository('AppBundle:Vehicle')
//            ->findByUser($this->getUser()->getId());

//        $vehicles = $em->getRepository('AppBundle:Vehicle')
//            ->findAll();

        $vehicles = $this->get('vehicle_service')
            ->getMyVehicles(['name' => 'ASC']);

        $data = array('vehicles' => array());
        foreach ($vehicles as $vehicle) {
            $data['vehicles'][] = $this->serializeObject($vehicle); //$this->serializeVehicle($vehicle);
        }
        $response = new Response(json_encode($data), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

//        $data = array("api" => "vehicles");
//        return new JsonResponse($data);
//        $view = $this->view($data);
//        return $this->handleView($view);
    }

//    /**
//     * @Route("/api/vehicles", name="api_vehicle_new")
//     * @Method("POST")
//     * @param $request Request
//     * @return Response
//     */
//    public function newAction(Request $request)
//    {
//        $this->denyAccessUnlessGranted('ROLE_USER');
//
//        $data = json_decode($request->getContent(), true);
//
//        $vehicle = new Vehicle();
//        $form = $this->createForm(VehicleFormType::class, $vehicle);
//        $form->submit($data);
//
//        $vehicle->setCreatedAt(new \DateTime('now'));
//        $vehicle->setModifiedAt(new \DateTime('now'));
//        $vehicle->setCreatedBy($this->getUser());
//        $vehicle->setModifiedBy($this->getUser());
//        $vehicle->setStatus(StatusEnums::Active);
//        $vehicle->setName($vehicle->getYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel());
//
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($vehicle);
//        $em->flush();
//
//        $response = new Response('Vehicle created', 201);
//        $response->headers->set('Location', 'vehicles/' . $vehicle->getId());
//        return $response;
//    }
//

    private function serializeObject($object)
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $array = array();

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }

        return $array;
    }
}
