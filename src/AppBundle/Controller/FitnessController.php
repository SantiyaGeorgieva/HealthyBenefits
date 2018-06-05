<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\DateTime;

class FitnessController extends Controller
{
    /**
     * @Route("/fitness", name="fitness")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $publications = $this->getDoctrine()->getRepository(Publication::class)
            ->findAll();

        $user1 = new User();

        $form = $this->createForm(UserType::class, $user1);

        $em = $this->getDoctrine()->getManager();
        $fitness = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'fitness')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->execute();

        $fitness1 = $em
            ->getRepository('AppBundle:Publication')
            ->createQueryBuilder('e')
            ->where('e.criteria = :criteria')
            ->setParameter('criteria', 'fitness')
            ->addOrderBy('e.dateAdded', 'DESC')
            ->setFirstResult(2)
            ->getQuery()
            ->execute();

//        $about = $this->getDoctrine()->getRepository(AboutUs::class)->findOneBy(['id' => $id]);

        return $this->render('default/fitness.html.twig', ['user' => $user, 'publications'=>$publications, 'fitness'=>$fitness, 'fitness1'=>$fitness1,
            'form' => $form->createView()]);
    }

}
