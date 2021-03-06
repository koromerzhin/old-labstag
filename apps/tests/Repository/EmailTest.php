<?php

namespace Labstag\Tests\Repository;

use Doctrine\ORM\QueryBuilder;
use Labstag\Entity\Email;
use Labstag\Entity\User;
use Labstag\Lib\RepositoryTestLib;
use Labstag\Repository\EmailRepository;
use Labstag\Repository\UserRepository;

/**
 * @internal
 * @coversNothing
 */
class EmailTest extends RepositoryTestLib
{

    /**
     * @var EmailRepository
     */
    private $repository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        /** @var EmailRepository $repository */
        $repository       = $this->entityManager->getRepository(
            Email::class
        );
        $this->repository = $repository;
        /** @var UserRepository $userRepository */
        $userRepository       = $this->entityManager->getRepository(
            User::class
        );
        $this->userRepository = $userRepository;
    }

    public function testFindAll(): void
    {
        $all = $this->repository->findAll();
        $this->assertTrue(is_array($all));
    }

    public function testfindOneRandom(): void
    {
        $all = $this->repository->findAll();
        if (0 != count($all)) {
            $random = $this->repository->findOneRandom();
            $this->assertSame(get_class($random), Email::class);

            return;
        }

        $this->assertTrue(true);
    }

    public function testfindEmailByUser(): void
    {
        /** @var null $empty */
        $empty = $this->repository->findEmailByUser(null);
        $this->AssertNull($empty);
        $user = $this->userRepository->findOneRandom();
        if ($user instanceof User) {
            /** @var QueryBuilder $emails */
            $emails = $this->repository->findEmailByUser($user);
            $this->assertSame(get_class($emails), QueryBuilder::class);

            return;
        }

        $this->assertTrue(true);
    }
}
