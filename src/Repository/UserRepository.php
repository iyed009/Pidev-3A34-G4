<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByRoleSortedByCreationDate($role, $sortDirection = 'ASC')
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
            SELECT * FROM user 
            WHERE JSON_CONTAINS(roles, :role) = 1
            ORDER BY created_at ' . $sortDirection;
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['role' => '"' . $role . '"']);

        // Fetch and return results using fetchAllAssociative() for associative array results
        return $result->fetchAllAssociative();
    }

    public function findUsersByStringAndRoleAdmin($str)
    {
        $queryResult = $this->getEntityManager()
            ->createQuery(
                'SELECT u
                FROM App\Entity\User u
                WHERE u.email LIKE :str 
                OR u.nom LIKE :str
                OR u.prenom LIKE :str'
            )
            ->setParameter('str', '%' . $str . '%')
            ->getResult();

        // Filtrer en PHP
        $admins = array_filter($queryResult, function ($user) {
            return in_array('ROLE_ADMIN', $user->getRoles()); // Assurez-vous que getRoles() retourne un tableau des rôles de l'utilisateur
        });

        return $admins;
    }

    public function findUsersByStringAndRoleClient($str)
    {
        $queryResult = $this->getEntityManager()
            ->createQuery(
                'SELECT u
                FROM App\Entity\User u
                WHERE u.email LIKE :str 
                OR u.nom LIKE :str
                OR u.prenom LIKE :str'
            )
            ->setParameter('str', '%' . $str . '%')
            ->getResult();

        // Filtrer en PHP
        $clients = array_filter($queryResult, function ($user) {
            return in_array('ROLE_CLIENT', $user->getRoles());
        });

        return $clients;
    }

    public function countVerifiedUsersByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.roles LIKE :role')
            ->andWhere('u.isVerified = true')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNonVerifiedUsersByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.roles LIKE :role')
            ->andWhere('u.isVerified = false')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUsersByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getUserStatisticsForChart()
    {
        $roles = ['ROLE_CLIENT', 'ROLE_ADMIN'];
        $statistics = [];

        foreach ($roles as $role) {
            $verifiedCount = $this->countVerifiedUsersByRole($role);
            $nonVerifiedCount = $this->countNonVerifiedUsersByRole($role);

            $statistics[$role] = [
                'verified' => (int) $verifiedCount,
                'nonVerified' => (int) $nonVerifiedCount,
            ];
        }

        return $statistics;
    }
}
