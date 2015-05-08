<?php
namespace Page\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class AttachmentForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('attachment-form');
        $this->setHydrator(new DoctrineHydrator($objectManager));
		
        $fieldset = new AttachmentFieldset($objectManager);
        $fieldset->setUseAsBaseFieldset(true);
        $this->add($fieldset);
		
    }
}