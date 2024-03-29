<?php

/*
 * This file is part of phpDNSAdmin.
 * (c) 2010 Matthias Lohr - http://phpdnsadmin.sourceforge.net/
 *
 * phpDNSAdmin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpDNSAdmin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phpDNSAdmin. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package phpDNSAdmin
 * @subpackage Core
 * @author Matthias Lohr <mail@matthias-lohr.net>
 */

/**
 * @package phpDNSAdmin
 * @subpackage Core
 * @author Matthias Lohr <mail@matthias-lohr.net>
 */
class Configuration {

	private static $instance = null;

	private $config = array();

	protected function __construct($configuration) {
		$this->config = $configuration;
	}

	public function debugMode() {
		if (isset($this->config['internal']) && isset($this->config['internal']['debug'])) return $this->config['internal']['debug'];
		return false;
	}

	public function getAuthenticationConfig() {
		return $this->config['authentication'];
	}

	public function getAuthorizationConfig() {
		return $this->config['authorization'];
	}

	public function getAutologinConfig() {
		return $this->config['autologin'];
	}

	/**
	 *
	 * @return Configuration configuration class
	 */
	public static function getInstance() {
		return self::$instance;
	}

	public function getZoneConfig() {
		return $this->config['zone'];
	}

	public static function load($filename) {
		require($filename);
		self::$instance = new Configuration($config);
		return self::$instance;
	}

}

?>