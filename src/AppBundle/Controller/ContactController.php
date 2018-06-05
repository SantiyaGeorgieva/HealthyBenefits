<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AboutUs;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Form\MessageType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * Adds a flash message to the current session for type.
     *
     * @param string $type    The type
     * @param string $message The message
     *
     * @throws \LogicException
     */
    protected function addFlash($type, $message){
        // Retrieve flashbag from the controller
        $flashBag = $this->get('session')->getFlashBag();

        // Add flash message
        $flashBag->add($type, $message);
    }

    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $currentUser = $this->getUser();

        if ($currentUser == null){
           $user = new User();

           $form = $this->createForm(UserType::class, $user);

           return $this->render('default/contactGuest.html.twig',
               ['user' => $user,'form' => $form->createView()]);
        }

        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($currentUser->getId());

        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();

        $contacts = [];
        for($i = 0; $i < count($users); $i++){
            if(((in_array("ROLE_ADMIN", $users[$i]->getRoles())) || (in_array("ROLE_SUPERADMIN", $users[$i]->getRoles())))
                && $currentUser->getId() != $users[$i]->getId()){
                $contacts[] = $users[$i];
            }
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user_Id = $request->request->get("contacts");

            $recipient_Id = $this->getDoctrine()->getRepository(User::class)
                ->find($user_Id);

            $message->setSenderUserId($currentUser);
            $message->setRecipientUserId($recipient_Id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute("contact");
        }


        return $this->render('default/contact.html.twig', ['user'=>$user, "contacts" => $contacts,
            "form" => $form->createView()]);
    }
}
