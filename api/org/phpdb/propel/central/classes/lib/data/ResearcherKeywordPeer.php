<?php

  // include base peer class
  require_once 'lib/data/om/BaseResearcherKeywordPeer.php';

  // include object class
  include_once 'lib/data/ResearcherKeyword.php';


/**
 * Skeleton subclass for performing query and update operations on the 'RESEARCHER_KEYWORD' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ResearcherKeywordPeer extends BaseResearcherKeywordPeer {

	/**
	 * 
	 *
	 */
	public static function getTagsByEntity($p_iEntityId, $p_iEntityTypeId){
	  $strReturnArray = array();
  	  $strQuery = "select keyword_term 
  	  			   from researcher_keyword 
  	  			   where entity_id=? 
  	  			     and entity_type_id=?";
  	
  	  $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iEntityId);
      $oStatement->setInt(2, $p_iEntityTypeId);
  	  $oResultsSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultsSet->next()){
        array_push($strReturnArray, $oResultsSet->getString('KEYWORD_TERM'));
      }
    return $strReturnArray;
	}
	
} // ResearcherKeywordPeer
