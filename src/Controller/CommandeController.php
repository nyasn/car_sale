<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{

    /**
     * UserController constructor.
     * @param CommandeRepository $commandeRepository
     * @param ProduitRepository $produitRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandeRepository $commandeRepository,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->produitRepository = $produitRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande", name="commande")
     */
    public function index()
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    /**
     * @Route("/commande/add", name="add_order")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function addfournisseur(Request $request)
    {

        $currentUser =  $this->getUser();
        $products = $request->get('products');
       if(count($products)>0){
           foreach($products as $product){
               $produit_id = $product['id'];
               $quantity = $product['quantity'];
               $number =  $this->generate_number();
               $number_existant = $this->commandeRepository->findBy(['number_order'=>$number]);
               $i = 1;
               while(count($number_existant)>0) {
                   $number = $number.''.$i;
                   $number_existant = $this->commandeRepository->findBy(['number_order'=>$number]);
                   $i++;
               }
               $order = new Commande();
               $product = $this->produitRepository->find($produit_id);
               $order->setNumberOrder($number);
               $order->setProduit($product);
               $order->setQuantity($quantity);
               $order->setUser($currentUser);
               $order->setCreatedAt(new \DateTime('now'));
               $this->entityManager->persist($order);
               $this->entityManager->flush();
           }
       }
        
        $dataReturn = [
            'status' => true,
        ];
        //$this->addFlash('notice_success', "Votre commande est enregistrée !");

        return new JsonResponse($dataReturn);

    }

    /**
     * @Route("/commande/edit/{id}", name="edit_order")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function editfournisseur($id,Request $request)
    {
        $quantity = $request->get("quantity");
        $order = $this->commandeRepository->find($id);
        if (!$order){
            $dataReturn = [
                'status' => false,
                'message' => "Commandes not found !",
            ];
            return JsonResponse($dataReturn);
        }

        $order->setQuantity($quantity);
        $this->entityManager->flush();

        $dataReturn = [
            'status' => true,
            'message' => "Modification Commandes avec success !",
        ];
        return new JsonResponse($dataReturn);

    }

    /**
     * @Route("/commande/delete/{id}", name="delete_order")
     * @param $id
     * @return JsonResponse
     */
    public function deletefournisseur($id)
    {
        $order = $this->commandeRepository->find($id);
        if (!$order){
            $dataReturn = [
                'status' => false,
                'message' => "fournisseur not found !",
            ];
            return JsonResponse($dataReturn);
        }
        $this->entityManager->remove($order);
        $this->entityManager->flush();
        $dataReturn = [
            'status' => true,
            'message' => "Suppression Commandes avec success !",
        ];
        return new JsonResponse($dataReturn);
    }

    private function generate_number($length = 6) {
        $dico[0] = Array(1,2,3,4,5,6,7,8,9,0);
        $dico[1] = Array(1,2,3,4,5,6,7,8,9,0);
        $text = "";
        for($i = 0; $i<$length; $i++) {
            $option = mt_rand(0, 1);
            $case = mt_rand(0,count($dico[$option])-1);
            $text .= $dico[$option][$case];
        }

        return $text;
    }
}
