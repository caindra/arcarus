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

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(UserSectionContent $userSectionContent): void
    {
        $this->getEntityManager()->remove($userSectionContent);
    }

    public function add(UserSectionContent $userSectionContent): void
    {
        $this->getEntityManager()->persist($userSectionContent);
    }
}
