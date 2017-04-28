<?php

namespace AppApiBundle\Controller;

use AppBundle\Entity\Vehicle;
use AppBundle\Form\VehicleFormType;
use AppBundle\Includes\StaticFunctions;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VehicleController
 *
 * @Route("api/vehicles")
 * @package AppApiBundle\Controller
 */
class VehicleController extends Controller
{
    /**
     * @Route("/", name="api_vehicle_list")
     * @Method("GET")
     *
     * @return Response
     */
    public function getAllAction()
    {
//        $em = $this->getDoctrine()->getManager();

//        $vehicles = $em->getRepository('AppBundle:Vehicle')
//            ->findByUser($this->getUser()->getId());

//        $vehicles = $em->getRepository('AppBundle:Vehicle')
//            ->findAll();

        $vehicles = $this->get('vehicle_service')
            ->getMyVehicles(['name' => 'ASC']);

        $data = array('vehicles' => array());

        foreach ($vehicles as $vehicle) {
            $data['vehicles'][] = StaticFunctions::serializeObject($vehicle); //$this->serializeVehicle($vehicle);
        }

        $response = new Response(json_encode($data), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;

//        $data = array("api" => "vehicles");
//        return new JsonResponse($data);
//        $view = $this->view($data);
//        return $this->handleView($view);
    }

    /**
     * @Route("/{id}", name="api_vehicle_show")
     * @Method("GET")
     *
     * @param $id int
     * @return Response
     */
    public function getOneAction(int $id)
    {
        $vehicle = null;

        $em = $this->getDoctrine()->getManager();

        $vehicle = $em->getRepository('AppBundle:Vehicle')
            ->findOneBy([
                'id' => $id,
                'createdBy' => $this->get('user_service')->getEntitledUsers(),
            ]);

        // Check if it exists
        if (!$vehicle) {
//            return new JsonResponse('No vehicle found with Id = ' . $id, Response::HTTP_NO_CONTENT);
            throw $this->createNotFoundException(sprintf('No vehicle found with Id = ' . $id));
        }

        // Get all the vehicle info records
        $specs = $em->getRepository('AppBundle:VehicleInfo')
            ->findByVehicle($id);

        $services = $em->getRepository('AppBundle:Service')
            ->findByVehicle($id);

        $data = array('vehicle' => array(), 'specs' => array(), 'services' => array());
        $data['vehicle'] = StaticFunctions::serializeObject($vehicle);


        foreach ($specs as $spec) {
            $data['specs'][] = StaticFunctions::serializeObject($spec); //$this->serializeVehicle($vehicle);
        }

        foreach ($services as $service) {
            $data['services'][] = StaticFunctions::serializeObject($service); //$this->serializeVehicle($vehicle);
        }

        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/", name="api_vehicle_new")
     * @Method("POST")
     *
     * @param $request Request
     * @return Response
     */
    public function newAction(Request $request)
    {
//        $this->denyAccessUnlessGranted(RoleEnums::Admin);
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

        $response = new JsonResponse(StaticFunctions::serializeObject($vehicle), Response::HTTP_CREATED);
        $response->headers->set('Location', $this->generateUrl('api_vehicle_show', [
            'id' => $vehicle->getId()
        ]));

        return $response;
    }

    /**
     * @Route("/{id}")
     * @Method("PUT")
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function editAction(int $id, Request $request)
    {
//        $this->denyAccessUnlessGranted(RoleEnums::Admin);
        $em = $this->getDoctrine()->getManager();

        $vehicle = $em->getRepository('AppBundle:Vehicle')
            ->findOneBy([
                'id' => $id,
                'status' => StatusEnums::Active, // it's editable only if active, otherwise they are cheating
                'createdBy' => $this->get('user_service')->getEntitledUsers(),
            ]);

        if (!$vehicle) {
//            return new JsonResponse('No vehicle found with Id = ' . $id, Response::HTTP_NO_CONTENT);
            throw $this->createNotFoundException(sprintf('No vehicle found with Id = ' . $id));
        }

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(VehicleFormType::class, $vehicle);
        $form->submit($data);

        $vehicle->setCreatedAt(new \DateTime('now'));
        $vehicle->setModifiedAt(new \DateTime('now'));
        $vehicle->setCreatedBy($this->getUser());
        $vehicle->setModifiedBy($this->getUser());
        $vehicle->setStatus(StatusEnums::Active);
        $vehicle->setName($vehicle->getYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel());

        $em->persist($vehicle);
        $em->flush();

        $response = new JsonResponse(StaticFunctions::serializeObject($vehicle), Response::HTTP_OK);
        $response->headers->set('Location', $this->generateUrl('api_vehicle_show', [
            'id' => $vehicle->getId()
        ]));

        return $response;
    }
}
