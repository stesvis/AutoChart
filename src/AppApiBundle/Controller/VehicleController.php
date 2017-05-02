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
     * Returns all the vehicles that belong to the current user
     *
     * @Route("/", name="api_vehicle_list")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $vehicles = $this->get('vehicle_service')
            ->getMyVehicles(['name' => 'ASC']);

        $serializer = $this->get('jms_serializer');
        $response = new JsonResponse($serializer->toArray($vehicles), JsonResponse::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Returns on vehicle, if found
     *
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

        $serializer = $this->get('jms_serializer');
        $response = new JsonResponse($serializer->toArray($vehicle), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Creates a new vehicle
     *
     * @Route("/new", name="api_vehicle_new")
     * @Method("POST")
     *
     * @param $request Request
     * @return Response
     */
    public function newAction(Request $request)
    {
//        $this->denyAccessUnlessGranted(RoleEnums::Admin);
        $serializer = $this->get('jms_serializer');
        $content = $serializer->deserialize($request->getContent(), Vehicle::class, 'json');
        $data = $serializer->toArray($content);
//        $data = json_decode($request->getContent(), true);
        dump($data);
        die();

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

//        $response = new JsonResponse(StaticFunctions::serializeObject($vehicle), Response::HTTP_CREATED);
        $response = new JsonResponse($serializer->serialize($vehicle, 'json'), Response::HTTP_CREATED);
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
