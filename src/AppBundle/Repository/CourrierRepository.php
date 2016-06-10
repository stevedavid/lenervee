<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Courrier;
use \Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\Container;


class CourrierRepository extends EntityRepository
{
    const NOT_NULL = 'IS NOT NULL';

    public function findNext(Courrier $courrier, $isAdmin = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'C')
            ->where('c.id > :id')
            ->leftJoin('c.categorie', 'C')
            ->setParameter(':id', $courrier->getId())
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(1)
        ;

        if ($isAdmin === false) {
            $qb->andWhere('c.published = 1');
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneBySlugWithReactionsFiltered($slug, $status)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('r')
            ->leftJoin('c.reactions', 'r', Expr\Join::WITH, 'r.courrier = c.id AND (r.status = :status OR r.status IS NULL)')
            ->where('c.slug = :slug')
            ->setParameters([
                'slug' => $slug,
                'status' => $status,
            ])
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLast($limit, $date = null, $isAdmin = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.envoi', 'DESC')
            ->setMaxResults($limit)

        ;

        if ($date instanceof \DateTime) {
            $qb
                ->andWhere('c.envoi < :date')
                ->setParameter(':date', $date)
            ;
        } else {
            $qb
                ->andWhere('c.envoi < :date')
                ->setParameter(':date', new \DateTime());
        }

        if ($isAdmin === false) {
            $qb->andWhere('c.published = 1');
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPrevious(Courrier $courrier, $isAdmin = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'C')
            ->where('c.id < :id')
            ->leftJoin('c.categorie', 'C')
            ->setParameter(':id', $courrier->getId())
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
        ;

        if ($isAdmin === false) {
            $qb->andWhere('c.published = 1');
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findMostLiked($limit)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.likeCount', 'DESC')
            ->setMaxResults($limit)
            ->where('c.published = 1')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMostCommented($limit)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.reactions', 'r')
            ->select('c, COUNT(r.id) AS HIDDEN nbReactions')
            ->where('c.published = 1')
            ->orderBy('nbReactions', 'DESC')
            ->setMaxResults($limit)
            ->groupBy('c.id')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLikeBy(array $parameters = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.published = 1')
            ->leftJoin('c.categorie', 'C')
            ->orderBy('c.envoi', 'DESC')
        ;

        if (!empty($parameters)) {
            foreach ($parameters as $field => $value) {
                $qb
                    ->andWhere('c.' . $field . ' LIKE :value')
                    ->setParameter('value', '%' .  $value . '%')
                ;
            }
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByTag($idTag)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.tags', 't')
        ;

        $qb
            ->where('t.id = :id')
            ->setParameter(':id', $idTag)
        ;

        $qb
            ->orderBy('c.envoi', 'DESC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function count(array $parameters = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        foreach ($parameters as $field => $value) {

            if (is_bool($value)) {
                $qb
                    ->andWhere('c.' . $field . ' = :value')
                    ->setParameter(':value', $value)
                ;
            } elseif ($value == self::NOT_NULL) {
                $qb
                    ->andWhere($qb->expr()->isNotNull('c.' . $field))
                ;
            }

        }
        return $qb
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function findCourrierAvecReponse()
    {
        return $this->createQueryBuilder('c')
            ->where('c.reponse IS NOT NULL')
            ->orderBy('c.envoi', 'DESC')
            ->andWhere('c.published = 1')
            ->getQuery()
            ->getResult()
        ;
    }
}