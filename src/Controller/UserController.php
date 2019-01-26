<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="user_register")
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !"
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/profile", name="user_edit")
     */
    public function edit(Request $request, ObjectManager $manager)
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
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/password-update", name="user_update_password")
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $hash = $passwordEncoder->encodePassword($user, $passwordUpdate->password);
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre mot de passe à bien été modifié !"
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{id}/{slug?}", name="user_show")
     */
    public function show($id, $slug, UserRepository $userRepository)
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['id' => $id]);

        if ($slug != $user->getSlug()) {
            return $this->redirectToRoute('user_show', ['id' => $id, 'slug' => $user->getSlug()]);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/account", name="user_my_account")
     */
    public function myAccount()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/show.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
