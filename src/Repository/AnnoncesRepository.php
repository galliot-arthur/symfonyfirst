<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    // /**
    //  * @return Annonces[] Returns an array of Annonces objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Make a search in the database
     *
     * @param string $word
     * @param string $category
     * @return Annonces
     */
    public function search($word = null, $category = null, int $page, int $limit)
    {
        $query = $this->createQueryBuilder('a');
        $query
            ->where('a.active = 1')
            ->orderBy('a.created_at');
        if ($word != null) {
            $query
                ->andWhere('a.title LIKE :word')
                ->setParameter('word', "%$word%");
        }
        if ($category != null) {
            $query->leftJoin('a.categories', 'c');
            $query
                ->andWhere('c.id = :id')
                //->andWhere('c.parent_id = :id')
                ->setParameter('id', $category);
        }
        $query
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }



    /**
     * Know if a favori already exists or not.
     *
     * @param int $annonce
     * @param int $user
     * @return boolean
     */
    public function isFavoriExists(int $annonce, int $user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare("SELECT *
                FROM annonces_users a
                WHERE a.annonces_id = :annonce
                AND a.users_id = :user");

        return $stmt->executeQuery([
            'annonce' => $annonce,
            'user' => $user,
        ])->fetch();
    }

    /**
     * Query annonces with pagination criteria
     *
     * @param integer $page Actual page
     * @param integer $limit Limit of annonces by page
     * @return Annonces
     */
    public function getPaginatedAnnonces(int $page, int $limit)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.active = 1')
            ->orderBy('a.created_at', 'DESC')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    public function countOnSearch($word = null, $categories = null)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.active = 1');
        if (!empty($word)) $query
            ->andWhere('a.title LIKE :word')
            ->setParameter('word', "%$word%");
        if ($categories) $query
            ->andWhere('a.categories = :categories')
            ->setParameter('categories', $categories);
        $result = $query->getQuery()->getResult();
        return count($result);
    }
}
