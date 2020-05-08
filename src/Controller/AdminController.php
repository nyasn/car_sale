<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Entity\Produit;
use App\Repository\CommandeRepository;
use App\Repository\FournisseurRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(CommandeRepository $commandeRepository)
    {
        $data = $commandeRepository->getAllcommande();
        return $this->render('admin/order_manage.html.twig', [
            'name' => 'AdminController',
            'commandes'=>$data
        ]);
    }

    /**
     * @Route("/admin/fournisseur", name="admin_fournisseur")
     * @param FournisseurRepository $fournisseurRepository
     * @return Response
     */
    public function fournisseur(FournisseurRepository $fournisseurRepository)
    {
        $data = $fournisseurRepository->findAll();
        return $this->render('admin/provider.html.twig', [
            'name' => 'Les fournisseurs',
            'fournisseurs'=>$data
        ]);
    }


    /**
     * @Route("/admin/fournisseur/add", name="add_fournisseur")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function addfournisseur(Request $request,EntityManagerInterface $entityManager)
    {
        $titre = $request->get("name");
        $description = $request->get("description");
        $provider = new Fournisseur();
        $provider->setName($titre);
        $provider->setDescription($description);
        $provider->setCreatedAt(new \DateTime('now'));
        $entityManager->persist($provider);
        $entityManager->flush();
        $dataReturn = [
            'status' => true,
            'message' => "Ajout nouvelle fournisseur avec success !",
        ];
        return new JsonResponse($dataReturn);

    }

    /**
     * @Route("/admin/fournisseur/edit", name="edit_fournisseur")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FournisseurRepository $fournisseurRepository
     * @return JsonResponse
     * @throws \Exception
     */
    public function editfournisseur(Request $request,EntityManagerInterface $entityManager,FournisseurRepository $fournisseurRepository)
    {
        $provider = $fournisseurRepository->find((int)$request->get("id"));
        if (!$provider){
            $dataReturn = [
                'status' => false,
                'message' => "fournisseur not found !",
            ];
            return JsonResponse($dataReturn);
        }

        $provider->setName($request->get('name'));
        $provider->setDescription($request->get('description'));
        $entityManager->flush();

        $dataReturn = [
            'status' => true,
            'message' => "Modification avec success !",
        ];
        return new JsonResponse($dataReturn);
    }

    /**
     * @Route("/admin/fournisseur/delete/{id}", name="delete_fournisseur")
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @param FournisseurRepository $fournisseurRepository
     * @return
     */
    public function deletefournisseur($id,EntityManagerInterface $entityManager,FournisseurRepository $fournisseurRepository)
    {
        $provider = $fournisseurRepository->find($id);
        if (!$provider){
            $dataReturn = [
                'status' => false,
                'message' => "fournisseur not found !",
            ];
            return JsonResponse($dataReturn);
        }
        $entityManager->remove($provider);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_fournisseur'));
    }


    /**
     * @Route("/admin/produit", name="admin_produit")
     * @param FournisseurRepository $fournisseurRepository
     * @return Response
     */
    public function produit(FournisseurRepository $fournisseurRepository,ProduitRepository $produitRepository)
    {
        $data =$produitRepository->findAllProduct();
        $data_f = $fournisseurRepository->findAll();
        return $this->render('admin/product.html.twig', [
            'name' => 'Les produits',
            'products'=>$data,
            'fournisseurs'=>$data_f
        ]);
    }

    /**
     * @Route("/admin/product/add", name="add_product")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function addproduct(Request $request,EntityManagerInterface $entityManager,FournisseurRepository $fournisseurRepository)
    {
        $titre = $request->get("titre");
        $description = $request->get("description");
        $qte = $request->get("qte");
        $prix = $request->get("prix");
        $genre = $request->get("genre");
        $fournisseur = $fournisseurRepository->find( $request->get("fournisseur"));
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
     * @Route("/admin/product/edit", name="edit_product")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function editproduct(Request $request,ProduitRepository $produitRepository,EntityManagerInterface $entityManager,FournisseurRepository $fournisseurRepository)
    {

        $titre = $request->get("titre");
        $description = $request->get("description");
        $qte = $request->get("qte");
        $prix = $request->get("prix");
        $genre = $request->get("genre");
        $product = $produitRepository->find( $request->get("id"));
        if (!$product){
            $dataReturn = [
                'status' => false,
                'message' => "product not found !",
            ];
            return JsonResponse($dataReturn);
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

    /**
     * @Route("/admin/produit/delete/{id}", name="delete_product")
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @param ProduitRepository $produitRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteproduct($id,EntityManagerInterface $entityManager,ProduitRepository $produitRepository)
    {
        $product = $produitRepository->find($id);
        if (!$product){
            $dataReturn = [
                'status' => false,
                'message' => "Product not found !",
            ];
            return JsonResponse($dataReturn);
        }
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_produit'));
    }

    /**
     * @Route("/admin/stock", name="admin_stock")
     */
    public function stock()
    {
        return $this->render('admin/stock.html.twig', [
            'name' => 'Les stock',
        ]);
    }
}
