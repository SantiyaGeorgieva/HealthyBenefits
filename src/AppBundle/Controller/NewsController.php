<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Publication;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /**
     * @Route("/news", name="news")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $user1 = new User();

        $form = $this->createForm(UserType::class, $user1);

        $em = $this->getDoctrine()->getManager();
        $news = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'news')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->execute();

//        $news = array_filter($publications, function ($publication){
//            return $publication->getCriteria() === "news";
//        });
        return $this->render('default/news.html.twig', ['user'=>$user, 'news'=> $news, "form" => $form->createView()]);
    }
}
