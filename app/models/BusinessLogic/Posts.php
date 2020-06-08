<?php
namespace Model\BusinessLogic;

use Phalcon\Di\Injectable;

use Library\Exceptions\BadRequest;
use Library\Exceptions\Forbidden;
use Library\Exceptions\NotFound;
use Model\Entity\Posts as PostsModel;

class Posts extends Injectable
{
    /**
     * Create a new post
     *
     * @param array $inputData the data sent by the user
     * 
     * @return Model\Entity\Posts the new Post model
     */
    public function create(array $inputData): PostsModel
    {
        $post = new PostsModel(
            [
                'text' => $inputData['text'],
                'userId' => 1 //$inputData['userId']
            ]
        );
        if($post->create())
        {
            return PostsModel::findFirst($post->postId);
        }
        else
        {
            $errorMessages = implode(", ", $post->getMessages());
            throw new BadRequest($errorMessages);
        }
    }

    /**
     * Return the requested post
     * 
     * @param int $postId the post ID
     * 
     * @return Model\Entity\Posts the post
     */
    public function read(int $postId): PostsModel
    {
        $post = PostsModel::findFirst($postId);
        if(!$post)
        {
            throw new NotFound("Post not found");
        }

        return $post;
    }

    /**
     * Delete the specified post
     * 
     * @param int $postId the post ID
     * @param int $userId the ID of the user requesting the deletion
     * 
     * @return nothing
     */
    public function delete(int $postId, int $userId)
    {
        $post = PostsModel::findFirst($postId);
        if(!$post)
        {
            throw new NotFound("Post not found");
        }
        if($post->userId != $userId)
        {
            throw new Forbidden("Posts can only be deleted by their owner");
        }

        $post->delete();
    }

    /**
     * Return a list of likes for the given post
     * 
     * @param  int    $postId the post ID
     * 
     * @return array all the post's likes
     */
    public function getLikes(int $postId): array
    {
        $post = PostsModel::findFirst($postId);
        if(!$post)
        {
            throw new NotFound("Post not found");
        }

        return $post->Likes->toArray();
    }
}
