<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Courrier;

class TagRepository extends EntityRepository
{
    public function findMostPopular($limit)
    {
        return $this->createQueryBuilder('t')
            ->select('t.slug', 'COUNT(c.id) AS nbCourriers')
            ->leftJoin('t.courriers', 'c')
            ->setMaxResults($limit)
            ->groupBy('t.name')
            ->orderBy('nbCourriers', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
