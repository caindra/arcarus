<?php

namespace App\Repository;

use App\Entity\ClassPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassPicture>
 *
 * @method ClassPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassPicture[]    findAll()
 * @method ClassPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassPicture::class);
    }

//    /**
//     * @return ClassPicture[] Returns an array of ClassPicture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ClassPicture
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
