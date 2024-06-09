<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    public function add(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function findAllBySurnameName()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.surnames', 'ASC')
            ->addOrderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithPictures()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.picture', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();
    }

}
