<?php
/**
 * PublishersListTest
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 26.02.18
 * Time: 14:37
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
 * @package  Swissbib
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */

/**
 * PublishersListTest
 *
 * @category Swissbib_VuFind2
 * @package  ${PACKAGE}
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
namespace SwitchSharedAttributesAPIClient;

/**
 * PublishersListTest
 *
 * @category Swissbib
 * @package  Swissbib
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class PublishersListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Publishers List
     *
     * @var PublishersList $publishersList
     */
    protected $publishersList;

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

        $filePath = __DIR__ . '/fixtures/publisher-libraries.json';

        $publishersJsonData
            = file_exists($filePath) ? file_get_contents($filePath) : '';

        $this->publishersList->loadPublishersFromJsonFile($publishersJsonData);

    }

    /**
     * Test load Publishers
     *
     * @return void
     */
    public function testLoadPublishersFromJsonFile()
    {

        $publishers = $this->publishersList->getPublishers();

        $this->assertEquals(
            "Cambridge University Press",
            $publishers[0]->getName()
        );
        $this->assertEquals(
            "https://www.cambridge.org/core/journals/",
            $publishers[0]->getInfoUrl()
        );
        $this->assertEquals(
            ["Z01", "RE01001"],
            $publishers[0]->getLibrariesWithContract()
        );
        $this->assertEquals(
            "Thieme",
            $publishers[2]->getShortName()
        );
    }

    /**
     * Test get Publishers for a library
     *
     * @return void
     */
    public function testGetPublishersForALibrary()
    {
        /**
         * Publishers List
         *
         * @var PublishersList $publishersZ01List
         */
        $publishersZ01List = $this->publishersList->getPublishersForALibrary("Z01");
        $publishersZ01 = $publishersZ01List->getPublishers();

        $this->assertEquals(
            "Cambridge University Press",
            $publishersZ01[0]->getName()
        );
        $this->assertEquals(
            "Thieme long title",
            $publishersZ01[1]->getName()
        );
        $this->assertEquals(
            $publishersZ01List->numberOfPublishers(),
            2
        );
    }
}
