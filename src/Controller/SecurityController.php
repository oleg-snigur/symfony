<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Отримати помилку входу, якщо вона є
        $error = $authenticationUtils->getLastAuthenticationError();
        // Останнє введене ім'я користувача
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
       // Якщо користувач вже увійшов — на стрічку, якщо ні — на логін
       if ($this->getUser()) {
           return $this->redirectToRoute('app_feed');
       }

    return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Цей метод буде перехоплений firewall-ом.');
    }
}
