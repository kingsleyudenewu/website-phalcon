<?php

namespace Models\Article;

use Phalcon\Mvc\Model;

class Tag extends Model
{

    public function getSource()
    {
        return 'article_tag';
    }

    public function initialize()
    {
        $this->hasOne('tag_id', \Models\Tag::class, 'id', ['alias' => 'tag']);
    }

}