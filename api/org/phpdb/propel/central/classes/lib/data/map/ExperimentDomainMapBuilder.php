<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EXPERIMENT_DOMAIN' table to 'NEEScentral' DatabaseMap object.
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
class ExperimentDomainMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ExperimentDomainMapBuilder';

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

		$tMap = $this->dbMap->addTable('EXPERIMENT_DOMAIN');
		$tMap->setPhpName('ExperimentDomain');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EXPERIMENT_DOMAIN_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('DISPLAY_NAME', 'DisplayName', 'string', CreoleTypes::VARCHAR, true, 400);

		$tMap->addColumn('SYSTEM_NAME', 'SystemName', 'string', CreoleTypes::VARCHAR, true, 128);

		$tMap->addColumn('DISPLAY_ORDER', 'DisplayOrder', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DISPLAY_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'DISPLAY_NAME');

		$tMap->addValidator('DISPLAY_NAME', 'required', 'propel.validator.RequiredValidator', '', 'DISPLAY_NAME');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('SYSTEM_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'SYSTEM_NAME');

		$tMap->addValidator('SYSTEM_NAME', 'required', 'propel.validator.RequiredValidator', '', 'SYSTEM_NAME');

	} // doBuild()

} // ExperimentDomainMapBuilder
