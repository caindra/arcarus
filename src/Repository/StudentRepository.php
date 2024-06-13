<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Student $student): void
    {
        $this->getEntityManager()->remove($student);
    }

    public function add(Student $student): void
    {
        $this->getEntityManager()->persist($student);
    }

    public function findAllWithPictures()
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.picture', 'pic')
            ->addSelect('pic')
            ->getQuery()
            ->getResult();
    }

    public function findByGroup(Group $group)
    {
        return $this->createQueryBuilder('s')
            ->where('s.group = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getResult();
    }
}
