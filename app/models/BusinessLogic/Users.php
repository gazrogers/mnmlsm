<?php
namespace Model\BusinessLogic;

use Phalcon\Di\Injectable;

use Library\Exceptions\BadRequest;
use Library\Exceptions\NotFound;
use Model\Entity\Users as UsersModel;

class Users extends Injectable
{
    /**
     * Create a new user
     *
     * @param array $inputData the data sent by the user
     * 
     * @return Model\Entity\Users the new user model
     */
    public function create(array $inputData): UsersModel
    {
        $user = new UsersModel(
            [
                'name' => $inputData['name'],
                'email' => $inputData['email']
            ]
        );
        if($user->create())
        {
            return UsersModel::findFirst($user->userId);
        }
        else
        {
            $errorMessages = implode(", ", $user->getMessages());
            throw new BadRequest($errorMessages);
        }
    }

    /**
     * Return the requested user
     * 
     * @param int $userId the user ID
     * 
     * @return Model\Entity\Users the user
     */
    public function read(int $userId): UsersModel
    {
        $user = UsersModel::findFirst($userId);
        if(!$user)
        {
            throw new NotFound("User not found");
        }

        return $user;
    }

    /**
     * Update the requested user's details
     * 
     * @param int   $userId    the user ID
     * @param array $inputData the updated details
     * 
     * @return Model\Entity\Users the updated user
     */
    public function update(int $userId, array $inputData): UsersModel
    {
        $user = UsersModel::findFirst($userId);
        if(!$user)
        {
            throw new NotFound("User not found");
        }

        $user->assign(
            [
                'name' => $inputData['name'],
                'email' => $inputData['email']
            ]
        );
        if($user->update())
        {
            return UsersModel::findFirst($user->userId);
        }
        else
        {
            $errorMessages = implode(", ", $user->getMessages());
            throw new BadRequest($errorMessages);
        }
    }
}
