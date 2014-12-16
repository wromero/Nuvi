<?php

namespace NS\SentinelBundle\Form\Filters;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of Region
 *
 * @author gnat
 */
class Region extends BaseObject
{
    /**
     * @param ObjectManager $entityMgr
     * @param string $class
     */
    public function __construct(ObjectManager $entityMgr, $class = null)
    {
        $this->entityMgr = $entityMgr;
        $this->class     = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'region';
    }
}
