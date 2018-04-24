<?php
/*
+---------------------------------------------------------------------------+
| SimpleScraper                                                             |
| Copyright (c) 2013-2016, Ramon Kayo                                       |
+---------------------------------------------------------------------------+
| Author        : Ramon Kayo                                                |
| Email         : contato@ramonkayo.com                                     |
| License       : Distributed under the MIT License                         |
| Full license  : https://github.com/ramonztro/simple-scraper               |
+---------------------------------------------------------------------------+
| "Simplicity is the ultimate sophistication." - Leonardo Da Vinci          |
+---------------------------------------------------------------------------+
*/
namespace Ramonztro\SimpleScraper;

use \DOMDocument;
use \Exception;
use \InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class SimpleScraper {
	
	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var \GuzzleHttp\Client
	 */
	private $client;

	/**
	 * @var string
	 */
	private $contentType;

	/**
	 * @var array
	 */
	private $data;
	
	/**
	 * @var int
	 */
	private $httpCode;
	
	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $url;
	
	/**
	 * 
	 * @param \GuzzleHttp\Client $client A Guzzle client that will do the fetching
	 * @param string $url the URL we want to fetch
	 * @throws \InvalidArgumentException
	 */
	public function __construct(Client $client, $url) {
		$this->data = array(
			'ogp' => array(),
			'twitter' => array(),
			'meta' => array()
		);
		$this->client = $client;
		
		$urlPattern = '~^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$~iu';
		if (!is_string($url))
			throw new InvalidArgumentException("Argument 'url' is invalid (not a string).");
		if (!(preg_match($urlPattern, $url)))
			throw new InvalidArgumentException("Argument 'url' is invalid.");
		$this->url = $url;
		$this->load();
	}	

	private function load()
	{
		$response = $this->client->get ($this->url);
		$this->httpCode = $response->getStatusCode();

		if ($response->getStatusCode()>400){
			throw new ClientException();
		}
		$this->body = $response->getBody();
		$this->contentType = $response->getHeader('Content-Type');
		libxml_use_internal_errors(true);
		$dom = new DOMDocument(null, 'UTF-8');
		$dom->loadHTML($this->body);
		$this->parseTitleTag($dom);
		$this->parseMetaTags($dom);

	}

	private function parseTitleTag(DOMDocument $dom)
	{
		$list = $dom->getElementsByTagName("title");
		if ($list->length > 0) {
			$this->title = $list->item(0)->textContent;
		}
	}

	private function parseMetaTags(DOMDocument $dom)
	{
		$metaTags = $dom->getElementsByTagName('meta');

		for ($i=0; $i<$metaTags->length; $i++) {
			$attributes = $metaTags->item($i)->attributes;
			$attrArray = array();
			foreach ($attributes as $attr) $attrArray[$attr->nodeName] = $attr->nodeValue;
			
			if (
				array_key_exists('property', $attrArray) && 
				array_key_exists('content', $attrArray) &&
				preg_match('~og:([a-zA-Z:_]+)~', $attrArray['property'], $matches)
			) {
				$this->data['ogp'][$matches[1]] = $attrArray['content'];
			} else if (
				array_key_exists('name', $attrArray) &&
				array_key_exists('content', $attrArray) &&
				preg_match('~twitter:([a-zA-Z:_]+)~', $attrArray['name'], $matches)
			) {
				$this->data['twitter'][$matches[1]] = $attrArray['content'];
			} else if (
				array_key_exists('name', $attrArray) &&
				array_key_exists('content', $attrArray)
			) {
				$this->data['meta'][$attrArray['name']] = $attrArray['content'];
			}
		}
	}

	/**
	 *
	 * @return array 
	 */
	public function getAllData() {
		return $this->data;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->body;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 *
	 * @return string
	 */
	public function getHttpCode() {
		return $this->httpCode;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getMeta() {
		return $this->data['meta'];
	}
	
	/**
	 *
	 * @return array
	 */
	public function getOgp() {
		return $this->data['ogp'];
	}
	
	/**
	 *
	 * @return array
	 */
	public function getTwitter() {
		return $this->data['twitter'];
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->data['meta']['description'];
	}
}