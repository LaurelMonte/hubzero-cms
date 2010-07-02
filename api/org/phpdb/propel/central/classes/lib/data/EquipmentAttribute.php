<?php

require_once 'lib/data/om/BaseEquipmentAttribute.php';


/**
 * Skeleton subclass for representing a row from the 'EquipmentAttribute' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:02:54 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 * @uses Unit
 */
class EquipmentAttribute extends BaseEquipmentAttribute {

  const DATATYPE_GROUP = "GROUP";
  const DATATYPE_INTEGER = "INTEGER";
  const DATATYPE_NUMBER = "NUMBER";
  const DATATYPE_STRING = "STRING";
  const DATATYPE_URL = "URL";

  /**
   * Initializes internal state of EquipmentAttribute object.
   */
  public function __construct($name=null,
                              EquipmentAttribute $parent=null,
                              $description=null,
                              Unit $unit=null,
                              $minValue=null,
                              $maxValue=null,
                              $dataType=null,
                              $label=null)
  {
		$this->setName($name);
		$this->setEquipmentAttributeRelatedByParentId($parent);
		$this->setDescription($description);
		$this->setUnit($unit);
		$this->setMinValue($minValue);
		$this->setMaxValue($maxValue);
		$this->setDataType($dataType);
		$this->setLabel($label);
	}


	/**
	 * Get the Parent EquipmentAttribute
	 * Backward compatible with NEEScentral 1.7
	 *
	 * @return EquipmentAttribute
	 */
  public function getParent() {
    return $this->getEquipmentAttributeRelatedByParentId();
  }


	/**
	 * Set the Parent EquipmentAttribute
	 * Backward compatible with NEEScentral 1.7
	 *
	 * @param $parent: The parent EquipmentAttribute
	 */
  public function setParent($parent) {
    return $this->setEquipmentAttributeRelatedByParentId($parent);
  }


  public function getChildren() {
    return $this->getEquipmentAttributesRelatedByParentId();
  }

} // EquipmentAttribute
?>
