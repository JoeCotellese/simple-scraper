<?php
declare (strict_types=1);
namespace Ramonztro\SimpleScraper;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;


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
        $obj = new SimpleScraper ($this->client, 'https://www.google.com/asdffdssdffjs');
    }
}