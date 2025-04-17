<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
            ->andWhere('e.iyedIdUser = :employeeId')
            ->setParameter('employeeId', $employeeId)
            ->getQuery()
            ->getResult();
    }
} 