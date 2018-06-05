<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\NewsLetter;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\MessageType;
use AppBundle\Form\PublicationType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin_panel", name="admin_panel")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("GET")
     */
    public function adminViewAction(Request $request)
    {
        $user = $this->getUser();
        $publication = $this->getDoctrine()->getRepository(Publication::class)->findAll();

        $count_publications = count($publication);

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
//        $roles = $this->getDoctrine()->getRepository(Role::class)->find($id);
//        $roles = $roles->getRole();
//        'id'=>$roles->getId()
        $count_users = count($users)-1;

        $subscribers = $this->getDoctrine()->getRepository(NewsLetter::class)
            ->findAll();

        $count_subscribers = count($subscribers);

        $em = $this->getDoctrine()->getManager();

        $news = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'news')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();

        $fitness = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'fitness')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();

        $foods = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'food')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setFirstResult(0)
            ->getQuery()
            ->execute();

        return $this->render('admin/admin_panel.html.twig', [
            'user'=>$user,
            'publications' => $publication,
            'users'=>$users,
            'news'=> $news,
            'fitness'=>$fitness,
            'foods'=> $foods,
            'count_users' => $count_users,
            'count_publications' => $count_publications,
            'subscribers' => $subscribers,
            'count_subscribers' => $count_subscribers]);
    }

    /**
     * @Route("/admin_panel/admin_register_user", name="admin_register_user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function adminRegisterAdmin(Request $request)
    {
        $user = $this->getUser();


        // 1) build the form
        $register_user = new User();
        $form = $this->createForm(UserType::class,  $register_user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($register_user, $register_user->getPassword());
            $register_user->setPassword($password);

            // 4) save the User!
            $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_USER']);


            $register_user->addRole($role);

            $em = $this->getDoctrine()->getManager();
            $em->persist($register_user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('admin_panel', ['user'=> $user]);
        }

        return $this->render(
            'admin/users/admin_register_user.html.twig',
            array(
                'user'  => $user,
                'form' => $form->createView())
        );
    }

    /**
     * @Route("/admin_panel/admin_register_admin", name="admin_register_admin")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function adminRegisterAction(Request $request)
    {
        $user = $this->getUser();

        // 1) build the form
        $register_user = new User();
        $form = $this->createForm(UserType::class,  $register_user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($register_user, $register_user->getPassword());
            $register_user->setPassword($password);

            // 4) save the User!
            $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);

            $register_user->addRole($role);

            $em = $this->getDoctrine()->getManager();
            $em->persist($register_user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('admin_panel', ['user'=> $user]);
        }

        return $this->render(
            'admin/users/admin_register_admin.html.twig',
            array(
                'user'=> $user,
                'form' => $form->createView())
        );
    }

    /**
     * @Route("/admin_panel/user/edit/{user_id}", name="admin_edit_user")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $user_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @return JsonResponse
     */
    public function adminEditUserAction(Request $request, $user_id)
    {
        $user = $this->getUser();
        $authors = $this->getDoctrine()->getRepository(User::class)->find($user_id);

        $form = $this->createForm(UserType::class, $authors);
        $form->remove("password");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pass = $request->request->get("pass");

            if($pass != null){
                $password = $this->get('security.password_encoder')
                    ->encodePassword($authors, $pass);
                $authors->setPassword($password);
            }

            $role = $request->request->get("roles");

            if (($role == "ROLE_ADMIN" && in_array("ROLE_ADMIN", $authors->getRoles()))
                || ($role == "ROLE_SUPER_ADMIN" && in_array("ROLE_SUPER_ADMIN", $authors
                        ->getRoles()))) {
                return $this->redirectToRoute("admin_edit_user", ["user_id" => $user_id]);
            }

            if ($role === "ROLE_ADMIN") {
                $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);
                $authors->addRole($role);
            } else if ($role === "ROLE_USER") {
                $roleAdmin = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);
                $roleSuperAdmin = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_SUPER_ADMIN']);
                $authors->removeUserRole($roleAdmin);
                $authors->removeUserRole($roleSuperAdmin);
            } else {
                $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_SUPER_ADMIN']);
                $authors->addRole($role);
            }

            $data = $this->getDoctrine()->getManager();
            $data->persist($authors);
            $data->flush();
            return $this->redirectToRoute('admin_panel', ['user' => $user, 'authors' => $authors]);
        }

        return $this->render('admin/users/admin_user_edit.html.twig', [
            'authors' => $authors,
            'user' => $user,
            'error_message' => "",
            'form' => $form->createView()
        ]);
    }

    /**
     * Returns a JSON string with the roles of the Role with the providen id.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listRolesOfRoleAction(Request $request)
    {
        // Get Entity manager and repository
        $em = $this->getDoctrine()->getManager();
        $roleRepository = $em->getRepository("AppBundle:Role");

        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $roles = $roleRepository->createQueryBuilder("q")
            ->where("q.id = :id")
            ->setParameter("id", $request->query->get("id"))
            ->getQuery()
            ->getResult();


        // Serialize into an array the data that we need, in this case only name and id
        // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
        $responseArray = array();
        foreach ($roles as $role) {
            $responseArray[] = array(
                "id" => $role->getId(),
                "name" => $role->getName()
            );
    }

        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);
    }
//        // e.g
//        // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]

    /**
     * @Route("/admin_panel/delete/user/{id}", name="admin_delete_user")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminDeleteUserAction($id)
    {
        $user = $this->getUser();
        $users = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($users != null) {

            $data = $this->getDoctrine()->getManager();
            $data->remove($users);
            $data->flush();
        }

        return $this->redirectToRoute('admin_panel', ['user'=>$user, 'users'=>$users]);
    }

    /**
     * @Route("/admin_panel/edit/{id}", name="admin_edit")
     * @param $id
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminEditAction($id, Request $request)
    {
        $user = $this->getUser();
        $publication = $this->getDoctrine()->getRepository(Publication::class)
            ->find($id);

        if ($publication == null){
            return $this->redirectToRoute("admin_panel");
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->remove("image");

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $data = $this->getDoctrine()->getManager();
            $data->persist($publication);
            $data->flush();

            return $this->redirectToRoute('admin_panel', ['user' => $user]);
        }

        return $this->render('admin/publication/admin_edit.html.twig', [
            'publications' => $publication,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin_panel/delete/confirm/{id}", name="admin_confirm_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminDeleteAction($id)
    {
        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($id);

        if ($publication == null) {
            return $this -> redirectToRoute('user_profile');
        }

        if ($publication != null) {

            $data = $this->getDoctrine()->getManager();
            $data->remove($publication);
            $data->flush();
        }

        return $this->redirectToRoute('admin_panel');
    }

    /**
     * @Route("/profile/{authorId}/{id}", name="profile")
     * @param $authorId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileUserAction($authorId, $id, Request $request)
    {
//        $user = $this->getUser()->getId();
        $author = $this->getDoctrine()->getRepository(User::class)
            ->find($authorId);
        $publications = $this->getDoctrine()->getRepository(Publication::class)
            ->findBy(['authorId'=>$authorId]);

        $currentPublication = $this->getDoctrine()->getRepository(Publication::class)
            ->find($id);

        $currentUser = $this->getUser();

        $user = $this->getDoctrine()->getRepository(User::class)
            ->find($currentUser->getId());

        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();

        $contacts = [];
        for($i = 0; $i < count($users); $i++){
            if(((in_array("ROLE_ADMIN", $users[$i]->getRoles())) || (in_array("ROLE_SUPERADMIN", $users[$i]->getRoles())))
                    || (in_array("ROLE_SUPERADMIN", $users[$i]->getRoles()))
                && $currentUser->getId() != $users[$i]->getId()){
                $contacts[] = $users[$i];
            }
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user_Id = $request->request->get("contacts");
            $author = $this->getDoctrine()->getRepository(User::class)
                ->find($authorId);
            $currentPublication = $this->getDoctrine()->getRepository(Publication::class)
                ->find($id);

            $recipient_Id = $author;

            $message->setSenderUserId($currentUser);
            $message->setRecipientUserId($recipient_Id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute("profile", [
                'user' => $currentUser,
                'authorId'=>$authorId,
                'id'=> $currentPublication->getId(),
                'publications'=> $publications
            ]);
        }

        return $this->render("default/users_profile.html.twig", [
            'user' => $currentUser,
            'author'=>$author,
            'id'=> $currentPublication,
            'publications'=> $publications,
            "form" => $form->createView()]);
    }
}