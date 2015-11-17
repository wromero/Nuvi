<?php

namespace NS\ImportBundle\Exceptions;

use Ddeboer\DataImport\Exception;

class DuplicateCaseException extends \RuntimeException implements Exception
{
    /**
     * @inheritDoc
     */
    public function __construct($message, $code, \Exception $previous)
    {
        $args = $this->setDefaults($message);
        parent::__construct(sprint('Duplicate case record detected: Country "%s" Case id: "%s. Found %d cases"', $args['country'], $args['caseId'], $args['count']), $code, $previous);
    }

    /**
     * @param $message
     * @return array
     */
    private function setDefaults($message)
    {
        $default = array('country' => 'NOT SET', 'caseId' => 'NOT SET', 'count' => 'NOT SET');

        if (is_array($message)) {
            return array_merge($message, $default);
        }

        return $default;
    }
}