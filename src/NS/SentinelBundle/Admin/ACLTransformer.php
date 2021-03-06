<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 01/04/16
 * Time: 12:42 PM
 */

namespace NS\SentinelBundle\Admin;


use NS\SecurityBundle\Entity\BaseACL;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ACLTransformer implements DataTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        //value should be an array of type, object_id
        if($value instanceof BaseACL && $value->getObjectId()) {
            $objectStrg = json_encode(array(array('id'=>$value->getObjectId(),'name'=>'Gnat')));
        } else {
            $objectStrg = json_encode(array());
        }

        return array('type'=>$value->getType(),'object_id'=>$objectStrg);
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        // TODO: Implement reverseTransform() method.
    }

}
