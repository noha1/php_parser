<?php

/**
* PARSER - PHP json parser with dot notation
*
* @author  Noha Hassaan
* @link    https://github.com/
*
* It must be possible to specify multiple configuration files to be loaded, and have later files
* override settings in earlier ones.
*/

class Parser
{
	/** @var string **/
	protected $delimiter = ".";
	public $jsonArray;


	public function __construct(string $rawJson, $delimiter = ".") {

		$this->jsonArray = $this->getJsonArray($rawJson);
		$this->delimiter = $delimiter ?: ".";

	}


	public function getJsonArray(string $rawJson) {
		if ($this->json_validate($rawJson) === true) {
			return json_decode($rawJson, true);
		}
		print "invalid json file";
		return null;
	}


	public function json_validate(string $raw_json): bool {
		return (json_decode($raw_json, true) == NULL) ? false : true;

	}

	// used to look up/find node if deeply nested and without dot notation
	private function recursiveFind(array $haystack, $needle) {
		$iterator = new RecursiveArrayIterator($haystack);
		$recursive = new RecursiveIteratorIterator(
			$iterator,
			RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ($recursive as $key => $value) {

			if ($key != $needle) {

				$parent = $key;
			}
			if ($key === $needle) {

				yield [$parent,
					$needle,
					$value];
			}
		}
	}

	
	// extract the node value , using the dot delimiter
	private function getSegment(string $key, $default = null) {
        $array = $this->jsonArray;
		// check type
		if (!is_string($key)) {
			return $default;
		}

		// extract the node value , using the dot delimiter
		foreach (explode($this->delimiter, $key) as $segment) {
			// no mutation
			$array = $array[$segment];
		}
        // check if not found
		if (is_null($array)){
			// throw new Exception('path is not correct.');
			print ($key.", path not found!");
			return;
 
		} 
				
		return $array; 
	
	}

	// load external json files usage: ('config/db.json');
	public function load ($filename){
		$filecontent = (file_exists($filename))? json_decode(file_get_contents($filename), true) : null; 
		return $filecontent;
	}


    // convert uploaded file/s into jsonarrays
    public function merge($files = []){
        $data = []; 
        foreach ($files as $f) 
            {
                $data = array_merge($data, json_decode(file_get_contents($f), true));
             } 
        return json_encode($data);
    }


    public function get(string $key, $default = null){
        $array= $this->jsonArray; 
      
        //case  one  segment , simple node no dots
       if (strpos($key, $this->delimiter) === false)
        {   //top level key
            if($this->exists($array, $key)){
                return $this->jsonArray[$key] ?? $default;
            }
            else{
                 // not top level key, dbl check if deeply nested.
                 // used to find even wrong paths.
                $needle = $key;
				
                foreach ($this->recursiveFind($array = $this->jsonArray, $needle) as $value) {
                    $recursiveFindArray = array($value[0][$needle] => $value[2]);
					}
					return $recursiveFindArray;
                }
        }
        // option: nested and dots notation 
        else{
            $this->getSegment($key);
        }
      
    }


	/**
	* Check if a given key or keys exists
	* @param  array<TKey>|int|string  $keys
	*/
	private function exists($array, $key) {
		reset($array);
		return array_key_exists($key, $array);
	}

}


$json = '{
  "environment": "production",
  "database": {
    "host": "mysql",
    "port": 3306,
    "username": "divido",
    "password": "divido"
  },
  "cache": {
    "mum": {
      "host": "redis",
      "port": 6379
    }
  }
}';
// usage
$foo = new Parser($json);
$array = json_decode($json, true);
// $data = $foo->getSegment('mum.host');


// $output = $foo->recursiveFind($array, 'port');
// var_dump($output);

$res = $foo->get('cache');
var_dump($res);

?>