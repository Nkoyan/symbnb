<?php

namespace App\Controller\Admin;

use App\Service\StatsService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard_index")
     */
    public function index(ObjectManager $manager, StatsService $stats)
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats->getsStats(),
            'bestAds' => $stats->getAdsStats('DESC'),
            'worstAds' => $stats->getAdsStats('ASC'),
        ]);
    }
}
