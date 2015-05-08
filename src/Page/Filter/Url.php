<?php
namespace Page\Filter;

use Zend\Filter\FilterInterface;

class Url implements FilterInterface
{
    public function filter($value)
    {
		$pattern = '/[^\p{L}\p{N}\'\/\s-]/u';
		
		$filterChain = new \Zend\Filter\FilterChain();
		$filterChain->attach(new \Zend\Filter\StringToLower());
		
		$valueFiltered = str_replace(' ','-',$filterChain->filter($value));
		

        return preg_replace($pattern, '', (string) $valueFiltered);
    }
}
