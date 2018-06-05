<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AboutUs;
use AppBundle\Entity\User;
use AppBundle\Form\AboutUsType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AboutUsController extends Controller
{
    /**
     * @Route("/aboutUs", name="about_us")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("GET")
     */
    public function aboutUsAction(Request $request)
    {
        $user = $this->getUser();
        $about = $this->getDoctrine()->getRepository(AboutUs::class)->
            find(2);

        $user1 = new User();

        $form = $this->createForm(UserType::class, $user1);


        return $this->render('default/about_us.html.twig', ['user'=>$user, 'about' => $about,
            'form' => $form->createView()]);
    }

//    /**
//     * @param Request $request
//     *
//     * @Route("/about_us/create", name="about_us_create")
//     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
//     *
//     * @return \Symfony\Component\HttpFoundation\Response
//     *
//     */
//    public function createContent(Request $request)
//    {
//        $user = $this->getUser();
//
//        $about = new AboutUs();
//        $form = $this->createForm(AboutUsType::class, $about);
//        $abouts = $this->getDoctrine()->getRepository(AboutUsType::class)->findAll();
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()){
//
//            $about->setAdminEdit($this->getUser());
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($about);
//            $em->flush();
//
//            return $this->redirectToRoute("about_us");
//        }
//
//        return $this->render("admin/about_us/create.html.twig",
//            array('form' => $form->createView(), 'user'=>$user));
//    }

    /**
     * @Route("/aboutUs/edit/{id}", name="about_us_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function adminEdit($id, Request $request)
    {
        $user = $this->getUser();
        $about = $this->getDoctrine()->getRepository(AboutUs::class)->find($id);
//        $abouts = $this->getDoctrine()->getRepository(AboutUs::class)->findAll();

        $currentUser = $this->getUser();

        if (!$currentUser->isSuperAdmin())
        {
            return $this->redirectToRoute("blog_index");
        }

        $form = $this->createForm(AboutUsType::class, $about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($about);
            $em->flush();

            return $this->redirectToRoute('about_us', ['user' => $user]);
        }

        return $this->render('admin/about_us/admin_aboutUs_edit.html.twig',
            array('about'=>$about,
                  'user'=>$user,
                  'form'=>$form->createView()));
    }
}
