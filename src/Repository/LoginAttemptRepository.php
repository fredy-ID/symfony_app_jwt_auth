<?php

namespace App\Repository;

use App\Entity\LoginAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LoginAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginAttempt[]    findAll()
 * @method LoginAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginAttemptRepository extends ServiceEntityRepository
{

    const DELAY_IN_MINUTES = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    public function countRecentLoginAttempts(string $username): int
    {
        $timeAgo = new \DateTimeImmutable(sprintf('-%d minutes', self::DELAY_IN_MINUTES));

        return $this->createQueryBuilder('la')
            ->select('COUNT(la)')
            ->where('la.date >= :date')
            ->andWhere('la.useremail = :username')
            ->getQuery()
            ->setParameters([
                'date' => $timeAgo,
                'username' => $username,
            ])
            ->getSingleScalarResult()
        ;
    }

    // /**
    //  * @return LoginAttempt[] Returns an array of LoginAttempt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoginAttempt
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
