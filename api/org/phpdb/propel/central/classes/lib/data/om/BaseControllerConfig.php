<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';

include_once 'creole/util/Clob.php';
include_once 'creole/util/Blob.php';


include_once 'propel/util/Criteria.php';

include_once 'lib/data/ControllerConfigPeer.php';

/**
 * Base class that represents a row from the 'CONTROLLER_CONFIG' table.
 *
 * 
 *
 * @package    lib.data.om
 */
abstract class BaseControllerConfig extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ControllerConfigPeer
	 */
	protected static $peer;


	/**
	 * The value for the id field.
	 * @var        double
	 */
	protected $id;


	/**
	 * The value for the config_data_file_id field.
	 * @var        double
	 */
	protected $config_data_file_id;


	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;


	/**
	 * The value for the equipment_id field.
	 * @var        double
	 */
	protected $equipment_id;


	/**
	 * The value for the input_data_file_id field.
	 * @var        double
	 */
	protected $input_data_file_id;


	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;


	/**
	 * The value for the peak_base_acceleration field.
	 * @var        double
	 */
	protected $peak_base_acceleration;


	/**
	 * The value for the peak_base_acceleration_unit_id field.
	 * @var        double
	 */
	protected $peak_base_acceleration_unit_id;


	/**
	 * The value for the trial_id field.
	 * @var        double
	 */
	protected $trial_id;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByInputDataFileId;

	/**
	 * @var        DataFile
	 */
	protected $aDataFileRelatedByConfigDataFileId;

	/**
	 * @var        Equipment
	 */
	protected $aEquipment;

	/**
	 * @var        MeasurementUnit
	 */
	protected $aMeasurementUnit;

	/**
	 * @var        Trial
	 */
	protected $aTrial;

	/**
	 * Collection to store aggregation of collControllerChannels.
	 * @var        array
	 */
	protected $collControllerChannels;

	/**
	 * The criteria used to select the current contents of collControllerChannels.
	 * @var        Criteria
	 */
	protected $lastControllerChannelCriteria = null;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Get the [id] column value.
	 * 
	 * @return     double
	 */
	public function getId()
	{

		return $this->id;
	}

	/**
	 * Get the [config_data_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getConfigDataFileId()
	{

		return $this->config_data_file_id;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{

		return $this->description;
	}

	/**
	 * Get the [equipment_id] column value.
	 * 
	 * @return     double
	 */
	public function getEquipmentId()
	{

		return $this->equipment_id;
	}

	/**
	 * Get the [input_data_file_id] column value.
	 * 
	 * @return     double
	 */
	public function getInputDataFileId()
	{

		return $this->input_data_file_id;
	}

	/**
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{

		return $this->name;
	}

	/**
	 * Get the [peak_base_acceleration] column value.
	 * 
	 * @return     double
	 */
	public function getPeakBaseAcceleration()
	{

		return $this->peak_base_acceleration;
	}

	/**
	 * Get the [peak_base_acceleration_unit_id] column value.
	 * 
	 * @return     double
	 */
	public function getPeakBaseAccelerationUnitId()
	{

		return $this->peak_base_acceleration_unit_id;
	}

	/**
	 * Get the [trial_id] column value.
	 * 
	 * @return     double
	 */
	public function getTrialId()
	{

		return $this->trial_id;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::ID;
		}

	} // setId()

	/**
	 * Set the value of [config_data_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setConfigDataFileId($v)
	{

		if ($this->config_data_file_id !== $v) {
			$this->config_data_file_id = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::CONFIG_DATA_FILE_ID;
		}

		if ($this->aDataFileRelatedByConfigDataFileId !== null && $this->aDataFileRelatedByConfigDataFileId->getId() !== $v) {
			$this->aDataFileRelatedByConfigDataFileId = null;
		}

	} // setConfigDataFileId()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDescription($v)
	{

		// if the passed in parameter is the *same* object that
		// is stored internally then we use the Lob->isModified()
		// method to know whether contents changed.
		if ($v instanceof Lob && $v === $this->description) {
			$changed = $v->isModified();
		} else {
			$changed = ($this->description !== $v);
		}
		if ($changed) {
			if ( !($v instanceof Lob) ) {
				$obj = new Clob();
				$obj->setContents($v);
			} else {
				$obj = $v;
			}
			$this->description = $obj;
			$this->modifiedColumns[] = ControllerConfigPeer::DESCRIPTION;
		}

	} // setDescription()

	/**
	 * Set the value of [equipment_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setEquipmentId($v)
	{

		if ($this->equipment_id !== $v) {
			$this->equipment_id = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::EQUIPMENT_ID;
		}

		if ($this->aEquipment !== null && $this->aEquipment->getId() !== $v) {
			$this->aEquipment = null;
		}

	} // setEquipmentId()

	/**
	 * Set the value of [input_data_file_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setInputDataFileId($v)
	{

		if ($this->input_data_file_id !== $v) {
			$this->input_data_file_id = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::INPUT_DATA_FILE_ID;
		}

		if ($this->aDataFileRelatedByInputDataFileId !== null && $this->aDataFileRelatedByInputDataFileId->getId() !== $v) {
			$this->aDataFileRelatedByInputDataFileId = null;
		}

	} // setInputDataFileId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::NAME;
		}

	} // setName()

	/**
	 * Set the value of [peak_base_acceleration] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPeakBaseAcceleration($v)
	{

		if ($this->peak_base_acceleration !== $v) {
			$this->peak_base_acceleration = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::PEAK_BASE_ACCELERATION;
		}

	} // setPeakBaseAcceleration()

	/**
	 * Set the value of [peak_base_acceleration_unit_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setPeakBaseAccelerationUnitId($v)
	{

		if ($this->peak_base_acceleration_unit_id !== $v) {
			$this->peak_base_acceleration_unit_id = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID;
		}

		if ($this->aMeasurementUnit !== null && $this->aMeasurementUnit->getId() !== $v) {
			$this->aMeasurementUnit = null;
		}

	} // setPeakBaseAccelerationUnitId()

	/**
	 * Set the value of [trial_id] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setTrialId($v)
	{

		if ($this->trial_id !== $v) {
			$this->trial_id = $v;
			$this->modifiedColumns[] = ControllerConfigPeer::TRIAL_ID;
		}

		if ($this->aTrial !== null && $this->aTrial->getId() !== $v) {
			$this->aTrial = null;
		}

	} // setTrialId()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (1-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
	 * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->id = $rs->getFloat($startcol + 0);

			$this->config_data_file_id = $rs->getFloat($startcol + 1);

			$this->description = $rs->getClob($startcol + 2);

			$this->equipment_id = $rs->getFloat($startcol + 3);

			$this->input_data_file_id = $rs->getFloat($startcol + 4);

			$this->name = $rs->getString($startcol + 5);

			$this->peak_base_acceleration = $rs->getFloat($startcol + 6);

			$this->peak_base_acceleration_unit_id = $rs->getFloat($startcol + 7);

			$this->trial_id = $rs->getFloat($startcol + 8);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = ControllerConfigPeer::NUM_COLUMNS - ControllerConfigPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ControllerConfig object", $e);
		}
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      Connection $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(ControllerConfigPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			ControllerConfigPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.  If the object is new,
	 * it inserts it; otherwise an update is performed.  This method
	 * wraps the doSave() worker method in a transaction.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(ControllerConfigPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave($con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aDataFileRelatedByInputDataFileId !== null) {
				if ($this->aDataFileRelatedByInputDataFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByInputDataFileId->save($con);
				}
				$this->setDataFileRelatedByInputDataFileId($this->aDataFileRelatedByInputDataFileId);
			}

			if ($this->aDataFileRelatedByConfigDataFileId !== null) {
				if ($this->aDataFileRelatedByConfigDataFileId->isModified()) {
					$affectedRows += $this->aDataFileRelatedByConfigDataFileId->save($con);
				}
				$this->setDataFileRelatedByConfigDataFileId($this->aDataFileRelatedByConfigDataFileId);
			}

			if ($this->aEquipment !== null) {
				if ($this->aEquipment->isModified()) {
					$affectedRows += $this->aEquipment->save($con);
				}
				$this->setEquipment($this->aEquipment);
			}

			if ($this->aMeasurementUnit !== null) {
				if ($this->aMeasurementUnit->isModified()) {
					$affectedRows += $this->aMeasurementUnit->save($con);
				}
				$this->setMeasurementUnit($this->aMeasurementUnit);
			}

			if ($this->aTrial !== null) {
				if ($this->aTrial->isModified()) {
					$affectedRows += $this->aTrial->save($con);
				}
				$this->setTrial($this->aTrial);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ControllerConfigPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ControllerConfigPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collControllerChannels !== null) {
				foreach($this->collControllerChannels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aDataFileRelatedByInputDataFileId !== null) {
				if (!$this->aDataFileRelatedByInputDataFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByInputDataFileId->getValidationFailures());
				}
			}

			if ($this->aDataFileRelatedByConfigDataFileId !== null) {
				if (!$this->aDataFileRelatedByConfigDataFileId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aDataFileRelatedByConfigDataFileId->getValidationFailures());
				}
			}

			if ($this->aEquipment !== null) {
				if (!$this->aEquipment->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEquipment->getValidationFailures());
				}
			}

			if ($this->aMeasurementUnit !== null) {
				if (!$this->aMeasurementUnit->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMeasurementUnit->getValidationFailures());
				}
			}

			if ($this->aTrial !== null) {
				if (!$this->aTrial->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aTrial->getValidationFailures());
				}
			}


			if (($retval = ControllerConfigPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collControllerChannels !== null) {
					foreach($this->collControllerChannels as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = ControllerConfigPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getConfigDataFileId();
				break;
			case 2:
				return $this->getDescription();
				break;
			case 3:
				return $this->getEquipmentId();
				break;
			case 4:
				return $this->getInputDataFileId();
				break;
			case 5:
				return $this->getName();
				break;
			case 6:
				return $this->getPeakBaseAcceleration();
				break;
			case 7:
				return $this->getPeakBaseAccelerationUnitId();
				break;
			case 8:
				return $this->getTrialId();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType One of the class type constants TYPE_PHPNAME,
	 *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = ControllerConfigPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getConfigDataFileId(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getEquipmentId(),
			$keys[4] => $this->getInputDataFileId(),
			$keys[5] => $this->getName(),
			$keys[6] => $this->getPeakBaseAcceleration(),
			$keys[7] => $this->getPeakBaseAccelerationUnitId(),
			$keys[8] => $this->getTrialId(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = ControllerConfigPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setConfigDataFileId($value);
				break;
			case 2:
				$this->setDescription($value);
				break;
			case 3:
				$this->setEquipmentId($value);
				break;
			case 4:
				$this->setInputDataFileId($value);
				break;
			case 5:
				$this->setName($value);
				break;
			case 6:
				$this->setPeakBaseAcceleration($value);
				break;
			case 7:
				$this->setPeakBaseAccelerationUnitId($value);
				break;
			case 8:
				$this->setTrialId($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
	 * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = ControllerConfigPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setConfigDataFileId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setEquipmentId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setInputDataFileId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPeakBaseAcceleration($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setPeakBaseAccelerationUnitId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setTrialId($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ControllerConfigPeer::DATABASE_NAME);

		if ($this->isColumnModified(ControllerConfigPeer::ID)) $criteria->add(ControllerConfigPeer::ID, $this->id);
		if ($this->isColumnModified(ControllerConfigPeer::CONFIG_DATA_FILE_ID)) $criteria->add(ControllerConfigPeer::CONFIG_DATA_FILE_ID, $this->config_data_file_id);
		if ($this->isColumnModified(ControllerConfigPeer::DESCRIPTION)) $criteria->add(ControllerConfigPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(ControllerConfigPeer::EQUIPMENT_ID)) $criteria->add(ControllerConfigPeer::EQUIPMENT_ID, $this->equipment_id);
		if ($this->isColumnModified(ControllerConfigPeer::INPUT_DATA_FILE_ID)) $criteria->add(ControllerConfigPeer::INPUT_DATA_FILE_ID, $this->input_data_file_id);
		if ($this->isColumnModified(ControllerConfigPeer::NAME)) $criteria->add(ControllerConfigPeer::NAME, $this->name);
		if ($this->isColumnModified(ControllerConfigPeer::PEAK_BASE_ACCELERATION)) $criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION, $this->peak_base_acceleration);
		if ($this->isColumnModified(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID)) $criteria->add(ControllerConfigPeer::PEAK_BASE_ACCELERATION_UNIT_ID, $this->peak_base_acceleration_unit_id);
		if ($this->isColumnModified(ControllerConfigPeer::TRIAL_ID)) $criteria->add(ControllerConfigPeer::TRIAL_ID, $this->trial_id);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(ControllerConfigPeer::DATABASE_NAME);

		$criteria->add(ControllerConfigPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     double
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      double $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ControllerConfig (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setConfigDataFileId($this->config_data_file_id);

		$copyObj->setDescription($this->description);

		$copyObj->setEquipmentId($this->equipment_id);

		$copyObj->setInputDataFileId($this->input_data_file_id);

		$copyObj->setName($this->name);

		$copyObj->setPeakBaseAcceleration($this->peak_base_acceleration);

		$copyObj->setPeakBaseAccelerationUnitId($this->peak_base_acceleration_unit_id);

		$copyObj->setTrialId($this->trial_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach($this->getControllerChannels() as $relObj) {
				$copyObj->addControllerChannel($relObj->copy($deepCopy));
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a pkey column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     ControllerConfig Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     ControllerConfigPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ControllerConfigPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedByInputDataFileId($v)
	{


		if ($v === null) {
			$this->setInputDataFileId(NULL);
		} else {
			$this->setInputDataFileId($v->getId());
		}


		$this->aDataFileRelatedByInputDataFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByInputDataFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByInputDataFileId === null && ($this->input_data_file_id > 0)) {

			$this->aDataFileRelatedByInputDataFileId = DataFilePeer::retrieveByPK($this->input_data_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->input_data_file_id, $con);
			   $obj->addDataFilesRelatedByInputDataFileId($this);
			 */
		}
		return $this->aDataFileRelatedByInputDataFileId;
	}

	/**
	 * Declares an association between this object and a DataFile object.
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setDataFileRelatedByConfigDataFileId($v)
	{


		if ($v === null) {
			$this->setConfigDataFileId(NULL);
		} else {
			$this->setConfigDataFileId($v->getId());
		}


		$this->aDataFileRelatedByConfigDataFileId = $v;
	}


	/**
	 * Get the associated DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
	public function getDataFileRelatedByConfigDataFileId($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseDataFilePeer.php';

		if ($this->aDataFileRelatedByConfigDataFileId === null && ($this->config_data_file_id > 0)) {

			$this->aDataFileRelatedByConfigDataFileId = DataFilePeer::retrieveByPK($this->config_data_file_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = DataFilePeer::retrieveByPK($this->config_data_file_id, $con);
			   $obj->addDataFilesRelatedByConfigDataFileId($this);
			 */
		}
		return $this->aDataFileRelatedByConfigDataFileId;
	}

	/**
	 * Declares an association between this object and a Equipment object.
	 *
	 * @param      Equipment $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setEquipment($v)
	{


		if ($v === null) {
			$this->setEquipmentId(NULL);
		} else {
			$this->setEquipmentId($v->getId());
		}


		$this->aEquipment = $v;
	}


	/**
	 * Get the associated Equipment object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Equipment The associated Equipment object.
	 * @throws     PropelException
	 */
	public function getEquipment($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseEquipmentPeer.php';

		if ($this->aEquipment === null && ($this->equipment_id > 0)) {

			$this->aEquipment = EquipmentPeer::retrieveByPK($this->equipment_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = EquipmentPeer::retrieveByPK($this->equipment_id, $con);
			   $obj->addEquipments($this);
			 */
		}
		return $this->aEquipment;
	}

	/**
	 * Declares an association between this object and a MeasurementUnit object.
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setMeasurementUnit($v)
	{


		if ($v === null) {
			$this->setPeakBaseAccelerationUnitId(NULL);
		} else {
			$this->setPeakBaseAccelerationUnitId($v->getId());
		}


		$this->aMeasurementUnit = $v;
	}


	/**
	 * Get the associated MeasurementUnit object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getMeasurementUnit($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseMeasurementUnitPeer.php';

		if ($this->aMeasurementUnit === null && ($this->peak_base_acceleration_unit_id > 0)) {

			$this->aMeasurementUnit = MeasurementUnitPeer::retrieveByPK($this->peak_base_acceleration_unit_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = MeasurementUnitPeer::retrieveByPK($this->peak_base_acceleration_unit_id, $con);
			   $obj->addMeasurementUnits($this);
			 */
		}
		return $this->aMeasurementUnit;
	}

	/**
	 * Declares an association between this object and a Trial object.
	 *
	 * @param      Trial $v
	 * @return     void
	 * @throws     PropelException
	 */
	public function setTrial($v)
	{


		if ($v === null) {
			$this->setTrialId(NULL);
		} else {
			$this->setTrialId($v->getId());
		}


		$this->aTrial = $v;
	}


	/**
	 * Get the associated Trial object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     Trial The associated Trial object.
	 * @throws     PropelException
	 */
	public function getTrial($con = null)
	{
		// include the related Peer class
		include_once 'lib/data/om/BaseTrialPeer.php';

		if ($this->aTrial === null && ($this->trial_id > 0)) {

			$this->aTrial = TrialPeer::retrieveByPK($this->trial_id, $con);

			/* The following can be used instead of the line above to
			   guarantee the related object contains a reference
			   to this object, but this level of coupling
			   may be undesirable in many circumstances.
			   As it can lead to a db query with many results that may
			   never be used.
			   $obj = TrialPeer::retrieveByPK($this->trial_id, $con);
			   $obj->addTrials($this);
			 */
		}
		return $this->aTrial;
	}

	/**
	 * Temporary storage of collControllerChannels to save a possible db hit in
	 * the event objects are add to the collection, but the
	 * complete collection is never requested.
	 * @return     void
	 */
	public function initControllerChannels()
	{
		if ($this->collControllerChannels === null) {
			$this->collControllerChannels = array();
		}
	}

	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ControllerConfig has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 * If this ControllerConfig is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
	 */
	public function getControllerChannels($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
			   $this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

				ControllerChannelPeer::addSelectColumns($criteria);
				if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
					$this->collControllerChannels = ControllerChannelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastControllerChannelCriteria = $criteria;
		return $this->collControllerChannels;
	}

	/**
	 * Returns the number of related ControllerChannels.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      Connection $con
	 * @throws     PropelException
	 */
	public function countControllerChannels($criteria = null, $distinct = false, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

		return ControllerChannelPeer::doCount($criteria, $distinct, $con);
	}

	/**
	 * Method called to associate a ControllerChannel object to this object
	 * through the ControllerChannel foreign key attribute
	 *
	 * @param      ControllerChannel $l ControllerChannel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addControllerChannel(ControllerChannel $l)
	{
		$this->collControllerChannels[] = $l;
		$l->setControllerConfig($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ControllerConfig is new, it will return
	 * an empty collection; or if this ControllerConfig has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ControllerConfig.
	 */
	public function getControllerChannelsJoinDataFile($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinDataFile($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ControllerConfig is new, it will return
	 * an empty collection; or if this ControllerConfig has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ControllerConfig.
	 */
	public function getControllerChannelsJoinEquipment($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinEquipment($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinEquipment($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ControllerConfig is new, it will return
	 * an empty collection; or if this ControllerConfig has previously
	 * been saved, it will retrieve related ControllerChannels from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ControllerConfig.
	 */
	public function getControllerChannelsJoinLocation($criteria = null, $con = null)
	{
		// include the Peer class
		include_once 'lib/data/om/BaseControllerChannelPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collControllerChannels === null) {
			if ($this->isNew()) {
				$this->collControllerChannels = array();
			} else {

				$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(ControllerChannelPeer::CONTROLLER_CONFIG_ID, $this->getId());

			if (!isset($this->lastControllerChannelCriteria) || !$this->lastControllerChannelCriteria->equals($criteria)) {
				$this->collControllerChannels = ControllerChannelPeer::doSelectJoinLocation($criteria, $con);
			}
		}
		$this->lastControllerChannelCriteria = $criteria;

		return $this->collControllerChannels;
	}

} // BaseControllerConfig
