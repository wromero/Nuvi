<?php

namespace NS\SentinelBundle\Form;

use Lunetics\LocaleBundle\Form\Extension\Type\LocaleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('plainPassword', RepeatedType::class,
                     array(
                         'type'            => 'password',
                         'invalid_message' => 'The password fields must match.',
                         'options'         => array('attr' => array('class' => 'password-field', 'autocomplete'=>'off')),
                         'required'        => false,
                         'first_options'   => array('label' => 'Password'),
                         'second_options'  => array('label' => 'Repeat Password'),
                         )
                 )
            ->add('language', LocaleType::class, array('label'=>'Preferred Language'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\SentinelBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ns_sentinelbundle_user';
    }
}
