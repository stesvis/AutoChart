<?php

namespace AppBundle\Controller;

use AppBundle\Includes\RoleEnums;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


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
    public function indexAction(Request $request)
    {

        //-------------------- Vehicles --------------------//
        $vehicles = $this->get('vehicle_service')->getMyVehicles([
            'createdBy' => 'ASC'
        ]);

        //-------------------- Tasks --------------------//
        $tasks = $this->get('task_service')->getMyTasks([
            'createdBy' => 'ASC'
        ]);

        //-------------------- Services --------------------//
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Service')->createQueryBuilder('s');

        if (in_array(RoleEnums::SuperAdmin, $this->getUser()->getRoles())) {
            $services = $queryBuilder
                ->orderBy('s.createdBy')
                ->getQuery()
                ->getResult();
        } else {
            $services = $queryBuilder
                ->andWhere('s.createdBy = :user_id')
                ->setParameter('user_id', $this->getUser()->getId())
                ->getQuery()
                ->getResult();
        }

//        $queryBuilder = $em->getRepository('AppBundle:Service')->createQueryBuilder('s');
//
//        $services = $queryBuilder
//            ->andWhere('s.createdBy = :user_id')
//            ->setParameter('user_id', $this->getUser()->getId())
//            ->getQuery()
//            ->getResult();

//        $services = $this->get('service_service')->getMyServices();

        //-------------------- Categories --------------------//
//        $categories = $this->get('category_service')->getMyCategories([
//            'createdBy' => 'ASC'
//        ]);

//        $queryBuilder = $em->getRepository('AppBundle:Category')->createQueryBuilder('c');
//
//        if (in_array(RoleEnums::SuperAdmin, $this->getUser()->getRoles())) {
//            $categories = $queryBuilder
//                ->orderBy('c.createdBy')
//                ->getQuery()
//                ->execute();
//        } else {
//            $categories = $queryBuilder
//                ->Where('c.createdBy = :user_id')
//                ->setParameter('user_id', $this->getUser()->getId())
//                ->orWhere('c.type = :type')
//                ->setParameter('type', TypeEnums::System)
//                ->orderBy('c.name')
//                ->getQuery()
//                ->execute();
//        }

        return $this->render('dashboard/index.html.twig', [
            'vehicles' => $vehicles,
            'tasks' => $tasks,
            'services' => $services,
//            'categories' => $categories,
        ]);
    }
}
