<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by NCCuratedNCIDCrossRefPeer::getOMClass()
include_once 'lib/data/curation/NCCuratedNCIDCrossRef.php';

/**
 * Base static class for performing query and update operations on the 'CURATEDNCIDCROSS_REF' table.
 *
 * 
 *
 * @package    lib.data.curation.om
 */
abstract class BaseNCCuratedNCIDCrossRefPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'NEEScentral';

	/** the table name for this class */
	const TABLE_NAME = 'CURATEDNCIDCROSS_REF';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'lib.data.curation.NCCuratedNCIDCrossRef';

	/** The total number of columns. */
	const NUM_COLUMNS = 6;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;


	/** the column name for the ID field */
	const ID = 'CURATEDNCIDCROSS_REF.ID';

	/** the column name for the CREATED_BY field */
	const CREATED_BY = 'CURATEDNCIDCROSS_REF.CREATED_BY';

	/** the column name for the CREATED_DATE field */
	const CREATED_DATE = 'CURATEDNCIDCROSS_REF.CREATED_DATE';

	/** the column name for the CURATED_ENTITYID field */
	const CURATED_ENTITYID = 'CURATEDNCIDCROSS_REF.CURATED_ENTITYID';

	/** the column name for the NEESCENTRAL_OBJECTID field */
	const NEESCENTRAL_OBJECTID = 'CURATEDNCIDCROSS_REF.NEESCENTRAL_OBJECTID';

	/** the column name for the NEESCENTRAL_TABLE_SOURCE field */
	const NEESCENTRAL_TABLE_SOURCE = 'CURATEDNCIDCROSS_REF.NEESCENTRAL_TABLE_SOURCE';

	/** The PHP to DB Name Mapping */
	private static $phpNameMap = null;


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'CreatedBy', 'CreatedDate', 'CuratedEntityId', 'NEEScentralObjectId', 'NEEScentralTableSource', ),
		BasePeer::TYPE_COLNAME => array (NCCuratedNCIDCrossRefPeer::ID, NCCuratedNCIDCrossRefPeer::CREATED_BY, NCCuratedNCIDCrossRefPeer::CREATED_DATE, NCCuratedNCIDCrossRefPeer::CURATED_ENTITYID, NCCuratedNCIDCrossRefPeer::NEESCENTRAL_OBJECTID, NCCuratedNCIDCrossRefPeer::NEESCENTRAL_TABLE_SOURCE, ),
		BasePeer::TYPE_FIELDNAME => array ('ID', 'CREATED_BY', 'CREATED_DATE', 'CURATED_ENTITYID', 'NEESCENTRAL_OBJECTID', 'NEESCENTRAL_TABLE_SOURCE', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'CreatedBy' => 1, 'CreatedDate' => 2, 'CuratedEntityId' => 3, 'NEEScentralObjectId' => 4, 'NEEScentralTableSource' => 5, ),
		BasePeer::TYPE_COLNAME => array (NCCuratedNCIDCrossRefPeer::ID => 0, NCCuratedNCIDCrossRefPeer::CREATED_BY => 1, NCCuratedNCIDCrossRefPeer::CREATED_DATE => 2, NCCuratedNCIDCrossRefPeer::CURATED_ENTITYID => 3, NCCuratedNCIDCrossRefPeer::NEESCENTRAL_OBJECTID => 4, NCCuratedNCIDCrossRefPeer::NEESCENTRAL_TABLE_SOURCE => 5, ),
		BasePeer::TYPE_FIELDNAME => array ('ID' => 0, 'CREATED_BY' => 1, 'CREATED_DATE' => 2, 'CURATED_ENTITYID' => 3, 'NEESCENTRAL_OBJECTID' => 4, 'NEESCENTRAL_TABLE_SOURCE' => 5, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, )
	);

	/**
	 * @return     MapBuilder the map builder for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getMapBuilder()
	{
		include_once 'lib/data/curation/map/NCCuratedNCIDCrossRefMapBuilder.php';
		return BasePeer::getMapBuilder('lib.data.curation.map.NCCuratedNCIDCrossRefMapBuilder');
	}
	/**
	 * Gets a map (hash) of PHP names to DB column names.
	 *
	 * @return     array The PHP to DB name map for this peer
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
	 */
	public static function getPhpNameMap()
	{
		if (self::$phpNameMap === null) {
			$map = NCCuratedNCIDCrossRefPeer::getTableMap();
			$columns = $map->getColumns();
			$nameMap = array();
			foreach ($columns as $column) {
				$nameMap[$column->getPhpName()] = $column->getColumnName();
			}
			self::$phpNameMap = $nameMap;
		}
		return self::$phpNameMap;
	}
	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants TYPE_PHPNAME,
	 *                         TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants TYPE_PHPNAME,
	 *                      TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. NCCuratedNCIDCrossRefPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(NCCuratedNCIDCrossRefPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{

		$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::ID);

		$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::CREATED_BY);

		$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::CREATED_DATE);

		$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::CURATED_ENTITYID);

		$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::NEESCENTRAL_OBJECTID);

		$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::NEESCENTRAL_TABLE_SOURCE);

	}

	const COUNT = 'COUNT(CURATEDNCIDCROSS_REF.ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT CURATEDNCIDCROSS_REF.ID)';

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
	 * @param      Connection $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, $con = null)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// clear out anything that might confuse the ORDER BY clause
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(NCCuratedNCIDCrossRefPeer::COUNT);
		}

		// just in case we're grouping: add those columns to the select statement
		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = NCCuratedNCIDCrossRefPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
			// no rows returned; we infer that means 0 matches.
			return 0;
		}
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      Connection $con
	 * @return     NCCuratedNCIDCrossRef
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = NCCuratedNCIDCrossRefPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      Connection $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, $con = null)
	{
		return NCCuratedNCIDCrossRefPeer::populateObjects(NCCuratedNCIDCrossRefPeer::doSelectRS($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect()
	 * method to get a ResultSet.
	 *
	 * Use this method directly if you want to just get the resultset
	 * (instead of an array of objects).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      Connection $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     ResultSet The resultset object with numerically-indexed fields.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectRS(Criteria $criteria, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if (!$criteria->getSelectColumns()) {
			$criteria = clone $criteria;
			NCCuratedNCIDCrossRefPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a Creole ResultSet, set to return
		// rows indexed numerically.
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(ResultSet $rs)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = NCCuratedNCIDCrossRefPeer::getOMClass();
		$cls = Propel::import($cls);
		// populate the object(s)
		while($rs->next()) {
		
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * This uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass()
	{
		return NCCuratedNCIDCrossRefPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a NCCuratedNCIDCrossRef or Criteria object.
	 *
	 * @param      mixed $values Criteria or NCCuratedNCIDCrossRef object containing data that is used to create the INSERT statement.
	 * @param      Connection $con the connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from NCCuratedNCIDCrossRef object
		}

		$criteria->remove(NCCuratedNCIDCrossRefPeer::ID); // remove pkey col since this table uses auto-increment


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->begin();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollback();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a NCCuratedNCIDCrossRef or Criteria object.
	 *
	 * @param      mixed $values Criteria or NCCuratedNCIDCrossRef object containing data that is used to create the UPDATE statement.
	 * @param      Connection $con The connection to use (specify Connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(NCCuratedNCIDCrossRefPeer::ID);
			$selectCriteria->add(NCCuratedNCIDCrossRefPeer::ID, $criteria->remove(NCCuratedNCIDCrossRefPeer::ID), $comparison);

		} else { // $values is NCCuratedNCIDCrossRef object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the CURATEDNCIDCROSS_REF table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->begin();
			$affectedRows += BasePeer::doDeleteAll(NCCuratedNCIDCrossRefPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a NCCuratedNCIDCrossRef or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or NCCuratedNCIDCrossRef object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      Connection $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(NCCuratedNCIDCrossRefPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} elseif ($values instanceof NCCuratedNCIDCrossRef) {

			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(NCCuratedNCIDCrossRefPeer::ID, (array) $values, Criteria::IN);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->begin();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given NCCuratedNCIDCrossRef object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      NCCuratedNCIDCrossRef $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(NCCuratedNCIDCrossRef $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(NCCuratedNCIDCrossRefPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(NCCuratedNCIDCrossRefPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedNCIDCrossRefPeer::CREATED_BY))
			$columns[NCCuratedNCIDCrossRefPeer::CREATED_BY] = $obj->getCreatedBy();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedNCIDCrossRefPeer::CREATED_DATE))
			$columns[NCCuratedNCIDCrossRefPeer::CREATED_DATE] = $obj->getCreatedDate();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedNCIDCrossRefPeer::CURATED_ENTITYID))
			$columns[NCCuratedNCIDCrossRefPeer::CURATED_ENTITYID] = $obj->getCuratedEntityId();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedNCIDCrossRefPeer::NEESCENTRAL_OBJECTID))
			$columns[NCCuratedNCIDCrossRefPeer::NEESCENTRAL_OBJECTID] = $obj->getNEEScentralObjectId();

		if ($obj->isNew() || $obj->isColumnModified(NCCuratedNCIDCrossRefPeer::NEESCENTRAL_TABLE_SOURCE))
			$columns[NCCuratedNCIDCrossRefPeer::NEESCENTRAL_TABLE_SOURCE] = $obj->getNEEScentralTableSource();

		}

		return BasePeer::doValidate(NCCuratedNCIDCrossRefPeer::DATABASE_NAME, NCCuratedNCIDCrossRefPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      mixed $pk the primary key.
	 * @param      Connection $con the connection to use
	 * @return     NCCuratedNCIDCrossRef
	 */
	public static function retrieveByPK($pk, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$criteria = new Criteria(NCCuratedNCIDCrossRefPeer::DATABASE_NAME);

		$criteria->add(NCCuratedNCIDCrossRefPeer::ID, $pk);


		$v = NCCuratedNCIDCrossRefPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      Connection $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria();
			$criteria->add(NCCuratedNCIDCrossRefPeer::ID, $pks, Criteria::IN);
			$objs = NCCuratedNCIDCrossRefPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseNCCuratedNCIDCrossRefPeer

// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
	// the MapBuilder classes register themselves with Propel during initialization
	// so we need to load them here.
	try {
		BaseNCCuratedNCIDCrossRefPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
	// even if Propel is not yet initialized, the map builder class can be registered
	// now and then it will be loaded when Propel initializes.
	require_once 'lib/data/curation/map/NCCuratedNCIDCrossRefMapBuilder.php';
	Propel::registerMapBuilder('lib.data.curation.map.NCCuratedNCIDCrossRefMapBuilder');
}
