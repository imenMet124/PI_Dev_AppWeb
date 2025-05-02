<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * Find formations ordered by creation date (newest first)
     * Only returns non-deleted formations by default
     */
    public function findAllOrderedByDate(bool $includeDeleted = false): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->orderBy('f.dateCreation', 'DESC');

        if (!$includeDeleted) {
            $queryBuilder->andWhere('f.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * Find formations with their associated quiz
     * Only returns non-deleted formations by default
     */
    public function findWithQuiz(int $id, bool $includeDeleted = false): ?Formation
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->leftJoin('f.quiz', 'q')
            ->addSelect('q')
            ->where('f.id = :id')
            ->setParameter('id', $id);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('f.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all deleted formations
     */
    public function findAllDeleted(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.deletedAt IS NOT NULL')
            ->orderBy('f.deletedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Override the default findAll to exclude soft-deleted entities
     */
    public function findAll(): array
    {
        return $this->findBy(['deletedAt' => null]);
    }

    /**
     * Override the default findBy to exclude soft-deleted entities
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        // Add deletedAt IS NULL to criteria if not explicitly set
        if (!isset($criteria['deletedAt'])) {
            $criteria['deletedAt'] = null;
        }

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}
