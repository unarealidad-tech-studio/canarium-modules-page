<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Page;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
    }

    public function getConfig()
    {
        return array_merge(
			include __DIR__ . '/config/module.config.php',
			include __DIR__ . '/config/extra/admin.navigation.global.php'
		);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	
	public function getViewHelperConfig()
	{
		return array(
			'factories' => array(
				'getPage' => function($sm) {
					//$sm is the view helper manager, so we need to fetch the main service manager
					$locator = $sm->getServiceLocator();
					$objectManager = $locator->get('doctrine.entitymanager.orm_default');
					return new \Page\View\Helper\GetPage($objectManager);
				},
			),
		);
	}
}
