<?php
/**
* PARSER - PHP json parser with dot notation
*
* @author  Noha Hassaan
* @link    https://github.com/noha1
*
* It must be possible to specify multiple configuration files to be loaded, and have later files
* override settings in earlier ones.
* expected files are "json","xml", "yaml"
*/

class Parser
{
	/** @var string **/
	protected string $delimiter;
	public array $allowFileTypes;
	public $jsonArray;

	// load option specifies if it is passed string or a loaded file.

	public function __construct(string $rawJson, $load = false, $delimiter = ".") {
		$this->delimiter = $delimiter ?: ".";
		$this->allowFileTypes = array('json', 'xml' , 'yaml');
		$this->jsonArray = ($load === true) ? $this->load($rawJson) : $this->getJsonArray($rawJson);

	}


	public function getJsonArray(string $rawJson) {
		if ($this->validate($rawJson) === true) {
			return json_decode($rawJson, true);
		}
		print "invalid json file";
		return null;
	}


	public function validate(string $raw_json): bool {
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
		if (is_null($array)) {
			// throw new Exception('path is not correct.');
			print ($key.", path not found!");
			return;

		}

		return $array;

	}

	// load external json files
	public function load ($filename) {
		//check file type , converto to json and then decode it 
		$filext= pathinfo($filename, PATHINFO_EXTENSION);
		if(!file_exists($filename) or !in_array($filext, $this->allowFileTypes)){	
			print "Error encountered";
			return null;
		 } 
		
		if  ($filext ==='json'){
			return json_decode(file_get_contents($filename), true);
		}
		else{
			$data = $this->jsonify($filename);
			return $this->getJsonArray($data);
		}
	}


	// convert uploaded file/s into jsonarrays
	public function merge($files = []) {
		$data = [];
		foreach ($files as $f) {
			$data = array_merge($data, json_decode(file_get_contents($f), true));
		}
		return json_encode($data);
	}


	public function get(string $key, $default = null) {
		$array = $this->jsonArray;

		//case  one  segment , simple node no dots
		if (strpos($key, $this->delimiter) === false) {
			//top level key
			if ($this->exists($array, $key)) {
				return $this->jsonArray[$key] ?? $default;
			} else {
				// not top level key, dbl check if deeply nested.
				// used to find even wrong paths.
				$needle = $key;

				foreach ($this->recursiveFind($array = $this->jsonArray, $needle) as $value) {
					$recursiveFindArray = $value;
				}
				return $recursiveFindArray;
			}
		}
		// option: nested and dots notation
		else {
			return $this->getSegment($key);
		}

	}

	private function jsonify($file){
		$filetype = pathinfo($file, PATHINFO_EXTENSION);
		// txt files , xml and yaml
		$json='';
		switch ($filetype) {
			case 'txt':
				$json = json_encode( explode("\r\n",file_get_contents($file)) );
				break;
			case 'yaml':
				$json = json_encode(yaml_parse_file($file));
				break;
			case 'xml':
				$xml_string = file_get_contents($file);
				$xml = simplexml_load_string($xml_string);
				$json = json_encode($xml);
				break;
		}
		
		return $json;
	}
	
	/**
	* util Check if a given key or keys exists
	* @param  array<TKey>4|int|string  $keys
	*/
	private function exists($array, $key) {
		reset($array);
		return array_key_exists($key, $array);
	}

}

?>