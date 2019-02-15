<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getAdsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getBookingsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getCommentsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getsStats()
    {
        return [
            'users' => $this->getUsersCount(),
            'ads' => $this->getAdsCount(),
            'bookings' => $this->getBookingsCount(),
            'comments' => $this->getCommentsCount(),
        ];
    }

    public function getAdsStats($order = 'DESC')
    {
        if ('ASC' !== $order && 'DESC' !== $order) {
            throw new \Exception("\$order doit être égal a 'ASC' ou 'DESC'");
        }

        $dql = /* @lang DQL */
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Ad a 
            JOIN a.comments c 
            JOIN a.author u
            GROUP BY a 
            ORDER BY note '.$order;

        return $this->manager->createQuery($dql)->setMaxResults(5)->getResult();
    }
}
