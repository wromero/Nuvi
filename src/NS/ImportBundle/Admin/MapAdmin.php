<?php

namespace NS\ImportBundle\Admin;

use NS\ImportBundle\Form\ClassType;
use \NS\ImportBundle\Services\MapBuilder;
use \Sonata\AdminBundle\Admin\Admin;
use \Sonata\AdminBundle\Datagrid\DatagridMapper;
use \Sonata\AdminBundle\Datagrid\ListMapper;
use \Sonata\AdminBundle\Form\FormMapper;
use \Sonata\AdminBundle\Route\RouteCollection;
use \Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * MapAdmin class
 */
class MapAdmin extends Admin
{
    private $mapBuilder;

    /**
     * @param MapBuilder $mapBuilder
     * @return \NS\ImportBundle\Admin\MapAdmin
     */
    public function setMapBuilder(MapBuilder $mapBuilder)
    {
        $this->mapBuilder = $mapBuilder;
        return $this;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('version')
            ->add('class', 'doctrine_orm_callback',
                array(
                    'field_type'=>'choice',
                    'field_options'=> array('choices'=>array('NS\\SentinelBundle\\Entity\\IBD'=>'IBD', 'NS\\SentinelBundle\\Entity\\RotaVirus'=>'RotaVirus', ), 'placeholder'=>' '),
                    'callback' => array($this, 'filterClassType')
                )
            )
        ;
    }

    /**
     * @param $queryBuilder
     * @param $alias
     * @param $field
     * @param $value
     * @return bool|void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function filterClassType($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return;
        }

        $queryBuilder
            ->andWhere(sprintf('%s.class = :class', $alias))
            ->setParameter('class', $value['value']);

        return true;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('selectName')
            ->add('description', null, array('label'=>'notes'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show'   => array(),
                    'edit'   => array(),
                    'delete' => array(),
                    'clone'  => array('template' => 'NSImportBundle:MapAdmin:list__action_clone.html.twig'),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isNew = ($this->getSubject()->getId() > 0);

        $formMapper
            ->add('name',           null, array('label_attr'=>array('class'=>'col-sm-2')))
            ->add('description',    null, array('label'=>'Notes', 'label_attr'=>array('class'=>'col-sm-2')))
            ->add('class',          ClassType::class, array('label_attr'=>array('class'=>'col-sm-2')))
            ->add('version',        null, array('required'=>true, 'label_attr'=>array('class'=>'col-sm-2')))
            ->add('headerRow',      IntegerType::class, array('label_attr'=>array('class'=>'col-sm-2')))
            ->add('caseLinker',     ChoiceType::class, array(
                'label_attr' => array('class'=>'col-sm-2'),
                'choices' => array('ns_import.standard_case_linker' => 'Case Id and Site Code', 'ns_import.reference_case_linker'=>'Case Id and Verify Country'),
                'placeholder' => 'Please Select...',
                'disabled'=>$isNew))
        ;

        if (!$isNew) {
            $formMapper
                ->add('labPreference', ChoiceType::class, array(
                    'label_attr' => array('class'=>'col-sm-2'),
                    'choices'=>array('referenceLab'=>'RRL', 'nationalLab'=>'NL')
                ))
                ->add('file', FileType::class, array(
                    'required' => false,
                    'label_attr' => array('class'=>'col-sm-2'),
                ));
        } else {
            $formMapper->add('columns', 'sonata_type_collection', array('error_bubbling'=>false, 'by_reference' => true, 'label_attr'=>array('class'=>'col-md-12 align-left')), array('edit'=>'inline', 'inline'=>'table', 'template'=>'NSImportBundle:edi_orm_one_to_many.html.twig'));
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('version')
            ->add('columns')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($map)
    {
        // have a file so build the columns dynamically
        if (!$map->getId() && $map->getFile()) {
            $metaData   = $this->modelManager->getMetadata($map->getClass());
            $this->mapBuilder->setMetaData($metaData);
            $this->mapBuilder->setSiteMetaData($this->modelManager->getMetadata($metaData->getAssociationTargetClass('siteLab')));
            $this->mapBuilder->setNlMetaData($this->modelManager->getMetadata($metaData->getAssociationTargetClass('nationalLab')));

            $this->mapBuilder->process($map);
        } elseif ($map->getColumns()) {
            foreach ($map->getColumns() as $a) {
                $a->setMap($map);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($map)
    {
        if ($map->getColumns()) {
            foreach ($map->getColumns() as $a) {
                $a->setMap($map);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter() . '/clone');
    }
}
