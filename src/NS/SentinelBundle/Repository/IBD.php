<?php

namespace NS\SentinelBundle\Repository;

use NS\SentinelBundle\Repository\Common;
use Doctrine\ORM\Query;
use \NS\SentinelBundle\Exceptions\NonExistentCase;
use \Doctrine\ORM\NoResultException;

/**
 * Description of Common
 *
 * @author gnat
 */
class IBD extends Common
{
    public function getStats(\DateTime $start = null, \DateTime $end = null)
    {
        $results = array();
        $qb      = $this->_em
                   ->createQueryBuilder()
                   ->select('COUNT(m.id) theCount')
                   ->from($this->getClassName(),'m')
                   ->innerJoin('m.lab', 'sl')
                   ->where('m.cxrDone = :cxr')
                   ->setParameter('cxr', \NS\SentinelBundle\Form\Types\TripleChoice::YES);

        $results['cxr'] = $this->secure($qb)->getQuery()->getSingleScalarResult();

        $qb      = $this->_em
                   ->createQueryBuilder()
                   ->select('m.csfCollected, COUNT(m.csfCollected) theCount')
                   ->from($this->getClassName(),'m')
//                   ->innerJoin('m.lab', 'sl')
                   ->groupBy('m.csfCollected');
        
        $res     = $this->secure($qb)->getQuery()->getResult();

        foreach($res as $r)
        {
            if($r['csfCollected'])
                $results['csfCollected'] = $r['theCount'];
            else
                $results['csfNotCollected'] = $r['theCount'];
        }
        
        return $results;
    }

    public function getForAutoComplete($fields, array $value, $limit)
    {
        $alias = 'd';
        $qb    = $this->_em->createQueryBuilder()
                              ->select($alias)
                              ->from($this->getClassName(), $alias)
                              ->setMaxResults($limit);

        if(!empty($value) && $value['value'][0]=='*') {
            return $qb->getQuery();
        }
        
        if(!empty($value))
        {
            if(is_array($fields))
            {
                foreach ($fields as $f)
                {
                    $field = "$alias.$f";
                    $qb->addOrderBy($field)
                       ->orWhere("$field LIKE :param")->setParameter('param',$value['value'].'%');
                }
            }
            else
            {
                $field = "$alias.$fields";
                $qb->orderBy($field)->andWhere("$field LIKE :param")->setParameter('param',$value['value'].'%');
            }
        }

        return $qb->getQuery();        
    }

    public function getLatestQuery()
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('m,l')
                   ->leftJoin('m.lab', 'l')
                   ->orderBy('m.id','DESC');
        return $this->secure($qb);
    }

    public function getLatest($limit = 10)
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('m,l')
                   ->leftJoin('m.lab', 'l')
                   ->orderBy('m.id','DESC')
                   ->setMaxResults($limit);
        return $this->secure($qb)->getQuery()->getResult();
    }
    
    public function getByCountry()
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('COUNT(m) as numberOfCases, partial m.{id,admDate}, c')
                   ->innerJoin('m.country', 'c')
                   ->groupBy('m.country');

        return $this->secure($qb)->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function getByDiagnosis()
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('COUNT(m) as numberOfCases, partial m.{id,dischDx}')
                   ->groupBy('m.dischDx');

        return $this->secure($qb)->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function getBySite()
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('COUNT(m) as numberOfCases, partial m.{id,admDate}, s ')
                   ->innerJoin('m.site', 's')
                   ->groupBy('m.site');

        return $this->secure($qb)->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function get($id)
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('m,s,c,r,l')
                   ->innerJoin('m.site', 's')
                   ->innerJoin('s.country', 'c')
                   ->innerJoin('m.region', 'r')
                   ->leftJoin('m.lab','l')
                   ->where('m.id = :id')->setParameter('id',$id);
        try
        {
            return $this->secure($qb)->getQuery()->getSingleResult();
        }
        catch(NoResultException $e)
        {
            throw new NonExistentCase("This case does not exist!");
        }
    }

    public function search($id)
    {
        $qb = $this->createQueryBuilder('m')
                   ->where('m.id LIKE :id')->setParameter('id',"%$id%");

        return $this->secure($qb)->getQuery()->getResult();
    }

    public function checkExistence($id)
    {
        try 
        {
            $qb = $this->createQueryBuilder('m')
                       ->where('m.id = :id')
                       ->setParameter('id', $id);

            if($this->hasSecuredQuery())
                return $this->secure($qb)->getQuery()->getSingleResult();
            else
                return $qb->getQuery()->getSingleResult();
        }
        catch(NoResultException $e)
        {
            throw new NonExistentCase("This case does not exist!");
        }
    }

    public function find($id)
    {
        try
        {
            $qb = $this->createQueryBuilder('m')
                       ->addSelect('r,c,s,l')
                       ->leftJoin('m.region', 'r')
                       ->leftJoin('m.country', 'c')
                       ->leftJoin('m.site', 's')
                       ->leftJoin('m.lab','l')
                       ->andWhere('m.id = :id')
                       ->setParameter('id', $id);

            $qb = ($this->hasSecuredQuery()) ? $this->secure($qb): $qb;
            
            return $qb->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD,true)->getSingleResult();
        }
        catch(NoResultException $e)
        {
            throw new NonExistentCase("This case does not exist!");
        }
    }

    public function findOrCreate($caseId, $id = null)
    {
        if($id == null && $caseId == null)
            throw new \InvalidArgumentException("Id or Case must be provided");

        $qb = $this->createQueryBuilder('m')
                   ->select('m,s,c,r,l')
                   ->innerJoin('m.site', 's')
                   ->innerJoin('s.country', 'c')
                   ->innerJoin('m.region', 'r')
                   ->leftJoin('m.lab','l')
                   ->where('m.caseId = :caseId')
                   ->setParameter('caseId', $caseId);

        if($id)
            $qb->orWhere('m.id = :id')->setParameter('id', $id);

        try
        {
            return $this->secure($qb)->getQuery()->getSingleResult();
        }
        catch (NoResultException $ex)
        {
            $res = new \NS\SentinelBundle\Entity\IBD();
            $res->setCaseId($caseId);

            return $res;
        }
    }

    public function getFilterQueryBuilder($alias = 'm')
    {
        return $this->secure($this->_em
                    ->createQueryBuilder()
                    ->select("$alias,l")
                    ->from($this->getClassName(),$alias)
                    ->leftJoin("$alias.lab",'l')
                    ->orderBy('m.id','DESC'))
                    ;
    }

    public function findModified($modifiedSince = null)
    {
        $qb = $this->createQueryBuilder('m')
                    ->select('m,l')
                    ->leftJoin("m.lab",'l')
                    ->orderBy('m.id','DESC')
                ;

        if($modifiedSince)
            $qb->where('m.updatedAt >= :updatedAt')->setParameter ('updatedAt', $modifiedSince);

        return $qb->getQuery()->getResult();
    }

    public function getAnnualAgeDistribution(\DateTime $from, \DateTime $to)
    {
        $qb = $this->createQueryBuilder("m")
                   ->where('m.updatedAt BETWEEN :from AND :to')
                   ->setParameters(array(
                                    'from' => $from->format('Y-m-d'),
                                    'to'   => $to->format('Y-m-d'))
                                  )
                ;

        if(method_exists($this, 'secure'))
            $qb = $this->secure($qb);

        $r       = $qb->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();
        $results = array();

        foreach($r as $case)
        {
            if(!isset($results[$case->getYear()]))
                $results[$case->getYear()] = array(5=>0,11=>0,23=>0,59=>0, 'unknown'=>0);

            $results[$case->getYear()][$case->getAgeDistribution()]++;
        }

        return $results;
    }
}