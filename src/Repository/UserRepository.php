<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findOneByEmail($getData)
{
    return $this->createQueryBuilder('u')
        ->where('u.email = :email')
        ->setParameter('email', $getData)
        ->getQuery()
        ->getOneOrNullResult(); // will return only one result or null 'getResult' will return a collection

}
public function findUsersCountPerDepartment()
{
    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id) as userCount, d.libelle')
        ->leftJoin('u.departement', 'd')
        ->groupBy('u.idDepartement')
        ->getQuery()
        ->getResult();
}

public function findUserCountPerRole()
{
    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id) as userCount, u.roles')
        ->groupBy('u.roles')
        ->getQuery()
        ->getResult();
}
public function findUserCountPerEnabledStatus()
{
    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id) as userCount, u.enabled')
        ->groupBy('u.enabled')
        ->getQuery()
        ->getResult();
}
public function isEmailEnabled(string $email): bool
{
    $user = $this->findOneBy(['email' => $email]);

    if (!$user) {
        // L'email n'existe pas
        return false;
    }

    return $user->isEnabled();
}

}
