<?php

namespace Model\Entity;

class Posts extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */
    public $postId;

    /**
     *
     * @var string
     */
    public $text;

    /**
     *
     * @var string
     */
    public $created;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("posts");
        $this->hasMany(
            'postId',
            'Model\Entity\Likes',
            'postId',
            [
                'alias' => 'Likes',
                'params' => [
                    'conditions' => 'deleted = 0'
                ]
            ]
        );
    }
}
