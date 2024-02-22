<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    #[Route(path: '/admin/login', name: 'admin_account_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $username = $authenticationUtils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError' => null !== $error,
            'username' => $username,
        ]);
    }

    #[Route(path: '/admin/logout', name: 'admin_account_logout')]
    public function logout()
    {
    }
}
