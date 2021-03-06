<?php

namespace NS\SentinelBundle\Twig;

use NS\SentinelBundle\Entity\BaseCase;

/**
 * Description of CaseStatus
 *
 * @author gnat
 */
class CaseStatus extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        $isSafe = array('is_safe' => array('html'));

        return array(
            new \Twig_SimpleFunction('case_label', array($this, 'getLabel'), $isSafe),
            new \Twig_SimpleFunction('case_lab_label', array($this, 'getLabLabel'), $isSafe),
            new \Twig_SimpleFunction('case_rrl_label', array($this, 'getRRLLabel'), $isSafe),
            new \Twig_SimpleFunction('case_nl_label', array($this, 'getNLLabel'), $isSafe),
        );
    }

    /**
     * @param BaseCase $obj
     * @param $message
     * @return null|string
     */
    public function getNLLabel(BaseCase $obj, $message)
    {
        if ($obj->getSentToNationalLab() || $obj->hasNationalLab()) {
            if ($obj->getSentToNationalLab() && $obj->hasNationalLab()) {
                $class = ($obj->getNationalLab()->isComplete()) ? 'label-success fa fa-check' : 'label-warning fa fa-warning-sign';
            } else {
                $class = 'label-danger fa fa-exclamation-sign';
            }

            return '<span class="label label-sm ' . $class . '">' . $message . '</span>';
        }

        return null;
    }

    /**
     * @param BaseCase $obj
     * @param $message
     * @return null|string
     */
    public function getRRLLabel(BaseCase $obj, $message)
    {
        if ($obj->getSentToReferenceLab() || $obj->hasReferenceLab()) {
            if ($obj->getSentToReferenceLab() && $obj->hasReferenceLab()) {
                $class = ($obj->getReferenceLab()->isComplete()) ? 'label-success fa fa-check' : 'label-warning fa fa-warning-sign';
            } else {
                $class = 'label-danger fa fa-exclamation-sign';
            }

            return '<span class="label label-sm ' . $class . '">' . $message . '</span>';
        }

        return null;
    }

    /**
     * @param BaseCase $obj
     * @param $message
     * @return string
     */
    public function getLabLabel(BaseCase $obj, $message)
    {
        if ($obj->hasSiteLab()) {
            $class = $obj->getSiteLab()->isComplete() ? 'label-success fa fa-check' : 'label-warning fa fa-exclamation-sign';
        } else {
            $class = 'label-danger fa fa-exclamation-sign';
        }

        return '<span class="label label-sm ' . $class . '">' . $message . '</span>';
    }

    /**
     * @param BaseCase $obj
     * @param $message
     * @return string
     */
    public function getLabel(BaseCase $obj, $message)
    {
        $noError = true;

        if ($obj->hasReferenceLab() && !$obj->getSentToReferenceLab()) {
            $noError = false;
        }

        if (!$obj->hasReferenceLab() && $obj->getSentToReferenceLab()) {
            $noError = false;
        }

        if ($obj->hasNationalLab() && !$obj->getSentToNationalLab()) {
            $noError = false;
        }

        if (!$obj->hasNationalLab() && $obj->getSentToNationalLab()) {
            $noError = false;
        }

        if ($noError) {
            $class = ($obj->isComplete()) ? 'label-success fa fa-check' : 'label-warning fa fa-exclamation-sign';
        } else {
            $class = 'label-danger fa fa-exclamation-sign';
        }

        return '<span class="label label-sm ' . $class . '">' . $message . '</span>';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_case_status';
    }
}
