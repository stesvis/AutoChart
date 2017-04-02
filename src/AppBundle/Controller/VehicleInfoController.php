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
     * @Route("/saveInfoAjax", name="vehicle_info_add")
     * @param $request
     * @Method("POST")
     * @return JsonResponse
     */
    public function saveInfoAjax(Request $request)
    {
        try {
            $info = $this->insertVehicleInfo($request);
            if ($info->getId() > 0) {
                $response['success'] = true;
                $response['message'] = 'Info Added.';
                $response['infoId'] = $info->getId();
                $response['infoName'] = $info->getName();
                $response['infoValue'] = $info->getValue();
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

    /**
     * @Route("/{id}", name="vehicle_info_delete")
     * @param $request
     * @param $id
     * @Method("DELETE")
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $info = $em->getRepository('AppBundle:VehicleInfo')
                ->findOneBy([
                    'id' => $id,
                    'status' => StatusEnums::Active,
                    'createdBy' => $this->getUser(),
                ]);

            if (!$info) {
                throw $this->createNotFoundException(
                    'No info found for id ' . $id
                );
            }

            // Safe to remove
            $info->setStatus(StatusEnums::Deleted);
            $em->persist($info);
            $em->flush();

            $response['success'] = true;
            $response['message'] = 'Info deleted.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    private function insertVehicleInfo(Request $request): VehicleInfo
    {
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
        }

        return $info;
    }
}
