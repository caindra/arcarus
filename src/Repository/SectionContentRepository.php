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

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(SectionContent $sectionContent): void
    {
        $this->getEntityManager()->remove($sectionContent);
    }

    public function add(SectionContent $sectionContent): void
    {
        $this->getEntityManager()->persist($sectionContent);
    }
}
