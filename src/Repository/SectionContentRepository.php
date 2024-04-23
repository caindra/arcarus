<?php

namespace App\Repository;

use App\Entity\SectionContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SectionContent>
 *
 * @method SectionContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionContent[]    findAll()
 * @method SectionContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionContent::class);
    }

//    /**
//     * @return SectionContent[] Returns an array of SectionContent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SectionContent
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
