<?php

namespace App\Controller\Admin;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    #[Route(path: '/admin/ads', name: 'admin_ad_index')]
    public function index(Request $request, AdRepository $adRepository, Pagination $pagination)
    {
        $pagination = $pagination
            ->setEntityClass(Ad::class)
            ->setCurrentPage($request->query->get('page', 1))
            ->paginate();

        return $this->render('admin/ad/index.html.twig', [
            'ads' => $pagination->getData(),
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/admin/ads/{id}/edit', name: 'admin_ad_edit')]
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/ads/{id}/delete', name: 'admin_ad_delete')]
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        if ($ad->getBookings()->count() > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des
                 réservations !"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
            );
        }

        return $this->redirectToRoute('admin_ad_index');
    }
}
