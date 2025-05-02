<?php

namespace App\Repository;

use App\Entity\Option;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Option>
 *
 * @method Option|null find($id, $lockMode = null, $lockVersion = null)
 * @method Option|null findOneBy(array $criteria, array $orderBy = null)
 * @method Option[]    findAll()
 * @method Option[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }

    /**
     * Find options by question
     * Only returns non-deleted options by default
     */
    public function findByQuestion(Question $question, bool $includeDeleted = false): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->where('o.question = :question')
            ->setParameter('question', $question);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('o.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * Find correct options for a question
     * Only returns non-deleted options by default
     */
    public function findCorrectByQuestion(Question $question, bool $includeDeleted = false): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->where('o.question = :question')
            ->andWhere('o.is_correct = true')
            ->setParameter('question', $question);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('o.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all deleted options
     */
    public function findAllDeleted(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.deletedAt IS NOT NULL')
            ->orderBy('o.deletedAt', 'DESC')
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
