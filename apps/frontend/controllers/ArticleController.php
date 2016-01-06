<?php

namespace Phalcon\Frontend\Controllers;

use Models\Article;
use Phalcon\Paginator\Adapter\QueryBuilder;

/**
 * Class ArticleController
 * @package Phalcon\Frontend\Controllers
 */
class ArticleController extends ControllerBase
{

    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page');

        $item = new Article();

        $builder = $item->getModelsManager()->createBuilder()
            ->columns('*')
            ->orderBy('id desc')
            ->addFrom(Article::class);
        /* @var $builder \Phalcon\Mvc\Model\Query\Builder */


        $paginator = new QueryBuilder(array(
                'builder' => $builder,
                'limit'   => 10,
                'page'    => $currentPage
            )
        );

        $this->view->setVar('page', $paginator->getPaginate());
    }
}