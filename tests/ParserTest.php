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
    protected $jsonfile;
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
        $this->jsonfile = 'ParserTest.json';
        $this->parser = new Parser($this->rawJson, $this->delimiter);
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
        $raw = '{
            "database": {
            "host": "mysql",
            "port": 3306,
            "username": "test",
            "password": "test"
            }';

        $parser = new Parser($raw);
        $data = json_decode($raw, true);
        fwrite(STDOUT, __METHOD__ . "\n");
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

    public function testValidate(): void
    {
        $invalidJson = '{["Hello", 3.14, true, ]}';
        $expected = false;
        fwrite(STDOUT, __METHOD__ . "\n");
        $this->assertSame($expected, $this->parser->validate($invalidJson));

    }


    public function testLoad(): void
    {
        $invalidfilepath= 'hoax.json';
        $expected = null;
        fwrite(STDOUT, __METHOD__ . "\n");
        $this->assertEquals($expected, $this->parser->load($invalidfilepath));

    }


    public function testGet(): void
    {
        fwrite(STDOUT, __METHOD__ . "\n");
        $this->assertEquals('environment', $this->parser->get('production'), 'check if passing environment we get production');
        $this->assertArrayHasKey('setting', $this->parser->get('cache'), 'check if passing environment we get production');
        $this->assertEquals('mysql', $this->parser->get('database.host'), 'check if passing environment we get production');

    }
}


