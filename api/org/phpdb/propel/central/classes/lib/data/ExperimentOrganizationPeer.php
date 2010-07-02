<?php

  // include base peer class
  require_once 'lib/data/om/BaseExperimentOrganizationPeer.php';

  // include object class
  include_once 'lib/data/ExperimentOrganization.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ExperimentOrganization' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:02:59 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ExperimentOrganizationPeer extends BaseExperimentOrganizationPeer {

  /**
   * Find an ExperimentOrganization object based on its ID
   *
   * @param int $id
   * @return ExperimentOrganization
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all ExperimentOrganizations
   *
   * @return array <ExperimentOrganization>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  //case "findByExperiment":
  //return new Finder($finderName, "SELECT * FROM {$this->getTable()} WHERE expid=?");
  public static function findByExperiment($expid) {
    $c =new Criteria();
    $c->add(self::EXPID, $expid);

    return self::doSelect($c);
  }

  //case "findByOrganization":
  //return new Finder($finderName, "SELECT * FROM {$this->getTable()} WHERE orgid=?");
  public static function findByOrganization($orgid) {
    $c =new Criteria();
    $c->add(self::ORGID, $orgid);

    return self::doSelect($c);
  }



  //case "findByExperimentOrganization":
  //return new Finder($finderName, "SELECT * FROM {$this->getTable()} WHERE expid=? AND orgid=?");
  public static function findByExperimentOrganization($expid, $orgid) {
    $c =new Criteria();
    $c->add(self::EXPID, $expid);
    $c->add(self::ORGID, $orgid);
    return self::doSelectOne($c);
  }

  public static function removeExperimentOrganizations(Experiment $exp, $orgids) {
    if (!count ($orgids)) return;
    $c = new Criteria();
    $c->add(self::EXPID, $exp->getId());
    $c->add(self::ORGID, $orgids, Criteria::IN);
    self::doDelete($c);

    // DELETE FROM SENSOR_POOL WHERE SENSOR_POOL.MANIFEST_ID IN (SELECT ID FROM SENSOR_MANIFEST WHERE ID IN (SELECT SENSOR_MANIFEST_ID FROM ORGANIZATION WHERE ORGID IN ( $orgs) ));
    if (count($orgids)) {
      $orgs = implode(',', $orgids);
      $c = new Criteria();
      $c->add(self::EXPID, $exp->getId());
      $c->add(SensorPoolPeer::MANIFEST_ID, SensorPoolPeer::MANIFEST_ID . " IN (SELECT ID FROM SENSOR_MANIFEST WHERE ID IN (SELECT SENSOR_MANIFEST_ID FROM ORGANIZATION WHERE ORGID IN ( " . $orgs . ")))", Criteria::CUSTOM, $orgids);
      SensorPoolPeer::doDelete($c);
    }
  }


  /**
   * Clone the ExperimentOrganization to another
   *
   * @param Experiment $old_exp
   * @param Experiment $new_exp
   * @return boolean value, true if successed, false if failed
   */
  public static function cloneExperimentOrganization(Experiment $old_exp, Experiment $new_exp){

    if(is_null($old_exp) || is_null($new_exp)) return false;

    $sql = "INSERT INTO
              Experiment_Organization (ID, ORGID, EXPID)
            SELECT
              XPRMNT_RGNZTN_SEQ.NEXTVAL,
              ORGID,
              ?
            FROM
              Experiment_Organization
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


} // ExperimentOrganizationPeer
?>
