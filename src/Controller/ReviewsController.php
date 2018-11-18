<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Reviews;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class ReviewsController extends Controller
{
    /**
     * @Route("/product/{id}/reviews", name="review_list",  methods={"GET","HEAD"})
     * 
     */
    public function index( $id )
    {
       //$reviews = $this->getDoctrine()->getRepository(Reviews::class)->find('product_id', $id);
       
       //взимаме продукта по ID 
       $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

       $user = $this->getUser();

       $em = $this->getDoctrine()->getManager();

       $querry = 'SELECT * FROM reviews where product_id = :id LIMIT 100;';
       
       $statement = $em->getConnection()->prepare($querry);
       // Set parameters 
       $statement->bindValue('id', $id);
       $statement->execute();

       $reviews = $statement->fetchAll();


       return $this->render('reviews/index.html.twig', array('reviews' => $reviews, 'product' => $product, 'user'=>$user));
    }
    /**
     * @Route("/product/{id}/reviews/new", name="new_review", methods={"GET","POST","HEAD"} )
     * 
     */
    public function new($id, Request $request){
        $review = new Reviews();

       $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        
        $form = $this->createFormBuilder($review)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('description', TextareaType::class, array(
          'required' => true,
          'attr' => array('class' => 'form-control')
        ))
        ->add('rating', NumberType::class, array(
            'required' => true,
            'attr' => array('class' => 'form-control',
                            'maxlength' => 1)
          ))
        ->add('product_id', HiddenType::class, array(
            'data' => $id
        ))
        //HARDCORED FOR NOW , WHEN INSTALL USER AUTH WILL PASS USER ID
        ->add('user_id', HiddenType::class, array(
            'data' => 1
        ))
        //HARDCORED FOR NOW , WHEN INSTALL USER AUTH WILL PASS DEFAULT APPROVED ==0
        //AND WILL MAKE ADMIN PANEL WHERE AMDINS WILL APPROVED
        ->add('approved', HiddenType::class, array(
            'data' => 1
        ))
        ->add('save', SubmitType::class, array(
          'label' => 'Create',
          'attr' => array('class' => 'btn btn-primary mt-3')
        ))
        ->getForm();
      $form->handleRequest($request);
      
      if($form->isSubmitted() && $form->isValid()) {
        $review = $form->getData();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($review);
        $entityManager->flush();
        return $this->redirectToRoute('review_list', array('id' => $id));
      }
      return $this->render('reviews/new.html.twig', array(
        'form' => $form->createView(),
        'product' => $product
      ));
    }

    }



