<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MEASUREMENT_UNIT' table to 'NEEScentral' DatabaseMap object.
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
class MeasurementUnitMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.MeasurementUnitMapBuilder';

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

		$tMap = $this->dbMap->addTable('MEASUREMENT_UNIT');
		$tMap->setPhpName('MeasurementUnit');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MEASUREMENT_UNIT_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ABBREVIATION', 'Abbreviation', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('BASE_UNIT', 'BaseUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addForeignKey('CATEGORY', 'CategoryId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT_CATEGORY', 'ID', false, 22);

		$tMap->addColumn('COMMENTS', 'Comment', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('ABBREVIATION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'ABBREVIATION');

		$tMap->addValidator('ABBREVIATION', 'required', 'propel.validator.RequiredValidator', '', 'ABBREVIATION');

		$tMap->addValidator('BASE_UNIT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BASE_UNIT');

		$tMap->addValidator('BASE_UNIT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BASE_UNIT');

		$tMap->addValidator('BASE_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'BASE_UNIT');

		$tMap->addValidator('CATEGORY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CATEGORY');

		$tMap->addValidator('CATEGORY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CATEGORY');

		$tMap->addValidator('CATEGORY', 'required', 'propel.validator.RequiredValidator', '', 'CATEGORY');

		$tMap->addValidator('COMMENTS', 'required', 'propel.validator.RequiredValidator', '', 'COMMENTS');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

	} // doBuild()

} // MeasurementUnitMapBuilder
