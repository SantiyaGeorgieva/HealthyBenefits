<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Publication;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CommentController extends Controller
{
//    /**
//     * @Route("/publication/commentCreate/{id}", name="comment_create")
//     * @param Request $request
//     * @param $id
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function createComment(Request $request, $id)
//    {
//        $user = $this->getUser();
//
//        $publication = $this->getDoctrine()->getRepository(Publication::class)->find(['id' => $id]);
//
//        $author = $this->getDoctrine()->getRepository(User::class)->find($user->getId());
//
//        $comment = new Comment();
//        $form = $this->createForm(CommentType::class, $comment);
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $comment->setUserComment($author);
//            $comment->setAuthorComment($publication);
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($comment);
//            $em->flush();
//
//            return $this->redirectToRoute("publication_view", ['id'=>$id]);
//        }
//
//        return $this->render('publication/create_comment.html.twig',
//            array('form' => $form->createView(), 'user'=>$user, 'publication' => $publication));
//    }

    /**
     * @Route("/publication/comment/edit/{id}", name="comment_edit")
     * @param $id
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editComment($id, Request $request)
    {
        $user = $this->getUser();
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAll();
        $commentDB = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        $publication = $this->getDoctrine()->getRepository(Publication::class)
            ->findOneBy(['id'=>$commentDB->getPublicationId()]);

        $form = $this->createForm(CommentType::class, $commentDB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user == null){
                return $this->redirectToRoute("user_register");
            }

            $em = $this->getDoctrine()->getManager();
            $em->merge($commentDB);
            $em->flush();

            return $this->redirectToRoute("publication_view", ['id'=>$publication->getId()]);
        }

        return $this->render('publication/edit_comment.html.twig', ['user'=>$user, 'comment'=>$commentDB,
            'form' => $form->createView()]);
    }

    /**
     * @Route("/comment/delete/confirm/{id}", name="comment_confirm_delete")
     * @param $id
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmDelete($id)
    {
        $user = $this->getUser();
//        $publications  = $this->getDoctrine()->getRepository(Publication::class)->findAll();

        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);

//        if ($comment == null) {
//            return $this -> redirectToRoute('blog_index');
//        }

//        $currentUser = $this->getUser();
//
//        if (!$currentUser->isAuthorComment($comment) || !$currentUser->isAdmin() || !$currentUser->isSuperAdmin())
//        {
//            return $this->redirectToRoute("blog_index");
//        }

        if ($comment != null) {

            $data = $this->getDoctrine()->getManager();
            $data->remove($comment);
            $data->flush();
        }

        return $this->redirectToRoute('publication_view', ['id'=>$comment->getPublicationId()]);
    }
}
