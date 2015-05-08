<?php
return array(
	'navigation' => array(
         'admin' => array(
			 array(
				'label' => 'Page',
				'route' => 'admin/page',
				'resource' => 'admin',
				'controller' => 'Admin\Page',
				'icon' => 'fa fa-file-o',
				'pages' => array(
					array(
						'label' => 'List',
						'route' => 'admin/page',
						'controller' => 'Admin\Page',
						'resource' => 'admin',
						'action'     => 'index',
				 		'icon' => 'fa fa-th-list',
					),
					array(
						'label' => 'Create',
						'route' => 'admin/page',
						'controller' => 'Admin\Page',
						'resource' => 'admin',
						'action'     => 'create',
						'icon' => 'fa fa-plus-circle',
					),
				),
             ),
         ),
     ),
);