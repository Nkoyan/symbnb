<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->createUsers($manager, 10);
        $ads = $this->createAds($manager, $users, 30);
        $images = $this->createImages($manager, $ads, 100);
        $bookings = $this->createBookings($manager, $ads, $users, 100);
        $comments = $this->createComments($manager, $ads, $users, 200);

        $manager->flush();
    }

    private function createAdmin(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setFirstName('Yoann')
            ->setLastName('Kergall')
            ->setEmail('yoann.kergall@gmail.com')
            ->setPassword($this->passwordEncoder->encodePassword($admin, '1111'))
            ->setPicture('https://avatars.io/twitter/yoann')
            ->setIntroduction($this->faker->sentence)
            ->setDescription('<p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        return $admin;
    }

    private function createUsers(ObjectManager $manager, int $amount)
    {
        $users = [];
        $users[] = $this->createAdmin($manager);
        $genres = ['male', 'female'];

        for ($i = 0; $i < $amount; ++$i) {
            $user = new User();
            $genre = $this->faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $picture .= ('male' === $genre ? 'men/' : 'women/').mt_rand(0, 99).'.jpg';

            $user->setFirstName($this->faker->firstName($genre))
                ->setLastName($this->faker->lastName)
                ->setEmail($this->faker->email)
                ->setIntroduction($this->faker->sentence)
                ->setDescription('<p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>')
                ->setPassword($this->passwordEncoder->encodePassword($user, '1111'))
                ->setCreatedAt($this->faker->dateTimeBetween('-2 years'))
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function createAds(ObjectManager $manager, array $users, int $amount)
    {
        $ads = [];

        for ($i = 0; $i < $amount; ++$i) {
            $ad = new Ad();

            $title = $this->faker->sentence();
            $coverImage = $this->faker->imageUrl(1000, 350);
            $introduction = $this->faker->paragraph(2);
            $content = '<p>'.implode('</p><p>', $this->faker->paragraphs(5)).'</p>';
            $createdAt = $this->faker->dateTimeBetween('-1 year');

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($users[mt_rand(0, \count($users) - 1)])
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($createdAt)
            ;

            $manager->persist($ad);
            $ads[] = $ad;
        }

        return $ads;
    }

    private function createImages(ObjectManager $manager, array $ads, int $amount)
    {
        $images = [];

        for ($i = 0; $i < $amount; ++$i) {
            $image = new Image();
            $image->setUrl($this->faker->imageUrl())
                ->setCaption($this->faker->sentence())
                ->setAd($ads[mt_rand(0, \count($ads) - 1)]);

            $manager->persist($image);
            $images[] = $image;
        }

        return $images;
    }

    private function createBookings(ObjectManager $manager, array $ads, array $users, int $amount)
    {
        $bookings = [];

        for ($i = 0; $i < $amount; ++$i) {
            $ad = $ads[mt_rand(0, \count($ads) - 1)];
            $booker = $users[mt_rand(0, \count($users) - 1)];
            $booking = new Booking();

            $createdAt = $this->faker->dateTimeBetween($ad->getCreatedAt());
            $startDate = (clone $createdAt)->modify('+'.mt_rand(3, 100).' days');
            $duration = mt_rand(3, 10);
            $endDate = (clone $startDate)->modify('+'.$duration.' days');
            $totalPrice = $ad->getPrice() * $duration;

            $booking->setAd($ad)
                ->setBooker($booker)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setCreatedAt($createdAt)
                ->setAmount($totalPrice)
                ->setComment($this->faker->paragraph());

            $bookings[] = $booking;
            $manager->persist($booking);
        }

        return $bookings;
    }

    private function createComments(ObjectManager $manager, array $ads, array $users, int $amount)
    {
        $comments = [];

        for ($i = 0; $i < $amount; ++$i) {
            $comment = new Comment();
            /** @var Ad $ad */
            $ad = $ads[mt_rand(0, \count($ads) - 1)];
            $author = $users[mt_rand(0, \count($users) - 1)];

            $comment->setContent($this->faker->paragraph)
                ->setRating(mt_rand(1, 5))
                ->setAuthor($author)
                ->setAd($ad)
                ->setCreatedAt($this->faker->dateTimeBetween($ad->getCreatedAt()));

            $comments[] = $comment;
            $manager->persist($comment);
        }

        return $comments;
    }
}
