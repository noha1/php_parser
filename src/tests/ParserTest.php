<?php

use PHPUnit\Framework\TestCase as FrameworkTestCase;
require_once "./src/parser.php";
use ReflectionClass;
/**
* Class ParserTest.
*
* @covers Parser
*/

final class ParserTest extends FrameworkTestCase
{

	private $parser;
	/** @var string **/
	private $rawJson;
	private $load;
	private $delimiter;


	protected function setUp(): void
	{
		parent::setUp();

		$this->rawJson = '{
                "environment": "production",
                "database": {
                  "host": "mysql",
                  "port": 3306,
                  "username": "divido",
                  "password": "divido"
                },
                "cache": {
                  "setting": {
                    "host": "redis",
                    "port": 6379
                  }
                }
              }';

		$this->delimiter = ".";
		$this->parser = new Parser($this->rawJson, false, $this->delimiter);
	}

	protected function tearDown(): void
	{
		parent::tearDown();

		unset($this->parser);
		unset($this->rawJson);
		unset($this->delimiter);
	}

	public function testClassConstructor(): void
	{
		fwrite(STDOUT, __METHOD__ . "\n");
		$this->assertArrayHasKey('host', $this->parser->get('database'));
		$this->assertSame('.', $this->delimiter);

	}

	public function testGetJsonArray(): void
	{
		fwrite(STDOUT, __METHOD__ . "\n");
		$expected = null;
		$property = (new ReflectionClass(Parser::class))
		->getProperty('jsonArray');
		$property->setValue($this->parser, $expected);
		// empty and invalid
		$this->assertSame($expected, $this->parser->getJsonArray(''), 'check an empty string');
		$this->assertSame($expected, $this->parser->getJsonArray('{,,,}', 'check invalid json'));
	}

	public function testValidate(): void
	{
		$invalidJson = '{["Hello", 3.14, true, ]}';
		$expected = false;
		fwrite(STDOUT, __METHOD__ . "\n");
		$this->assertSame($expected, $this->parser->validate($invalidJson));

	}

	public function testLoad(): void
	{
		$invalidfilepath = 'hoax.json';
		$expected = null;
		fwrite(STDOUT, __METHOD__ . "\n");
		$this->assertEquals($expected, $this->parser->load($invalidfilepath));

	}

	public function testGet(): void
	{
		fwrite(STDOUT, __METHOD__ . "\n");
		$this->assertEquals('production', $this->parser->get('environment'), 'check if passing environment we get production');
		$this->assertArrayHasKey('setting', $this->parser->get('cache'), 'check if passing environment we get production');
		$this->assertEquals('mysql', $this->parser->get('database.host'), 'check if passing environment we get production');

	}
}