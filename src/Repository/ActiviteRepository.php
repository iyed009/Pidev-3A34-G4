<?php

namespace App\Repository;

use App\Entity\Activite;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activite>
 *
 * @method Activite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activite[]    findAll()
 * @method Activite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activite::class);
    }

//    /**
//     * @return Activite[] Returns an array of Activite objects
//     */
    public function findBySalle(Salle $salle)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.salle = :salle')
            ->setParameter('salle', $salle)
            ->getQuery()
            ->getResult();
    }
    public function findActiviteByNbrAbonnes()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a FROM App\Entity\Activite a ORDER BY a.nbrMax ASC'
        );

        return $query->getResult();
    }

    public function findActiviteByNbrAbonnesDESC()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a FROM App\Entity\activite a ORDER BY a.nbrMax DESC'
        );

        return $query->getResult();
    }

    public function findactiviteByName()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT a FROM App\Entity\activite a ORDER BY a.nom ASC'
        );
        return $query->getResult();
    }
    public function findEntitiesByString($str) {
        try {
            return $this->getEntityManager()
                ->createQuery('SELECT a
                FROM App\Entity\Activite a
                WHERE a.nom LIKE :str 
                OR a.coach LIKE :str'
                )
                ->setParameter('str', '%'.$str.'%')
                ->getResult();
        } catch (\Exception $e) {
            // Gérer l'erreur ici, par exemple, journaliser l'erreur ou renvoyer une réponse d'erreur
            return [];
        }
    }

//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Activite
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findActivitiesByUserId(int $userId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
             FROM App\Entity\Activite a
             JOIN a.reservation u
             WHERE u.id = :userId'
        )->setParameter('userId', $userId);

        return $query->getResult();
    }

}
