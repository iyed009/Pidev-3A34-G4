<?php

namespace App\Repository;

use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Salle>
 *
 * @method Salle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salle[]    findAll()
 * @method Salle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }
    public function findSalleByNbrAbonnes()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Salle s ORDER BY s.nbrClient ASC'
        );

        return $query->getResult();
    }

    public function findSalleByNbrAbonnesDESC()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Salle s ORDER BY s.nbrClient DESC'
        );

        return $query->getResult();
    }

    public function findSallesByName()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Salle s ORDER BY s.nom ASC'
        );
        return $query->getResult();
    }

//    /**
//     * @return Salle[] Returns an array of Salle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Salle
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findEntitiesByString($str) {
        try {
            return $this->getEntityManager()
                ->createQuery(
                    'SELECT s
                FROM App\Entity\Salle s
                WHERE s.nom LIKE :str 
                OR s.addresse LIKE :str'
                )
                ->setParameter('str', '%'.$str.'%')
                ->getResult();
        } catch (\Exception $e) {
            // Gérer l'erreur ici, par exemple, journaliser l'erreur ou renvoyer une réponse d'erreur
            return [];
        }
    }
}
