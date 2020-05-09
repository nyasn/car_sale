<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class RegistrationController extends BaseController
{
    private $eventDispatcher;
    private $userManager;
    private $tokenStorage;
    private $tokenGenerator;


    const ROLE_USER = 'ROLE_USER';

    /**
     * RegistrationController constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserManagerInterface $userManager
     * @param TokenStorageInterface $tokenStorage
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserManagerInterface $userManager,
        TokenStorageInterface $tokenStorage,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @Route("/create-account", name="sign_up")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function registerAction(Request $request)
    {
        $user = new User();

        if($this->checkConnected()){
            return $this->redirectToRoute('_login_redirect');
        }
        $event = new GetResponseUserEvent($user, $request);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $user->setEnabled(true);
            $user->setRoles([self::ROLE_USER]);
            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $this->addFlash(
                    'notice_success',
                    'Votre compte est enregistrer avec succès !'
                );
                return $this->redirectToRoute('connexion');
            }

            if (null !== $response = $event->getResponse()) {
                $this->addFlash(
                    'notice_error',
                    $response
                );
                return $this->render("auth/register.html.twig",[
                    'registrationForm' => $form->createView(),
                    'data'=> $user
                ]);
            }
        }
        return $this->render("auth/register.html.twig",[
            'registrationForm' => $form->createView()
        ]);
    }



    public function checkConnected(){
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_USER')) {
            return true;
        }elseif($securityContext->isGranted('ROLE_ADMIN')){
            return true;
        }
        return false;
    }

    /**
     * Tell the user his account is now confirmed.
     * @param Request $request
     * @return Response
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Registration/confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
        ));
    }

    /**
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session)
    {
        $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }

        return null;
    }
}
