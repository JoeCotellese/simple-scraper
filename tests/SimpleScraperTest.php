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
        $mock = new MockHandler ([
            new Response (200, ['Content-Type'=> 'text/html; charset=UTF-8'], ExampleBodies::$completeExample)
        ]);
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler'=>$handler]);
    }
    
    public function testCanBeCreated()
    {
        $obj = new SimpleScraper($this->client, 'https://www.example.com');
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
        $obj = new SimpleScraper ($client, 'https://www.example.com/asdffdssdffjs');
    }

    public function testGetAddData()
    {
        $obj = new SimpleScraper ($this->client, 'https://www.example.com/asdffdssdffjs');
        $data = $obj->getAllData();
        $this->assertArrayHasKey('twitter', $data);
        $this->assertArrayHasKey('ogp', $data);
        $this->assertArrayHasKey('meta', $data);
    }
    public function testGetMeta()
    {
        $obj = new SimpleScraper ($this->client, 'https://www.example.com/foo');
        $data = $obj->getTwitter();
        $this->assertArrayHasKey('card', $data);
        $this->assertSame($data['card'], 'summary_large_image');
        $this->assertSame($data['title'], 'Twitter title');
        $this->assertSame($data['site'], '@TwitterSite');
        $this->assertSame($data['image'], 'example_image.png');
        $this->assertSame($data['creator'], '@TwitterCreator');
    }

    public function testGetTitle()
    {
        $obj = new SimpleScraper ($this->client, 'https://www.example.com/foo');
        $title = $obj->getTitle();
        $this->assertSame($title, 'This is a test title');

    }
    
    public function testGetDescription()
    {
        $obj = new SimpleScraper ($this->client, 'https://www.example.com/foo');
        $desc = $obj->getDescription();
        $this->assertSame($desc, 'This is a test description');

    }

    public function testMissingContent()
    {
        $mock = new MockHandler ([
            new Response (200, ['Content-Type'=> 'text/html; charset=UTF-8'], ExampleBodies::$missingContentExample)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler'=>$handler]);
        $obj = new SimpleScraper($client, 'http://www.example.com/foo');
        $og = $obj->getOgp();
        $this->assertArrayNotHasKey('locale', $og);

    }
    
    public function testRealSite()
    {
        $client = new Client();
        $obj = new SimpleScraper ($client, 'https://blog.hubspot.com/marketing/free-stock-photo-websites');
        $data = $obj->getTitle();
        $this->assertSame('20 of the Best Free Stock Photo Sites to Use in 2018', $data);
    }
}