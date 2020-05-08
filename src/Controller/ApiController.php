<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\FournisseurRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/product/add", name="api_add_product")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function addproduct(Request $request,EntityManagerInterface $entityManager,FournisseurRepository $fournisseurRepository)
    {

        $titre = $request->get("titre");
        $description = $request->get("description");
        $qte = $request->get("quantite");
        $prix = $request->get("prix");
        $genre = $request->get("genre");
        $fournisseur = $fournisseurRepository->find( $request->get("fournisseur_id"));
        if (!$fournisseur){
            $dataReturn = [
                'status' => false,
                'message' => "Fournisseur not found !",
            ];
            return new JsonResponse($dataReturn);
        }
        $product = new Produit();
        $product->setTitre($titre);
        $product->setDescription($description);
        $product->setFournisseur($fournisseur);
        $product->setPrix($prix);
        $product->setGenre($genre);
        $product->setQuantite($qte);
        $product->setCreatedAt(new \DateTime('now'));
        $entityManager->persist($product);
        $entityManager->flush();
        $dataReturn = [
            'status' => true,
            'message' => "Ajout nouvelle produit avec success !",
        ];
        return new JsonResponse($dataReturn);
    }

    /**
     * @Route("/api/product/edit", name="api_edit_product")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function editproduct(Request $request,ProduitRepository $produitRepository,EntityManagerInterface $entityManager,FournisseurRepository $fournisseurRepository)
    {
        $titre = $request->get("titre");
        $description = $request->get("description");
        $qte = $request->get("quantite");
        $prix = $request->get("prix");
        $genre = $request->get("genre");
        $product = $produitRepository->find( $request->get("produit_id"));
        if (!$product){
            $dataReturn = [
                'status' => false,
                'message' => "product not found !",
            ];
            return new JsonResponse($dataReturn);
        }
        $product->setTitre($titre);
        $product->setDescription($description);
        $product->setPrix($prix);
        $product->setGenre($genre);
        $product->setQuantite($qte);
        $entityManager->persist($product);
        $entityManager->flush();
        $dataReturn = [
            'status' => true,
            'message' => "Modification avec success !",
        ];
        return new JsonResponse($dataReturn);
    }
}
