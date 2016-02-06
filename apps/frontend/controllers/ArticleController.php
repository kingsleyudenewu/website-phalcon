<?php

namespace Phalcon\Frontend\Controllers;

use Models\Article;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller\BindModelInterface;

/**
 * Class ArticleController
 * @package Phalcon\Frontend\Controllers
 */
class ArticleController extends CrudController implements BindModelInterface
{

    const PAGE_SIZE = 5;

    static function getModelName()
    {
        return Article::class;
    }
}