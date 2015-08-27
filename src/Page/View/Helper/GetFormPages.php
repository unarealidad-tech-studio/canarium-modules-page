<?php
namespace Page\View\Helper;

use Doctrine\ORM\EntityManager;
use Zend\View\Helper\AbstractHelper;

class GetFormPages extends AbstractHelper
{
    public function __construct(EntityManager $objectManager){
        $this->objectManager = $objectManager;
    }

    public function __invoke(){
        $query_builder = $this->objectManager->createQueryBuilder();

        $results = $query_builder->select('p')
            ->from('Page\Entity\Page', 'p')
            ->where($query_builder->expr()->andX(
                $query_builder->expr()->isNotNull('p.form'),
                $query_builder->expr()->neq('p.status', $query_builder->expr()->literal('Draft'))
            ))
            ->orderBy('p.title', 'ASC')
            ->getQuery()->getResult();

        return $results;
    }
}