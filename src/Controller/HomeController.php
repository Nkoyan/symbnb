<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $adRepository, UserRepository $userRepository)
    {
        return $this->render('home.html.twig', [
            'bestAds' => $adRepository->findBestAds(3),
            'bestUsers' => $userRepository->findBestUsers(2),
        ]);
    }
}
