<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val',$text = $data->getText(); $value)
            ->getQuery()
        ->getOneOrNullResult()
    ;
    }

    public function findByCategory($categories)
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.category', 'c');
        foreach ($categories as $key => $category) {
            $qb->orWhere('c.name = :category' . $key)
               ->setParameter('category' . $key, $category);
        }
        return $qb->getQuery()
                  ->getResult();
    }

*/
    public function findBySearch(SearchData $data)
    {
        $text =
        $categories = $data->getCategories();
        $min = $data->getMin();
        $max = $data->getMax();
        $order = $data->getOrder();
        //dd($min);
        $qb =$this->createQueryBuilder('p');

//        if(!empty($text)) {
//            $qb->andWhere('p.name LIKE :text')
//               ->setParameter('text','%'.$text.'%');
//        }
        if($categories != null && !empty($categories)) {
            foreach ($categories as $key => $category) {
                $qb->orWhere('p.category = :category' . $key)
                    ->setParameter('category' . $key, $category->getId());
            }
        }
        if($min != null) {
            $qb->andWhere('p.price > :min')
                ->setParameter('min', $min);
        }
        if($max != null) {
            $qb->andWhere('p.price <= :max')
                ->setParameter('max', $max);
        }
        if($order == 0) {
            $qb->orderBy('p.name','DESC');
        } elseif ($order == 1) {
            $qb->orderBy('p.name','ASC');
        } elseif ($order == 2) {
            $qb->orderBy('p.price','ASC');
        } elseif ($order == 3) {
            $qb->orderBy('p.price', 'DESC');
        }

        //dd($qb->getQuery());

        $query = $qb->getQuery();
        return $query->execute();
    }
}
