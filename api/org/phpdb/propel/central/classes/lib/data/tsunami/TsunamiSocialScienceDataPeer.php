<?php

// include base peer class
require_once 'lib/data/tsunami/om/BaseTsunamiSocialScienceDataPeer.php';

// include object class
include_once 'lib/data/tsunami/TsunamiSocialScienceData.php';
include_once 'lib/data/tsunami/TsunamiSiteDocRelationship.php';

/**
 * Skeleton subclass for performing query and update operations on the 'TsunamiSocialScienceData' table.
 *
 *
 *
 * This class was autogenerated by Propel on:
 *
 * Sat Feb  9 00:03:17 2008
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data.tsunami
 */
class TsunamiSocialScienceDataPeer extends BaseTsunamiSocialScienceDataPeer {

  /**
   * Find a TsunamiSocialScienceData object based on its ID
   *
   * @param int $id
   * @return TsunamiSocialScienceData
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all TsunamiSocialScienceDatas
   *
   * @return array <TsunamiSocialScienceData>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find all TsunamiSocialScienceData given by a condition
   * @param String $field_name
   * @return (int ???) $field_value
   */
  public static function findByColumnValue($field_name, $field_value) {
    $c = new Criteria();
    $field_name = self::getTableMap()->getName() . "." . strtoupper($field_name);
    $c->add($field_name, $field_value);

    return self::doSelect($c);
  }



  function listTsunamiSocialScienceDataBySite($siteId, $filterArr = null, $expanded = false) {

    $c = new Criteria();
    $c->addJoin(self::TSUNAMI_DOC_LIB_ID, TsunamiSiteDocRelationshipPeer::TSUNAMI_DOC_LIB_ID);
    $c->add(TsunamiSiteDocRelationshipPeer::TSUNAMI_SITE_ID, $siteId);

    if(is_array($filterArr)  && ! empty($filterArr)) {
      $c->add(self::getTableMap()->getName() . "." . strtoupper($filterArr[0]), $filterArr[1]);
    }

    $c->addAscendingOrderByColumn(self::TSUNAMI_SOCIAL_SCIENCE_DATA_ID);

    $objs = self::doSelect($c);

    if($expanded) {
      return $objs;
    }
    else {
      $objIds = array();
      foreach($objs as $obj) {
        $objIds[] = $obj->getId();
      }
      return $objIds;
    }
  }

} // TsunamiSocialScienceDataPeer
