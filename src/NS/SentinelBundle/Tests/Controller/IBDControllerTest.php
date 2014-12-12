<?php

namespace NS\SentinelBundle\Tests\Controller;

use \Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Description of IBDControllerTest
 *
 * @author gnat
 */
class IBDControllerTest extends WebTestCase
{
    const ID = 'CA-ALBCHLD-14-000001';

    public function testIbdEdit()
    {
        // add all your doctrine fixtures classes
        $classes = array(
            // classes implementing Doctrine\Common\DataFixtures\FixtureInterface
            'NS\SentinelBundle\DataFixtures\ORM\LoadRegionData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadReferenceLabsData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadUserData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadIBDCaseData',
        );
        $this->loadFixtures($classes);

        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/edit/' . self::ID);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testIbdIndex()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testIbdShow()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/show/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testIbdLab()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/lab/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testIbdRRL()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/rrl/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testIbdNL()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/nl/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testIbdOutcome()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/ibd/outcome/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    private function login()
    {
        $user = $this->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('NSSentinelBundle:User')
            ->findOneByEmail(array('email' => 'ca-full@noblet.ca'));

        $this->loginAs($user, 'main_app');
        $client = $this->makeClient();
        $client->followRedirects();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        return $client;
    }

}
