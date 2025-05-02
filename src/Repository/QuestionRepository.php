<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Find questions by quiz
     * Only returns non-deleted questions by default
     */
    public function findByQuiz(Quiz $quiz, bool $includeDeleted = false): array
    {
        $queryBuilder = $this->createQueryBuilder('q')
            ->where('q.quiz = :quiz')
            ->setParameter('quiz', $quiz);

        if (!$includeDeleted) {
            $queryBuilder->andWhere('q.deletedAt IS NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * Find question with associated options
     * Only returns non-deleted questions by default
     */
    public function findWithOptions(int $id, bool $includeDeleted = false): ?Question
    {
        $queryBuilder = $this->createQueryBuilder('q')
            ->leftJoin('q.options', 'o')
            ->addSelect('o')
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
     * Find all deleted questions
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
