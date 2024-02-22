<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route(path: '/register', name: 'user_register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !'
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/account/profile', name: 'user_edit')]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user); // not needed
            $manager->flush();

            $this->addFlash(
                'success',
                'Les données du profil ont été enregistrées avec succès !'
            );
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/account/password-update', name: 'user_update_password')]
    public function updatePassword(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        #[CurrentUser] User $user,
    ): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $passwordHasher->hashPassword($user, $passwordUpdate->password);
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe à bien été modifié !'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/user/{id}/{slug?}', name: 'user_show')]
    public function show($id, $slug, UserRepository $userRepository): Response
    {
        /** @var User|null $user */
        $user = $userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }

        if ($slug !== $user->getSlug()) {
            return $this->redirectToRoute('user_show', ['id' => $id, 'slug' => $user->getSlug()]);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/account', name: 'user_my_account')]
    public function myAccount(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/account/bookings', name: 'user_bookings')]
    public function bookings(): Response
    {
        return $this->render('user/bookings.html.twig');
    }
}
