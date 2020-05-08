<?php


namespace App\Service;


use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $commandeRepository;
    protected $coursRepository;
    protected $entityManager;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->produitRepository = $produitRepository;
        $this->entityManager = $entityManager;
        if ($this->session->get('products') === null) {
            $this->session->set('products', array());
        }
    }

    public function addProducts(Produit $produit, $user)
    {
        $cart = $this->session->get('cart');
        $product = array(
            'cours' => $cours,
            'quantity' => 1,
            'price' => $cours->getPrice(),
            'remise' => $cours->getRemise()
        );
        $result = array();
        if(!array_key_exists($cours->getId(), $cart)){
            $cart[$cours->getId()] = $product;

            if($user){
                $comm = $this->commandeRepository->findOneBy(['user'=> $user, 'cours'=> $cours]);
                if($comm){
                    if(!$comm->getStatus()){
                        $this->session->set('cart', $cart);
                        $result['save_cart'] = true;
                    }else{
                        $result['save_cart'] = false;
                    }
                }else{
                    $commande = new Commande();
                    $commande->setUser($user);
                    $commande->setCours($cours);
                    $commande->setStatus(false);
                    $this->entityManager->persist($commande);
                    $this->entityManager->flush();
                    $this->session->set('cart', $cart);
                    $result['save_cart'] = true;
                }
            }else{
                $this->session->set('cart', $cart);
                $result['save_cart'] = true;
            }
        }
        return $result;
    }

    public function getCartContent($user){
        $cart = $this->session->get('cart');
        return $cart;
    }

    public function getTotal(){
        $cart = $this->session->get('cart');
        $total = 0;
        foreach ($cart as $cours) {
            if($cours['cours']->getRemise()>0){
                $remise = ($cours['cours']->getPrice() -(($cours['cours']->getPrice()*$cours['cours']->getRemise())/100))*$cours['quantity'];
            }else{
                $remise = $cours['cours']->getPrice() * $cours['quantity'];
            }
            $total += $remise;
        };
        return $total;
    }

    public function emptyCart($user){
        if($user){
            $comm = $this->commandeRepository->findBy(['user'=> $user, 'status'=> false]);
            if(is_array($comm)){
                foreach ($comm as $com){
                    $this->entityManager->remove($com);
                    $this->entityManager->flush();
                }
            }
        }
        $this->session->set('cart', array());
    }

    public function isEmpty(){
        if(empty($this->session->get('cart'))){
            return true;
        }
        return false;
    }

    public function removeCours(Cours $cours, $user){
        $cart = $this->session->get('cart');
        if($this->getCours($cours->getId())){
            unset($cart[$cours->getId()]);
            $this->session->set('cart', $cart);
            if($user){
                $comm = $this->commandeRepository->findOneBy(['user'=> $user,'cours'=> $cours, 'status'=> false]);
                if($comm){
                    $this->entityManager->remove($comm);
                    $this->entityManager->flush();
                }
            }
        }
    }

    public function getCours($id){
        $cart = $this->session->get('cart');
        if(array_key_exists($id, $cart)){
            return $cart[$id]['cours'];
        }else{
            return null;
        }
    }

    public function countCours()
    {
        $cart = $this->session->get('cart');
        $count = 0;
        foreach ($cart as $cours) {
            $count += $cours['quantity'];
        };
        return $count;
    }
}