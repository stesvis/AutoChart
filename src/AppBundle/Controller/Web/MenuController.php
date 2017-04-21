<?php

namespace AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends Controller
{
    /**
     * @Route("/contact", name="contact_index")
     * @param $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        try {// Create the form according to the FormType created previously.
            // And give the proper parameters
            $form = $this->createForm('AppBundle\Form\ContactFormType', null);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Send mail
                if ($this->sendEmail($form->getData())) {

                    $form = $this->createForm('AppBundle\Form\ContactFormType', null);

                    // Everything OK, redirect to wherever you want ! :
                    return $this->render('menu/contact.html.twig', array(
                        'contactForm' => $form->createView(),
                        'success' => true,
                        'message' => 'Your email was successfully sent. Thank you.'
                    ));
                } else {
                    return $this->render('menu/contact.html.twig', array(
                        'contactForm' => $form->createView(),
                        'error' => true,
                    ));
                }
            }

            return $this->render('menu/contact.html.twig', array(
                'contactForm' => $form->createView()
            ));
        } catch (Exception $e) {
            return $this->render('menu/contact.html.twig', array(
                'contactForm' => $form->createView(),
                'error' => true,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * @param $data
     * @return int
     */
    private function sendEmail($data)
    {
        $mailerEmail = $this->getParameter('mailer_user');
        $mailerPassword = $this->getParameter('mailer_password');

        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername($mailerEmail)->setPassword($mailerPassword);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance($data['subject'])
            ->setFrom(array($data['email'] => $data['name']))
            ->setTo(array($mailerEmail => 'Cristian Merli'))
            ->setBody($data['message'], 'text/html');

        return $mailer->send($message);
    }

    /**
     * @Route("/about", name="about_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('menu/about.html.twig');
    }
}
