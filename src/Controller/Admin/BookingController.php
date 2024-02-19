<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_booking_index")
     */
    public function index(BookingRepository $bookingRepository, Request $request, Pagination $pagination)
    {
        $pagination = $pagination
            ->setEntityClass(Booking::class)
            ->setCurrentPage($request->query->get('page', 1))
            ->paginate();

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $pagination->getData(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°{$booking->getId()} a bien été modifée !"
            );

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            'La réservation a bien été supprimée !'
        );

        return $this->redirectToRoute('admin_booking_index');
    }
}
