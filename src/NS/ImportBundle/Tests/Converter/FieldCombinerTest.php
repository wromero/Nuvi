<?php

namespace NS\ImportBundle\Tests\Converter;

use Ddeboer\DataImport\ReporterInterface;
use NS\ImportBundle\Converter\FieldCombinerConverter;

class FieldCombinerTest extends \PHPUnit_Framework_TestCase
{
    public function testConverterMissingSource()
    {
        $data = array();
        $converter = new FieldCombinerConverter('source', 'destination');
        $returned = $converter->__invoke($data);
        $this->assertEquals($data, $returned);
        $this->assertTrue($converter->hasMessage());
        $this->assertEquals('Unable to find source source field', $converter->getMessage());
        $this->assertEquals(ReporterInterface::ERROR, $converter->getSeverity());
    }

    public function testConverterMissingDestination()
    {
        $data = array('source'=>'data');
        $converter = new FieldCombinerConverter('source', 'destination');
        $returned = $converter->__invoke($data);
        $this->assertEquals($data, $returned);
        $this->assertTrue($converter->hasMessage());
        $this->assertEquals('Unable to find destination destination field', $converter->getMessage());
    }

    public function testConverterEmptySource()
    {
        $data = array('s'=>'','d'=>'data');
        $converter = new FieldCombinerConverter('s', 'd');
        $returned = $converter->__invoke($data);
        $this->assertEquals($data, $returned);
        $this->assertFalse($converter->hasMessage());
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider getNumberData
     */
    public function testConverterTestSourceInteger($data, $expected)
    {
        $converter = new FieldCombinerConverter('s', 'd');
        $returned = $converter->__invoke($data);
        $this->assertEquals($expected, $returned['d']);
        $this->assertFalse($converter->hasMessage());
    }

    public function getNumberData()
    {
        return array(
            array(array('s'=>'0.3250','d'=>'3421'),'3421.325'),
            array(array('s'=>'0.3250','d'=>'634523421'),'634523421.325'),
            array(array('s'=>'0.53014120','d'=>'3421'),'3421.53014120'),
            array(array('s'=>'42005','d'=>'0.375'),42005.375),
            array(array('d'=>'68323','s'=>'.266975458'),68323.266975458),
            array(array('d'=>'9752','s'=>'.2007671141'),9752.2007671141),
            array(array('d'=>'43542','s'=>'.5717100276'),43542.5717100276),
            array(array('d'=>'17178','s'=>'.106029306'),17178.106029306),
            array(array('d'=>'17568','s'=>'.4029610256'),17568.4029610256),
            array(array('d'=>'19011','s'=>'.1951842068'),19011.1951842068),
            array(array('d'=>'23048','s'=>'.6208075774'),23048.6208075774),
            array(array('d'=>'1378','s'=>'.903111922'),1378.903111922),
            array(array('d'=>'27309','s'=>'.5547441419'),27309.5547441419),
            array(array('d'=>'22776','s'=>'.196476063'),22776.196476063),
            array(array('d'=>'46456','s'=>'.0826363282'),46456.0826363282),
            array(array('d'=>'65334','s'=>'.3232810246'),65334.3232810246),
            array(array('d'=>'47752','s'=>'.3071314071'),47752.3071314071),
            array(array('d'=>'21903','s'=>'.400315471'),21903.400315471),
            array(array('d'=>'622','s'=>'.0324823543'),622.0324823543),
            array(array('d'=>'11793','s'=>'.1042418214'),11793.1042418214),
        );
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider getStringData
     */
    public function testConverterTestSourceString($data, $expected)
    {
        $converter = new FieldCombinerConverter('s', 'd');
        $returned = $converter->__invoke($data);
        $this->assertEquals($expected, $returned['d']);
        $this->assertFalse($converter->hasMessage());
    }

    public function getStringData()
    {
        return array(
            array(array('s'=>'world','d'=>'hello'),'hello world'),
        );
    }

    public function testConverterMisMatchedType()
    {
        $data= array('s'=>120,'d'=>'world');
        $converter = new FieldCombinerConverter('s', 'd');
        $returned = $converter->__invoke($data);
        $this->assertEquals($data, $returned);
        $this->assertTrue($converter->hasMessage());

        $this->assertEquals('Mismatched types source: integer != dest: string', $converter->getMessage());
    }

    public function testConverterNotStringOrNumber()
    {
        $data= array('s'=>new \stdClass(),'d'=>new \stdClass());
        $converter = new FieldCombinerConverter('s', 'd');
        $returned = $converter->__invoke($data);
        $this->assertEquals($data, $returned);
        $this->assertTrue($converter->hasMessage());

        $this->assertEquals('Expected string or number, got object instead', $converter->getMessage());
    }
}
