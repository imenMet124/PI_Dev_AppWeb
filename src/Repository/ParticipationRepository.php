<?php

namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\Participation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Participation>
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }
    public function findByEvenement(Evenement $evenement): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.evenement = :evenement')
        ->setParameter('evenement', $evenement)
        ->leftJoin('p.utilisateur', 'u') // pour accéder à l'utilisateur facilement dans le Twig
        ->addSelect('u')
        ->orderBy('p.dateParticipation', 'DESC')
        ->getQuery()
        ->getResult();
}


//    /**
//     * @return Participation[] Returns an array of Participation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Participation
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
