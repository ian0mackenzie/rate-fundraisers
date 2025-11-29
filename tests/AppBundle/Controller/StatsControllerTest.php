<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatsControllerTest extends WebTestCase
{
    /**
     * Test that the stats/debug endpoint returns a successful response
     * without division by zero errors.
     */
    public function testDebugStatsDoesNotCrash(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/stats/debug');
        
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Debug stats - no errors here!', $data['message']);
    }
    
    /**
     * Test that the stats/trigger-error endpoint now returns a successful response
     * instead of throwing a division by zero error.
     */
    public function testTriggerErrorEndpointIsSafe(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/stats/trigger-error');
        
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Stats calculated safely', $data['message']);
    }
    
    /**
     * Test that the sentry-test page loads successfully.
     */
    public function testSentryTestPageLoads(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/sentry-test');
        
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Sentry Test Page', $response->getContent());
    }
}
