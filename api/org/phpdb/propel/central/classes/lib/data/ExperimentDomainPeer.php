<?php

  // include base peer class
  require_once 'lib/data/om/BaseExperimentDomainPeer.php';

  // include object class
  include_once 'lib/data/ExperimentDomain.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ExperimentDomain' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:02:57 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ExperimentDomainPeer extends BaseExperimentDomainPeer {

  /**
   * Find an ExperimentDomain object based on its ID
   *
   * @param int $id
   * @return ExperimentDomain
   */
  public static function find($idOrName) {
    $c = new Criteria();
    $cton1 = $c->getNewCriterion(self::ID, $idOrName);
    $cton2 = $c->getNewCriterion(self::DISPLAY_NAME, $idOrName);
    $cton1->addOr($cton2);
    $c->add($cton1);

    return self::doSelectOne($c);
  }

  /**
   * Find one ExperimentDomain type of Simulation
   *
   * @param Criteria $c
   * @param Connection $conn
   * @return ExperimentDomain $simulationDomain
   */
  public static function findSimulationDomain(Criteria $c = null, Connection $conn = null) {
    if (is_null($c)) $c = new Criteria();
    $c->add(self::SYSTEM_NAME,"simulation");
    return self::doSelectOne($c);
  }

  /**
   * Find all ExperimentDomains
   *
   * @return array <ExperimentDomain>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::DISPLAY_ORDER);

    return self::doSelect($c);
  }


  /**
   * Find one ExperimentDomain by SystenName
   *
   * @param String $systenName
   * @return ExperimentDomainn
   */
  public static function findByName($system_name) {
    $c = new Criteria();
    $c->add(self::SYSTEM_NAME, $system_name);
    $c->setIgnoreCase(true);
    return self::doSelectOne($c);
  }


  /**
   * Find one ExperimentDomain by SystenName or Id
   *
   * @param String $systenName
   * @param int $id
   * @return ExperimentDomain
   */
  public static function findByNameOrId($system_name, $id) {
    $c = new Criteria();
    $cton1 = $c->getNewCriterion(self::SYSTEM_NAME, $system_name);
    $cton2 = $c->getNewCriterion(self::ID, $id);
    $cton1->addOr($cton2);
    $c->add($cton1);

    return self::doSelectOne($c);
  }

} // ExperimentDomainPeer
?>
