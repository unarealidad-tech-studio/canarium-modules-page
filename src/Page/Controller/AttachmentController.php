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

use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class AttachmentController extends AbstractActionController
{
	public function jsonEditAction(){
		$pageid = $this->params()->fromQuery('pageid');
		$file = $this->params()->fromQuery('file');
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$form = new \Page\Form\AttachmentForm($objectManager);
		$entity = $objectManager->getRepository('Page\Entity\Attachment')->findOneBy(array('page' => $pageid, 'name' => $file));

		if (!$entity) {
			throw new \ErrorHandler\Exception\NotFoundException('Page Attachment Not Found');
		}

		$form->bind($entity);
		if ($this->request->isPost()) {
			$post = $this->request->getPost();
			$form->setData($post);

			if ($form->isValid()) {
				$objectManager->persist($entity);
				$objectManager->flush();
				return $this->redirect()->toRoute('admin/page', array('action' => 'edit' , 'id' => $entity->getPage()->getId()));
			}
		}

		$view = new ViewModel();
		$view->setTemplate('page/attachment/json-edit');
		$view->setTerminal(true);
		$view->form = $form;
		$view->entity = $entity;
		return $view;
	}
}
