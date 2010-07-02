<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MATERIAL_DATA_FILE' table to 'NEEScentral' DatabaseMap object.
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
class MaterialFileMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.MaterialFileMapBuilder';

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

		$tMap = $this->dbMap->addTable('MATERIAL_DATA_FILE');
		$tMap->setPhpName('MaterialFile');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MATERIAL_DATA_FILE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('DATA_FILE_ID', 'DataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addForeignKey('MATERIAL_ID', 'MaterialId', 'double', CreoleTypes::NUMERIC, 'MATERIAL', 'ID', false, 22);

		$tMap->addValidator('DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('MATERIAL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MATERIAL_ID');

		$tMap->addValidator('MATERIAL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MATERIAL_ID');

		$tMap->addValidator('MATERIAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'MATERIAL_ID');

	} // doBuild()

} // MaterialFileMapBuilder
