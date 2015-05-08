<?php
namespace Page\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
class AttachmentFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('attachment');

        $this->setHydrator(new DoctrineHydrator($objectManager,'Page\Entity\Attachment'))->setObject(new \Page\Entity\Attachment($objectManager));
		$this->objectManager = $objectManager;
		$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'title',
			'attributes' => array(
				'autocomplete' => 'off',
				'placeholder' => 'Title',
				'class' => 'form-control',
			),
        ));
		
		$this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'description',
			'attributes' => array(
				'placeholder' => 'Description',
				'class' => 'form-control',
			),
        ));
		
    }
	
	public function getInputFilterSpecification(){
		return array(			
			
        );
	}

    
}