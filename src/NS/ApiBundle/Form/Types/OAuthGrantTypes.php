<?php

namespace NS\ApiBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OAuth2\OAuth2;

/**
 * Description of OAuthGrantTypes
 *
 */
class OAuthGrantTypes extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                                    'multiple' => true,
                                    'choices'  => array(
                                                    OAuth2::GRANT_TYPE_AUTH_CODE          => 'authorization_code',
                                                    OAuth2::GRANT_TYPE_IMPLICIT           => 'token',
                                                    OAuth2::GRANT_TYPE_USER_CREDENTIALS   => 'password',
                                                    OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS => 'client_credentials',
                                                    OAuth2::GRANT_TYPE_REFRESH_TOKEN      => 'refresh_token',
                                                    OAuth2::GRANT_TYPE_EXTENSIONS         => 'extensions',
                                                        )
                                    )
                                );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
