<?php

namespace Page\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Form\Service\Form as FormService;

use Doctrine\Common\Collections\ArrayCollection;

class PageController extends AbstractActionController
{
	/**
     * @var FormService
     */
    protected $formService;

    public function permalinkAction()
    {
		$ref = $this->params()->fromQuery('ref',0);
		$session = new Container('form');
		$view = new ViewModel();
		$permalink = $this->params()->fromRoute('permalink');

		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$page = $objectManager->getRepository('Page\Entity\Page')->findOneBy(array('permalink' => $permalink, 'status' => 'Published'));
		if( !$page )
			throw new \ErrorHandler\Exception\NotFoundException('Page Not Found');

		$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
		$renderer->headTitle($page->getTitle());
		$renderer->headMeta()->appendName('description', $page->getMetadescription());
		$renderer->headMeta()->appendName('keywords', $page->getMetakeywords());

		$reference = $objectManager->getRepository('Form\Entity\ParentData')->find($ref);

		// FORM BEGIN
		$formEntity = $page->getForm();
		if($formEntity){
			$form = new \Zend\Form\Form($formEntity->getName());
			$form->setLabel($formEntity->getLabel());
			foreach($formEntity->getFieldset()->filter(
				function($entry) {
					  return $entry->getParent()?false:true;
					}
				) as $fieldsetEntity){
				$fieldset = $fieldsetEntity->generateFieldsetObject($fieldsetEntity);
				$form->add($fieldset);

				// GET FIELDSET'S CHILDREN
				foreach($fieldsetEntity->getChildren() as $fieldsetChildEntity){
					$fieldset->add($fieldsetChildEntity->generateFieldsetObject());
				}
			}

			// PASS THE FORM IN VIEWSCRIPT
			$view->form = $form;

			if ($this->request->isPost()) {
				$post = array_merge_recursive(
					$this->request->getPost()->toArray(),
					$this->request->getFiles()->toArray()
				);

				$form->setData($post);

				// @TODO - FORM VALIDATION
				#if ($form->isValid()) {
					$parentData = new \Form\Entity\ParentData();
					$parentData->setForm($formEntity);
					$parentData->setUser($this->zfcUserAuthentication()->getIdentity());

					$form->setData($this->params()->fromPost());

					$request_post_data = $this->params()->fromPost();

					$data_objects = new ArrayCollection();

					foreach($request_post_data as $sections){
						$current_data_objects = $this->getFormService()->getDataObjectsFromArray(
							$sections
						);

						$data_objects = new ArrayCollection(
						    array_merge($data_objects->toArray(), $current_data_objects->toArray())
						);
					}

					$parentData->addData($data_objects);

					$request_files_data = $this->getRequest()->getFiles();

					$upload_objects = new ArrayCollection();

					foreach($request_files_data as $sections){
						$current_upload_objects = $this->getFormService()->getUploadObjectsFromArray(
							$sections,
							$parentData
						);

						$upload_objects = new ArrayCollection(
						    array_merge($upload_objects->toArray(), $current_upload_objects->toArray())
						);
					}

					$parentData->addUploads($upload_objects);

					if ($reference) {
						$parentData->setParent($reference);
					}

					$objectManager->persist($parentData);
					$objectManager->flush();
					$session->parentData = $parentData->getId();
					return $this->redirect()->toUrl($formEntity->getRedirect()->getPermalink());
				#}
			}
		}

		$view->page = $page;
		$view->reference = $reference;
		if($session->parentData) $view->parentData = $objectManager->getRepository('Form\Entity\ParentData')->find($session->parentData);
		if($page->getTemplate())
			$view->setTemplate($page->getTemplate());
		else $view->setTemplate('page/page/index');

		return $view;
    }

	public function indexAction()
    {
		$id = $this->params()->fromRoute('id');

		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$page = $objectManager->getRepository('Page\Entity\Page')->findOneBy(array('id' => $id, 'status' => 'Published'));
		if( !$page )
			throw new \ErrorHandler\Exception\NotFoundException('Page Not Found');

		$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
		$renderer->headTitle($page->getTitle());
		$renderer->headMeta()->appendName('description', $page->getMetadescription());
		$renderer->headMeta()->appendName('keywords', $page->getMetakeywords());

		$view = new ViewModel();
		$view->page = $page;
		$view->setTemplate('page/page/index');
		return $view;
    }

    public function getFormService()
    {
        if (!$this->formService) {
            $this->formService = $this->getServiceLocator()->get('form_form_service');
        }
        return $this->formService;
    }

    public function setFormService(FormService $formService)
    {
        $this->formService = $formService;
        return $this;
    }
}
