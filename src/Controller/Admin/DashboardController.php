<?php

namespace App\Controller\Admin;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin_dashboard_index')]
    public function index(EntityManagerInterface $manager, StatsService $stats)
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats->getsStats(),
            'bestAds' => $stats->getAdsStats('DESC'),
            'worstAds' => $stats->getAdsStats('ASC'),
        ]);
    }
}
