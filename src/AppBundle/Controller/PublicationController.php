<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\PublicationLike;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Form\PublicationLikeType;
use AppBundle\Form\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PublicationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/publication/create", name="publication_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function createPublication(Request $request)
    {
        $user = $this->getUser();

        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $publications = $this->getDoctrine()->getRepository(Publication::class)->findAll();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $file = $request->files->get("image");

//            /**
//             * @var UploadedFile $file
//             */
//            $file = $publication->getImage();

//            $fileName = $file->getClientOriginalName();

//            var_dump($file);
            $fileName = md5(uniqid()) . "." . $file->guessExtension();

            $file->move(
                $this->getParameter("publication_directory"), $fileName
            );

            $publication->setImage($fileName);

            $publication->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $em->flush();

            return $this->redirectToRoute("user_profile", ['id' => $user->getId()]);
        }

        return $this->render("publication/create.html.twig",
            array('form' => $form->createView(), 'user'=>$user, 'publications'=>$publications));
    }

    /**
     * @param Request $request
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/publication/like/{id}", name="publication_like")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function addPublicationLike(Request $request, $id)
    {
        $user = $this->getUser();
        $likes = $this->getDoctrine()->getRepository(PublicationLike::class)
            ->findBy(['publicationId' => $id]);

        for ($i=0; $i<count($likes); $i++){
            if ($likes[$i]->getUserId() == $user->getId() &&
                $likes[$i]->getPublicationId() == $id){

                return $this->redirectToRoute("publication_view", ['id' => $id]);
            }
        }

        $publicationLike = new PublicationLike();
        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($id);

        $publicationLike->setPublicationLike($publication);
        $publicationLike->setUserLike($user);

        $publication->setViewLikes($publication->getViewLikes()+1);
        $publication->setViewCount($publication->getViewCount() - 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($publicationLike);
        $em->persist($publication);
        $em->flush();

        return $this->redirectToRoute('publication_view', ['id' => $id]);
    }

    /**
     * @Route("publication/{id}", name="publication_view")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewPublication($id, Request $request)
    {
        $user = $this->getUser();
        $likes = $this->getDoctrine()->getRepository(PublicationLike::class)
                ->findBy(['publicationId' => $id]);

        if ($user == null){

            $publication = $this->getDoctrine()->getRepository(Publication::class)->findOneBy(['id' => $id]);
            $comments = $this->getDoctrine()->getRepository(Comment::class)
                ->findBy(['publicationId'=>$publication->getId()], ["dateAdded"=>'desc']);

//            $comments = $em
//                ->getRepository('AppBundle:Comment')
//                ->createQueryBuilder('e')
//                ->where('e.publicationId = :id')
//                ->setParameter('id', $id)
//                ->addOrderBy('e.dateAdded', 'DESC')
//                ->setMaxResults(10)
//                ->getQuery()
//                ->execute();
        }else{
            $publication = $this->getDoctrine()->getRepository(Publication::class)->findOneBy(['id' => $id]);

            $comments = $this->getDoctrine()->getRepository(Comment::class)
                ->findBy(['publicationId'=>$publication->getId()], ["dateAdded"=>'desc']);

            $author = $this->getDoctrine()->getRepository(Comment::class)->find(['id'=>$user->getId()]);
            $user = $this->getDoctrine()->getRepository(User::class)->find(['id'=>$user->getId()]);
        }

//        $publication = $this->getDoctrine()->getRepository(Publication::class)->findOneBy(['id' => $id]);
//        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['publicationId'=>$publication->getId()]);
//        $author= $this->getDoctrine()->getRepository(Comment::class)->find(['id'=>$user->getId()]);
//        $user = $this->getDoctrine()->getRepository(User::class)->find(['id'=>$user->getId()]);

        $myCount = count($comments);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        $publication->setViewCount($publication->getViewCount()+1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($publication);
        $em->flush();

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user == null){
                return $this->redirectToRoute("user_register");
            }

            $comment->setUserComment($user);
            $comment->setAuthorComment($publication);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute("publication_view", ['id'=>$id]);
        }
        if ($user == null){
            return $this->render('publication/publication.html.twig', [
                'publication' => $publication,
                'comments'=>$comments,
                'myCount'=> $myCount,
                'likes' => count($likes),
                'form' => $form->createView()]);
        }
        return $this->render('publication/publication.html.twig', [
            'user' => $user,
            'publication' => $publication,
            'comments'=>$comments,
            'author'=>$author,
            'myCount' => $myCount,
            'likes' => count($likes),
            'form' => $form->createView()]);
    }

    /**
     * @Route("/profile/publication/edit/{id}", name="publication_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPublication($id, Request $request)
    {
        $user = $this->getUser();
        $publication = $this->getDoctrine()->getRepository(Publication::class)
            ->find($id);

        if ($publication == null){
            return $this->redirectToRoute("user_profile");
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($publication) && !$currentUser->isAdmin() && !$currentUser->isSuperAdmin())
        {
            return $this->redirectToRoute("blog_index");
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->remove("image");

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $file = $request->files->get("image");

            if($file != null) {

                if ($publication->getImage() != null) {
                    $image = $publication->getImage();
                    $path = $this->getParameter('publication_directory') . '/' . $image;
                    $fs = new Filesystem();
                    $fs->remove(array($path));
                }

                $fileName = md5(uniqid()) . "." . $file->guessExtension();

                $file->move(
                    $this->getParameter("publication_directory"), $fileName
                );

                $publication->setImage($fileName);
            }

            $data = $this->getDoctrine()->getManager();
            $data->persist($publication);
            $data->flush();

            return $this->redirectToRoute('publication_view',
                ['user' => $user, 'id' => $publication->getId()]);
        }

        return $this->render('publication/edit.html.twig', [
            'publication' => $publication,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/publication/delete/confirm/{id}", name="publication_confirm_delete")
     * @param $id
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmDelete($id)
    {
//        $user = $this->getUser();
//        $publications  = $this->getDoctrine()->getRepository(Publication::class)->findAll();

        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($id);

        if ($publication == null) {
            return $this -> redirectToRoute('user_profile');
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($publication) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute("blog_index");
        }

        if ($publication != null) {

            $em = $this->getDoctrine()->getManager();

            $em->remove($publication);
            $em->flush();
        }

        return $this->redirectToRoute('user_profile');
    }
}