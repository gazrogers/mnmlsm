<?php 
namespace Test\Api;

use \ApiTester;
use Codeception\Stub;
use Codeception\Util\HttpCode;

class UsersCest
{
    public function _before(ApiTester $I)
    {
        // Turn off logging for automated tests
        $I->addServiceToContainer('logger', function() { return Stub::makeEmpty('Phalcon\Logger'); });
    }

    // TEST USER CREATION
    public function createUserNonJson(ApiTester $I)
    {
        $I->wantTo("Check input format is checked for create user endpoint");
        $I->sendPOST('/user', ['name' => 'Bob Dond', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::UNSUPPORTED_MEDIA_TYPE);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
    }

    public function createUserBadData(ApiTester $I)
    {
        $I->wantTo("Check bad input data is rejected with appropriate error message");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/user', ['name' => '', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('name is required');
    }

    public function createUserBadEmailAddress(ApiTester $I)
    {
        $I->wantTo("Check bad email addresses are rejected with appropriate error message");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/user', ['name' => 'Bob Dond', 'email' => 'bob_dond.com']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Please enter a correct email address');
    }

    public function createUserMissingField(ApiTester $I)
    {
        $I->wantTo("Check missing field is rejected with appropriate error message");
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user', ['name' => 'Bob Dond']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Create requests require \'email\'');
    }

    public function createUserSuccess(ApiTester $I)
    {
        $I->wantTo("Create a new user");
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user', ['name' => 'Bob Dond', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->dontSeeResponseContains('errors');
        $I->seeResponseContains('data');
    }

    // TEST USER READING
    public function readUserNotInteger(ApiTester $I)
    {
        $I->wantTo("Try to read a user with a non-numeric ID");
        $I->sendGET('/user/invalid');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Not Found');
    }

    public function readUserNotExist(ApiTester $I)
    {
        $I->wantTo("Read a user that does not exist");
        $I->sendGET('/user/3');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('User not found');
    }
    
    public function readUserExists(ApiTester $I)
    {
        $I->wantTo("Read a user that does exist");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->sendGET('/user/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContains('errors');
        $I->seeResponseContains('data');
        $I->seeResponseContains('readUserExists Jones');
    }

    // TEST USER UPDATE
    public function updateUserNotInteger(ApiTester $I)
    {
        $I->wantTo("Try to update a user with a non-numeric ID");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/invalid', ['name' => 'Bob Dond', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Not Found');
    }

    public function updateUserNonJson(ApiTester $I)
    {
        $I->wantTo("Check input format is checked for update user endpoint");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->sendPOST('/user/1', ['name' => 'Bob Dond', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::UNSUPPORTED_MEDIA_TYPE);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
    }

    public function updateUserNotExist(ApiTester $I)
    {
        $I->wantTo("Update a user that does not exist");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/3', ['name' => 'Bob Dond', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('User not found');
    }

    public function updateUserBadData(ApiTester $I)
    {
        $I->wantTo("Check bad input data is rejected with appropriate error message");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/user/1', ['name' => '', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('name is required');
    }

    public function updateUserBadEmailAddress(ApiTester $I)
    {
        $I->wantTo("Check bad email addresses are rejected with appropriate error message");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/user/1', ['name' => 'Bob Dond', 'email' => 'bob_dond.com']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Please enter a correct email address');
    }

    public function updateUserMissingField(ApiTester $I)
    {
        $I->wantTo("Check missing field is rejected with appropriate error message");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/1', ['name' => 'Bob Dond']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseContains('Update requests require \'email\'');
    }

    public function updateUserSuccess(ApiTester $I)
    {
        $I->wantTo("Update a user that does exist");
        $I->haveRecord(
            'Model\Entity\Users',
            [
                'userId' => 1, 'name' => 'readUserExists Jones', 'email' => 'my@example.com'
            ]
        );
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/1', ['name' => 'Bob Dond', 'email' => 'bob@dond.com']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContains('errors');
        $I->seeResponseContains('data');
        $I->dontSeeResponseContains('readUserExists Jones');
        $I->seeResponseContains('Bob Dond');
    }
}
