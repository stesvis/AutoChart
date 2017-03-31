<?php

namespace AppBundle\Controller;

use AppBundle\Entity\VehicleInfo;
use AppBundle\Form\VehicleInfoFormType;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VehicleInfoController
 *
 * @Route("/vehicle_info")
 * @package AppBundle\Controller
 */
class VehicleInfoController extends Controller
{
    /**
     * @Route("/addInfoAjax", name="vehicle_info_add")
     * @param $request
     * @Method("POST")
     * @return JsonResponse
     */
    public function addInfoAjax(Request $request)
    {
        try {
            if ($this->insertVehicleInfo($request)) {
                $response['success'] = true;
                $response['message'] = 'Info Added.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Could not save the info';
                $response['debug'] = 'Form did not pass validation';
            }
        } catch (Exception $ex) {
            $response['success'] = false;
            $response['message'] = 'Info Added.';
            $response['debug'] = $ex->getMessage();
        }

        return new JsonResponse($response);
    }

    private function insertVehicleInfo(Request $request): bool
    {
        $success = false;

        $info = new VehicleInfo();
        $form = $this->createForm(VehicleInfoFormType::class, $info);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $info = $form->getData();

            $info->setCreatedAt(new \DateTime('now'));
            $info->setModifiedAt(new \DateTime('now'));
            $info->setCreatedBy($this->getUser());
            $info->setModifiedBy($this->getUser());
            $info->setStatus(StatusEnums::Active);

            $em->persist($info);
            $em->flush();

            $success = true;
        }

        return $success;
    }
}
