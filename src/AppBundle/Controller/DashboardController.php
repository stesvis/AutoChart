<?php

namespace AppBundle\Controller;

use AppBundle\Includes\StatusEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Class DashboardController
 * @package AppBundle\Controller
 * @Route("/")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        //-------------------- Vehicles --------------------//
        $vehicles = $this->get('vehicle_service')->getMyVehicles(StatusEnums::Active);

        //-------------------- Tasks --------------------//
        $tasks = $this->get('task_service')->getMyTasks(StatusEnums::Active);

        //-------------------- Services --------------------//
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Service')->createQueryBuilder('s');

        $services = $queryBuilder
            ->andWhere('s.createdBy = :user_id')
            ->setParameter('user_id', $this->getUser()->getId())
            ->getQuery()
            ->getResult();

//        $services = $this->get('service_service')->getMyServices(StatusEnums::Active);

        //-------------------- Categories --------------------//
        $categories = $this->get('category_service')->getMyCategories(StatusEnums::Active);

        return $this->render('dashboard/index.html.twig', [
            'vehicles' => $vehicles,
            'tasks' => $tasks,
            'services' => $services,
            'categories' => $categories,
        ]);
    }
}
