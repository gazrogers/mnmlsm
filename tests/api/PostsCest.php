<?php 
namespace Test\Api;

use \ApiTester;
use Codeception\Stub;
use Codeception\Util\HttpCode;

class PostsCest
{
    public function _before(ApiTester $I)
    {
        // Turn off logging for automated tests
        // $I->addServiceToContainer('logger', function() { return Stub::makeEmpty('Phalcon\Logger'); });
    }

    // TEST POST CREATION
    public function createPostNonJson(ApiTester $I)
    {
        $I->wantTo("Check input format is checked for create post endpoint");
        $I->sendPOST('/post', ['text' => 'Test text']);
        $I->seeResponseCodeIs(HttpCode::UNSUPPORTED_MEDIA_TYPE);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
    }

    public function createPostBadData(ApiTester $I)
    {
        $I->wantTo("Check bad input data is rejected with appropriate error message");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/post', ['text' => '']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
    }

    public function createPostMissingField(ApiTester $I)
    {
        $I->wantTo("Check missing field is rejected with appropriate error message");
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/post', []);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Create requests require \'text\'');
    }

    public function createPostSuccess(ApiTester $I)
    {
        $I->wantTo("Create a new post");
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/post', ['text' => 'Test text']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->dontSeeResponseContains('errors');
        $I->seeResponseContains('data');
    }

    // TEST POST READING
    public function readPostNotInteger(ApiTester $I)
    {
        $I->wantTo("Try to read a post with a non-numeric ID");
        $I->sendGET('/post/invalid');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Not Found');
    }

    public function readPostNotExist(ApiTester $I)
    {
        $I->wantTo("Read a post that does not exist");
        $I->sendGET('/post/3');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Post not found');
    }
    
    public function readPostExists(ApiTester $I)
    {
        $I->wantTo("Read a post that does exist");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'readPostExists Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->sendGET('/post/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContains('errors');
        $I->seeResponseContains('data');
        $I->seeResponseContains('readPostExists Test Text');
    }

    // TEST POST DELETION
    public function deletePostNotExist(ApiTester $I)
    {
        $I->wantTo("Delete a post that does not exist");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'deletePostNotExist Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->sendDELETE('/post/2');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Post not found');
    }

    public function deletePostWrongUser(ApiTester $I)
    {
        $I->wantTo("Delete a post that is not mine");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'deletePostWrongUser Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 2
            ]
        );
        $I->sendDELETE('/post/1');
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Posts can only be deleted by their owner');
    }

    public function deletePostSuccess(ApiTester $I)
    {
        $I->wantTo("Delete a post successfully");
        $I->haveRecord(
            'Model\Entity\Posts',
            [
                'postId' => 1, 'text' => 'deletePostSuccess Test Text', 'created' => date('Y-m-d h:i:s'), 'userId' => 1
            ]
        );
        $I->sendDELETE('/post/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
    }
}
