<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Tache>
 *
 * @method Tache|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tache|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tache[]    findAll()
 * @method Tache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    public function save(Tache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Tache[] Returns an array of Tache objects
     */
    public function findByProjet($projetId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.projet = :val')
            ->setParameter('val', $projetId)
            ->orderBy('t.dateDebutTache', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTasksByEmployee($employeeId): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.affectations', 'a')
            ->leftJoin('a.employe', 'e')
            ->andWhere('e.id = :employeeId')
            ->setParameter('employeeId', $employeeId)
            ->getQuery()
            ->getResult();
    }

    // Get total number of tasks
    public function getTotalTasks(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id_tache)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Get total number of completed tasks
    public function getTotalCompletedTasks(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id_tache)')
            ->where('t.statut_tache = :completed')
            ->setParameter('completed', 'Completed')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Get completion rate per user (array of [user, completed, total])
    public function getCompletionRatePerUser(): array
    {
        $qb = $this->getEntityManager()->createQuery(
            'SELECT e.name as userName, COUNT(t.id_tache) as total, 
                SUM(CASE WHEN t.statut_tache = :completed THEN 1 ELSE 0 END) as completed
             FROM App\\Entity\\Tache t
             JOIN t.affectations a
             JOIN a.employe e
             GROUP BY e.id
             ORDER BY completed DESC'
        )->setParameter('completed', 'Completed');
        return $qb->getResult();
    }

    // Get user who completed the most tasks
    public function getTopCompleter(): ?array
    {
        $result = $this->getCompletionRatePerUser();
        return $result[0] ?? null;
    }

    // Get user who completed the least tasks (but has at least one task)
    public function getLeastCompleter(): ?array
    {
        $result = $this->getCompletionRatePerUser();
        if (empty($result)) return null;
        return $result[array_key_last($result)];
    }

    /**
     * Returns a QueryBuilder for searching tasks by a term (title or description)
     */
    public function getSearchQueryBuilder(?string $term): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');
        if ($term) {
            $qb->andWhere('t.titre_tache LIKE :term OR t.desc_tache LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('t.id_tache', 'DESC');
    }
}