<?php

namespace NS\SentinelBundle\Entity\Types;
use NS\UtilBundle\Entity\Types\ArrayChoice;

class BinaxResult extends ArrayChoice
{
    protected $convert_class = 'NS\SentinelBundle\Form\Types\BinaxResult';

    public function getName()
    {
        return 'BinaxResult';
    }   
}
