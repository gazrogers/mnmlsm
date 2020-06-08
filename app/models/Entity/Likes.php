<?php

namespace Model\Entity;

class Likes extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $postId;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var string
     */
    public $created;

    /**
     *
     * @var string
     */
    public $deleted;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("likes");
        $this->belongsTo('postId', 'Model\Entity\Posts', 'postId', ['alias' => 'Posts']);
        $this->belongsTo('userId', 'Model\Entity\Users', 'userId', ['alias' => 'Users']);
    }
}
