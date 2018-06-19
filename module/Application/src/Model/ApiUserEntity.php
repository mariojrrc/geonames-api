<?php

namespace Application\Model;

use Realejo\Stdlib\ArrayObject;

/**
 * @property integer idApiUser
 * @property string name
 * @property ApiUserStatus status
 * @property ApiUserRole role
 * @property string token
 * @property string ip
 *
 * joinLeft
 * @property string userName
 * @property string userEmail
 *
 */
class ApiUserEntity extends ArrayObject
{
    protected $mappedKeys = [
        'id_apiuser' => 'idApiUser',
        'user_name' => 'userName',
        'user_email' => 'userEmail',
    ];

    protected $intKeys = [
        'id_apiuser'
    ];

    protected $enumKeys = array(
        'role' => ApiUserRole::class,
        'status' => ApiUserStatus::class
    );
}
