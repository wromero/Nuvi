<?php

namespace NS\ImportBundle\Tests\Form;

use \Doctrine\Common\Collections\ArrayCollection;
use \NS\ImportBundle\Form\ClassType;
use \NS\ImportBundle\Form\ImportSelectType;
use \Symfony\Bridge\Doctrine\Form\Type\EntityType;
use \Symfony\Component\Form\Extension\Core\Type\CollectionType;
use \Symfony\Component\Form\PreloadedExtension;
use \Symfony\Component\Form\Test\TypeTestCase;
use \Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Description of AllFormsTest
 *
 * @author gnat
 */
class AllFormsTest extends TypeTestCase
{

    public function testClassType()
    {
        $choices = array(
            'FullyQualifiedClassName'  => 'Friendly Class Name',
            'FullyQualifiedClassName1' => 'Friendly Class Name 1',
            'FullyQualifiedClassName2' => 'Friendly Class Name 2',
        );

        $formData = array('FullyQualifiedClassName1');

        $type = new ClassType($choices);
        $form = $this->factory->create($type);
        $form->submit($formData);

        $this->assertTrue($form->isValid());
//        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($formData, $form->getData());
//        $view     = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($formData) as $key)
//        {
//            $this->assertArrayHasKey($key, $children);
//        }
    }
}