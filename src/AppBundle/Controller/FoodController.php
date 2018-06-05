<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FoodController extends Controller
{
    /**
     * @Route("/food", name="food")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $user1 = new User();
        $form = $this->createForm(UserType::class, $user1);

        $em = $this->getDoctrine()->getManager();

        $em = $this->getDoctrine()->getManager();
        $salads = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria_food = :criteria_food')
            ->setParameter('criteria_food', 'salads')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();

        $em = $this->getDoctrine()->getManager();
        $fresh = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria_food = :criteria_food')
            ->setParameter('criteria_food', 'fresh')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();

        $em = $this->getDoctrine()->getManager();
        $smoothies = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria_food = :criteria_food')
            ->setParameter('criteria_food', 'smoothies')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();

        return $this->render('default/food.html.twig', ['user' => $user, 'salads' => $salads,
            'fresh' => $fresh, 'smoothies' => $smoothies,
            'form' => $form->createView()]);
    }
}
