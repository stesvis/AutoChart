<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\VehicleInfo;
use AppBundle\Form\VehicleInfoFormType;
use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleInfoController
 *
 * @Route("/vehicle_info")
 * @package AppBundle\Controller
 */
class VehicleInfoController extends Controller
{
    /**
     * @Route("/{id}/edit", name="vehicle_info_edit")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $info = $em->getRepository('AppBundle:VehicleInfo')
                ->findOneBy([
                    'id' => $id,
                    'createdBy' => $this->get('user_service')->getEntitledUsers(),
                ]);

            if (!$info) {
                throw $this->createNotFoundException(
                    'No info found for id ' . $id
                );
            }

            $form = $this->createForm(VehicleInfoFormType::class, $info, [
                'hideVehicle' => true,
                'hideSubmit' => true,
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();

                    $info = $form->getData();

                    $info->setCreatedAt(new \DateTime('now'));
                    $info->setModifiedAt(new \DateTime('now'));
                    $info->setCreatedBy($this->getUser());
                    $info->setModifiedBy($this->getUser());
                    $info->setStatus(StatusEnums::Active);

                    $em->persist($info);
                    $em->flush();

                    $response['success'] = true;
                    $response['message'] = 'Info updated.';
                    $response['infoId'] = $info->getId();
                    $response['infoName'] = $info->getName();
                    $response['infoValue'] = $info->getValue();
                    return new JsonResponse($response, 200);
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Form not valid.';
                    return new JsonResponse($response, 400);
                }
            }

            return $this->render('vehicleInfo/_form.html.twig', [
                'infoForm' => $form->createView()
            ]);

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }
    }

    /**
     * @Route("/new", name="vehicle_info_new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        try {
            $info = new VehicleInfo();
            $em = $this->getDoctrine()->getManager();

            if (null !== $request->query->get('vehicle')) {
                $vehicle = $em->getRepository('AppBundle:Vehicle')
                    ->findOneBy([
                        'id' => $request->query->get('vehicle'),
                        'createdBy' => $this->get('user_service')->getEntitledUsers(),
                    ]);
                $info->setVehicle($vehicle);
            }

            $form = $this->createForm(VehicleInfoFormType::class, $info, [
                'hideVehicle' => true,
                'hideSubmit' => true,
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    $info = $form->getData();

                    $info->setCreatedAt(new \DateTime('now'));
                    $info->setModifiedAt(new \DateTime('now'));
                    $info->setCreatedBy($this->getUser());
                    $info->setModifiedBy($this->getUser());
                    $info->setStatus(StatusEnums::Active);

                    $em->persist($info);
                    $em->flush();

                    $response['success'] = true;
                    $response['message'] = 'Info updated.';
                    $response['infoId'] = $info->getId();
                    $response['infoName'] = $info->getName();
                    $response['infoValue'] = $info->getValue();

                    return new JsonResponse($response, 200);

                } else {
                    $response['success'] = false;
                    $response['message'] = 'Form not valid.';

                    return new JsonResponse($response, 400);
                }
            }

            return $this->render('vehicleInfo/_form.html.twig', [
                'infoForm' => $form->createView()
            ]);

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }
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
                    'createdBy' => $this->get('user_service')->getEntitledUsers(),
                ]);

            if (!$info) {
                throw $this->createNotFoundException(
                    'No info found for id ' . $id
                );
            }

            // Safe to remove
            $info->setStatus(StatusEnums::Deleted);
            $info->setModifiedBy($this->getUser());
            $em->persist($info);
            $em->flush();

            $response['success'] = true;
            $response['message'] = 'Info deleted.';

            return new JsonResponse($response, 200);

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();

            return new JsonResponse($response, 500);
        }


    }
}
