<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

class BearCMSTestCase extends BearFramework\AddonTests\PHPUnitTestCase
{

    protected function initializeApp(array $config = []): void
    {
        parent::initializeApp($config);
        $app = $this->getApp();
        $app->bearCMS->initialize([
            'serverUrl' => 'https://dummy.bearcms.com/',
            'appSecretKey' => 'dummy1'
        ]);
    }

    /**
     * 
     * @return string Returns the new user ID
     */
    protected function createUser(): string
    {
        $app = $this->getApp();
        $userID = uniqid();
        $userData = '{
    "id": "' . $userID . '",
    "registerTime": 1234567890,
    "lastLoginTime": 1234567890,
    "hashedPassword": "aaa",
    "emails": [
        "john@gmail.com"
    ],
    "permissions": [
        "modifyContent",
        "managePages",
        "manageAppearance",
        "manageBlog",
        "manageFiles",
        "manageAddons",
        "manageAdministrators",
        "manageSettings",
        "viewAboutInformation"
    ]
}';
        $app->data->setValue('bearcms/users/user/' . md5($userID) . '.json', $userData);
        return $userID;
    }

    /**
     * 
     * @param string $userID
     * @return string Returns the logged in user session key
     */
    protected function loginUser(string $userID): string
    {
        $app = $this->getApp();
        $sessionKey = str_repeat(uniqid(), 90 / strlen(uniqid()));
        \BearCMS\Internal\Cookies::setList(\BearCMS\Internal\Cookies::TYPE_SERVER, [['name' => '_s', 'value' => $sessionKey, 'expire' => time() + 86400]]);
        $app->data->setValue('.temp/bearcms/userkeys/' . md5($sessionKey), $userID);
        return $sessionKey;
    }

    /**
     * 
     * @return void
     */
    protected function logoutUser(): void
    {
        $app = $this->getApp();
        $app->bearCMS->currentUser->logout();
    }

    /**
     * 
     * @return void
     */
    protected function createAndLoginUser(): void
    {
        $userID = $this->createUser();
        $this->loginUser($userID);
    }

}
