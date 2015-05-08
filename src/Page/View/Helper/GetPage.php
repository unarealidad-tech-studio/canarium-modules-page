<?php
namespace Page\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Doctrine\Common\Persistence\ObjectManager;

class GetPage extends AbstractHelper
{
	public function __construct(ObjectManager $objectManager){
		$this->objectManager = $objectManager;
	}

    public function __invoke(array $value){
        $page = $this->objectManager->getRepository('Page\Entity\Page')->findOneBy($value);
		return $page;
    }
}