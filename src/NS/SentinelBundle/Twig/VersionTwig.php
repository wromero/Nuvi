<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 24/05/16
 * Time: 4:07 PM
 */

namespace NS\SentinelBundle\Twig;

use NS\SentinelBundle\Version;

class VersionTwig extends \Twig_Extension
{
    public function getGlobals()
    {
        return array(
            'current_version' => Version::VERSION,
        );
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'version';
    }

}
