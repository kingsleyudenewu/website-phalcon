<?php

namespace Models;

use Phalcon\Mvc\Model;

class Tag extends Model
{

    public function getSource()
    {
        return 'tags';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    public function scopeSlug($query, $slug)
    {
        //TODO
    }
}
