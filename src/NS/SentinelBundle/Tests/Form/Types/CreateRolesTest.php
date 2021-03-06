<?php

namespace NS\SentinelBundle\Tests\Form\Types;

use NS\SentinelBundle\Form\Types\CaseCreationType;
use \Symfony\Component\Form\Test\TypeTestCase;

/**
 * Description of CreateRolesTest
 *
 * @author gnat
 */
class CreateRolesTest extends TypeTestCase
{
    const CREATE = 'ROLE_CAN_CREATE';

    const CREATE_CASE = 'ROLE_CAN_CREATE_CASE';

    const CREATE_LAB = 'ROLE_CAN_CREATE_LAB';

    const CREATE_RRL = 'ROLE_CAN_CREATE_RRL_LAB';

    const CREATE_NL = 'ROLE_CAN_CREATE_NL_LAB';

    /**
     * @dataProvider roleProvider
     * @param $count
     * @param $data
     */
    public function testByRoles($count, $data)
    {
        $authChecker = $this->getAuthorizationChecker($data);
        $createRole  = new CaseCreationType();
        $createRole->setAuthChecker($authChecker);
        $form        = $this->factory->create($createRole);
        $this->assertEquals('case_creation', $form->getName());
        $choices     = $form->getConfig()->getOption('choices');
        $this->assertCount($count, $choices);
    }

    public function roleProvider()
    {
        return array(
            array('count' => 2, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => true),
                    2 => array('param' => self::CREATE_LAB, 'ret' => true),
                    3 => array('param' => self::CREATE_RRL, 'ret' => false),
                    4 => array('param' => self::CREATE_NL, 'ret' => false),
                )),
            array('count' => 2, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => true),
                    2 => array('param' => self::CREATE_LAB, 'ret' => false),
                    3 => array('param' => self::CREATE_RRL, 'ret' => true),
                    4 => array('param' => self::CREATE_NL, 'ret' => false),
                )),
            array('count' => 2, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => true),
                    2 => array('param' => self::CREATE_LAB, 'ret' => false),
                    3 => array('param' => self::CREATE_RRL, 'ret' => false),
                    4 => array('param' => self::CREATE_NL, 'ret' => true),
                )),
            array('count' => 4, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => true),
                    2 => array('param' => self::CREATE_LAB, 'ret' => true),
                    3 => array('param' => self::CREATE_RRL, 'ret' => true),
                    4 => array('param' => self::CREATE_NL, 'ret' => true),
                )),
            array('count' => 2, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => false),
                    2 => array('param' => self::CREATE_LAB, 'ret' => false),
                    3 => array('param' => self::CREATE_RRL, 'ret' => true),
                    4 => array('param' => self::CREATE_NL, 'ret' => true),
                )),
            array('count' => 2, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => false),
                    2 => array('param' => self::CREATE_LAB, 'ret' => true),
                    3 => array('param' => self::CREATE_RRL, 'ret' => true),
                    4 => array('param' => self::CREATE_NL, 'ret' => false),
                )),
            array('count' => 3, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => true),
                    1 => array('param' => self::CREATE_CASE, 'ret' => false),
                    2 => array('param' => self::CREATE_LAB, 'ret' => true),
                    3 => array('param' => self::CREATE_RRL, 'ret' => true),
                    4 => array('param' => self::CREATE_NL, 'ret' => true),
                )),
            array('count' => 0, 'data'  => array(
                    0 => array('param' => self::CREATE, 'ret' => false),
                )),
        );
    }

    /**
     * @param $role
     * @param $route
     *
     * @dataProvider getRoutes
     */
    public function testGetRoute($role, $route)
    {
        $form = new CaseCreationType($role);
        $baseRoute = 'base';
        $this->assertEquals(sprintf('%s%s',$baseRoute,$route),$form->getRoute($baseRoute));
    }

    public function getRoutes()
    {
        return array(
            array(CaseCreationType::BASE,'Edit'),
            array(CaseCreationType::SITE,'LabEdit'),
            array(CaseCreationType::NL,'NLEdit'),
            array(CaseCreationType::RRL,'RRLEdit'),
            array(null,'Index'),
        );
    }

    public function getAuthorizationChecker(array $calls = array())
    {
        $authChecker = $this->getMockBuilder('\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($calls as $index => $call) {
            $authChecker->expects($this->at($index))
                ->method('isGranted')
                ->with($call['param'])
                ->willReturn($call['ret']);
        }

        return $authChecker;
    }
}
