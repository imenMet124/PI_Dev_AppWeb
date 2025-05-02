<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 *
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * Find quiz with associated formation
     * Only returns non-deleted quizzes by default
     */
    public function findWithFormation(int $id, bool $includeDeleted = false): ?Quiz
    {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.formation', 'f')
            ->addSelect('f')
            ->where('q.id = :id')
            ->setParameter('id', $id);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('q.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find quiz with associated questions
     * Only returns non-deleted quizzes by default
     */
    public function findWithQuestions(int $id, bool $includeDeleted = false): ?Quiz
    {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.questions', 'qs')
            ->addSelect('qs')
            ->where('q.id = :id')
            ->setParameter('id', $id);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('q.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find quiz with associated formation, questions and options
     * Only returns non-deleted quizzes by default
     */
    public function findWithQuestionsAndOptions(int $id, bool $includeDeleted = false): ?Quiz
    {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.formation', 'f')
            ->leftJoin('q.questions', 'qs')
            ->leftJoin('qs.options', 'o')
            ->addSelect('f', 'qs', 'o')
            ->where('q.id = :id')
            ->setParameter('id', $id);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('q.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all deleted quizzes
     */
    public function findAllDeleted(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.deletedAt IS NOT NULL')
            ->orderBy('q.deletedAt', 'DESC')
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
