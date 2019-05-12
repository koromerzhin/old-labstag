<?php

namespace Labstag\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Labstag\Entity\History;
use Labstag\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HistoryFixtures extends Fixture implements DependentFixtureInterface
{
    private const NUMBER = 10;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->userRepository->findAll();
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < self::NUMBER; ++$i) {
            $history = new History();
            $history->setName($faker->unique()->safeColorName);
            $enable = rand(0, 1);
            $history->setEnable($enable);
            $user = rand(0, 1);
            if ($user) {
                $tabIndex = array_rand($users);
                $history->setRefuser($users[$tabIndex]);
            }

            $end = rand(0, 1);
            $history->setEnd($end);

            $addImage = rand(0, 1);
            if (1 === $addImage) {
                $image   = $faker->unique()->imageUrl(1920, 1920);
                $content = file_get_contents($image);
                $tmpfile = tmpfile();
                $data    = stream_get_meta_data($tmpfile);
                file_put_contents($data['uri'], $content);
                $file = new UploadedFile(
                    $data['uri'],
                    'image.jpg',
                    filesize($data['uri']),
                    null,
                    true
                );

                $history->setImageFile($file);
            }

            $manager->persist($history);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FilesFixtures::class,
            UserFixtures::class,
        ];
    }
}