<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Entity\Produit;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    
    private $produitRepository;
    protected $session;
   
    /**
     * CartController constructor.
     * @param ProduitRepository $produitRepository
     * @param CartService $cartService
     */
    public function __construct(
        ProduitRepository $produitRepository,
        SessionInterface $session
    ) {
        $this->session = $session;
        $this->produitRepository = $produitRepository;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(Request $request)
    {
        return $this->render('fo/cart/index.html.twig');
    }

    /**
     * @Route("/cart-ajax", name="cart_ajax")
     * @param Request $request
     * @return JsonResponse
     */
    public function cart_ajax(Request $request)
    {
        $user = $this->getUser();
        $cart = $this->cartService->getCartContent($user);


        /*$CartIdNotIn = array();
        $cartCourse = array();
        foreach ($cart as $key => $acart) {
            if (!is_null($acart['cours'])) {
                $coursId = $acart['cours']->getId();
                $course = $this->coursRepository->find($acart['cours']->getId());
                $cartCourse[$key]['cours']['id'] = $course->getId();
                $cartCourse[$key]['cours']['name_cours'] = $course->getNamecours();
                $cartCourse[$key]['cours']['court_description'] = $course->getCourtDescription();
                $cartCourse[$key]['cours']['price'] = $course->getPrice();
                $cartCourse[$key]['cours']['remise'] = $course->getRemise();
                $cartCourse[$key]['cours']['url_image'] = $course->getUrlImage();
                $cartCourse[$key]['price'] = $acart['price'];
                if (!in_array($coursId, $CartIdNotIn)) {
                    array_push($CartIdNotIn, $coursId);
                }
            }
        }

        $coursNotIN = $this->coursRepository->findCoursNotInCart($CartIdNotIn);


        $otherCourse_array = array();
        if (!is_null($coursNotIN) && is_array($coursNotIN) && count($coursNotIN) > 0) {
            $otherCourse_array = $this->profileservice->findCoursWithChapitre($coursNotIN);
        }

        return new JsonResponse([
            'cart' => $cartCourse,
            'total' => $this->cartService->getTotal(),
            'otherCourse' => $otherCourse_array,
            'favory' => $this->profileservice->favorybyUserId($user),
            'cartindex' => $this->cartService->getCartContent($user)
        ]);*/
    }

    /**
     * @Route("/cart/checkout", name="cart_checkout")
     */
    public function cart_checkout()
    {
        $securityContext = $this->container->get(
            'security.authorization_checker'
        );

        if (!$this->securityService->checkConnected($securityContext)) {
            return $this->redirectToRoute('connexion');
        }
        $currentUser = $this->getUser();
        $itemCarts = $this->cartService->getCartContent($currentUser);
        if(count($itemCarts) > 0){
            $method = $this->methodPaymentRepository->findBy(['etat' => true],['name_mp' => 'ASC']);

            return $this->render('fo/cart/cart_checkout.html.twig', [
                'cart' => $itemCarts,
                'total' => $this->cartService->getTotal(),
                'itemsInCart' => count($itemCarts),
                'mpayements' => $method,
                'stripe_api_key' => $this->getParameter('stripe_api_key'),
                'reference'=>$this->generate_ref(6)
            ]);
        }else{
            $this->addFlash('notice_error', $this->translator->trans('app.cart.cart_empty'));
            return $this->redirectToRoute('cart');
        }

    }
    function generate_ref($length){
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $reference = '';
        for($i=0; $i<$length; $i++){
            $reference .= $chars[rand(0, strlen($chars)-1)];
        }
        $reference = 'REF_'.$reference;
        if($this->payementRepository->checkReference($reference))
            $this->generate_ref($length);
        else
            return $reference;
    }

    /**
     * @Route("/cart/countItem", name="cart_count_item")
     */
    public function cart_count_item()
    {
        $currentUser = $this->getUser();
        $itemCarts = $this->cartService->getCartContent($currentUser);
        return new JsonResponse([
            'status' => 'OK',
            'cartCount' => count($itemCarts)
        ]);
    }

    /**
     * @Route("/cart/add/item", name="cart.addItem")
     * @param Request $request
     * @return JsonResponse
     */
    public function addItem(
        Request $request
    ) {
        $currentUser = $this->getUser();
        if ($request->request->get('idcours')) {
            $cours = $this->coursRepository->find(
                $request->request->get('idcours')
            );
            $save = $this->cartService->addCours($cours,$currentUser);
//            $dispatcherInterface->dispatch(
//                CoursEvent::COUR_ADDED,
//                new CoursEvent($cours, 1)
//            );
            //            return $this->redirectToRoute('cart');
            if($save['save_cart']){
                return new JsonResponse([
                    'status' => 'OK',
                    'cartCount' => count($this->cartService->getCartContent($currentUser)),
                    'message' => $cours->getNamecours(). ' ' .$this->translator->trans('app.cart.msg_add_cart')
                ]);
            }
            else{
                return new JsonResponse([
                    'status' => 'KO',
                    'cartCount' => count($this->cartService->getCartContent($currentUser)),
                    'message' => $cours->getNamecours(). ' ' .$this->translator->trans('app.cart.msg_add_cart_but_exist' )
                ]);
            }

        }
        return  new JsonResponse([
            'status' => 'KO',
            'message' =>  $this->translator->trans('app.cart.error_add_cart')
        ]);
    }

    /**
     * @Route("/cart/check/connected", name="cart.check.connected")
     * @param Request $request
     * @param EventDispatcherInterface $dispatcherInterface
     * @param CsrfTokenManagerInterface $tokenManager
     * @return Response
     */
    public function check_connected(
        Request $request,
        EventDispatcherInterface $dispatcherInterface,
        CsrfTokenManagerInterface $tokenManager
    ) {
        $lastUsername = '';
        $error = null;
        $csrfToken = $tokenManager
            ? $tokenManager->getToken('authenticate')->getValue()
            : null;
        $form = $this->createForm(LoginModalForm::class);

        return $this->render('fo/auth/login_modal.html.twig', array(
            'csrf_token' => $csrfToken,
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView()
        ));

        //        return new JsonResponse(['status'=> 'OK', 'csrf_token' => $csrfToken]);
    }

    /**
     * @Route("/cart/remove/item", name="cart.removeItem")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeItem(
        Request $request
    ) {
        $currentUser = $this->getUser();
        $cours = $this->cartService->getCours($request->request->get('idcours'));
        if ($cours) {
            $this->cartService->removeCours($cours, $currentUser);
//            $dispatcherInterface->dispatch(
//                CoursEvent::COUR_DELETED,
//                new CoursEvent($cours)
//            );
            return new JsonResponse([
                'status' => 'OK',
                'cartCount' => count($this->cartService->getCartContent($currentUser)),
                'message' => $this->translator->trans('app.cart.the_course').' '.$cours->getNamecours() .' '. $this->translator->trans('app.cart.has_deleted')
            ]);

        }
//        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/clear/all", name="cart.clear")
     * @param Request $request
     * @return JsonResponse
     */
    public function clear(Request $request)
    {
        $currentUser = $this->getUser();
        $this->cartService->emptyCart($currentUser);
//        return $this->redirectToRoute('cart');
        return new JsonResponse([
            'status' => 'OK',
            'cartCount' => count($this->cartService->getCartContent($currentUser)),
            'message' => $this->translator->trans('app.cart.deleted_cart')
        ]);
    }

    /**
     * @Route("/change-language", name="change.lang")
     * @param null $langue
     * @return JsonResponse
     */
    public function change_language(Request $request)
    {
        if($request->get('language') != null)
        {
            $this->get('session')->set('_locale', $request->get('language'));
        }

        $url = $request->headers->get('referer');
        if(empty($url))
        {
            $url = $this->container->get('router')->generate('home');
        }
        return $this->redirect($url);
    }
}
