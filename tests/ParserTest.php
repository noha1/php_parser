<?php

namespace Tests;

use Parser;
use ReflectionClass;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

/**
 * Class Test.
 *
 * @covers \Parser
 */
final class ParserTest extends FrameworkTestCase
{
    protected $parser;
    protected $rawJson;
    protected $delimiter;


    protected function setUp(): void
    {
        parent::setUp();
        fwrite(STDOUT, __METHOD__ . "\n");
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
        $this->delimiter = '.';
        $this->parser = new Parser($this->rawJson, $this->delimiter);
    }

 
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->parser);
        unset($this->rawJson);
        unset($this->delimiter);
    }

    public function testClassConstructor()
    {   
        $raw = '{
            "database": {
            "host": "mysql",
            "port": 3306,
            "username": "divido",
            "password": "divido"
            }';

        $parser = new Parser($raw , '.');
        $data = json_decode($raw, true);
        $this->assertArrayHasKey('environment', $data);
        $this->assertSame('.', $this->delimiter);

    }

    public function testGetJsonArray(): void
    {
        $expected = null;
        $property = (new ReflectionClass(Parser::class))
            ->getProperty('jsonArray');
        $property->setValue($this->parser, $expected);
        // empty and invalid 
        $this->assertSame($expected, $this->parser->getJsonArray(''));
        $this->assertSame($expected, $this->parser->getJsonArray('{,,,}'));
    }

    public function testJson_validate(): void
    {
        $invalidJson = '{["Hello", 3.14, true, ]}';
        $expected = false;
        $this->assertSame($expected, $this->parser->json_validate($invalidJson));

    }

    public function testGet(): void
    {
        $this->assertEquals('environment', $this->parser->get('production'), 'check if passing environment we get production');
        $this->assertArrayHasKey('setting', $this->parser->get('cache'), 'check if passing environment we get production');
        $this->assertArrayHasKey('mysql', $this->parser->get('database.host'), 'check if passing environment we get production');

    }
}
