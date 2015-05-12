<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Page\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class PageController extends AbstractActionController
{

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
					$collection = new \Doctrine\Common\Collections\ArrayCollection();
					$collectionUpload = new \Doctrine\Common\Collections\ArrayCollection();
					$form->setData($this->params()->fromPost());
					foreach($this->params()->fromPost() as $sectionFromPost) {
						foreach($sectionFromPost as $fieldsetFromPost){
							foreach($fieldsetFromPost as $k => $v){
								$element = $objectManager->getRepository('Form\Entity\Element')->find($k);
								$data = new \Form\Entity\Data();
								$data->setElement($element);
								$data->setValue(serialize($v));
								$collection->add($data);
							}
						}
					}


					$files = $this->getRequest()->getFiles();
					$dir = "./data/uploads/tmp/";
					if(!file_exists($dir)){
						if(!mkdir($dir, 0755, true))
							throw new \Exception("Failed to create upload folders. Please allow php to write to the working directories.");
					}
					$filter = new \Zend\Filter\File\RenameUpload($dir);
					$filter->setUseUploadName(true);
					$filter->setOverwrite(true);
					foreach($this->getRequest()->getFiles() as $sections){
						foreach($sections as $files){
							foreach($files as $file){
								if($file['error'] != 0) continue;
								$upload = new \Form\Entity\Upload($this->getServiceLocator());
								$upload->setParentData($parentData);
								$upload->setName($file['name']);
								$upload->setType($file['type']);
								$upload->setTmpName($file['tmp_name']);
								$upload->setError($file['error']);
								$upload->setSize($file['size']);
								$collectionUpload->add($upload);

								$filter->filter($file);
							}
						}
					}
					$parentData->addData($collection);
					$parentData->addUploads($collectionUpload);

					if($reference) $parentData->setParent($reference);

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
}
