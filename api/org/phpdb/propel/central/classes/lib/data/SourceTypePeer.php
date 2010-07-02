<?php

  // include base peer class
  require_once 'lib/data/om/BaseSourceTypePeer.php';

  // include object class
  include_once 'lib/data/SourceType.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SourceType' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:03:12 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SourceTypePeer extends BaseSourceTypePeer {

  /**
   * Find a SourceType object based on its ID
   *
   * @param int $id
   * @return SourceType
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all SourceTypes
   *
   * @return array <SourceType>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  //"findByName" => array("SELECT * FROM SourceType WHERE name = ?", false),
  public static function findByName($name) {

    $c = new Criteria();
    $c->add(self::NAME, $name);
    $c->setIgnoreCase(true);
    return self::doSelect($c);
  }


  //"findBySysName" => array("SELECT * FROM SourceType WHERE system_name = ?", false),
  public static function findBySysName($system_name) {

    $c = new Criteria();
    $c->add(self::SYSTEM_NAME, $system_name);

    return self::doSelect($c);
  }


} // SourceTypePeer
?>
