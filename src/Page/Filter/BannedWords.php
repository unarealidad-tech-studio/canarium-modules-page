<?php
/* Jethro */

namespace Template\Filter;

use Zend\Filter\FilterInterface;

class BannedWords implements FilterInterface
{
	
	private $words = array();
	
	public function __construct(array $words = array()){
		$this->words = array_merge($this->words, $words);
	}
	
    public function filter($value)
    {
		/* @TODO
		  * ADD levenshtein function
		  * http://php.net/manual/en/function.levenshtein.php
		*/
		$valueFiltered = str_ireplace($this->words, ":blocked:", $value);

        return $valueFiltered;
    }
}