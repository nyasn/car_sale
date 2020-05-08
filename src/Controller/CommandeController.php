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
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function addfournisseur(Request $request,EntityManagerInterface $entityManager,ProduitRepository $produitRepository,CommandeRepository $commandeRepository)
    {
        $produit_id = $request->get("produit_id");
        $user_id = $request->get("user_id");
        $quantity = $request->get("quantity");
        $number =  $this->generate_number();
        $number_existant = $commandeRepository->findBy(['number_order'=>$number]);
        $i = 1;
        while(count($number_existant)>0) {
            $number = $number.''.$i;
            $number_existant = $commandeRepository->findBy(['number_order'=>$number]);
            $i++;
        }
        $order = new Commande();
        $product = $produitRepository->find($produit_id);
        $user = $entityManager->find($user_id);
        $order->setNumberOrder($number);
        $order->setProduit($product);
        $order->setQuantity($quantity);
        $order->setUser($user);
        $order->setCreatedAt(new \DateTime('now'));
        $entityManager->persist($order);
        $entityManager->flush();
        $dataReturn = [
            'status' => true,
            'message' => "Ajout nouvelle commande avec success !",
        ];
        return new JsonResponse($dataReturn);

    }

    /**
     * @Route("/commande/edit/{id}", name="edit_order")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CommandeRepository $commandeRepository
     * @return JsonResponse
     */
    public function editfournisseur($id,Request $request,EntityManagerInterface $entityManager,CommandeRepository $commandeRepository)
    {
        $quantity = $request->get("quantity");
        $order = $commandeRepository->find($id);
        if (!$order){
            $dataReturn = [
                'status' => false,
                'message' => "Commandes not found !",
            ];
            return JsonResponse($dataReturn);
        }

        $order->setQuantity($quantity);
        $entityManager->flush();

        $dataReturn = [
            'status' => true,
            'message' => "Modification Commandes avec success !",
        ];
        return new JsonResponse($dataReturn);

    }

    /**
     * @Route("/commande/delete/{id}", name="delete_order")
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @param CommandeRepository $commandeRepository
     * @return JsonResponse
     */
    public function deletefournisseur($id,EntityManagerInterface $entityManager,CommandeRepository $commandeRepository)
    {
        $order = $commandeRepository->find($id);
        if (!$order){
            $dataReturn = [
                'status' => false,
                'message' => "fournisseur not found !",
            ];
            return JsonResponse($dataReturn);
        }
        $entityManager->remove($order);
        $entityManager->flush();
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
