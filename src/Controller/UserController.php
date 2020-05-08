<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class UserController extends AbstractController
{
    private $tokenManager;
    private $session;
    private $profileRepository;
    private $userManager;
    private $entityManager;
    private  $translator;

    const ROLE_USER = 'ROLE_USER';

    /**
     * UserController constructor.
     * @param CsrfTokenManagerInterface|null $tokenManager
     * @param SessionInterface $session
     * @param UserManagerInterface $userManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CsrfTokenManagerInterface $tokenManager = null,
        SessionInterface $session,
        UserManagerInterface $userManager,
        EntityManagerInterface $entityManager
    ) {
        $this->tokenManager = $tokenManager;
        $this->session = $session;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     *@Route("/connexion", name="connexion")
     * @return Response
     */
    public function loginAction(Request $request)
    {
        /** @var $session Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        if ($this->checkConnected()) {
            return $this->redirectToRoute('_login_redirect');
        }

        // last username entered by the user
        $lastUsername =
            null === $session ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken
        ));
    }

    /**
     * @param array $data
     * @return Response
     */
    public function renderLogin(array $data)
    {
        return $this->render('auth/login.html.twig', $data);
    }

    public function checkConnected()
    {
        $securityContext = $this->container->get(
            'security.authorization_checker'
        );
        if ($securityContext->isGranted('ROLE_USER')) {
            return true;
        } elseif ($securityContext->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }

    /**
     * @Route("/connexion/redirect", name="_login_redirect")
     */
    public function redirectAction()
    {
        $authChecker = $this->container->get('security.authorization_checker');


        //////////////////////////////////////////////


        /////////////////////////////////////////////

        $cookie = new Cookie(
            'connected',
            true,
            time() + 3600 * 24 * 60,
            '/',
            null,
            false,
            false
        );

        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $response = $this->redirectToRoute('home_admin');
            $response->headers->setCookie($cookie);

            return $response;
        } elseif ($authChecker->isGranted('ROLE_USER')) {
            $response = $this->redirectToRoute('home');
            $response->headers->setCookie($cookie);

            return $response;
        }
        $response = $this->redirectToRoute('home');
        $response->headers->setCookie($cookie);

        return $response;
    }


}
