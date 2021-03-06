<?php

namespace NS\SentinelBundle\Filter\Type;

use NS\AceBundle\Filter\Type\DateRangeFilterType;
use \NS\SecurityBundle\Role\ACLConverter;
use \Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Form\FormEvent;
use \Symfony\Component\Form\FormEvents;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of BaseReportFilterType
 *
 * @author gnat
 */
class BaseReportFilterType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var ACLConverter
     */
    private $converter;

    /**
     * BaseReportFilterType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authChecker
     * @param ACLConverter $converter
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authChecker, ACLConverter $converter)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authChecker  = $authChecker;
        $this->converter    = $converter;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adm_date', DateRangeFilterType::class, array('label' => 'report-filter-form.admitted-between', ))
            ->add('createdAt', DateRangeFilterType::class, array('label' => 'report-filter-form.created-between'));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form     = $event->getForm();
        $options  = $form->getConfig()->getOptions();
        $siteType = (isset($options['site_type']) && $options['site_type'] == 'advanced') ? 'NS\SentinelBundle\Filter\Type\SiteFilterType' : 'NS\SentinelBundle\Filter\Type\SiteType';

        if ($this->authChecker->isGranted('ROLE_REGION')) {
            $objectIds = $this->converter->getObjectIdsForRole($this->tokenStorage->getToken(), 'ROLE_REGION');
            if (count($objectIds) > 1) {
                $form->add('region', 'NS\SentinelBundle\Filter\Type\RegionType');
            }

            $form->add('country', 'NS\SentinelBundle\Filter\Type\CountryType', array('placeholder' => '', 'required' => false));
            $form->add('site', $siteType);
        } elseif ($this->authChecker->isGranted('ROLE_COUNTRY')) {
            $form->add('site', $siteType);
        } elseif ($this->authChecker->isGranted('ROLE_SITE')) {
            $token     = $this->tokenStorage->getToken();
            $objectIds = $this->converter->getObjectIdsForRole($token, 'ROLE_SITE');
            if (count($objectIds) > 1) {
                $form->add('site', $siteType);
            }
        }

        if ($options['include_filter']) {
            $form->add('filter', SubmitType::class, array(
                'label'=>'filter',
                'icon' => 'fa fa-search',
                'attr' => array('class' => 'btn btn-xs btn-success')));
        }

        if ($options['include_export']) {
            $form->add('export', SubmitType::class, array(
                'label' => 'export',
                'icon' => 'fa fa-cloud-download',
                'attr' => array('class' => 'btn btn-xs btn-info')));
        }

        if ($options['include_reset']) {
            $form->add('reset', SubmitType::class, array(
                'label' => 'reset',
                'icon' => 'fa fa-times-circle',
                'attr' => array('class' => 'btn btn-xs btn-danger')));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'include_filter' => true,
            'include_export' => true,
            'include_reset'  => true)
        );

        $resolver->setDefined(array('site_type'));
        $resolver->setAllowedValues('site_type', array('simple', 'advanced'));
        $resolver->setRequired('data_class');
    }
}
