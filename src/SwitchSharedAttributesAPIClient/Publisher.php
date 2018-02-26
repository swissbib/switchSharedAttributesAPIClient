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

    protected $wayflessUrl;

    protected $description;

    protected $switchGroupId;

    protected $librariesWithContract;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return mixed
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getWayflessUrl()
    {
        return $this->wayflessUrl;
    }

    /**
     * @param mixed $wayflessUrl
     *
     * @return mixed
     */
    public function setWayflessUrl($wayflessUrl)
    {
        $this->wayflessUrl = $wayflessUrl;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return mixed
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSwitchGroupId()
    {
        return $this->switchGroupId;
    }

    /**
     * @param mixed $switchGroupId
     *
     * @return mixed
     */
    public function setSwitchGroupId($switchGroupId)
    {
        $this->switchGroupId = $switchGroupId;
    }

    /**
     * @return mixed
     */
    public function getLibrariesWithContract()
    {
        return $this->librariesWithContract;
    }

    /**
     * @param mixed $librariesWithContract
     *
     * @return mixed
     */
    public function setLibrariesWithContract($librariesWithContract)
    {
        $this->librariesWithContract = $librariesWithContract;
    }
    
    
    
}