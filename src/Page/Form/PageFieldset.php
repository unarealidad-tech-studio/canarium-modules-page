<?php
namespace Page\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
class PageFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('page');

        $this->setHydrator(new DoctrineHydrator($objectManager,'Page\Entity\Page'))->setObject(new \Page\Entity\Page());
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
            'type' => 'Zend\Form\Element\Text',
            'name' => 'template',
			'attributes' => array(
				'autocomplete' => 'off',
				'placeholder' => 'Template',
				'class' => 'form-control',
			),
        ));

		$sectionFieldset = new SectionFieldset($objectManager);
        $this->add(array(
            'type'    => 'Zend\Form\Element\Collection',
            'name'    => 'section',
            'options' => array(
                'count'           => 1,
                'target_element' => $sectionFieldset
            )
        ));

		$this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'metakeywords',
			'attributes' => array(
				'autocomplete' => 'off',
				'placeholder' => 'Meta Keyword',
				'class' => 'form-control',
			),
        ));

		$this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'metadescription',
			'attributes' => array(
				'placeholder' => 'Meta Description',
				'class' => 'form-control',
			),
        ));

		$this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'permalink',
			'attributes' => array(
				'autocomplete' => 'off',
				'placeholder' => 'Permalink',
				'class' => 'form-control',
			),
        ));

		$button = new Element\Button('submit');
		$button->setLabel('Draft')
			   ->setValue('Draft')
			   ->setAttribute('class','btn btn-md btn-warning')->setAttribute('type','submit');
		$this->add($button);

		$this->add(
			array(
				'type' => 'DoctrineModule\Form\Element\ObjectSelect',
				'name' => 'form',
				'options' => array(
					'label' => 'Attach Form',
					'object_manager'  => $this->objectManager,
					'target_class'    => 'Form\Entity\Form',
					'property'       => 'name',
					'display_empty_item' => true,
           			'empty_item_label'   => '---',
				),
				'attributes' => array(
					'class' => 'form-control',
				),
			)
		);

    }

	public function getInputFilterSpecification(){
		return array(
			'name' => array(
				'required' => true,
				'validators' => array(
					array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
							'use_context' => true,
							'object_manager' => $this->objectManager,
                            'object_repository' => $this->objectManager->getRepository('Page\Entity\Page'),
                            'fields' => 'name',
							'context' => array('id'),
							'messages' => array(
								'objectNotUnique' => 'This permalink is already taken.'
							),
                        ),
                    )
				),
			),
			'title' => array(
				'required' => false,
			),
			'metakeywords' => array(
				'required' => false,
			),
			'metadescription' => array(
				'required' => false,
			),
			'section' => array(
				'required' => false,
			),
			'form' => array(
				'required' => false,
			),
			'permalink' => array(
				'required' => false,
				'filters' => array(
					new \Page\Filter\Url()
				),
				'validators' => array(
					array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
							'use_context' => true,
							'object_manager' => $this->objectManager,
                            'object_repository' => $this->objectManager->getRepository('Page\Entity\Page'),
                            'fields' => 'permalink',
							'context' => array('id'),
							'messages' => array(
								'objectNotUnique' => 'This permalink is already taken.'
							),
                        ),
                    )
				),
			),
        );
	}


}