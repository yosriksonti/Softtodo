<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedProjectException;
use Symfony\Component\Security\Core\Project\PasswordAuthenticatedProjectInterface;
use Symfony\Component\Security\Core\Project\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository 
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Project::class);
    }

    public function add(Project $project, bool $flush = false): void
    {
        $this->getEntityManager()->persist($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $project, bool $flush = false): void
    {
        $this->getEntityManager()->remove($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findByClause(string $title, string $status, string $filename): array
    {
        // Create a query builder
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        // Build a WHERE query
        $queryBuilder
            ->select('p')
            ->from('App\Entity\Project', 'p')
            ->where($queryBuilder->expr()->like('p.title', ':title'))
            ->andWhere($queryBuilder->expr()->like('p.status', ':status'))
            ->andWhere($queryBuilder->expr()->like('p.filename', ':filename'))
            ->setParameter(':title','%' . $title . '%')
            ->setParameter(':status','%' . $status . '%')
            ->setParameter(':filename','%' . $filename . '%');
        // Execute the query
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();
        return $result;
    }
}
