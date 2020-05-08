<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FournisseurRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    private $markRepository;
    private $entityManager;
    private $productRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FournisseurRepository $markRepository,
        ProduitRepository $productRepository
    ) {
        $this->markRepository = $markRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $marks_array = array();
        $marks = $this->markRepository->findAll();
        foreach($marks as $i => $mark ){
            $products = $this->productRepository->findBy(['fournisseur' => $mark]);
            $marks_array[$i]['products'] = $products;
            $marks_array[$i]['marks'] = $mark;
        }
        return $this->render('home/index.html.twig', [
            'list_mark_by_product' => $marks_array,
        ]);
    }

    /**
     * @Route("/wishlist", name="wishlist")
    */

    public function wishlist()
    {
        return $this->render('cart/index.html.twig');
    }

    /**
     * @Route("/register", name="register")
    */

    public function register()
    {
        return $this->render('cart/index.html.twig');
    }

    /**
     * @Route("/login", name="login")
    */

    public function login()
    {
        return $this->render('cart/index.html.twig');
    }

}
