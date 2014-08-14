<?php

namespace NS\SentinelBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Description of Builder
 *
 * @author gnat
 */
class Builder 
{
    private $factory;
    protected $securityContext;

    /**
     * @param FactoryInterface $factory
     * @param SecurityContext $securityContext
     */
    public function __construct(FactoryInterface $factory, SecurityContext $securityContext)
    {
        $this->factory         = $factory;
        $this->securityContext = $securityContext;
    }
    
    public function sidebar()
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class','nav nav-list');
        if( $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY') )
        {
            if($this->securityContext->isGranted('ROLE_CAN_CREATE'))
            {
                $d = $menu->addChild('Data Entry', array('label'=> 'menu.data-entry'))->setExtra('icon','icon-edit');
                $d->addChild('Meningitis',array('label'=>'menu.meningitis','route'=>'ibdIndex'));
                $d->addChild('Rotavirus', array('route'=>'rotavirusIndex'))->setExtra('translation_domain', 'NSSentinelBundle');
            }

            $reports = $menu->addChild('Reports', array('label'=> 'menu.data-reports','route'=>'reportDashboard'))->setExtra('icon','icon-dashboard');
            $reports->addChild('Age Distribution',array('label'=> 'menu.data-reports-age-distribution','route'=>'reportAnnualAgeDistribution'));
            $reports->addChild('Enrolment %',array('label'=> 'menu.data-reports-percent-enrolled','route'=>'reportPercentEnrolled'));
            $reports->addChild('# Per Month',array('label'=> 'menu.data-reports-number-per-month','route'=>'reportNumberPerMonth'));
            $reports->addChild('# Per Year Clinical',array('label'=> 'menu.data-reports-per-year-clinical','route'=>'reportNumberPerYearClinical'));
            $reports->addChild('Data Quality',array('label'=> 'menu.data-reports-data-quality','route'=>'reportDataQuality'));

            if($this->securityContext->isGranted('ROLE_API'))
            {
                $api = $menu->addChild('Api Resources',array('label'=>'Api Resources'))->setExtra('icon','icon-book');
                $api->addChild('Dashboard',array('label'=>'Dashboard','route'=>'ns_api_dashboard'));
                $api->addChild('Documentation',array('label'=>'Documentation','route'=>'nelmio_api_doc_index'));
            }

            if($this->securityContext->isGranted('ROLE_ADMIN'))
            {
                $admin = $menu->addChild('Admin', array('label'=> 'menu.data-admin'))->setExtra('icon','icon-desktop');
                $admin->addChild('Admin', array('label'=> 'menu.data-admin','route'=>'sonata_admin_dashboard'));
                $admin->addChild('Translation',array('label'=> 'menu.translation','route'=>'jms_translation_index'));
            }
        }

        return $menu;
    }
    
    public function user()
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav ace-nav');

        $p = $menu->addChild('Profile')->setExtra('icon', 'icon-profile');
        $p->setChildrenAttribute('class', 'user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close');

        $p->addChild('Settings')->setExtra('icon', 'icon-cog');
        $p->addChild(' ')->setAttribute('class', 'divider');
        $p->addChild('Logout',array('route' => 'logout'))->setExtra('icon','icon-off');
        
        return $menu;
    }
}
