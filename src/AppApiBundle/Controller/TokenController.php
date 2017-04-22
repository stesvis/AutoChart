<?php

namespace AppApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends Controller
{
    /**
     * @Route("/tokens")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://stesvis.com'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));
        $clientManager->updateClient($client);

        return $this->redirect($this->generateUrl('fos_oauth_server_authorize', array(
            'client_id' => $client->getPublicId(),
            'redirect_uri' => 'http://stesvis.com',
            'response_type' => 'code'
        )));
    }
}
