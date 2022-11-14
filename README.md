# PHP PARSER

### Intro

The parser is a utility to retrieve parts of the configuration by a dot-separated path; The Parser accepts raw json string or file : xml, yaml and json.

the loading file process is seemless by loading file, setting the load flag to true

Note:: To support the YAML files, you need to enable the yaml_parser by downloading the lib first
command:

` brew install libyaml`

### Usage

```
$foo = new Parser('tests/ParserTest.json', True);
```

or loading a valid JSON string

`$foo = new Parser('some raw json string');`

### To query the json file you can check a certain node or if you know the path you can use dot notation.

dot notation example:

```
$result = $foo->get('database.host');
print_r($result);
```

## Tests and Debugging

phpunit tests were used, to test the parser you can use the following line in your terminal
`phpunit --colors=always -v --debug src/tests/ParserTest.php`

Note: overiding the configuration file is not a feature of the parser.

This can be done from the configuration set up by setting the env. variable from the php cli.

which is considered to the easiest and most secure way.

```
export MY_VENV_AR=value
php script.php

script.php

echo getenv('MY_VAR').PHP_EOL;
```
