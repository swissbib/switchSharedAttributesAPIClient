<?php
/**
 * Client for SWITCH Edu-ID Shared Attributes API
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.ch
 */
namespace SwitchSharedAttributesAPIClient;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Class SwitchSharedAttributesAPIClient
 *
 * @category Swissbib
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.ch
 */
class SwitchSharedAttributesAPIClient
{
    /**
     * Switch Api configuration.
     *
     * @var array containing host, National Licence group id, ...
     */
    protected $configSwitchApi;

    /**
     * SwitchApi constructor.
     *
     * @param array $config Configuration (username, password,
     *                      national_licence_programme_group_id,
     *                      base_endpoint_url)
     *
     * @throws \Exception
     */
    public function __construct($config)
    {
        if (isset($config['auth_user'])) {
            $this->configSwitchApi['auth_user'] = $config['auth_user'];
        } else {
            throw new \Exception(
                'Was not possible to find the SWITCH API ' .
                'auth_user.'
            );
        }

        if (isset($config['auth_password'])) {
            $this->configSwitchApi['auth_password'] = $config['auth_password'];
        } else {
            throw new \Exception(
                'Was not possible to find the SWITCH API ' .
                'auth_password.'
            );
        }

        if (isset($config['base_endpoint_url'])) {
            $this->configSwitchApi['base_endpoint_url']
                = $config['base_endpoint_url'];
        } else {
            throw new \Exception(
                'Was not possible to find the SWITCH API ' .
                'base_endpoint_url.'
            );
        }

        //national_licence_programme_group_id is not mandatory
        if (isset($config['national_licence_programme_group_id'])) {
            $this->configSwitchApi['national_licence_programme_group_id']
                = $config['national_licence_programme_group_id'];
        }

        $this->configSwitchApi['schema_patch']
            = 'urn:ietf:params:scim:api:messages:2.0:PatchOp';
        $this->configSwitchApi['operation_add']
            = 'add';
        $this->configSwitchApi['operation_remove']
            = 'remove';
        $this->configSwitchApi['path_member']
            = 'members';
    }

    /**
     * Set national-licence-compliant flag to the user.
     *
     * @param string $userExternalId External id
     *
     * @return void
     * @throws \Exception
     */
    public function setNationalCompliantFlag($userExternalId)
    {
        // 1 create a user
        $internalId = $this->createSwitchUser($userExternalId);
        // 2 Add user to the National Compliant group
        $this->addUserToNationalCompliantGroup($internalId);
        // 3 verify that the user is on the National Compliant group
        if (!$this->userIsOnNationalCompliantSwitchGroup($userExternalId)) {
            throw new \Exception(
                'Was not possible to add user to the ' .
                'national-licence-compliant group'
            );
        }
    }

    /**
     * Add a Switch edu-ID user to a group, for example to set
     * common-lib-terms for a given publisher
     *
     * @param string $userExternalId EduId number like 169330697816@eduid.ch
     * @param string $publisherId    The id of the group in Switch API
     *
     * @return void
     * @throws \Exception
     */
    public function activatePublisherForUser($userExternalId, $publisherId)
    {
        // 1 create a user
        $internalId = $this->createSwitchUser($userExternalId);
        // 2 Add user to the group
        $this->addUserToGroup($internalId, $publisherId);
        // 3 verify that the user is on the National Compliant group
        if (!$this->userIsOnGroup($userExternalId, $publisherId)) {
            throw new \Exception(
                'Was not possible to add user to the ' .
                'publisher group ' . $publisherId
            );
        }
    }

    /**
     * Create a user in the National Licenses registration platform.
     *
     * @param string $externalId External id
     *
     * @return mixed
     * @throws \Exception
     */
    protected function createSwitchUser($externalId)
    {
        $client = $this->getBaseClient(Request::METHOD_POST, '/Users');
        $params = ['externalID' => $externalId];
        $client->setRawBody(json_encode($params, JSON_UNESCAPED_SLASHES));
        /**
         * Response.
         *
         * @var Response $response
         */
        $response = $client->send();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        if ($statusCode !== 200) {
            throw new \Exception("Status code: $statusCode result: $body");
        }
        $res = json_decode($body);

        return $res->id;
    }

    /**
     * Get an instance of the HTTP Client with some basic configuration.
     *
     * @param string $method   Method
     * @param string $relPath  Relative path
     * @param string $basePath The base path
     *
     * @return Client
     * @throws \Exception
     */
    protected function getBaseClient(
        $method = Request::METHOD_GET,
        $relPath = '', $basePath = null
    ) {
        if (empty($basePath)) {
            $basePath = $this->configSwitchApi['base_endpoint_url'];
        }
        $client = new Client(
            $basePath . $relPath, [
                'maxredirects' => 0,
                'timeout' => 30,
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
            ]
        );
        //echo $client->getUri();
        $client->setHeaders(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        );
        $client->setMethod($method);
        $username = $this->configSwitchApi['auth_user'];
        $passw = $this->configSwitchApi['auth_password'];
        if (empty($username) || empty($passw)) {
            throw new \Exception(
                'Was not possible to find the SWITCH API ' .
                'credentials.'
            );

        }
        $client->setAuth($username, $passw);

        return $client;
    }

    /**
     * Add user to the National Licenses Programme group on the National Licenses
     * registration platform.
     *
     * @param string $userInternalId User internal id
     *
     * @return void
     * @throws \Exception
     */
    protected function addUserToNationalCompliantGroup($userInternalId)
    {
        $this->addUserToGroup(
            $userInternalId,
            $this->configSwitchApi['national_licence_programme_group_id']
        );
    }

    /**
     * Add user to the National Licenses Programme group on the National Licenses
     * registration platform.
     *
     * @param string $userInternalId User internal id
     * @param string $groupId        Group id
     *
     * @return void
     * @throws \Exception
     */
    protected function addUserToGroup($userInternalId, $groupId)
    {
        $client = $this->getBaseClient(
            Request::METHOD_PATCH, '/Groups/' .
            $groupId
        );
        $params = [
            'schemas' => [
                $this->configSwitchApi['schema_patch'],
            ],
            'Operations' => [
                [
                    'op' => $this->configSwitchApi['operation_add'],
                    'path' => $this->configSwitchApi['path_member'],
                    'value' => [
                        [
                            '$ref' => $this->configSwitchApi['base_endpoint_url'] .
                                '/Users/' .
                                $userInternalId,
                            'value' => $userInternalId,
                        ],
                    ],
                ],
            ],
        ];
        //$str = json_encode($params, JSON_PRETTY_PRINT);
        //echo "<pre> $str < /pre>";
        $rawData = json_encode($params, JSON_UNESCAPED_SLASHES);
        $client->setRawBody($rawData);
        $response = $client->send();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        if ($statusCode !== 200) {
            throw new \Exception("Status code: $statusCode result: $body");
        }
    }

    /**
     * Check if the user is on the National Licenses Programme group.
     *
     * @param string $userExternalId User external id
     *
     * @return bool
     * @throws \Exception
     */
    public function userIsOnNationalCompliantSwitchGroup($userExternalId)
    {
        return $this->userIsOnGroup(
            $userExternalId,
            $this->configSwitchApi['national_licence_programme_group_id']
        );
    }

    /**
     * Check if the user is on the group $groupId
     *
     * @param string $userExternalId User external id
     * @param string $groupId        Group Id
     *
     * @return bool
     * @throws \Exception
     */
    public function userIsOnGroup($userExternalId, $groupId)
    {
        $internalId = $this->createSwitchUser($userExternalId);
        $switchUser = $this->getSwitchUserInfo($internalId);
        foreach ($switchUser->groups as $group) {
            if ($group->value === $groupId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user info from the National Licenses registration platform.
     *
     * @param string $internalId User external id
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getSwitchUserInfo($internalId)
    {
        $client = $this->getBaseClient(Request::METHOD_GET, '/Users/' . $internalId);
        $response = $client->send();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        if ($statusCode !== 200) {
            throw new \Exception("Status code: $statusCode result: $body");
        }
        $res = json_decode($body);

        return $res;
    }

    /**
     * Unset the national compliant flag from the user.
     *
     * @param string $userExternalId User external id
     *
     * @return void
     * @throws \Exception
     */
    public function unsetNationalCompliantFlag($userExternalId)
    {
        $this->removeUserFromGroupAndVerify(
            $userExternalId,
            $this->configSwitchApi['national_licence_programme_group_id']
        );
    }

    /**
     * Remove user from group.
     *
     * @param string $userExternalId User external id
     * @param string $groupId        Group id
     *
     * @return void
     * @throws \Exception
     */
    public function removeUserFromGroupAndVerify($userExternalId, $groupId)
    {
        // 1 create a user
        $internalId = $this->createSwitchUser($userExternalId);
        // 2 Add user to the National Compliant group
        $this->removeUserFromGroup($internalId, $groupId);
        // 3 verify that the user is not any more in the group
        if ($this->userIsOnGroup($userExternalId, $groupId)) {
            throw new \Exception(
                'Was not possible to remove the user' .
                $userExternalId . 'from the group' .
                $groupId
            );
        }
    }

    /**
     * Remove a national licence user from the national-licence-programme-group.
     *
     * @param string $userInternalId User internal id
     *
     * @return void
     * @throws \Exception
     */
    protected function removeUserFromNationalCompliantGroup($userInternalId)
    {
        $this->removeUserFromGroup(
            $userInternalId,
            $this->configSwitchApi['national_licence_programme_group_id']
        );
    }

    /**
     * Remove a user from a publisher group.
     *
     * @param string $userInternalId User internal id
     * @param string $groupId        Group Id
     *
     * @return void
     * @throws \Exception
     */
    protected function removeUserFromGroup($userInternalId, $groupId)
    {
        $client = $this->getBaseClient(
            Request::METHOD_PATCH,
            '/Groups/' . $groupId
        );
        $params = [
            'schemas' => [
                $this->configSwitchApi['schema_patch'],
            ],
            'Operations' => [
                [
                    'op' => $this->configSwitchApi['operation_remove'],
                    'path' => $this->configSwitchApi['path_member'] .
                        "[value eq \"$userInternalId\"]",
                ],
            ],
        ];

        $rawData = json_encode($params, JSON_UNESCAPED_SLASHES);
        $client->setRawBody($rawData);
        $response = $client->send();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        if ($statusCode !== 200) {
            throw new \Exception("Status code: $statusCode result: $body");
        }
    }
}
