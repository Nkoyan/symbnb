<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ad_index")
     */
    public function index(AdRepository $adRepository)
    {
        $ads = $adRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * @Route("/ads/new", name="ad_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !");

            return $this->redirectToRoute('ads_show', ['id' => $ad->getId(), 'slug' => $ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ads/{id}/{slug}/edit", name="ad_edit")
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        if (!$ad->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier');
        }

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !");

            return $this->redirectToRoute('ads_show', ['id' => $ad->getId(), 'slug' => $ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
        ]);
    }

    /**
     * @Route("/ads/{id}/{slug}/delete", name="ad_delete")
     */
    public function delete(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        if (!$ad->isAuthor($this->getUser()) || !$this->isCsrfTokenValid('delete', $request->get('token'))) {
            throw $this->createAccessDeniedException();
        }

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !");

        return $this->redirectToRoute('user_my_account');
    }

    /**
     * @Route("/ads/{id<\d+>}/{slug?}", name="ad_show")
     */
    public function show($id, $slug, AdRepository $repo)
    {
        $ad = $repo->findOneBy(['id' => $id]);

        if (!$ad) {
            throw $this->createNotFoundException();
        }

        if ($slug !== $ad->getSlug()) {
            return $this->redirectToRoute('ads_show', ['id' => $id, 'slug' => $ad->getSlug()]);
        }

        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }
}
