<?php

namespace Phalcon\Frontend\Controllers;

use Lib\Paginator\Adapter\QueryBuilder;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model;

abstract class CrudController extends Controller
{

    public function viewAction(Model $model)
    {
        $this->view->setVar('model', $model);
    }

    /**
     * @param array $options
     */
    public function indexAction(array $options = [])
    {
        return $this->listAction($options);
    }

    /**
     * @param array $options
     */
    public function listAction(array $options = [])
    {
        $currentPage = $this->request->getQuery('page');

        $model = $this->getModelName();
        $entity = new $model;

        $builder = $entity->getModelsManager()->createBuilder()
            ->columns('*')
            ->orderBy('id desc')
            ->addFrom($this->getModelName());
        /* @var $builder \Phalcon\Mvc\Model\Query\Builder */


        $paginator = new QueryBuilder(array(
                'builder'     => $builder,
                'limit'       => 10,
                'page'        => $currentPage,
                'with_params' => true,
            )
        );

        $this->view->setVar('page', $paginator->getPaginate());
    }
}