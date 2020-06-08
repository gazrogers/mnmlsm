<?php 
namespace Test\Api;

use \ApiTester;
use Codeception\Stub;
use Codeception\Util\HttpCode;

class LikesCest
{
    public function _before(ApiTester $I)
    {
        // Turn off logging for automated tests
        // $I->addServiceToContainer('logger', function() { return Stub::makeEmpty('Phalcon\Logger'); });
    }

    // TEST LIKE CREATION
    public function createLikePostNotExist(ApiTester $I)
    {
        $I->wantTo("Like a post that does not exist");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'likePostNotExist Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'likePostNotExist Jones', 'email' => 'my@example.com'
            ]
        );
        $I->sendPUT('/post/3/like');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Post not found');
    }

    public function createLikeNewSuccess(ApiTester $I)
    {
        $I->wantTo("Like a post - like does not already exist");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'likeNewSuccess Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'likeNewSuccess Jones', 'email' => 'my@example.com'
            ]
        );
        $I->dontSeeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1]);
        $I->sendPUT('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1]);
    }

    public function createLikePreviouslyDeletedSuccess(ApiTester $I)
    {
        $I->wantTo("Like a post - like was already created and deleted");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'likeNewSuccess Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'likeNewSuccess Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveRecord(
            'Model\Entity\Likes',
            [
                'postId' => 1, 'userId' => 1, 'deleted' => 1
            ]
        );
        $I->dontSeeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->sendPUT('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
    }

    public function createLikeAlreadyExists(ApiTester $I)
    {
        $I->wantTo("Like a post - like already exists");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'likeNewSuccess Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'likeNewSuccess Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveRecord(
            'Model\Entity\Likes',
            [
                'postId' => 1, 'userId' => 1, 'deleted' => 0
            ]
        );
        $I->dontSeeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 1]);
        $I->seeNumberOfRecords('Model\Entity\Likes', 1, ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->sendPUT('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeNumberOfRecords('Model\Entity\Likes', 1, ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
    }

    // TEST READ LIKES
    public function getLikesPostNotExist(ApiTester $I)
    {
        $I->wantTo("List all the likes for a post that does not exist");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'getLikesPostNotExist Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'getLikesPostNotExist Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->sendGET('/post/2/like');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Post not found');
    }

    public function getLikesPostOneLike(ApiTester $I)
    {
        $I->wantTo("List all the likes for a post with one like");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'getLikesPostOneLike Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'getLikesPostOneLike Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->sendGET('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeResponseJsonMatchesJsonPath('$.data[0].userId');
        $I->dontSeeResponseJsonMatchesJsonPath('$.data[1].userId');
    }

    public function getLikesPostTwoLikes(ApiTester $I)
    {
        $I->wantTo("List all the likes for a post with two likes");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'getLikesPostTwoLikes Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'getLikesPostTwoLikes Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Users', ['userId' => 2, 'name' => 'getLikesPostTwoLikes Smith', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 2, 'deleted' => 0]);
        $I->sendGET('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeResponseJsonMatchesJsonPath('$.data[0].userId');
        $I->seeResponseJsonMatchesJsonPath('$.data[1].userId');
        $I->dontSeeResponseJsonMatchesJsonPath('$.data[2].userId');
    }

    public function getLikesPostTwoLikesOneDeleted(ApiTester $I)
    {
        $I->wantTo("List all the likes for a post with two likes one of which is deleted");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'getLikesPostTwoLikes Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'getLikesPostTwoLikes Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Users', ['userId' => 2, 'name' => 'getLikesPostTwoLikes Smith', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 1]);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 2, 'deleted' => 0]);
        $I->sendGET('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeResponseJsonMatchesJsonPath('$.data[0].userId');
        $I->dontSeeResponseJsonMatchesJsonPath('$.data[1].userId');
    }

    public function removeLikeNotExist(ApiTester $I)
    {
        $I->wantTo("Remove a like that does not exist");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'removeLikeNotExist Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'removeLikeNotExist Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Users', ['userId' => 2, 'name' => 'removeLikeNotExist Smith', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 2, 'deleted' => 0]);
        $I->sendDELETE('/post/2/like');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Like not found');
    }

    public function removeLikeAlreadyDeleted(ApiTester $I)
    {
        $I->wantTo("Remove a like that is already deleted");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'removeLikeAlreadyDeleted Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'removeLikeAlreadyDeleted Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Users', ['userId' => 2, 'name' => 'removeLikeAlreadyDeleted Smith', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 1]);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 2, 'deleted' => 0]);
        $I->seeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 1]);
        $I->sendDELETE('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 1]);
    }

    public function removeLikeSuccess(ApiTester $I)
    {
        $I->wantTo("Remove a like that is not already deleted");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'removeLikeSuccess Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->haveRecord('Model\Entity\Users', ['userId' => 1, 'name' => 'removeLikeSuccess Jones', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Users', ['userId' => 2, 'name' => 'removeLikeSuccess Smith', 'email' => 'my@example.com']);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->haveRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 2, 'deleted' => 0]);
        $I->seeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 0]);
        $I->sendDELETE('/post/1/like');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeRecord('Model\Entity\Likes', ['postId' => 1, 'userId' => 1, 'deleted' => 1]);
    }
}
