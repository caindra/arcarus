<?php

namespace App\Repository;

use App\Entity\Professor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Professor>
 *
 * @method Professor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Professor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Professor[]    findAll()
 * @method Professor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfessorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professor::class);
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Professor $professor): void
    {
        $this->getEntityManager()->remove($professor);
    }

    public function add(Professor $professor): void
    {
        $this->getEntityManager()->persist($professor);
    }
}
