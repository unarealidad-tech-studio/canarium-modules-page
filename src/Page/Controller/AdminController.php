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

class AdminController extends AbstractActionController
{

    public function indexAction(){
		$page = $this->params()->fromQuery('page', 1);
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$query = $objectManager->createQuery('SELECT a FROM Page\Entity\Page a ORDER BY a.id DESC');
		$doctrinePaginator = new Paginator($query, $fetchJoinCollection = true);
		$pages = new ZendPaginator(new DoctrinePaginator($doctrinePaginator));
		$pages->setCurrentPageNumber( $page );
		$pages->setItemCountPerPage(10);

		$view = new ViewModel();
		$view->pages = $pages;
		$view->routeParams = array('route' => 'admin/page','urlParams' => array());
		return $view;
    }

	public function createAction(){
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$form = new \Page\Form\PageForm($objectManager);

		$id = $this->params()->fromRoute('id',0);
		$entity = $objectManager->getRepository('Page\Entity\Page')->find($id);
		if(!$entity) {
			$entity = new \Page\Entity\Page();
			$objectManager->persist($entity);
			$objectManager->flush();
			return $this->redirect()->toRoute('admin/page',array('action' => 'create', 'id' => $entity->getId() ));

		}
		$form->bind($entity);

		if ($this->request->isPost()) {
			$post = $this->request->getPost();
			$form->setData($post);

			if ($form->isValid()) {
				switch($post['page']['submit']){
					case 'Publish' :
						$entity->setStatus('Published');
						break;
					default :
						$entity->setStatus('Draft');
						break;
				}
				$objectManager->persist($entity);
				$objectManager->flush();
				return $this->redirect()->toRoute('admin/page');
			}
		}

		$view = new ViewModel();
		$view->form = $form;
		$view->page = $entity;
		return $view;
	}

	public function editAction(){
		$id = $this->params()->fromRoute('id');
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$form = new \Page\Form\PageForm($objectManager);
		$entity = $objectManager->getRepository('Page\Entity\Page')->find($id);

		if(!$entity){
			throw new \ErrorHandler\Exception\NotFoundException('Page Not Found');
		}

		$form->bind($entity);
		if ($this->request->isPost()) {
			$post = $this->request->getPost();
			$form->setData($post);

			switch($post['page']['submit']){
				case 'Publish' :
					$entity->setStatus('Published');
					break;
				default :
					$entity->setStatus('Draft');
					break;
			}
			if ($form->isValid()) {

				$objectManager->persist($entity);
				$objectManager->flush();
				if($post['page']['submit'] == 'Draft') return $this->redirect()->toRoute('admin/page');
			}
		}

		$view = new ViewModel();
		$view->setTemplate('page/admin/create');
		$view->form = $form;
		$view->page = $entity;
		return $view;
	}


	public function deleteAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$entity = $objectManager->getRepository('Page\Entity\Page')->find($id);
		if(!$entity){
			throw new \ErrorHandler\Exception\NotFoundException('Page Not Found');
		}

		if ($this->request->isPost()) {
				$delete = $this->getRequest()->getPost('delete', 'No');
				if ($delete == 'Yes') {
					$objectManager->remove($entity);
					$objectManager->flush();
				}

				return $this->redirect()->toRoute('admin/page');
		}


		$view = new ViewModel();
		$view->entity = $entity;
		return $view;
	}

	public function uploadAction()
    {
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$id = $this->params()->fromRoute('id',0);
		$page = $objectManager->getRepository('Page\Entity\Page')->find($id);
		$collection = new \Doctrine\Common\Collections\ArrayCollection();


		$upload_handler = new \Page\Plugins\UploadHandler(
		array(
			'page_id' => $id,
			'upload_dir' => './data/uploads/page/'.$id.'/',
			'upload_url' => '/page/'.$id.'/',
			'image_versions' => array(
				'thumbnail' => array(
					'upload_url' => '/page/'.$id.'/',
					'upload_dir' => '/page/'.$id.'/',
					'max_width' => 120,
					'max_height' => 96
				),
			),
		));

		switch($this->request->getMethod()){
			case 'POST' :
				$files = $this->request->getFiles()->toArray();
				foreach($files['files'] as $file) {
					$attachment = new \Page\Entity\Attachment($this->getServiceLocator());
					$attachment->setTmpname( $file['name'] );
					$attachment->setSize( $file['size'] );
					$attachment->setType( $file['type'] );
					$attachment->setName( $file['name'] );
					$collection->add($attachment);
				}
				$page->addAttachment($collection);
				$objectManager->flush();
				return $this->response;
				break;

			case 'DELETE' :
				$file = $this->params()->fromQuery('file');
				$attachment = $objectManager->getRepository('Page\Entity\Attachment')->findOneBy(array('tmp_name' => $file, 'page' => $id));
				if($attachment) {
					$objectManager->remove($attachment);
					$objectManager->flush();
				}
				break;
		}
		return $this->response;
	}

	public function testAction(){
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$entity = $objectManager->getRepository('Page\Entity\Page')->find( 12 );

		$x = $entity->getAttachmentById(14);
		echo $x->getName();


		return $this->response;
	}
}
