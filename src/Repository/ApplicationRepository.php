<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function countApplicationsPerMonth(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT 
                MONTH(submitted_at) AS month, 
                COUNT(id) AS count
            FROM application
            GROUP BY month
            ORDER BY month ASC
        ";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        $data = array_fill(1, 12, 0); // Initialize January to December with 0
        foreach ($result->fetchAllAssociative() as $row) {
            $data[(int)$row['month']] = (int)$row['count'];
        }

        return $data;
    }
}