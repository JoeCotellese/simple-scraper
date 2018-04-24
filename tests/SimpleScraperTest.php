<?php
declare (strict_types=1);
namespace Ramonztro\SimpleScraper;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

use PHPUnit\Framework\TestCase;
require_once ('example_bodies.php');

final class SimpleScraperTest extends TestCase
{
    public function setUp()
    {
        $this->client =  new Client();
    }
    
    public function testCanBeCreated()
    {
        $obj = new SimpleScraper($this->client, 'https://www.google.com');
        $this->assertInstanceOf(SimpleScraper::class, $obj);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testNoUrl()
    {
        $obj = new SimpleScraper($this->client, '');

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUrl()
    {
        $obj = new SimpleScraper($this->client, 'foobar');

    }

    /**
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function test400Exception()
    {
        $mock = new MockHandler ([
            new Response (403)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler'=>$handler]);
        $obj = new SimpleScraper ($this->client, 'https://www.google.com/asdffdssdffjs');
    }

    public function testGoodData()
    {
        $example = new ExampleBodies();
        $mock = new MockHandler ([
            new Response (200, [], $example->example1)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler'=>$handler]);

        $obj = new SimpleScraper ($client, 'https://www.google.com/asdffdssdffjs');
        $data = $obj->getAllData();
        $this->assertArrayHasKey('twitter', $data);
        $this->assertArrayHasKey('ogp', $data);
        $this->assertArrayHasKey('meta', $data);
    }
}