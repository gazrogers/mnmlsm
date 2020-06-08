<?php
namespace Model\BusinessLogic;

use Phalcon\Di\Injectable;

use Library\Exceptions\BadRequest;
use Library\Exceptions\Forbidden;
use Library\Exceptions\NotFound;
use Model\Entity\Posts as PostsModel;
use Model\Entity\Likes as LikesModel;

class Likes extends Injectable
{
    /**
     * Create a new like for the given post and user
     * 
     * @param  int    $postId the post ID
     * @param  int    $userId the user ID
     * 
     * @return nothing
     */
    public function create(int $postId, int $userId)
    {
        $post = PostsModel::findFirst($postId);
        if(!$post)
        {
            throw new NotFound("Post not found");
        }

        $like = LikesModel::findFirst(
            [
                "conditions" => "userId = :userId: AND postId = :postId:",
                "bind" => [
                    "userId" => $userId,
                    "postId" => $postId
                ]
            ]
        );
        if($like)
        {
            // This like was already registered.
            if($like->deleted == 1)
            {
                // It was previously undone.
                // We just flip the deleted flag to re-instate it and preserve the original creation timestamp.
                $like->deleted = 0;
                $like->update();
            }
        }
        else
        {
            // This like does not already exist, so we create a new entry in the likes table.
            $like = new LikesModel(
                [
                    'postId' => $postId,
                    'userId' => $userId,
                    'deleted' => 0
                ]
            );
            $like->create();
        }
    }

    /**
     * Remove the given like
     * 
     * @param  int    $postId the post ID
     * @param  int    $userId the user ID
     * 
     * @return nothing
     */
    public function remove(int $postId, int $userId)
    {
        $like = LikesModel::findFirst(
            [
                "conditions" => "userId = :userId: AND postId = :postId:",
                "bind" => [
                    "userId" => $userId,
                    "postId" => $postId
                ]
            ]
        );

        if($like)
        {
            if($like->deleted == 0)
            {
                // We only need to delete it if is not already deleted
                $like->deleted = 1;
                $like->update();
            }
        }
        else
        {
            throw new NotFound("Like not found");
        }
    }
}
