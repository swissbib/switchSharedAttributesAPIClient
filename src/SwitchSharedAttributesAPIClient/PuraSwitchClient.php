<?php

/**
 * PuraSwitchClient
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 26.02.18
 * Time: 16:25
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwitchSharedAttributesAPIClient;

/**
 * PuraSwitchClient
 *
 * @category Swissbib_VuFind2
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class PuraSwitchClient extends SwitchSharedAttributesAPIClient
{
    /**
     * Publishers List
     *
     * @var PublishersList $publisherList
     */
    protected $publishersList;

    /**
     * PuraSwitchClient constructor.
     *
     * @param array          $config         Config(username, password, url)
     * @param PublishersList $publishersList Publishers List
     *
     * @throws \Exception
     */
    public function __construct($config, $publishersList)
    {
        parent::__construct($config);
        $this->publishersList = $publishersList;
    }

    /**
     * Activate all publishers for this user and this library
     *
     * @param string $userExternalId EduId number like 169330697816@eduid.ch
     * @param string $libraryCode    library code
     *
     * @return array Success Status and Message
     */
    public function activatePublishers($userExternalId, $libraryCode)
    {
        /**
         * Publishers List
         *
         * @var PublishersList $libraryPublisherList
         */
        $libraryPublisherList = $this->publishersList
            ->getPublishersForALibrary($libraryCode);

        /**
         * Publisher
         *
         * @var Publisher $publisher
         */
        try{
            foreach ($libraryPublisherList as $publisher) {
                $this->activatePublisherForUser(
                    $userExternalId,
                    $publisher->getSwitchGroupId()
                );
            }
        } catch (\Exception $e) {
            return [
                'success' => 'false',
                'message' => $e->getMessage(),
            ];
        }

        $publishersActivated = [];
        foreach ($libraryPublisherList as $publisher) {
            $publishersActivated[] = $publisher->getName();
        }

        $message
            = 'Publishers activated : ' .
            implode(", ", $publishersActivated) .
            ".";

        return [
            'success' => 'true',
            'message' => $message,
        ];
    }

    /**
     * Deactivate all Publishers from $libraryCode for the user. If the user
     * is also registered in the otherLibraries and some of the other libraries
     * also have a contract with the same publisher, we don't deactivate this
     * publisher
     *
     * @param string $userExternalId EduId number like 169330697816@eduid.ch
     * @param string $libraryCode    Library Code
     * @param array  $otherLibraries Other Libraries where the user is registered
     *
     * @return array
     */
    public function deactivatePublishers(
        $userExternalId,
        $libraryCode,
        $otherLibraries = []
    ) {
        /**
         * Publishers List
         *
         * @var PublishersList $libraryPublisherList Publishers List for this Library
         */
        $libraryPublisherList = $this->publishersList
            ->getPublishersForALibrary($libraryCode);

        /**
         * Publisher
         *
         * @var Publisher $publisher publisher
         */
        try{
            foreach ($libraryPublisherList as $publisher) {
                $this->removeUserFromGroupAndVerify(
                    $userExternalId,
                    $publisher->getSwitchGroupId()
                );
            }
        } catch (\Exception $e) {
            return [
                'success' => 'false',
                'message' => $e->getMessage(),
            ];
        }

        //now we reactivate the other libraries to make sure we didn't deactivate
        //a publisher where the user is registered with another library

        try {
            foreach ($otherLibraries as $library) {
                $this->activatePublishers($userExternalId, $library);
            }
        } catch (\Exception $e) {
            return [
                'success' => 'false',
                'message' => $e->getMessage(),
            ];
        }


        $message = '';
        if (empty($otherLibraries)) {
            $publishersDeactivated = [];
            foreach ($libraryPublisherList as $publisher) {
                $publishersDeactivated[] = $publisher->getName();
            }

            $message
                = 'Publishers deactivated : ' .
                implode(", ", $publishersDeactivated) .
                ".";
        } else {
            $message = 'This user is also registered with ' .
                'other pura libraries. Only the publishers ' .
                'unique to your library have been deactivated.';
        }

        return [
            'success' => 'true',
            'message' => $message,
        ];
    }
}