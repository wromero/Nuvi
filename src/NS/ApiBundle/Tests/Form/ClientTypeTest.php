<?php

namespace NS\ApiBundle\Tests\Form;

use \NS\AceBundle\Form\TagType;
use \NS\ApiBundle\Form\ClientType;
use \NS\ApiBundle\Form\Types\OAuthGrantTypes;
use \OAuth2\OAuth2;
use \Symfony\Component\Form\PreloadedExtension;
use \Symfony\Component\Form\Test\TypeTestCase;

/**
 * Description of ClientTypeTest
 *
 * @author gnat
 */
class ClientTypeTest extends TypeTestCase
{
    public function testForm()
    {
        $formData = array(
            'name'              => 'ClientName',
            'redirectUris'      => 'https://localhost/,http://example.com/',
            'allowedGrantTypes' => array(OAuth2::GRANT_TYPE_AUTH_CODE, OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
                OAuth2::GRANT_TYPE_REFRESH_TOKEN)
        );

        $form     = $this->factory->create(ClientType::class);

        $form->submit($formData);
        $data = $form->getData();
        $this->assertInstanceOf('NS\ApiBundle\Entity\Client', $data);
        $this->assertEquals('ClientName', $data->getName());
        $this->assertEquals(array('https://localhost/', 'http://example.com/'), $data->getRedirectUris());
        $this->assertEquals(array(OAuth2::GRANT_TYPE_AUTH_CODE, OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
            OAuth2::GRANT_TYPE_REFRESH_TOKEN), $data->getAllowedGrantTypes());
    }

    public function getExtensions()
    {
        $oauthType = new OAuthGrantTypes();
        $tagType   = new TagType();
        return array(new PreloadedExtension(array($oauthType, $tagType), array()));
    }
}
