<?php

/**
 * Publisher
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 26.02.18
 * Time: 14:10
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
 * Publisher
 *
 * @category Swissbib_VuFind2
 * @package  SwitchSharedAttributesAPIClient
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Publisher
{
    protected $name;

    protected $url;

    protected $infoUrl;

    protected $description;

    protected $switchGroupId;

    protected $librariesWithContract;

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set Url
     *
     * @param mixed $url url*
     *
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get Info Url
     *
     * @return string info url
     */
    public function getInfoUrl()
    {
        return $this->infoUrl;
    }

    /**
     * Set Info Url
     *
     * @param string $infoUrl info url
     *
     * @return void
     */
    public function setInfoUrl($infoUrl)
    {
        $this->infoUrl = $infoUrl;
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description
     *
     * @param string $description Description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get Switch Group Id
     *
     * @return string
     */
    public function getSwitchGroupId()
    {
        return $this->switchGroupId;
    }

    /**
     * Set Switch Group Id
     *
     * @param string $switchGroupId switch group id
     *
     * @return void
     */
    public function setSwitchGroupId($switchGroupId)
    {
        $this->switchGroupId = $switchGroupId;
    }

    /**
     * Get the libraries which have contracts with this publisher
     *
     * @return array the library codes of the libraries
     */
    public function getLibrariesWithContract()
    {
        return $this->librariesWithContract;
    }

    /**
     * Set Libraries with contract
     *
     * @param array $librariesWithContract library codes of the libraries
     *
     * @return void
     */
    public function setLibrariesWithContract($librariesWithContract)
    {
        $this->librariesWithContract = $librariesWithContract;
    }
}
