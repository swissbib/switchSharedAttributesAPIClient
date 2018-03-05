<?php
/**
 * PuraSwitchClientTest
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 27.02.18
 * Time: 09:15
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

use Zend\Config\Config;
use Zend\Config\Reader\Ini as IniReader;

/**
 * PuraSwitchClientTest
 *
 * @category Swissbib_VuFind2
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class PuraSwitchClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The Publishers List for the tests
     *
     * @var PublishersList $publishersList
     */
    protected $publishersList;

    /**
     * The API client to use for the tests
     *
     * @var PuraSwitchClient $puraSwitchClient
     */
    protected $puraSwitchClient;

    /**
     * The id we use for the tests
     *
     * @var string $externalIdTest
     */
    protected $externalIdTest;

    /**
     * Set Up
     *
     * @throws \Exception
     *
     * @return void
     */
    public function setUp()
    {
        $this->publishersList = new PublishersList();


        $filePath = __DIR__ .  '/fixtures/publisher-libraries.json';

        $publishersJsonData
            = file_exists($filePath) ? file_get_contents($filePath) : '';

        $this->publishersList->loadPublishersFromJsonFile($publishersJsonData);

        $iniReader = new IniReader();

        $configFull = new Config(
            $iniReader->fromFile(__DIR__ .  '/fixtures/SwitchApi.ini')
        );
        $configSwitchAPI = $configFull['SwitchApi'];

        $configIni = new Config(
            $iniReader->fromFile(__DIR__ .  '/fixtures/config.ini')
        );
        $credentials = $configIni['SwitchApiCredentials'];

        $config = array_merge($credentials->toArray(), $configSwitchAPI->toArray());

        $this->puraSwitchClient = new PuraSwitchClient(
            $config,
            $this->publishersList
        );

        $this->externalIdTest = $configSwitchAPI['external_id_test'];

    }

    /**
     * Test ActivatePublishers
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testActivatePublishers()
    {
        $this->puraSwitchClient->activatePublishers($this->externalIdTest, 'Z01');
        $this->assertEquals(
            true,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                'f4d40595-6d7d-41bc-9fa2-7139d2fcf892'
            )
        );
        $this->assertEquals(
            false,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                'fffff'
            )
        );
        $this->assertEquals(
            true,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                '2c0ddd57-5172-412a-9a57-30e85d79ea40'
            )
        );
    }

    /**
     * Test deactivatePublishers
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testDeactivatePublishers()
    {
        $this->puraSwitchClient->activatePublishers(
            $this->externalIdTest,
            'Z01'
        );
        $this->puraSwitchClient->activatePublishers(
            $this->externalIdTest,
            'RE01001'
        );
        $this->puraSwitchClient->deactivatePublishers(
            $this->externalIdTest,
            'Z01',
            ['RE01001']
        );

        //user should still have access to Cambridge
        //as it is licensed for Z01 and RE01001
        $this->assertEquals(
            true,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                'f4d40595-6d7d-41bc-9fa2-7139d2fcf892'
            )
        );

        //no more access to thieme
        $this->assertEquals(
            false,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                '2c0ddd57-5172-412a-9a57-30e85d79ea40'
            )
        );
        $this->puraSwitchClient->deactivatePublishers(
            $this->externalIdTest,
            'Z01'
        );
        $this->puraSwitchClient->deactivatePublishers(
            $this->externalIdTest,
            'RE01001'
        );
        $this->assertEquals(
            false,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                'f4d40595-6d7d-41bc-9fa2-7139d2fcf892'
            )
        );
        $this->assertEquals(
            false,
            $this->puraSwitchClient->userIsOnGroup(
                $this->externalIdTest,
                '2c0ddd57-5172-412a-9a57-30e85d79ea40'
            )
        );

    }
}
