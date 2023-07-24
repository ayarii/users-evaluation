<?php

namespace App\Repository;

use App\Entity\AffectationBadge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AffectationBadge>
 *
 * @method AffectationBadge|null find($id, $lockMode = null, $lockVersion = null)
 * @method AffectationBadge|null findOneBy(array $criteria, array $orderBy = null)
 * @method AffectationBadge[]    findAll()
 * @method AffectationBadge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffectationBadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AffectationBadge::class);
    }

    public function save(AffectationBadge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AffectationBadge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AffactationBadge[] Returns an array of AffectationBadge objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AffectationBadge
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findBadgesByUserId($userId)
    {
        return $this->createQueryBuilder('a')
            ->select('b.id', 'b.libelle', 'b.image')
            ->leftJoin('a.idbadge', 'b')
            ->andWhere('a.iduser = :iduser')
            ->setParameter('iduser', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findUsersByBadgeId($badgeId)
    {
        return $this->createQueryBuilder('a')
            ->select('u.id', 'u.nom','u.prenom' ,'u.image')
            ->leftJoin('a.iduser', 'u')
            ->andWhere('a.idbadge = :idbadge')
            ->setParameter('idbadge', $badgeId)
            ->getQuery()
            ->getResult();
    }

    
    public function CountByBadgeForSession(string $sessionLibelle)
    {
        $queryBuilder = $this->createQueryBuilder('ab')
            ->select('b.libelle AS badge_libelle, COUNT(u.id) AS count_users') // Assuming the property name is 'badges' for the ManyToMany relationship with Badge
            ->join('ab.iduser', 'u')
            ->join('ab.idbadge', 'b')
            ->join('u.idDepartement', 'd')
            ->join('d.idSession', 's')
            ->where('s.libelle = :sessionLibelle')
            ->setParameter('sessionLibelle', $sessionLibelle)
            ->groupBy('b.libelle');

        return $queryBuilder->getQuery()->getResult();
    }

}
