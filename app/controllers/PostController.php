<?php
declare(strict_types=1);

namespace Controller;

use Library\Exceptions\BadRequest;
use Model\BusinessLogic\Likes;
use Model\BusinessLogic\Posts;

class PostController extends \Phalcon\Mvc\Controller
{
    /**
     * Create a new post
     * 
     * @return array the new post data
     */
    public function create()
    {
        $inputData = json_decode($this->di->get('request')->getRawBody(), true);
        $this->validateInput($inputData, 'create');

        $post = new Posts();
        $newPost = $post->create($inputData);

        return ['data' => [$newPost->toArray()]];
    }

    /**
     * Fetch a post by its ID
     * 
     * @param int $postId the post ID
     * 
     * @return array the post data
     */
    public function read(int $postId)
    {
        $post = new Posts();
        $readPost = $post->read($postId);

        return ['data' => [$readPost->toArray()]];
    }

    /**
     * Delete a post
     * 
     * @param int $postId the post ID
     * 
     * @return array empty
     */
    public function delete(int $postId)
    {
        $userId = 1; // When OpenID Connect is sorted we'll have a real user ID here
        $post = new Posts();
        $readPost = $post->delete($postId, $userId);

        return ['data' => []];
    }

    /**
     * Register a like of the post
     * 
     * @param int $postId the post to like
     * 
     * @return array empty
     */
    public function createLike(int $postId)
    {
        $userId = 1;
        $like = new Likes();
        $like->create($postId, $userId);

        return ['data' => []];
    }

    /**
     * Return all the likes for a given post
     * 
     * @param int $postId the post ID
     * 
     * @return array likes
     */
    public function readLikes(int $postId)
    {
        $post = new Posts();
        $likes = $post->getLikes($postId);

        return ['data' => $likes];
    }

    /**
     * Remove the user's like for the given post
     * 
     * @param int $postId the post ID
     * 
     * @return array empty
     */
    public function removeLike(int $postId)
    {
        $userId = 1;
        $like = new Likes();
        $like->remove($postId, $userId);

        return ['data' => []];
    }

    /**
     * Validate that the action has the required fields
     * 
     * @param array  $data   the data from the user
     * @param string $action the action requested
     * 
     * @return nothing
     */
    private function validateInput(array $data, string $action)
    {
        $requiredFields = [
            'create' => ['text'],
        ];
        if(array_key_exists($action, $requiredFields))
        {
            foreach($requiredFields[ $action ] as $field)
            {
                if(!array_key_exists($field, $data))
                {
                    throw new BadRequest(ucwords($action) . " requests require '" . $field . "' field");
                }
            }
        }
    }
}
