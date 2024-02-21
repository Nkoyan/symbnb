<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="date")
     */
    #[Assert\Date(message: 'Vous devez entrer une date valide')]
    #[Assert\GreaterThan('today', message: "La date d'arrivée doit être ultérieure à la date d'aujourd'hui !", groups: ['front'])]
    private $startDate;

    /**
     * @ORM\Column(type="date")
     */
    #[Assert\Date(message: 'Vous devez entrer une date valide')]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: "La date de départ doit etre supérieure à la date d'arrivée")]
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     *
     * @ORM\PreUpdate()
     */
    public function prePersist()
    {
        if (!$this->createdAt) {
            $this->setCreatedAt(new DateTime());
        }
        $this->setAmount($this->getDuration() * $this->getAd()->getPrice());
    }

    public function getDuration()
    {
        return $this->getStartDate()->diff($this->getEndDate())->days;
    }

    public function isBookableDates()
    {
        $notAvailableDays = $this->ad->getNotAvailableDays();
        $bookingDays = $this->getDays();

        $formatDay = fn(DateTime $day) => $day->format('Y-m-d');

        $days = array_map($formatDay, $bookingDays);
        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach ($days as $day) {
            if (false !== array_search($day, $notAvailable, true)) {
                return false;
            }
        }

        return true;
    }

    public function getDays()
    {
        $days = [];
        $start = $this->getStartDate()->getTimestamp();
        $end = $this->getEndDate()->getTimestamp();

        for ($i = $start; $i < $end; $i += 24 * 60 * 60) {
            $days[] = new DateTime("@$i");
        }

        return $days;
    }
}
