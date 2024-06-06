<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Group $group): void
    {
        $this->getEntityManager()->remove($group);
    }

    public function add(Group $group): void
    {
        $this->getEntityManager()->persist($group);
    }

    public function getUsersInGroup(int $groupId): array
    {
        return $this->createQueryBuilder('g')
            ->select('u')
            ->leftJoin('g.students', 's')
            ->leftJoin('g.professors', 'p')
            ->leftJoin('p.groups', 'pg')
            ->leftJoin('s.group', 'sg')
            ->where('g.id = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findProfessorsByGroupId(int $groupId): array
    {
        return $this->createQueryBuilder('g')
            ->select('p.id', 'p.name', 'p.surnames')
            ->innerJoin('g.professors', 'p')
            ->where('g.id = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findStudentsByGroupId(int $groupId): array
    {
        return $this->createQueryBuilder('g')
            ->select('s.id', 's.name', 's.surnames')
            ->innerJoin('g.students', 's')
            ->where('g.id = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getArrayResult();
    }
}
