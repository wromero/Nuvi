<?php

namespace NS\SentinelBundle\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use NS\SentinelBundle\Report\Result\AbstractGeneralStatisticResult;
use NS\SentinelBundle\Report\Export\Exporter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractReporter
 * @package NS\SentinelBundle\Services
 */
class AbstractReporter
{
    /**
     * @var FilterBuilderUpdaterInterface
     */
    protected $filter;

    /**
     * @var ObjectManager
     */
    protected $entityMgr;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Exporter $exporter
     */
    protected $exporter;

    /**
     * @param FilterBuilderUpdaterInterface $filter
     * @param ObjectManager $entityMgr
     * @param RouterInterface $router
     * @param Exporter $exporter
     */
    public function __construct(FilterBuilderUpdaterInterface $filter, ObjectManager $entityMgr, RouterInterface $router, Exporter $exporter)
    {
        $this->filter = $filter;
        $this->entityMgr = $entityMgr;
        $this->router = $router;
        $this->exporter = $exporter;
    }

    /**
     * @param $sites
     * @param ArrayCollection $results
     * @param $resultClass
     */
    public function populateSites($sites, ArrayCollection &$results, $resultClass)
    {
        foreach ($sites as $values) {
            $resultObj = new $resultClass($values[0]->getSite());
            $resultObj->setTotalCases($values['totalCases']);

            $results->set($resultObj->getSite()->getCode(), $resultObj);
        }
    }

    /**
     *
     * @param ArrayCollection $results
     * @param array $counts
     * @param callback $function
     */
    public function processColumn(ArrayCollection &$results, $counts, $function)
    {
        foreach ($counts as $c) {
            $fpr = $results->get($c['code']);
            // this should always be true.
            if ($fpr && method_exists($fpr, $function)) {
                call_user_func(array($fpr, $function), $c['caseCount']);
            } else {
                throw new \RuntimeException(sprintf('method error %s', $function));
            }
        }
    }

    /**
     * @param $columns
     * @param $repo
     * @param $alias
     * @param $results
     * @param $form
     */
    public function processResult($columns, $repo, $alias, &$results, $form)
    {
        foreach ($columns as $func => $pf) {
            if (method_exists($repo, $func)) {
                $query = $repo->$func($alias, $results->getKeys());

                $res = $this->filter
                    ->addFilterConditions($form, $query, $alias)
                    ->getQuery()
                    ->getResult(Query::HYDRATE_SCALAR);

                $this->processColumn($results, $res, $pf);
            }
        }
    }

    /**
     * @param $columns
     * @param $repo
     * @param $alias
     * @param $results
     * @param $form
     */
    public function processSitePerformanceResult($columns, $repo, $alias, &$results, $form)
    {
        foreach ($columns as $func => $pf) {
            if (method_exists($repo, $func)) {
                $query = $repo->$func($alias, $results->getKeys());

                $res = $this->filter
                    ->addFilterConditions($form, $query, $alias)
                    ->getQuery()
                    ->getResult(Query::HYDRATE_SCALAR);

                $this->processSitePerformanceColumn($results, $res, $pf);
            }
        }
    }

    /**
     *
     * @param ArrayCollection $results
     * @param array $counts
     * @param callback $function
     */
    public function processSitePerformanceColumn(ArrayCollection &$results, $counts, $function)
    {
        foreach ($counts as $c) {
            $fpr = $results->get($c['code']);
            // this should always be true.
            if ($fpr && method_exists($fpr, $function)) {
                call_user_func(array($fpr, $function), $c);
            }
        }
    }

    /**
     * @param $countries
     * @param ArrayCollection $results
     * @param $resultClass
     */
    public function populateCountries($countries, ArrayCollection &$results, $resultClass)
    {
        foreach ($countries as $values) {
            $resultObj = new $resultClass;
            $resultObj->setCountry($values[0]->getCountry());
            $resultObj->setTotalCases($values['totalCases']);

            $results->set($resultObj->getCountry()->getCode(), $resultObj);
        }
    }

    /**
     * @param $columns
     * @param $repo
     * @param $alias
     * @param $results
     * @param $form
     */
    public function processLinkingResult($columns, $repo, $alias, &$results, $form)
    {
        foreach ($columns as $func => $pf) {
            if (method_exists($repo, $func)) {
                $query = $repo->$func($alias, $results->getKeys());

                try {
                    $res = $this->filter
                        ->addFilterConditions($form, $query, 'i')
                        ->getQuery()
                        ->getResult(Query::HYDRATE_SCALAR);
                } catch (\Exception $exception) {
                    throw new \RuntimeException('SQL Exception with func: '.$func, null, $exception);
                }

                $this->processColumn($results, $res, $pf);
            }
        }
    }

    /**
     * @param string $repoClass
     * @param AbstractGeneralStatisticResult $result
     * @param Request $request
     * @param FormInterface $form
     * @param $redirectRoute
     * @return array|RedirectResponse
     */
    public function retrieveStats($repoClass, AbstractGeneralStatisticResult $result, Request $request, FormInterface $form, $redirectRoute)
    {
        $alias = 'i';

        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($form->get('reset')->isClicked()) {
                return new RedirectResponse($this->router->generate($redirectRoute));
            }

            $repo = $this->entityMgr->getRepository($repoClass);
            $columns = array(
                'getGenderDistribution' => 'setGenderDistribution',
                'getAgeInMonthDistribution' => 'setAgeInMonthDistribution',
                'getLocationDistribution' => 'setLocationDistribution',
                'getDischargeOutcomeDistribution' => 'setDischargeOutcomeDistribution',
                'getMonthlyDistribution' => 'setMonthlyDistribution'
            );

            foreach ($columns as $repoFunction => $resultFunction) {
                $query = $repo->$repoFunction($alias);
                $this->filter->addFilterConditions($form, $query, $alias);
                $results = $query->getQuery()->getScalarResult();
                $result->$resultFunction($results);
            }

            return array('form' => $form->createView(), 'result' => $result);
        }

        return array('form' => $form->createView(), 'result' => $result);
    }
}
