<?php

namespace App\Repository;

use App\Entity\UserClassGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserClassGroup>
 *
 * @method UserClassGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserClassGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserClassGroup[]    findAll()
 * @method UserClassGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserClassGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserClassGroup::class);
    }

//    /**
//     * @return UserClassGroup[] Returns an array of UserClassGroup objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserClassGroup
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
