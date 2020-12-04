<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\LoginAttempt;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LoginAttemptRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class JWTCreatedListener
{

    private $loginAttemptRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;
    private $entityManager;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, LoginAttemptRepository $loginAttemptRepository, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->loginAttemptRepository = $loginAttemptRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event) {
        $exception = $event->getException();

        //$credentials = [
        //    'email' => $request->request->get('email'),
        //];

        // Début de notre modification: on sauvegarde une tentative de connexion
        //$newLoginAttempt = new LoginAttempt($request->getClientIp(), $credentials['email']);
        $newLoginAttempt = new LoginAttempt('1:1:1:1', 'fff@fez.fr');

        $this->entityManager->persist($newLoginAttempt);
        $this->entityManager->flush();

        // Deuxième modification, la vérification
        if ($this->loginAttemptRepository->countRecentLoginAttempts('fff@fez.fr') > 3) {
            // CustomUserMessageAuthenticationException nous permet de définir nous-même le message,
            // qui sera affiché à l'utilisateur (ou bien sa clef de traduction).
            // Attention toutefois à ne pas révéler trop d'informations dans le messages,
            // notamment ne pas indiquer si le compte existe.
            throw new CustomUserMessageAuthenticationException('Vous avez essayé de vous connecter avec un mot'
                .' de passe incorrect de trop nombreuses fois. Veuillez patienter svp avant de ré-essayer.');
        }


        return $exception;

    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        if ($user instanceof User) {
            $data['data'] = array(
                'id'        => $user->getId(),
                'email'     => $user->getEmail(),
                'roles'     => $user->getRoles(),
            );
        }

        $event->setData($data);
    }

}