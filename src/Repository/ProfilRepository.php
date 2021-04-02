<?php

namespace App\Repository;

use App\Entity\Critere;
use App\Entity\Profil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Functions\DateDiffFunction;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profil[]    findAll()
 * @method Profil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profil::class);
    }

    public function calculAgeSelonDateDeNaissance($critereAgeRecherche) {

    $queryBuilder = $this->createQueryBuilder('p');

    $queryBuilder->select('p','YEAR(CURDATE()) - YEAR(date_naissance) AS age')
                 ->where('p.age = ?' );

    $query = $queryBuilder->getQuery();

    $result = $query->getResult();

    return $result;

        /*DATE_DIFF(CURRENT\_DATE(), p.date_naissance)/365*/


    }

    public function recupereCriteresCorrespondantsAuUser(Critere $critereDuUser) {

        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.sexe = :femme')
            ->andWhere('p.CodePostal LIKE :departement')
            ->andWhere('DATE_DIFF(CURRENT_DATE(), p.dateNaissance)/365 >= :ageMin' )
            ->setParameter('ageMin', $critereDuUser->getAgeRecherchesMin())
            ->andWhere('DATE_DIFF(CURRENT_DATE(), p.dateNaissance)/365 <= :ageMax' )
            ->setParameter('ageMax', $critereDuUser->getAgeRecherchesMax())
            ->setParameter('departement', $critereDuUser->getDepartementsRecherches() . '%')
            ->setParameter('femme', $critereDuUser->getSexesRecherches());

        $query = $queryBuilder->getQuery();

        return $query->execute();

    }

    // /**
    //  * @return Profil[] Returns an array of Profil objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Profil
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
