<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     */   
    public function findAllConteningNameKey(string $nameKey)
    {
        // automatically knows to select Users
        // the "u" is an alias you'll use in the rest of the query
        return $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :val')
            ->setParameter('val', '%' . $nameKey . '%')
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findAnnouncersStillVip($nowTime)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.validadmin = 1')
            ->andWhere('u.vip >= :now')
            ->setParameter('now', $nowTime)
            ->orderBy('u.vip', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return User[] Returns an array of User objects
     */
    public function findAnnouncersNotVip($nowTime)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.validadmin = 1')
            ->andWhere('u.vip < :now')
            ->setParameter('now', $nowTime)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
