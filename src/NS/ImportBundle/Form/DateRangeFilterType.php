<?php

namespace NS\ImportBundle\Form;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType as BaseDateRangeFilter;

/**
 * Description of DateRangeFilterType
 *
 * @author gnat
 */
class DateRangeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //These were originally NS\ImportBundle\..\DateFilters
        $builder->add('left_date', BaseDateRangeFilter::class, $options['left_date_options']);
        $builder->add('right_date', BaseDateRangeFilter::class, $options['right_date_options']);

        $builder->setAttribute('filter_value_keys', array(
            'left_date'  => $options['left_date_options'],
            'right_date' => $options['right_date_options'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BaseDateRangeFilter::class;
    }
}
