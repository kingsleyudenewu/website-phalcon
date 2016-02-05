<?php

namespace Lib\Paginator;

use Phalcon\Http\Request;
use Phalcon\Paginator\Adapter;

class QueryBuilder extends Adapter\QueryBuilder
{

    /**
     * Returns a slice of the resultset to show in the pagination
     */
    public function getPaginate()
    {
        $page = parent::getPaginate();

        if ($this->_config['with_params'] === true)
        {
            $page->firstQuery = $this->buildParamsQuery($page->first);
            $page->beforeQuery = $this->buildParamsQuery($page->before);
            $page->nextQuery = $this->buildParamsQuery($page->next);
            $page->lastQuery = $this->buildParamsQuery($page->last);
        }

        return $page;
    }

    private function buildParamsQuery($page)
    {
        $request = new Request();
        $params = $request->get();
        $params['page'] = $page;

        $url = $params['_url'];
        unset($params['_url']);

        $url .= '?' . http_build_query($params);

        return $url;
    }

}
