<?php

namespace NS\ApiBundle\Tests\Controller;

use NS\ApiBundle\Tests\WebTestCase;
use NS\UtilBundle\Form\Types\ArrayChoice;

/**
 * Description of RotaVirusController
 *
 * @author gnat
 */
class RotaVirusControllerTest extends WebTestCase
{
    const ID = 'CA-ALBCHLD-14-000001';

    public function testGetCase()
    {
        // add all your doctrine fixtures classes
        $classes = array(
            // classes implementing Doctrine\Common\DataFixtures\FixtureInterface
            'NS\SentinelBundle\DataFixtures\ORM\LoadUserData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadRegionData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadRotaVirusCaseData',
            'NS\ApiBundle\DataFixtures\ORM\LoadApiClientData',
        );

        $this->loadFixtures($classes);

        $route  = $this->getRoute();
        $user   = $this->getUser();
        $client = $this->createApiClient($user);
        $client->request('GET', $route);

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);

        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertArrayHasKey('Id', $decoded);
        $this->assertEquals('CA-ALBCHLD-14-000001', $decoded['Id']);
    }

    public function testPatchCase()
    {
        $user   = $this->getUser();
        $route  = $this->getRoute('nsApiRotaPatchCase');
        $client = $this->createApiClient($user);
        $client->request('PATCH', $route, array(), array(), array(), '{"rotavirus":{"lastName":"Fabien","gender":"2"}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $case = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NSSentinelBundle:RotaVirus')->find(self::ID);
        $this->assertEquals("Fabien", $case->getLastName(), "Change has occurred");
        $this->assertTrue($case->getGender()->equal(\NS\SentinelBundle\Form\Types\Gender::FEMALE));
    }

    public function testPostCase()
    {
        $user  = $this->getUser();
        $route = $this->getUrl('nsApiRotaPostCase');

        $client = $this->createApiClient($user);
        $client->request('POST', $route, array(), array(), array(), '{"create_rotavirus":{"caseId":"123","type":"1","site":"ALBCHLD"}}');

        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'), "We have a location header");

        $client->request('GET', $response->headers->get('Location'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('CaseId', $decoded);
        $this->assertArrayNotHasKey('Lab', $decoded);
        $this->assertEquals("123", $decoded['CaseId']);
    }

    public function testLabCase()
    {
        $user  = $this->getUser();
        $route = $this->getUrl('nsApiRotaPatchLab', array('objId' => self::ID));

        $client = $this->createApiClient($user);
        $client->request('PATCH', $route, array(), array(), array(), '{"rotavirus_lab":{"adequate":1,"elisaDone":1}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $client->request('GET', $response->headers->get('Location'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('Adequate', $decoded, print_r($decoded, true));
//        $this->assertEquals("ANewCaseId", $decoded['Adequate']);
    }

    public function testGetRRLCase()
    {
        $user  = $this->getUser();
        $client = $this->createApiClient($user);
        $client->request('GET', $this->getRoute('nsApiRotaGetRRL'));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

//        $this->assertArrayHasKey('Status', $decoded, print_r($decoded, true));
    }

    public function testPatchRRLCase()
    {
        $user   = $this->getUser();
        $route  = $this->getRoute('nsApiRotaPatchRRL');
        $client = $this->createApiClient($user);
        $client->request('PATCH', $route, array(), array(), array(), '{"rotavirus_referencelab":{"labId":"ANewCaseId"}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $client->request('GET', $this->getRoute('nsApiRotaGetRRL'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('LabId', $decoded, print_r(array_keys($decoded), true));
        $this->assertEquals("ANewCaseId", $decoded['LabId']);
    }

    public function testPutRRLCase()
    {
        $user  = $this->getUser();
        $route = $this->getRoute('nsApiRotaPutRRL');

        $client = $this->createApiClient($user);
        $client->request('PUT', $route, array(), array(), array(), '{"rotavirus_referencelab":{"labId":"ANewCaseId"}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $client->request('GET', $this->getRoute('nsApiRotaGetRRL'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('LabId', $decoded);
        $this->assertEquals("ANewCaseId", $decoded['LabId']);
    }

    public function testGetNLCase()
    {
        $user  = $this->getUser();
        $route = $this->getUrl('nsApiRotaGetNL', array('objId' => self::ID));

        $client = $this->createApiClient($user);
        $client->request('GET', $route);

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('Status', $decoded, print_r($decoded, true));
    }

    public function testPatchNLCase()
    {
        $user   = $this->getUser();
        $route  = $this->getRoute('nsApiRotaPatchNL');
        $client = $this->createApiClient($user);
        $client->request('PATCH', $route, array(), array(), array(), '{"rotavirus_nationallab":{"labId":"ANewCaseId"}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $client->request('GET', $this->getRoute('nsApiRotaGetNL'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('LabId', $decoded, print_r(array_keys($decoded), true));
        $this->assertEquals("ANewCaseId", $decoded['LabId']);
    }

    public function testPutNLCase()
    {
        $user   = $this->getUser();
        $route  = $this->getRoute('nsApiRotaPutNL');
        $client = $this->createApiClient($user);
        $client->request('PUT', $route, array(), array(), array(), '{"rotavirus_nationallab":{"labId":"ANewCaseId"}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $client->request('GET', $this->getRoute('nsApiRotaGetNL'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $decoded  = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('LabId', $decoded);
        $this->assertEquals("ANewCaseId", $decoded['LabId']);
    }

    public function testPutCase()
    {
        $user  = $this->getUser();
        $route = $this->getUrl('nsApiRotaPutCase', array('objId' => self::ID));

        $client = $this->createApiClient($user);
        $client->request('PUT', $route, array(), array(), array(), '{"rotavirus":{"lastName":"Fabien","caseId":"12"}}');

        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'), "We have a location header");

        $case = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NSSentinelBundle:RotaVirus')->find(self::ID);
        $this->assertEquals("Fabien", $case->getLastName(), "Change has occurred");
        $this->assertEquals(ArrayChoice::NO_SELECTION, $case->getGender()->getValue());
    }

    public function testPutCaseWithoutCaseId()
    {
        $user  = $this->getUser();
        $route = $this->getUrl('nsApiRotaPutCase', array('objId' => self::ID));

        $client = $this->createApiClient($user);
        $client->request('PUT', $route, array(), array(), array(), '{"rotavirus":{"lastName":"Fabien"');

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    private function getUser()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NSSentinelBundle:User')->findOneBy(array(
                'email' => 'ca-api@noblet.ca'));
    }

    private function getRoute($route = 'nsApiRotaGetCase', $id = null)
    {
        $objId = is_null($id) ? self::ID : $id;

        return $this->getUrl($route, array('objId' => $objId));
    }
}
