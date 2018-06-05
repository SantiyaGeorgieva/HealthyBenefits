<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AboutUs;
use AppBundle\Entity\Message;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\MessageType;
use AppBundle\Form\UserChangeType;
use AppBundle\Form\UserProfileType;
use AppBundle\Form\UserType;
use Doctrine\ORM\Id\UuidGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/registerForm", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $userDb = $this->getDoctrine()->getRepository(User::class)->findAll();

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $form->get('username')->getData();
            $email = $form->get('email')->getData();

            foreach ($userDb as $value) {
                if ($username == $value->getUsername() && $email == $value->getEmail()) {
                    return $this->redirectToRoute('blog_index');
                }
            }

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) save the User!
            $role = $this->getDoctrine()->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_USER']);

            $user->addRole($role);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'base.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("profile/additionalInfo", name="additional_info")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function additionalInfoAction(Request $request)
    {

        // 1) build the form
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());

        $form = $this->createForm(UserType::class, $user);

        $form->remove("password");

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $firstPass = $request->request->get("pass_first");

            if ($firstPass != null) {
                // 3) Encode the password (you could also do this via Doctrine listener)
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $firstPass);

                $user->setPassword($password);
            }


            $file = $request->files->get("image");

            if($file != null) {

                if ($user->getImage() != null) {
                    $image = $user->getImage();
                    $path = $this->getParameter('user_directory') . '/' . $image;
                    $fs = new Filesystem();
                    $fs->remove(array($path));
                }

//            /**
//             * @var UploadedFile $file
//             */
//            $file = $publication->getImage();

//            $fileName = $file->getClientOriginalName();

//            var_dump($file);
                $fileName = md5(uniqid()) . "." . $file->guessExtension();

                $file->move(
                    $this->getParameter("user_directory"), $fileName
                );

                $user->setImage($fileName);
            }

//            $pictureUrl = $request->request->get("pictureUrl");
            $info = $request->request->get("info");

//            if ($pictureUrl != null) {
//                $user->setPictureUrl($pictureUrl);
//            }

            if ($info != null) {
                $user->setInfo($info);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('user_profile');
//                ['id' => $this->getUser()->getId()]);
        }

        return $this->render(
            'user/additionalInfo.html.twig',
            array('user' => $user, 'form' => $form->createView())
        );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/profile", name="user_profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("GET")
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        $publications = $this->getDoctrine()->getRepository(Publication::class)->findBy(['authorId' => $user->getId()]);
//        $about = $this->getDoctrine()->getRepository(AboutUs::class)->findOneBy(['id' => $id]);

        $recipient_id = $this->getDoctrine()->getRepository(User::class)
            ->find($user->getId());
        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findBy(['recipientId' => $recipient_id->getId(),
                'isReaded' => false]);

        return $this->render("user/profile.html.twig", ['user' => $user,
            'publications' => $publications,
            'messageCount' => count($messages)]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/mailBox", name="mail_box")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("GET")
     */
    public function mailBox(Request $request)
    {
        $user = $this->getUser();

        $mails = $this->getDoctrine()->getRepository(Message::class)->findBy(
            ["recipientId" => $user->getId()]
        );

        return $this->render("user/mails.html.twig", ['user' => $user, 'mails' => $mails]);

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/mailBox/{id}", name="current_mail")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function currentMail(Request $request, $id)
    {
        $user = $this->getUser();

        $mail = $this->getDoctrine()->getRepository(Message::class)->find($id);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        $mail->setIsReaded(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($mail);
        $em->flush();

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setSenderUserId($user);
            $message->setRecipientUserId($mail->getSenderUserId());
            $message->setIsReaded(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute("current_mail", ['id' => $id]);
        }

        return $this->render("user/mailBox.html.twig", ['user' => $user,
            'mail' => $mail,
            'form' => $form->createView()]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/mailBox/delete/{id}", name="message_delete")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteMail(Request $request, $id)
    {
        $user = $this->getUser();

        $mail = $this->getDoctrine()->getRepository(Message::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($mail);
        $em->flush();
        return $this->redirectToRoute('mail_box');
    }
}
