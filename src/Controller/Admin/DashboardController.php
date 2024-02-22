<?php

namespace App\Controller\Admin;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin_dashboard_index')]
    public function index(EntityManagerInterface $manager, StatsService $stats): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats->getsStats(),
            'bestAds' => $stats->getAdsStats('DESC'),
            'worstAds' => $stats->getAdsStats('ASC'),
        ]);
    }
}
