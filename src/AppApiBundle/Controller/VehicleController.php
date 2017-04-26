<?php

namespace AppApiBundle\Controller;

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
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://stesvis.com'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));
        $clientManager->updateClient($client);

        return $this->redirect($this->generateUrl('fos_user_security_login', array(
            'client_id' => $client->getPublicId(),
            'redirect_uri' => 'http://stesvis.com',
            'response_type' => 'code'
        )));

        $this->denyAccessUnlessGranted('ROLE_USER');

        return new Response('indexAction');
    }

    /**
     * @Route("/api/demo")
     * @return mixed
     */
    public function getDemosAction()
    {
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }

}
