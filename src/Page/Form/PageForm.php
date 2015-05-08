<?php
namespace Page\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class PageForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('page-form');
		
        $this->setHydrator(new DoctrineHydrator($objectManager));
		
        $fieldset = new PageFieldset($objectManager);
        $fieldset->setUseAsBaseFieldset(true);
        $this->add($fieldset);
		
    }
}