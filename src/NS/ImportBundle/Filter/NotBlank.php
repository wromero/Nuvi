<?php

namespace NS\ImportBundle\Filter;

use \Ddeboer\DataImport\ReporterInterface;

/**
 * Description of NotBlank
 *
 * @author gnat
 */
class NotBlank implements ReporterInterface
{
    public $fields;

    private $message;

    private $severity = ReporterInterface::ERROR;

    /**
     * @param string $fields The field(s) that will be checked to not be empty
     */
    public function __construct($fields)
    {
        $this->fields = ((is_array($fields)) ? $fields : array($fields));
    }

    /**
     * Filter input
     *
     * @param array $item Input
     *
     * @return boolean If false is returned, the workflow will skip the input
     */
    public function __invoke(array $item)
    {
        $this->message = null;

        foreach ($this->fields as $field) {
            if (empty($item[$field])) {
                $this->message = sprintf('Field \'%s\' is blank',$field);
                return false;
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function hasMessage()
    {
        return ($this->message === null);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }
}
