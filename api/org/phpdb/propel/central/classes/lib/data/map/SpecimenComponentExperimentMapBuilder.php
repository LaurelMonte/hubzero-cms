<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SPECCOMP_EXPERIMENT' table to 'NEEScentral' DatabaseMap object.
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
class SpecimenComponentExperimentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SpecimenComponentExperimentMapBuilder';

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

		$tMap = $this->dbMap->addTable('SPECCOMP_EXPERIMENT');
		$tMap->setPhpName('SpecimenComponentExperiment');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SPECCOMP_EXPERIMENT_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('SPECIMEN_COMPONENT_ID', 'SpecimenComponentId', 'double', CreoleTypes::NUMERIC, 'SPECIMEN_COMPONENT', 'ID', true, 22);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', true, 22);

		$tMap->addForeignKey('COORD_RUN_EXP_ID', 'CoordinatorRunExperimentId', 'double', CreoleTypes::NUMERIC, 'COORDINATOR_RUN_EXPERIMENT', 'ID', false, 22);

	} // doBuild()

} // SpecimenComponentExperimentMapBuilder
