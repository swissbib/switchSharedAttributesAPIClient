<?php

/**
 * PublishersList
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 26.02.18
 * Time: 14:16
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

use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * PublishersList
 *
 * @category Swissbib_VuFind2
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class PublishersList implements \IteratorAggregate
{
    /**
     * List of Publishers
     *
     * @var array
     */
    protected $publishers = [];

    /**
     * Load the publishers contracts from a Json Data
     *
     * @param  string $publishersJsonData publishers contracts in Json
     *
     * @throws \Exception
     */
    public function loadPublishersFromJsonFile(string $publishersJsonData)
    {
        $publishers = json_decode($publishersJsonData, true);

        $hydrator = new ClassMethods();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());

        if (!isset($publishers['publishers'])) {
            throw new \Exception(
                "No valid publishers data supplied."
            );
        }

        foreach($publishers["publishers"] as $publisherArray) {

            $publisher = new Publisher();
            $hydrator->hydrate($publisherArray, $publisher);
            array_push($this->publishers, $publisher);
        }
    }

    /**
     * Return an array containing all publishers
     *
     * @return array
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }

    /**
     * Add a publisher to the list
     *
     * @param Publisher $publisher The publisher to add
     */
    public function addPublisher(Publisher $publisher)
    {
        array_push($this->publishers, $publisher);
    }

    /**
     * To allow foreach on PublishersList
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->publishers);
    }

    /**
     * Get the list of publishers which have a contract with this library
     *
     * @param string $libraryCode The library code, for example Z01
     *
     * @return PublishersList Publishers With Contracts with that library
     */
    public function getPublishersForALibrary($libraryCode)
    {
        $publishersWithContracts = new PublishersList();
        foreach ($this->publishers as $publisher) {
            if ($this->hasContract($libraryCode, $publisher)) {
                $publishersWithContracts->addPublisher($publisher);
            }
        }
        return $publishersWithContracts;
    }

    /**
     * Number of Publishers in the list
     *
     * @return int
     */
    public function numberOfPublishers()
    {
        return sizeof($this->publishers);
    }

    /**
     * Return true if a library has a contract with this publisher
     *
     * @param string    $libraryCode The library code, for example Z01
     * @param Publisher $publisher   The publisher
     *
     * @return bool
     */
    protected function hasContract($libraryCode, Publisher $publisher)
    {
        if (in_array($libraryCode, $publisher->getLibrariesWithContract())) {
            return true;
        } else {
            return false;
        }
    }
}
