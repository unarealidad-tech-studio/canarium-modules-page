<?php
namespace Page\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
class SectionFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('section');

        $this->setHydrator(new DoctrineHydrator($objectManager,'Page\Entity\Section'))->setObject(new \Page\Entity\Section());
		$this->objectManager = $objectManager;
		$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
			'attributes' => array(
				'autocomplete' => 'off',
				'placeholder' => 'Name',
				'class' => 'form-control',
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
            'name' => 'content',
			'attributes' => array(
				'placeholder' => 'Content',
				'class' => 'form-control ckeditor',
			),
        ));
		
		
		
		
    }
	
	public function getInputFilterSpecification(){
		return array(			
			'name' => array(
				'required' => true,
			),
			'content' => array(
				'required' => false,
			),
        );
	}

    
}