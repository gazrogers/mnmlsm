<?php
declare(strict_types=1);

namespace Controller;

use Library\Exceptions\BadRequest;
use Model\BusinessLogic\Users;

class UserController extends \Phalcon\Mvc\Controller
{
    /**
     * Create a new user based on the given data
     * 
     * @return array the new user info
     */
    public function create()
    {
        $inputData = json_decode($this->di->get('request')->getRawBody(), true);
        $this->validateInput($inputData, 'create');

        $user = new Users();
        $newUser = $user->create($inputData);

        return ['data' => [$newUser->toArray()]];
    }

    /**
     * Fetch a user by its ID
     * 
     * @param int $userId the user ID
     * 
     * @return array the user data
     */
    public function read(int $userId)
    {
        $user = new Users();
        $readUser = $user->read($userId);

        return ['data' => [$readUser->toArray()]];
    }

    /**
     * Update details of the requested user
     * 
     * @param int $userId the ID of the user to update
     * 
     * @return array the user info
     */
    public function update(int $userId)
    {
        $inputData = json_decode($this->di->get('request')->getRawBody(), true);
        $this->validateInput($inputData, 'update');

        $user = new Users();
        $updatedUser = $user->update($userId, $inputData);

        return ['data' => [$updatedUser->toArray()]];
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
            'create' => ['name', 'email'],
            'update' => ['name', 'email']
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

