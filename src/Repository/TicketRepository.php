<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

//    /**
//     * @return Ticket[] Returns an array of Ticket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ticket
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    public function findTicketsByUserId(string $userId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t
             FROM App\Entity\Ticket t
             JOIN t.users u
             WHERE u.id = :userId'
        )->setParameter('userId', $userId);

        return $query->getResult();
    }

    // Your custom method to decrement the number of tickets
    public function decrementTicket(Ticket $ticket): void
    {
        if ($ticket->getNbreTicket() > 0) {
            $ticket->setNbreTicket($ticket->getNbreTicket() - 1);
        } else {
            // Handle the case when there are no tickets left
            throw new \Exception('No tickets left to decrement');
        }
    }


    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t
            FROM App\Entity\Ticket t
            JOIN t.evenement e
            WHERE t.prix LIKE :str 
            OR t.type LIKE :str
            OR t.nbreTicket LIKE :str
            OR e.nom LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();
    }


    public function findTicketsByPrice()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t FROM App\Entity\Ticket t ORDER BY t.prix ASC'
        );

        return $query->getResult();
    }

    public function findTicketsByPriceDESC()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t FROM App\Entity\Ticket t ORDER BY t.prix DESC'
        );

        return $query->getResult();
    }
}
