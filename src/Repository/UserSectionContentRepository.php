<?php

namespace App\Repository;

use App\Entity\UserSectionContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSectionContent>
 *
 * @method UserSectionContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSectionContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSectionContent[]    findAll()
 * @method UserSectionContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSectionContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSectionContent::class);
    }

//    /**
//     * @return UserSectionContent[] Returns an array of UserSectionContent objects
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

//    public function findOneBySomeField($value): ?UserSectionContent
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
