<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Message;
use AppBundle\Entity\NewsLetter;
use AppBundle\Entity\Publication;
use AppBundle\Entity\PublicationLike;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\NewsLetterType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $user1 = new User();

        $form = $this->createForm(UserType::class, $user1);

        $em = $this->getDoctrine()->getManager();

        $slader_news = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'news')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->execute();

        $slader_fitness = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'fitness')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->execute();

        $slader_foods = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'food')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->execute();

        $newNews = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'news')
            ->addOrderBy('e.viewLikes', 'DESC')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->execute();

        $fitnes = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'fitness')
            ->addOrderBy('e.viewLikes', 'DESC')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->execute();

        $foods = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'food')
            ->addOrderBy('e.viewLikes', 'DESC')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->execute();

//        $foods->orderBy(array('e.viewLikes' => 'DESC'));

        $lastNews = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'news')
            ->addOrderBy('e.viewLikes', 'DESC')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setFirstResult(1)
            ->setMaxResults(3)
            ->getQuery()
            ->execute();

        $all = [];
        foreach ($slader_foods as $food){
            $all[]=$food;
        }

        foreach ($slader_news as $news){
            $all[]=$news;
        }

        foreach ($slader_fitness as $fitness){
            $all[]=$fitness;
        }

        return $this->render('default/index.html.twig', [
            'user' => $user,
            'lastNews' => $lastNews,
            'fitnes' => $fitnes,
            'foods' => $foods,
            'all' => $all,
            'form' => $form->createView(),
            'newNews' => $newNews,
            'login_val' => 'login']);
    }

    /**
     * @Route("/newsletter", name="news_letter")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsletterAction(Request $request){

//        $user = null;
//        if ($user != null){
            $newsletterEmails = $this->getDoctrine()->getRepository(NewsLetter::class)
                ->findAll();
//        }

        $emails = [];

        foreach ($newsletterEmails as $email){
            $emails[] = $email->getEmail();
        }

        $url = $request->request->get('url');

        $newsletter = new NewsLetter();

        $name = $request->request->get('newsName');
        $email = $request->request->get('newsEmail');

        if ($this->getUser() == null && ($name != null && $email != null)){
            $newsletter->setName($name);
            $newsletter->setEmail($email);

            return $this->redirectToRoute($url);
        }

        if (($name != null && $email != null) && in_array($email, $emails) != true) {

            $newsletter->setName($name);
            $newsletter->setEmail($email);
        }
        else {
            return $this->redirectToRoute($url);
        }
        $em = $this->getDoctrine()->getManager();

        $em->persist($newsletter);
        $em->flush();

        return $this->redirectToRoute($url);
    }
}
