<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    // /**
    //  * @return Commande[] Returns an array of Commande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAllcommande():array
    {
        $entityManager = $this->getEntityManager();
        $dql = "SELECT 
            c.id,
            u.last_name as nom,
            u.first_name as prenom,
            u.address ,
            u.phone,
            u.email,
            p.titre as produit, 
            c.quantity,
            p.prix,
            p.prix*c.quantity as total,
            f.name as fournisseur,
            c.created_at, 
            c.number_order 
        FROM App\Entity\Commande c
        LEFT JOIN App\Entity\User u with u.id = c.User
        LEFT JOIN App\Entity\Produit p with p.id = c.produit
        LEFT JOIN App\Entity\Fournisseur f with f.id = p.fournisseur
        GROUP BY c.id";

        //$query = $entityManager->createQuery();

        return $entityManager->createQuery($dql)->getResult();
    }
}
