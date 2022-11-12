# PHP PARSER

## Usage

## the Parser accepts raw json string or file.

## the loading file process is seemless.

## example:

## loading file, setting the load flag to true

$foo = new Parser('tests/ParserTest.json', True);

## or

$foo = new Parser('some raw json string');

## To query the json file you can check a certain node or if you know the path you can use dot notation.

### example:

$res = $foo->get('database.host');
var_dump($res);

## overider the configuration file is not a feature of the parser.

## This can be done from the configuration set up by setting the env. variable from the php cli.

## which is considered to the easiest and most secure way.

##

export MY_VENV_AR=value
php script.php

script.php

echo getenv('MY_VAR').PHP_EOL;
