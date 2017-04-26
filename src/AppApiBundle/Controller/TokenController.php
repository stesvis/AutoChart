<?php

namespace AppApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends Controller
{
    /**
     * @Route("/token")
     * @return JsonResponse
     */
    public function indexAction()
    {
        try {
            $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
            $client = $clientManager->createClient();
            $client->setRedirectUris(array('http://stesvis.com'));
            $client->setAllowedGrantTypes(array('token', 'authorization_code'));
            $clientManager->updateClient($client);

            $grantRequest = new Request(array(
                'client_id' => $client->getPublicId(),
                'client_secret' => $client->getSecret(),
                'grant_type' => 'password',
                'username' => 'admintest',
                'password' => 'password'
            ));

            $response['client_id'] = $client->getPublicId();
            $response['client_secret'] = $client->getSecret();

//            $tokenResponse = $this->get('fos_oauth_server.server')->grantAccessToken($grantRequest);

//            $token = $tokenResponse->getContent();

//        return $this->redirect($this->generateUrl('fos_oauth_server_authorize', array(
//            'client_id' => $client->getPublicId(),
//            'redirect_uri' => 'http://stesvis.com',
//            'response_type' => 'code'
//        )));

//            $response['token'] = $token;
        } catch (Exception $e) {
            $x = 0;
        }


        return new JsonResponse($response);
    }
}
