<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FlashMessageController extends Controller
{
    /**
     * @Route("/login_exception", name="flash_message")
     */
    public function flashMessageAction()
    {
        $user = $this->getUser();
        $this->addFlash('warning', '');

        return $this->render('security/login.html.twig', ['user'=>$user]);
    }
}
