<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ROLE' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.map
 */
class RoleMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.RoleMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('NEEScentral');

		$tMap = $this->dbMap->addTable('ROLE');
		$tMap->setPhpName('Role');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ROLE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DEFAULT_PERMISSIONS', 'DefaultPermissionsStr', 'string', CreoleTypes::VARCHAR, false, 112);

		$tMap->addForeignKey('ENTITY_TYPE_ID', 'EntityTypeId', 'double', CreoleTypes::NUMERIC, 'ENTITY_TYPE', 'ID', false, 22);

		$tMap->addColumn('DISPLAY_NAME', 'DisplayName', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('SYSTEM_NAME', 'SystemName', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('SUPER_ROLE', 'SuperRole', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('DEFAULT_PERMISSIONS', 'maxLength', 'propel.validator.MaxLengthValidator', '112', 'DEFAULT_PERMISSIONS');

		$tMap->addValidator('DEFAULT_PERMISSIONS', 'required', 'propel.validator.RequiredValidator', '', 'DEFAULT_PERMISSIONS');

		$tMap->addValidator('ENTITY_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('DISPLAY_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '255', 'NAME');

		$tMap->addValidator('DISPLAY_NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SYSTEM_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '255', 'NAME');

		$tMap->addValidator('SYSTEM_NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SUPER_ROLE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SUPER_ROLE');

		$tMap->addValidator('SUPER_ROLE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SUPER_ROLE');

		$tMap->addValidator('SUPER_ROLE', 'required', 'propel.validator.RequiredValidator', '', 'SUPER_ROLE');

	} // doBuild()

} // RoleMapBuilder
