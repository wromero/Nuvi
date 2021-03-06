<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 19/05/16
 * Time: 4:13 PM
 */

namespace NS\SentinelBundle\Filter\Type;

use NS\AceBundle\Form\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZeroReportFilterType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, array('choices'=> array('NSSentinelBundle:IBD'=>'IBD','NSSentinelBundle:RotaVirus'=>'RotaVirus'), 'placeholder' => 'Please Select...'))
            ->add('from', DatePickerType::class)
            ->add('to', DatePickerType::class);
    }
}

