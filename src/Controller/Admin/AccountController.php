<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $username = $authenticationUtils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError' => null !== $error,
            'username' => $username,
        ]);
    }

    /**
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout()
    {
    }
}
