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
abstract class ZoneModule {

	/**
	 * Return an array with feature information
	 *
	 * @return array feature list
	 */
	abstract public function getFeatures();

	/**
	 * Give an instance with the given config
	 *
	 * @param array $config
	 * @return ZoneModule zone module instance
	 * @throws ModuleConfigException on errors
	 */
	abstract public static function getInstance($config);

	/**
	 * Get a record by its unique ID
	 *
	 * @param integer $recordid ID of the record
	 * @return ResourceRecord the record (or null if it doesn't exist?)
	 */
	abstract public function getRecordById(Zone $zone,$recordid);

	/**
	 * Get a zone by full name
	 *
	 * @param string $zonename the name of the zone
	 * @return Zone the zone (or null if it doesn't exist)
	 */
	public function getZoneByName($zonename) {
		$tmpZone = new Zone($zonename,$this);
		if ($this->zoneExists($zone)) {
			return $tmpZone;
		}
		else {
			return null;
		}
	}

	public final function hasViews() {
		if ($this instanceof Views) {
			$views = $this->listViews();
			if (is_array($views) && count($views) > 1) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public function incrementSerial(Zone $zone) {
		$records = $this->listRecordsByType($zone,'SOA');
		if (is_array($records) && count($records) > 0) {
			$recordId = key($records);
			$soa = $records[$recordId];
			$aSerial = intval(date('Ymd00'));
			$newSerial = max((intval($soa->getField('serial'))+1),$aSerial);
			$soa->setField('serial',$newSerial);
			if ($this->recordUpdate($zone,$recordId,$soa)) {
				return $newSerial;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Get all records from all zones
	 *
	 * @return ResourceRecord[] the records
	 */
	public function listRecords(Zone $zone, $offset = 0, $limit = null) {
		return $this->listRecordsByFilter($zone,$offset,$limit);
	}

	/**
	 * Give a list of records with specified filter criteria
	 *
	 * @param Zone $zone zone object
	 * @param array $filter filter criteria
	 * @return ResourceRecord[] array with resource records
	 */
	abstract public function listRecordsByFilter(Zone $zone,array $filter = array(), $offset = 0, $limit = null);

	/**
	 * Give all records with a specified name
	 *
	 * @param Zone $zone zone object
	 * @param string $name name to search for
	 * @return ResourceRecord[] array with resource records
	 */
	public function listRecordsByname(Zone $zone,$name, $offset = 0, $limit = null) {
		return $this->listRecordsByFilter($zone,array('name' => $name),$offset,$limit);
	}

	/**
	 * Get records of a specific type from all zones
	 *
	 * @param string $type record type to search for
	 * @return ResourceRecord[] matching records
	 */
	public function listRecordsByType(Zone $zone,$type, $offset = 0, $limit = null) {
		return $this->listRecordsByFilter($zone,array('type' => $type),$offset,$limit);
	}

	/**
	 * Get all zones
	 *
	 * @return Zone[] all zones
	 */
	abstract public function listZones();

	/**
	 * Add a new record to a specific zone
	 *
	 * @param Zone $zone zone to add record to
	 * @param ResourceRecord $record record to add
	 * @return false or id of new record
	 */
	abstract public function recordAdd(Zone $zone,ResourceRecord $record);

	/**
	 * Delete a record from a specific zone
	 *
	 * @param Zone $zone zone to add record to
	 * @param integer $recordid ID of the record to delete
	 * @return boolean true on success, false otherwise?
	 */
	abstract public function recordDelete(Zone $zone, $recordid);

	/**
	 * Overwrite a specific record
	 *
	 * @param Zone $zone zone to add record to
	 * @param integer $recordid ID of the record to update
	 * @param ResourceRecord $record new record
	 * @return boolean true on success, false otherwise?
	 */
	abstract public function recordUpdate(Zone $zone, $recordid, ResourceRecord $record);

	/**
	 * Create a new zone in this server
	 *
	 * @param Zone $zone zone object to create here
	 * @return boolean success true/false
	 */
	abstract public function zoneCreate(Zone $zone);

	/**
	 * Make the script explode if a zone doesn't exist
	 *
	 * @param Zone $zone zone object
	 * @return boolean alway true - or a fat BOOOM
	 */
	protected function zoneAssureExistence(Zone $zone) {
		if (!$this->zoneExists($zone)) throw new NoSuchZoneException('No zone '.$zone->getName().' here!');
		return true;
	}

	/**
	 * Delete a zone
	 *
	 * @param Zone $zone zone to remove
	 * @return boolean true on success, false otherwise?
	 */
	abstract public function zoneDelete(Zone $zone);

	/**
	 * Check if a zone exists
	 *
	 * @param Zone $zone
	 * @return true if the zone exists, false otherwise
	 */
	abstract public function zoneExists(Zone $zone);
}

?>