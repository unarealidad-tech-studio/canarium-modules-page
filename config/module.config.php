<?php
return array(

	'doctrine' => array(
		'driver' => array(
			'page_entities' => array(
				'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/Page/Entity')
			),

			'orm_default' => array(
				'drivers' => array(
					'Page\Entity' => 'page_entities'
				),
			),
		),
	),

    'controllers' => array(
        'invokables' => array(
			'Page' => 'Page\Controller\PageController',
			'Admin\Page' => 'Page\Controller\AdminController',
			'Admin\Page\Attachment' => 'Page\Controller\AttachmentController',
        ),
    ),

	'bjyauthorize' => array(
        'guards' => array(
			'BjyAuthorize\Guard\Controller' => array(
				array('controller' => 'Admin\Page', 'roles' => array('admin')),
				array('controller' => 'Admin\Page\Attachment', 'roles' => array('admin')),
				array('controller' => 'Page', 'roles' => array('guest','user')),
            ),
        ),
    ),

	'router' => array(
        'routes' => array(

			'permalink' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/:permalink',
					'defaults' => array(
						'controller'    => 'Page',
						'action'        => 'permalink',
					),
					'constraints' => array(
						'permalink'     => '[a-zA-Z][a-zA-Z0-9_-]*',
					),
				),

				'may_terminate' => true,
			),

			'page' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/view[/:id]',
					'defaults' => array(
						'controller'    => 'Page',
						'action'        => 'index',
					),
					'constraints' => array(
						'id'     => '[0-9]*',
					),
				),

				'may_terminate' => true,
			),

			'admin' => array(
                'child_routes' => array(
                    'page' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/page[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]*',
                            ),
                            'defaults' => array(
								'controller'    => 'Admin\Page',
								'action'        => 'index',
                            ),
                        ),
                    ),
					'attachment' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/page/attachment[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]*',
                            ),
                            'defaults' => array(
								'controller'    => 'Admin\Page\Attachment',
								'action'        => 'index',
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'page' => __DIR__ . '/../view',
        ),
		'template_map' => array(

        ),
    ),
);