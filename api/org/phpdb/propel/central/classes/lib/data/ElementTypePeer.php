<?php

  // include base peer class
  require_once 'lib/data/om/BaseElementTypePeer.php';

  // include object class
  include_once 'lib/data/ElementType.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ElementType' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:02:52 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ElementTypePeer extends BaseElementTypePeer {

  /**
   * Find a ElementType object based on its ID
   *
   * @param int $id
   * @return ElementType
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all ElementTypes
   *
   * @return array <ElementType>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

} // ElementTypePeer
?>
