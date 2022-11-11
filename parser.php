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


	private function getNestedArr(string $key, $default = null) {
        $array = $this->jsonArray;
		// check type
		if (!is_string($key)) {
			return $default;
		}

		foreach (explode($this->delimiter, $key) as $segment) {
			// no mutation
			$array = $array[$segment];
		}
        // check 
		if (is_null($array)){
			// throw new Exception('path is not correct.');
			print ($key.", path not found!");
			return;
 
		} 
				
		return $array; 
	
	}


    // convert uploaded file/s into arrays
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
      
        //simple one node no dots
       if (strpos($key, $this->delimiter) === false)
        {   //top level
            if($this->exists($array, $key)){
                return $this->jsonArray[$key] ?? $default;
            }
            else{
                 // not top level and fall back
                 //go piano b meaning it is nested but the tree passed is not right
                $needle = $key;
				
                foreach ($this->recursiveFind($array = $this->jsonArray, $needle) as $value) {
                    $recursiveFindArray = array($value[0][$needle] => $value[2]);
					}
					//improve this array to do 
					return $recursiveFindArray;
                }
        }
        // nested and dots correctly
        else{
            $this->getNestedArr($key);
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
?>