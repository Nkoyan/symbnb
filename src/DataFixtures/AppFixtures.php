<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->createAdmin($manager);
        $users = $this->createUsers($manager, 10);
        $ads = $this->createAds($manager, $users, 30);
        $images = $this->createImages($manager, $ads, 100);

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
            ->setDescription('<p>' . join('</p><p>', $this->faker->paragraphs(3)) . '</p>')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
    }

    private function createUsers(ObjectManager $manager, int $amount)
    {
        $users = [];
        $genres = ['male', 'female'];

        for ($i = 0; $i < $amount; $i++) {
            $user = new User();
            $genre = $this->faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . mt_rand(0, 99) . '.jpg';

            $user->setFirstName($this->faker->firstName($genre))
                ->setLastName($this->faker->lastName)
                ->setEmail($this->faker->email)
                ->setIntroduction($this->faker->sentence)
                ->setDescription('<p>' . join('</p><p>', $this->faker->paragraphs(3)) . '</p>')
                ->setPassword($this->passwordEncoder->encodePassword($user, '1234'))
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function createAds(ObjectManager $manager, array $users, int $amount)
    {
        $ads = [];

        for ($i = 0; $i < $amount; $i++) {
            $ad = new Ad();

            $title = $this->faker->sentence();
            $coverImage = $this->faker->imageUrl(1000, 350);
            $introduction = $this->faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $this->faker->paragraphs(5)) . '</p>';

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($users[mt_rand(0, count($users) - 1)]);

            $manager->persist($ad);
            $ads[] = $ad;
        }

        return $ads;
    }

    private function createImages(ObjectManager $manager, array $ads, int $amount)
    {
        $images = [];

        for ($i = 0; $i < $amount; $i++) {
            $image = new Image();
            $image->setUrl($this->faker->imageUrl())
                ->setCaption($this->faker->sentence())
                ->setAd($ads[mt_rand(0, count($ads) - 1)]);

            $manager->persist($image);
            $images[] = $image;
        }

        return $images;
    }
}
