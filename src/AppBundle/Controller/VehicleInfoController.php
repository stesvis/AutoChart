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
 * Class VehicleInfoController
 *
 * @Route("/vehicle_info")
 * @package AppBundle\Controller
 */
class VehicleInfoController extends Controller
{
    /**
     * @Route("/addAjax", name="vehicle_info_add")
     * @param $request
     * @Method("POST")
     * @return JsonResponse
     */
    public function addAjax(Request $request)
    {
        var_dump($request);
        die();
        return new JsonResponse([
            'status' => 'OK',
            'request' => $request
        ]);
    }
}
