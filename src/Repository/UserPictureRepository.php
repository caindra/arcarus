<?php

namespace App\Repository;

use App\Entity\UserPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPicture>
 *
 * @method UserPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPicture[]    findAll()
 * @method UserPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPicture::class);
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(UserPicture $userPicture): void
    {
        $this->getEntityManager()->remove($userPicture);
    }

    public function add(UserPicture $userPicture): void
    {
        $this->getEntityManager()->persist($userPicture);
    }
}
