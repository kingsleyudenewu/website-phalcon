<?php

namespace Models;

use Carbon\Carbon;
use Phalcon\Mvc\Model;


/**
 * Class Article
 *
 * @method User getUser()
 * @method static Article published() Scope queries to articles that have been published.
 * @method static Article slug($slug) Scope queries to article containing slug.
 */
class Article extends Model
{

    public function getSource()
    {
        return 'articles';
    }

    const UNPUBLISHED = 0;
    const PUBLISHED = 1;

    private static $statuses = [
        self::UNPUBLISHED => 'Unpublished',
        self::PUBLISHED   => 'Published'
    ];

    public static function getStatuses()
    {
        return self::$statuses;
    }

    public function initialize()
    {
        //belongsToMany(Tag)
        $this->belongsTo('user_id', User::class, 'id', ['alias' => 'user']);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return Carbon
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * @param string $published_at
     */
    public function setPublishedAt(string $published_at)
    {
        $this->published_at = $published_at;
    }

    /**
     * @return int
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param int $published
     */
    public function setPublished(int $published)
    {
        $this->published = $published;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return self::$statuses[$this->getPublished()];
    }

    /**
     * TODO Scope queries to articles that have been published.
     *
     * @param $query
     */

    /**
     * TODO Scope queries to the slug provided.
     *
     * @param $query
     * @param $slug
     */

    /**
     * TODO Get list of Tag ids associated with current article.
     *
     * @return array
     */

}
