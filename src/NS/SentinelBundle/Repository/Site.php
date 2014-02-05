<?php

namespace NS\SentinelBundle\Repository;

use NS\SentinelBundle\Repository\Common as CommonRepository;

/**
 * Site
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Site extends CommonRepository
{
    public function getChain($ids)
    {
        $qb = $this->_em->createQueryBuilder()
                ->select('s,c,r')
                ->from('NSSentinelBundle:Site','s','s.id')
                ->innerJoin('s.country', 'c')
                ->innerJoin('c.region', 'r');

        if(is_array($ids))
            $qb->add('where', $qb->expr()->in('s.id', '?1'))->setParameter(1, $ids);
        else if(is_numeric($ids))
            $qb->where('s.id = :id')->setParameter('id',$ids);
        else
            throw new \InvalidArgumentException(sprintf("Must provide an array of ids or single integers. Received: %s",gettype($ids)));
        
        return $qb->getQuery()->getResult();
    }
}
