<?php

namespace AppBundle\Controller;

use AppBundle\Form\VehicleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VehicleController extends Controller
{
    /**
     * @Route("/vehicles", name="vehicle_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $vehicles = null;

        try {
            $em = $this->getDoctrine()->getManager();
            $vehicles = $em->getRepository('AppBundle:Vehicle')
                ->findAll();
        } catch (\Exception $ex) {

        }
        return $this->render('vehicle/index.html.twig', [
            'vehicles' => $vehicles
        ]);
    }

    /**
     * @Route("/vehicle/{id}/edit", name="vehicle_edit")
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
}
