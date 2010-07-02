<?php

  // include base peer class
  require_once 'lib/data/om/BaseMaterialPeer.php';

  // include object class
  include_once 'lib/data/Material.php';


/**
 * Skeleton subclass for performing query and update operations on the 'Material' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:03:01 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class MaterialPeer extends BaseMaterialPeer {

  /**
   * Find a Material object based on its ID
   *
   * @param int $id
   * @return Material
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find a Material object based on its ID and ExperimentId
   *
   * @param int $id
   * @param int $expid
   * @return Material
   */
  public static function findOneByIdAndExperiment($id, $expid) {
    $c = new Criteria();
    $c->add(self::ID, $id);
    $c->add(self::EXPID, $expid);

    return self::doSelectOne($c);
  }


  /**
   * Find all Materials except for all that do not associated with an Experiment ID
   *
   * @return array <Material>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->add(self::EXPID, null, Criteria::ISNOTNULL);

    return self::doSelect($c);
  }


  /**
   * Find all Materials belong to an Experiment
   *
   * @param $expid
   * @return array <Material>
  */
  public static function findByExperiment($expid) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }


  /**
   * Find all Materials belong to an Simulation
   *
   * @param $expid
   * @return array <Material>
  */
  public static function findBySimulation($expid) {
    return self::findByExperiment($expid);
  }


  /**
   * Find all Materials that owned by a Person
   *
   * @param int $personId
   * @return array <Material>
   */
  public static function findByPerson($personId) {

    include_once 'lib/data/PersonEntityRole.php';

    $c = new Criteria();
    $c->addJoin(self::EXPID, PersonEntityRolePeer::ENTITY_ID);
    $c->add(PersonEntityRolePeer::PERSON_ID, $personId);
    $c->add(PersonEntityRolePeer::ENTITY_TYPE_ID, 3);
    $c->addAscendingOrderByColumn(self::NAME);

    return self::doSelect($c);
  }


  /**
   * Find all Materials given by a MaterialType ID
   *
   * @param int $materialType_Id
   * @return array <Material>
   */
  public static function findByMaterialType($materialType_Id) {
    $c = new Criteria();
    $c->add(self::MATERIAL_TYPE_ID, $materialType_Id);

    return self::doSelect($c);
  }


  /**
   * Find all Materials that not belongs to any Experiment (Library Materials)
   *
   * @return array <Material>
   */
  public static function findAllLibraryMaterials() {
    $c = new Criteria();
    $c->add(self::EXPID, null, Criteria::ISNULL);

    return self::doSelect($c);
  }



  /**
   * Find all Materials that not belongs to any Experiment (Library Materials) and given by System Name
   *
   * @param String $system_name
   * @return array <Material>
   */
  public static function findAllLibraryMaterialsByType($system_name) {

    include_once 'lib/data/MaterialType.php';

    $c = new Criteria();
    $c->addJoin(self::MATERIAL_TYPE_ID, MaterialTypePeer::ID, Criteria::INNER_JOIN);
    $c->add(self::EXPID, null, Criteria::ISNULL);
    $c->add(MaterialTypePeer::SYSTEM_NAME, $system_name);

    return self::doSelect($c);
  }


  /**
   * get Keyword Search Columns for Search from NEEScentral
   *
   * @return array
   */
  public static function getKeywordSearchColumns() {
    return array("name", "description");
  }


  /**
   * Find all Materials that not belongs to any Experiment (Library Materials) and given by Type
   *
   * @param Object $type
   * @return array <Material>
   */
  public static function getLibraryMaterials($type = null) {
    if( $type ) {
      // Figure out whether they passed in a MaterialType object, or a string.
      $string = $type;
      if( get_class($type) ) {
        $string = $type->getSystemName();
      }
      return self::findAllLibraryMaterialsByType($string);
    } else {
      return self::findAllLibraryMaterials();
    }
  }


  /**
   * Clone the ExperimentMaterial to another
   *
   * @param Experiment $old_exp
   * @param Experiment $new_exp
   * @return boolean value, true if successed, false if failed
   */
  public static function cloneExperimentMaterial(Experiment $old_exp, Experiment $new_exp){

    if(is_null($old_exp) || is_null($new_exp)) return false;

    $sql = "INSERT INTO
              Material (ID, MATERIAL_TYPE_ID, PROTOTYPE_MATERIAL_ID, EXPID, NAME, DESCRIPTION)
            SELECT
              MATERIAL_SEQ.NEXTVAL,
              MATERIAL_TYPE_ID,
              PROTOTYPE_MATERIAL_ID,
              ?,
              NAME,
              DESCRIPTION
            FROM
              Material
            WHERE
              EXPID = ?";

    try {
      $conn = Propel::getConnection(self::DATABASE_NAME);

      $stmt = $conn->prepareStatement($sql);
      $stmt->setInt(1, $new_exp->getId());
      $stmt->setInt(2, $old_exp->getId());
      $stmt->executeUpdate();

      return true;
    }
    catch (Exeption $e) {
      return false;
    }
  }



} // MaterialPeer
?>
