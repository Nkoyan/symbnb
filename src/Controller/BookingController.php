<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/ads/{id}/{slug}/book', name: 'booking_new')]
    public function new(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() === $ad->getAuthor()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas réserver votre propre annonce');
        }

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setBooker($this->getUser());
            $booking->setAd($ad);

            // Si les dates ne sont pas disponibles, message d'erreur
            if (!$booking->isBookableDates()) {
                $this->addFlash('warning', 'Les dates que vous avez choisi ne peuvent être réservées : elles sont déja prises.');
            } else {
                // Sinon enregistrement et redirection
                $manager->persist($booking);
                $manager->flush();

                $authorUrl = $this->generateUrl('user_show', [
                    'id' => $ad->getAuthor()->getId(),
                    'slug' => $ad->getAuthor()->getSlug(),
                ]);

                $adUrl = $this->generateUrl('ads_show', [
                    'id' => $ad->getId(),
                    'slug' => $ad->getSlug(),
                ]);

                $this->addFlash('success', "Votre réservation auprès de
                <strong><a href=\"$authorUrl\">{$ad->getAuthor()->getFullName()}</a></strong>
                pour l'annonce
                <a href=\"$adUrl\">{$ad->getTitle()}</a>
                a bien été prise en compte !");

                return $this->redirectToRoute('booking_show', [
                    'id' => $booking->getId(),
                ]);
            }
        }

        return $this->render('booking/new.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/booking/{id}', name: 'booking_show')]
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager): Response
    {
        $userComment = $booking->getAd()->getCommentFromAuthor($this->getUser());

        if ($userComment) {
            return $this->render('booking/show.html.twig', [
                'booking' => $booking,
                'userComment' => $userComment,
            ]);
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setAd($booking->getAd());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été pris en compte !');

            return $this->render('booking/show.html.twig', [
                'booking' => $booking,
                'userComment' => $comment,
            ]);
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }
}
