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

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(ClassPicture $classPicture): void
    {
        $this->getEntityManager()->remove($classPicture);
    }

    public function add(ClassPicture $classPicture): void
    {
        $this->getEntityManager()->persist($classPicture);
    }
}
