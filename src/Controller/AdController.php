<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * @Route("/ads/new", name="ads_create")
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !");

            return $this->redirectToRoute('ads_show', ['id' => $ad->getId(), 'slug' => $ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ads/{id}/{slug}/edit", name="ads_edit")
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {

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
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/ads/{id}/{slug}", name="ads_show")
     */
    public function show($id, $slug, AdRepository $repo)
    {
        $ad = $repo->findOneBy(['id' => $id]);

        if ($slug != $ad->getSlug()) {
            return $this->redirectToRoute('ads_show', ['id' => $id, 'slug' => $ad->getSlug()]);
        }

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

}
