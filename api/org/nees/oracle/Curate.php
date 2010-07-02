<?php 

  require_once('api/org/nees/oracle/util/DbHelper.php');
  require_once('api/org/nees/oracle/util/DbParameter.php');
  require_once('api/org/nees/oracle/util/DbStatement.php');
  require_once('api/org/nees/html/CurateHtml.php');
  require_once('neesconfiguration.php');

  class Curate{
  	
    /**
  	 * Find project by primary key.
  	 *
  	 */
    public static function getExperimentById($p_nExperimentId){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select e.projid, e.expid, e.name as EXPERIMENT_NAME, e.title as EXPERIMENT_TITLE, ".
  	  			"to_char(e.DESCRIPTION) as EXPERIMENT_DESCRIPTION, e.viewable as EXPERIMENT_VIEWABLE, ".
  	  			"e.curation_status as EXPERIMENT_CURATION_STATUS, e.status as EXPERIMENT_STATUS, ". 
  	  			"to_char(e.start_date,'mm-dd-yyyy') as EXPERIMENT_START_DATE, to_char(e.end_date,'mm-dd-yyyy') as EXPERIMENT_END_DATE, ".
  	  			"co.object_id as CURATED_ID, co.version as CURATED_VERSION, co.object_type as CURATED_OBJECT_TYPE, co.title as CURATED_TITLE, ".
  	  			"co.short_title as CURATED_SHORT_TITLE, to_char(co.object_creation_date,'mm-dd-yyyy') as CURATE_OJECT_CREATION_DATE, co.name as CURATED_NAME, ".
  	  			"to_char(co.initial_curation_date, 'mm-dd-yyyy') as CURATEE_INITIAL_CUATION_DATE, to_char(co.DESCRIPTION) as CURATED_DESCRIPTION,  ".
  	  			"co.object_visibility as CURATED_OBJECT_VISIBILITY, co.object_status as CURATED_OBJECT_STATUS, ".
  	  			"co.link as CURATED_LINK, co.created_by as CURATED_CREATED_BY, to_char(co.created_date,'mm-dd-yyyy') as CURATE_CREATED_DATE, ".
  	  			"co.modified_by as CURATED_MODIFIED_BY, to_char(co.modified_date,'mm-dd-yyyy') as CURATED_MODIFIED_DATE ".
  	  			//"co.it_contact as CURATED_IT_CONTACT, co.it_contact_email as CURATED_IT_CONTACT_EMAIL ".
  		  	    "from ".NeesConfig::ORACLE_SCHEMA.".experiment e, ".
  	  					NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref cr, ".
  	  					NeesConfig::ORACLE_SCHEMA.".curated_objects co ".
  			    "where e.expid = :nExperimentId ".
  	  			"  and cr.neescentral_objectid (+)= e.expid ".
  	  			"  and co.object_id (+)= cr.curated_entityid";

  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nExperimentId", $p_nExperimentId);

  	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray)){
  	  	return $rowArray;
  	  }
  	  
      return $rowArray[0];
    }
  	
    /**
  	 * Find project by primary key.
  	 *
  	 */
    public static function getProjectById($p_nProjectId){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select p.projid, p.name as PROJECT_NAME, p.title as PROJECT_TITLE, p.short_title as PROJECT_TITLE_SHORT, ".
  	  			"to_char(p.DESCRIPTION) as PROJECT_DESCRIPTION, p.viewable as PROJECT_VIEWABLE, ".
  	  			"p.curation_status as PROJECT_CURATION_STATUS, p.status as PROJECT_STATUS, ". 
  	  			"to_char(p.start_date,'mm-dd-yyyy') as PROJECT_START_DATE, to_char(p.end_date,'mm-dd-yyyy') as PROJECT_END_DATE, p.contact_name, ".
  	  			"co.object_id as CURATED_ID, co.version as CURATED_VERSION, co.object_type as CURATED_OBJECT_TYPE, co.title as CURATED_TITLE, ".
  	  			"co.short_title as CURATED_SHORT_TITLE, to_char(co.object_creation_date,'mm-dd-yyyy') as CURATE_OBJECT_CREATION_DATE, co.name as CURATED_NAME, ".
  	  			"to_char(co.initial_curation_date, 'mm-dd-yyyy') as CURATEE_INITIAL_CUATION_DATE, to_char(co.DESCRIPTION) as CURATED_DESCRIPTION,  ".
  	  			"co.object_visibility as CURATED_OBJECT_VISIBILITY, co.object_status as CURATED_OBJECT_STATUS, ".
  	  			"co.link as CURATED_LINK, co.created_by as CURATED_CREATED_BY, to_char(co.created_date,'mm-dd-yyyy') as CURATE_CREATED_DATE, ".
  	  			"co.modified_by as CURATED_MODIFIED_BY, to_char(co.modified_date,'mm-dd-yyyy') as CURATED_MODIFIED_DATE ".
  	  			//"co.it_contact as CURATED_IT_CONTACT, co.it_contact_email as CURATED_IT_CONTACT_EMAIL ".
  		  	    "from ".NeesConfig::ORACLE_SCHEMA.".project p, ".
  	  					NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref cr, ".
  	  					NeesConfig::ORACLE_SCHEMA.".curated_objects co ".
  			    "where p.projid = :nProjectId ".
  	  			"  and cr.neescentral_objectid (+)= p.projid ".
  	  			"  and co.object_id (+)= cr.curated_entityid";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nProjectId", $p_nProjectId);

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray)){
  	  	return $rowArray;
  	  }
  	  
//    print_r($rowArray[0]);
  	  
  	  return $rowArray[0];
    }
  	
  	/**
     * Find all of the documents related to a specified project.
     * There are not any foreign keys that link the project to 
     * its rescpective data file.  The query is performed using 
     * a like clause to find all documents under the directory:
     * /nees/home/<project_name>.groups
     * @param $p_sProjectName - name of the given project
     * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
     * @return collection of rows (array)
     */
    public static function getProjectDocumentsAll($p_sProjectName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated){
      $sProjectFilePath = "/nees/home/".$p_sProjectName.".groups%";
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select df.id, df.name, SUBSTR(df.path, 33, 100) as path, df.created, to_char(df.DESCRIPTION) as DESCRIPTION, 
  	  			 df.filesize, /*df.mime_type,*/ df.thumb_id, co.object_type, co.object_id, co.object_status, 
  	  			 df.title, co.title as CURATED_TITLE, to_char(co.DESCRIPTION) as CURATED_DESCRIPTION,  
  	  			 co.version as CURATED_VERSION, to_char(co.object_creation_date,'mm-dd-yyyy') as CURATE_CREATION_DATE, 
  	  			 to_char(co.created_date,'mm-dd-yyyy') as CURATE_CREATED_DATE, df.path as DATA_FILE_PATH 
				 from data_file df 
				 left join curatedncidcross_ref cr on df.id = cr.neescentral_objectid
				 left join curated_objects co on cr.curated_entityid = co.object_id 
				 where df.path like :sPath 
				   and df.deleted = :nDeleted 
				   /*and df.directory = :nDirectory*/ 
				 order by df.path, df.name";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sPath", $sProjectFilePath);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  //$oDbStatement->bind(":nDirectory", 0);
  	  
  	  #execute query
	  $rowArray = array();
	  $iRowIndex = 0;
  	  $oResultSet = DbHelper::executeStatement($oConnection, $oDbStatement);
      while ($oResultArray = oci_fetch_array($oResultSet,OCI_BOTH)) {
      	$sFileName = $oResultArray['NAME'];
      	$strFileNameArray = explode(".", $sFileName);
      	$oResultArray['EXTENTION'] = $strFileNameArray[1];
      	
      	/*
      	 * If the project is uncurated, just display 
      	 * all of the possible object types.  Otherwise, 
      	 * find the selected object type.
      	 */
      	$iObjectId = $oResultArray['OBJECT_ID'];
      	if($iObjectId > 0){
      	  $sFileCategoryHtml = CurateHtml::getSelectedCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $oResultArray['OBJECT_TYPE'], $iRowIndex);
      	  $oResultArray['TITLE'] = $oResultArray['CURATED_TITLE'];
      	  $oResultArray['DESCRIPTION'] = $oResultArray['CURATED_DESCRIPTION']; 
      	}else{
      	  $sFileCategoryHtml = CurateHtml::getCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $iRowIndex);
      	}
      	$oResultArray['OBJECT_TYPE'] = $sFileCategoryHtml;
      	
      	if(!StringHelper::hasText($oResultArray["CURATE_CREATED_DATE"])){
      	  $oResultArray["CURATE_CREATED_DATE"] = date("m-d-Y");
      	}
      	
        if(!StringHelper::hasText($oResultArray["CURATED_VERSION"])){
      	  $oResultArray["CURATED_VERSION"] = "0";
      	}
      	
        array_push($rowArray, $oResultArray);
        ++$iRowIndex;
      }
      
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectDocumentsAll
    
    /**
     * Find all of the documents related to a specified project.
     * There are not any foreign keys that link the project to 
     * its rescpective data file.  The query is performed using 
     * a like clause to find all documents under the directory:
     * /nees/home/<project_name>.groups
     * @param $p_sProjectName - name of the given project
     * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
     * @return collection of rows (array)
     */
    public static function getProjectDocumentsByPage($p_sProjectName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated, $p_iPage){
      $sProjectFilePath = "/nees/home/".$p_sProjectName.".groups%";
      
      $iLowerLimit = $p_iPage * 100;
      $iUpperLimit = $iLowerLimit + 100; 
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "SELECT * 
				 FROM (
				 	 select df.id, df.name, SUBSTR(df.path, 33, 100) as path, df.created, to_char(df.DESCRIPTION) as DESCRIPTION, 
	  	  			 df.filesize, /*df.mime_type,*/ df.thumb_id, co.object_type, co.object_id, co.object_status, 
	  	  			 df.title, co.title as CURATED_TITLE, to_char(co.DESCRIPTION) as CURATED_DESCRIPTION,  
	  	  			 co.version as CURATED_VERSION, to_char(co.object_creation_date,'mm-dd-yyyy') as CURATE_CREATION_DATE, 
	  	  			 to_char(co.created_date,'mm-dd-yyyy') as CURATE_CREATED_DATE, df.path as DATA_FILE_PATH, 
	  	  			 row_number()
	  	  			 OVER (ORDER BY df.path, df.name) as rn   
					 from data_file df 
					 left join curatedncidcross_ref cr on df.id = cr.neescentral_objectid
					 left join curated_objects co on cr.curated_entityid = co.object_id 
					 where df.path like :sPath 
					   and df.deleted = :nDeleted 
					   /*and df.directory = :nDirectory*/ 
					 order by df.path, df.name
				) 
				WHERE rn BETWEEN $iLowerLimit AND $iUpperLimit";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sPath", $sProjectFilePath);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  //$oDbStatement->bind(":nDirectory", 0);
  	  
  	  #execute query
	  $rowArray = array();
	  $iRowIndex = 0;
  	  $oResultSet = DbHelper::executeStatement($oConnection, $oDbStatement);
      while ($oResultArray = oci_fetch_array($oResultSet,OCI_BOTH)) {
      	$sFileName = $oResultArray['NAME'];
      	$strFileNameArray = explode(".", $sFileName);
      	$oResultArray['EXTENTION'] = $strFileNameArray[1];
      	
      	/*
      	 * If the project is uncurated, just display 
      	 * all of the possible object types.  Otherwise, 
      	 * find the selected object type.
      	 */
      	$iObjectId = $oResultArray['OBJECT_ID'];
      	if($iObjectId > 0){
      	  $sFileCategoryHtml = CurateHtml::getSelectedCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $oResultArray['OBJECT_TYPE'], $iRowIndex);
      	  $oResultArray['TITLE'] = $oResultArray['CURATED_TITLE'];
      	  $oResultArray['DESCRIPTION'] = $oResultArray['CURATED_DESCRIPTION']; 
      	}else{
      	  $sFileCategoryHtml = CurateHtml::getCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $iRowIndex);
      	}
      	$oResultArray['OBJECT_TYPE'] = $sFileCategoryHtml;
      	
      	if(!StringHelper::hasText($oResultArray["CURATE_CREATED_DATE"])){
      	  $oResultArray["CURATE_CREATED_DATE"] = date("m-d-Y");
      	}
      	
        if(!StringHelper::hasText($oResultArray["CURATED_VERSION"])){
      	  $oResultArray["CURATED_VERSION"] = "0";
      	}
      	
        array_push($rowArray, $oResultArray);
        ++$iRowIndex;
      }
      
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectDocumentsAll
    
    /**
     * Find all of the documents related to a specified project.
     * There are not any foreign keys that link the project to 
     * its rescpective data file.  The query is performed using 
     * a like clause to find all documents under the directory:
     * /nees/home/<project_name>.groups
     * @param $p_sProjectName - name of the given project
     * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
     * @return collection of rows (array)
     */
    public static function getExperimentDocumentsAll($p_sProjectName, $p_strExperimentName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated){
      $sExperimentFilePath = "/nees/home/".$p_sProjectName.".groups/".$p_strExperimentName."%";
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select df.id, df.name, SUBSTR(df.path, 33, 100) as path, df.created, to_char(df.DESCRIPTION) as DESCRIPTION, 
  	  			 df.filesize, /*df.mime_type,*/ df.thumb_id, co.object_type, co.object_id, co.object_status, 
  	  			 df.title, co.title as CURATED_TITLE, to_char(co.DESCRIPTION) as CURATED_DESCRIPTION,  
  	  			 co.version as CURATED_VERSION, to_char(co.object_creation_date,'mm-dd-yyyy') as CURATE_CREATION_DATE, 
  	  			 to_char(co.created_date,'mm-dd-yyyy') as CURATE_CREATED_DATE, df.path as DATA_FILE_PATH  
				 from data_file df 
				 left join curatedncidcross_ref cr on df.id = cr.neescentral_objectid
				 left join curated_objects co on cr.curated_entityid = co.object_id 
				 where df.path like :sPath 
				   and df.deleted = :nDeleted 
				   /*and df.directory = :nDirectory*/ 
				 order by df.path, df.name";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sPath", $sExperimentFilePath);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  //$oDbStatement->bind(":nDirectory", 0);
  	  
  	  #execute query
	  $rowArray = array();
	  $iRowIndex = 0;
  	  $oResultSet = DbHelper::executeStatement($oConnection, $oDbStatement);
      while ($oResultArray = oci_fetch_array($oResultSet,OCI_BOTH)) {
      	$sFileName = $oResultArray['NAME'];
      	$strFileNameArray = explode(".", $sFileName);
      	$oResultArray['EXTENTION'] = $strFileNameArray[1];
      	
      	/*
      	 * If the project is uncurated, just display 
      	 * all of the possible object types.  Otherwise, 
      	 * find the selected object type.
      	 */
      	$iObjectId = $oResultArray['OBJECT_ID'];
      	if($iObjectId > 0){
      	  $sFileCategoryHtml = CurateHtml::getSelectedCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $oResultArray['OBJECT_TYPE'], $iRowIndex);
      	  $oResultArray['TITLE'] = $oResultArray['CURATED_TITLE'];
      	  $oResultArray['DESCRIPTION'] = $oResultArray['CURATED_DESCRIPTION']; 
      	}else{
      	  $sFileCategoryHtml = CurateHtml::getCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $iRowIndex);
      	}
      	$oResultArray['OBJECT_TYPE'] = $sFileCategoryHtml;
      	
      	if(!StringHelper::hasText($oResultArray["CURATE_CREATED_DATE"])){
      	  $oResultArray["CURATE_CREATED_DATE"] = date("m-d-Y");
      	}
      	
        if(!StringHelper::hasText($oResultArray["CURATED_VERSION"])){
      	  $oResultArray["CURATED_VERSION"] = "0";
      	}
      	
        array_push($rowArray, $oResultArray);
        ++$iRowIndex;
      }
      
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectDocumentsAll
    
    /**
     * Find all of the documents related to a specified experiment.
     * There are not any foreign keys that link the project to 
     * its rescpective data file.  The query is performed using 
     * a like clause to find all documents under the directory:
     * /nees/home/<project_name>.groups
     * @param $p_sProjectName - name of the given project
     * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
     * @return collection of rows (array)
     */
    public static function getExperimentDocumentsByPage($p_sProjectName, $p_strExperimentName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated, $p_iPage){
      $sExperimentFilePath = "/nees/home/".$p_sProjectName.".groups/".$p_strExperimentName."%";$sProjectFilePath = "/nees/home/".$p_sProjectName.".groups%";
      
      $iLowerLimit = $p_iPage * 100;
      $iUpperLimit = $iLowerLimit + 100; 
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "SELECT * 
				 FROM (
				 	 select df.id, df.name, SUBSTR(df.path, 33, 100) as path, df.created, to_char(df.DESCRIPTION) as DESCRIPTION, 
	  	  			 df.filesize, /*df.mime_type,*/ df.thumb_id, co.object_type, co.object_id, co.object_status, 
	  	  			 df.title, co.title as CURATED_TITLE, to_char(co.DESCRIPTION) as CURATED_DESCRIPTION,  
	  	  			 co.version as CURATED_VERSION, to_char(co.object_creation_date,'mm-dd-yyyy') as CURATE_CREATION_DATE, 
	  	  			 to_char(co.created_date,'mm-dd-yyyy') as CURATE_CREATED_DATE, df.path as DATA_FILE_PATH, 
	  	  			 row_number()
	  	  			 OVER (ORDER BY df.path, df.name) as rn   
					 from data_file df 
					 left join curatedncidcross_ref cr on df.id = cr.neescentral_objectid
					 left join curated_objects co on cr.curated_entityid = co.object_id 
					 where df.path like :sPath 
					   and df.deleted = :nDeleted 
					   /*and df.directory = :nDirectory*/ 
					 order by df.path, df.name
				) 
				WHERE rn BETWEEN $iLowerLimit AND $iUpperLimit";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sPath", $sExperimentFilePath);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  //$oDbStatement->bind(":nDirectory", 0);
  	  
  	  #execute query
	  $rowArray = array();
	  $iRowIndex = 0;
  	  $oResultSet = DbHelper::executeStatement($oConnection, $oDbStatement);
      while ($oResultArray = oci_fetch_array($oResultSet,OCI_BOTH)) {
      	$sFileName = $oResultArray['NAME'];
      	$strFileNameArray = explode(".", $sFileName);
      	$oResultArray['EXTENTION'] = $strFileNameArray[1];
      	
      	/*
      	 * If the project is uncurated, just display 
      	 * all of the possible object types.  Otherwise, 
      	 * find the selected object type.
      	 */
      	$iObjectId = $oResultArray['OBJECT_ID'];
      	if($iObjectId > 0){
      	  $sFileCategoryHtml = CurateHtml::getSelectedCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $oResultArray['OBJECT_TYPE'], $iRowIndex);
      	  $oResultArray['TITLE'] = $oResultArray['CURATED_TITLE'];
      	  $oResultArray['DESCRIPTION'] = $oResultArray['CURATED_DESCRIPTION']; 
      	}else{
      	  $sFileCategoryHtml = CurateHtml::getCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $iRowIndex);
      	}
      	$oResultArray['OBJECT_TYPE'] = $sFileCategoryHtml;
      	
      	if(!StringHelper::hasText($oResultArray["CURATE_CREATED_DATE"])){
      	  $oResultArray["CURATE_CREATED_DATE"] = date("m-d-Y");
      	}
      	
        if(!StringHelper::hasText($oResultArray["CURATED_VERSION"])){
      	  $oResultArray["CURATED_VERSION"] = "0";
      	}
      	
        array_push($rowArray, $oResultArray);
        ++$iRowIndex;
      }
      
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectDocumentsAll
  
  
    /**
     * Find all of the curation object types
     * @return array of curation object types
     */
    public static function getCurationObjectTypes(){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select distinct object_type from ".NeesConfig::ORACLE_SCHEMA.".curated_objects";
  	 
  	
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	
  	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getCurationObjectTypes
    
    /**
     * Find a list of projects by visibility and curation status.
     *
     */
    public static function getProjectsCount($p_nDeleted){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $sQuery =	"SELECT count(p.projid) as num ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project p, ". 
  	  					NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref cr ". 
  				"WHERE p.deleted=:nDeleted ".
    			"  and cr.neescentral_objectid (+)= p.projid ".
  	  			"  and cr.neescentral_objectid is null";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray[0]['NUM']))
  	    return 0;
  	  
  	  return $rowArray[0]['NUM'];
    }//end getProjectsByCurationStatus
    
    public static function getProjectsWithPagination($p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  #computer lower and upper limits
  	  $nLowerLimit = ($p_nCurrentIndex * $p_nDisplaySize) + $p_nCurrentIndex;
  	  $nUpperLimit = ($p_nCurrentIndex+1) * $p_nDisplaySize;

	  /* 	
	   * NOTE:
	   * If the display size is 0, the user wants ALL rows.
	   * Thus, the upper limit will be zero, and we can't 	
	   * use the BETWEEN clause.  The search must use  the 
       * greater than row number clause. 	  
	   */
  	  $sQuery =	"SELECT * ". 
				"FROM( ". 
  				"  SELECT p.projid, p.name, p.title, p.viewable, p.curation_status, ".
  	  			"         p.contact_name, p.contact_email, row_number() ". 
  				"  OVER (order by p.name desc) rn ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project p, ".
  	  					NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref cr ". 
  				"WHERE p.deleted=:nDeleted ".
    			"  and cr.neescentral_objectid (+)= p.projid ".
  	  			"  and cr.neescentral_objectid is null".
				") ";
	  if($nUpperLimit != 0){ 
	    $sQuery = $sQuery . "WHERE rn BETWEEN :nLowerLimit and :nUpperLimit "; 
	  }else{
	    $sQuery = $sQuery . "WHERE rn > :nUpperLimit ";            
	  }
	  $sQuery = $sQuery .	"order by name desc";
	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);

	  /*
       * See note above about setting search boundaries.
       */
	  if($nUpperLimit != 0)$oDbStatement->bind(":nLowerLimit", $nLowerLimit);
  	  $oDbStatement->bind(":nUpperLimit", $nUpperLimit);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectsByCurationStatusWithPagination
    
    /**
     * Find a list of projects by deleted and curation name.
     *
     */
    public static function getProjectsCountByName($p_nDeleted, $p_sName){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $sQuery =	"SELECT count(p.projid) as num ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project p, ". 
  	  					NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref cr ". 
  				"WHERE p.deleted=:nDeleted ".
    			"  and upper(p.name) like :sName ".
  	  			"  and cr.neescentral_objectid (+)= p.projid ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sName", "%".strtoupper($p_sName)."%");

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray[0]['NUM']))
  	    return 0;
  	  
  	  return $rowArray[0]['NUM'];
    }//end getProjectsByCurationStatus
    
    /**
     * 
     *
     */
    public static function getProjectsByNameWithPagination($p_sName, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  #computer lower and upper limits
  	  $nLowerLimit = $p_nCurrentIndex * $p_nDisplaySize;
  	  $nUpperLimit = ($p_nCurrentIndex+1) * $p_nDisplaySize;
  	  
  	  /* 	
	   * NOTE:
	   * If the display size is 0, the user wants ALL rows.
	   * Thus, the upper limit will be zero, and we can't 	
	   * use the BETWEEN clause.  The search must use  the 
       * greater than row number clause. 	  
	   */
  	  $sQuery =	"SELECT * ". 
				"FROM( ". 
  				"  SELECT p.projid, p.name, p.title, p.viewable, p.curation_status, ".
  	  			"         p.contact_name, p.contact_email, cr.neescentral_objectid, row_number() ". 
  				"  OVER (order by p.name desc) rn ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project p, ". 
  	  					NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref cr ". 
  				"WHERE p.deleted=:nDeleted ".
    			"  and upper(p.name) like :sName ".
  	  			"  and cr.neescentral_objectid (+)= p.projid ".
				") ";
	  if($nUpperLimit != 0){ 
	    $sQuery = $sQuery . "WHERE rn BETWEEN :nLowerLimit and :nUpperLimit "; 
	  }else{
	    $sQuery = $sQuery . "WHERE rn > :nUpperLimit ";            
	  }
	  $sQuery = $sQuery .	"order by name desc";
  	  
  	  //bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sName", "%".$p_sName."%");

	  /*
       * See note above about setting search boundaries.
       */
	  if($nUpperLimit != 0)$oDbStatement->bind(":nLowerLimit", $nLowerLimit);
  	  $oDbStatement->bind(":nUpperLimit", $nUpperLimit);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;	
    }//getProjectsByNameWithPagination
    
    /**
     * 
     *
     */
    public static function updateVersion($p_sValue, $p_nObjectId){
      if(is_null($p_sValue)){
      	throw new Exception("Version should not be blank.");
      }
      
      if(empty($p_nObjectId)){
      	throw new Exception("Select an object to update.");
      }
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      
      $sQuery = "update ".NeesConfig::ORACLE_SCHEMA.".curated_objects co ".
      			"set co.version=:sValue ".
      			"where co.object_id=:nObjectId"; 
      
      #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sValue", $p_sValue);
  	  $oDbStatement->bind(":nObjectId", $p_nObjectId);
  	  
  	  #execute query
  	  $nResults = DbHelper::executeUpdate($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  return $nResults;
    }
    
    /**
     * Update the curated_objects table given a column, its value, and primary key.
     * @param $p_strColumn
     * @param $p_strValue
     * @param $p_iObjectId
     *
     */
    public static function updateCuratedObject($p_strColumn, $p_strValue, $p_iObjectId){
      if(strlen($p_strColumn)===0){
      	throw new Exception("Column should not be blank.");
      }
      
      if(strlen($p_strValue)===0){
      	throw new Exception($p_strColumn."'s value should not be blank.");
      }
      
      if(strlen($p_iObjectId)===0){
      	throw new Exception("Select an object to update.");
      }
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      
      $sQuery = "update ".NeesConfig::ORACLE_SCHEMA.".curated_objects ".
      			"set ".$p_strColumn."=:sValue ".
      			"where object_id=:nObjectId"; 
      
      #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sValue", $p_strValue);
  	  $oDbStatement->bind(":nObjectId", $p_iObjectId);
  	  
  	  #execute query
  	  $nResults = DbHelper::executeUpdate($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  return $nResults;	
    }
    
    /**
     * Get a curated object attribute using its object id.
     */
    public static function getCuratedObjectAttribute($p_strColumn, $p_iObjectId){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      
      /*
       * description is a clob.  convert the return type using to_char()
       */  
      if(strtolower($p_strColumn) === "description"){
      	$p_strColumn = "to_char($p_strColumn)";
      }
      
      $sQuery = "select ".$p_strColumn." ".
      			"from ".NeesConfig::ORACLE_SCHEMA.".curated_objects ".
      			"where object_id=:iObjectId"; 
      
      //bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":iObjectId", $p_iObjectId);
  	  
  	  #execute query
  	  $oResultsArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  return $oResultsArray[0][strtoupper($p_strColumn)];
    }//end getCuratedObjectAttribute
    
    /**
     * Insert or update an array of DbStatements.
     * @param $p_oDocumentDbStatementArray - array of DbStatements (queries and bound values)
     */
    public static function executeBatch($p_oDocumentDbStatementArray){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      $bReturn = DbHelper::executeBatch($oConnection, $p_oDocumentDbStatementArray);
      DbHelper::close($oConnection);
      return $bReturn;
    }
    
    /**
     * Insert a project into the curated_object table. 
     *
     */
    public static function insertProject($p_iVersion, $p_strObjectType, $p_strName, 
    									$p_strTitle, $p_strTitleShort, $p_strDescription,
    									$p_oObjectCreationDate, $p_oInitialCurationDate,
    									$p_strCuratonState, $p_strObjectVisibility, 
    									$p_strObjectStatus, $p_strConformanceLevel,
    									$p_strLink,$p_strCreatedBy, $p_oCreatedDate,
    									$p_strModifiedBy, $p_oModifiedDate){

  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      
      $sQuery = "insert into ".NeesConfig::ORACLE_SCHEMA.".curated_objects (object_id, version,object_type,name,title,short_title, ".
      			"description,object_creation_date,initial_curation_date,curation_state,object_visibility,object_status, ".
      			"conformance_level,link,created_by,created_date,modified_by,modified_date) ".
      			"values(curated_objects_seq.nextval, :version, :object_type, :name, :title, :short_title, :description, to_date(:object_creation_date,'MM-dd-yyyy'), ".
      			"sysdate, :curation_state, :object_visibility, :object_status, :conformance_level, :link, ".
      			":created_by, sysdate, :modified_by, sysdate)"; 
      
      //bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":version", $p_iVersion);
  	  $oDbStatement->bind(":object_type", $p_strObjectType);
  	  $oDbStatement->bind(":name", $p_strName);
  	  $oDbStatement->bind(":title", $p_strTitle);
  	  $oDbStatement->bind(":short_title", $p_strTitleShort);
  	  $oDbStatement->bind(":description", $p_strDescription);
  	  $oDbStatement->bind(":object_creation_date", $p_oObjectCreationDate);
  	  $oDbStatement->bind(":curation_state", $p_strCuratonState);
  	  $oDbStatement->bind(":object_visibility", $p_strObjectVisibility); 
  	  $oDbStatement->bind(":object_status", $p_strObjectStatus);
  	  $oDbStatement->bind(":conformance_level", $p_strConformanceLevel);
  	  $oDbStatement->bind(":created_by", $p_strCreatedBy);
  	  $oDbStatement->bind(":link", $p_strLink);
  	  $oDbStatement->bind(":modified_by", $p_strModifiedBy);
  	  
      $bReturn = DbHelper::executeUpdate($oConnection, $oDbStatement);
      
      DbHelper::close($oConnection);
      
      return $bReturn;
    }
    
    /**
     * Inserts an experiment or project into curation_objects table.
     *
     */
    public static function insertObject($p_iVersion, $p_strObjectType, $p_strName, 
    									$p_strTitle, $p_strTitleShort, $p_strDescription,
    									$p_oObjectCreationDate, $p_oInitialCurationDate,
    									$p_strCuratonState, $p_strObjectVisibility, 
    									$p_strObjectStatus, $p_strConformanceLevel,
    									$p_strLink,$p_strCreatedBy, $p_oCreatedDate,
    									$p_strModifiedBy, $p_oModifiedDate){

  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      
      $sQuery = "insert into ".NeesConfig::ORACLE_SCHEMA.".curated_objects (object_id, version,object_type,name,title,short_title, ".
      			"description,object_creation_date,initial_curation_date,curation_state,object_visibility,object_status, ".
      			"conformance_level,link,created_by,created_date,modified_by,modified_date) ".
      			"values(curated_objects_seq.nextval, :version, :object_type, :name, :title, :short_title, :description, to_date(:object_creation_date,'MM-dd-yyyy'), ".
      			"sysdate, :curation_state, :object_visibility, :object_status, :conformance_level, :link, ".
      			":created_by, sysdate, :modified_by, sysdate)"; 
      
      //bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":version", $p_iVersion);
  	  $oDbStatement->bind(":object_type", $p_strObjectType);
  	  $oDbStatement->bind(":name", $p_strName);
  	  $oDbStatement->bind(":title", $p_strTitle);
  	  $oDbStatement->bind(":short_title", $p_strTitleShort);
  	  $oDbStatement->bind(":description", $p_strDescription);
  	  $oDbStatement->bind(":object_creation_date", $p_oObjectCreationDate);
  	  $oDbStatement->bind(":curation_state", $p_strCuratonState);
  	  $oDbStatement->bind(":object_visibility", $p_strObjectVisibility); 
  	  $oDbStatement->bind(":object_status", $p_strObjectStatus);
  	  $oDbStatement->bind(":conformance_level", $p_strConformanceLevel);
  	  $oDbStatement->bind(":created_by", $p_strCreatedBy);
  	  $oDbStatement->bind(":link", $p_strLink);
  	  $oDbStatement->bind(":modified_by", $p_strModifiedBy);
  	  
      $bReturn = DbHelper::executeUpdate($oConnection, $oDbStatement);
      
      DbHelper::close($oConnection);
      
      return $bReturn;
    }
    
    /**
     * Lookup a curated object using its title and link
     * @param $p_strTitle - title
     * @param $p_strLink - link to the object
     */
    public static function getCuratedObjectByTitleAndLink($p_strTitle, $p_strLink){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);

      $strQuery = "select object_id, version, object_type, name, title, short_title, to_char(description) as description, ".
      			  "object_creation_date, initial_curation_date, curation_state, object_visibility, object_status, ".
      			  "conformance_level, link, created_by, created_date, modified_by, modified_date ".
      			  "from ".NeesConfig::ORACLE_SCHEMA.".curated_objects ".
      			  "where title = :strTitle ".
      			  "  and link = :strLink";
      
      $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  $oDbStatement->bind(":strTitle", $p_strTitle);
  	  $oDbStatement->bind(":strLink", $p_strLink);
  	  
  	  //execute query
  	  $oResultsArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(!empty($oResultsArray)){
  	  	return $oResultsArray[0];
  	  }else{
  	  	return array();
  	  }
    }
    
    /**
     * Insert a record into the curatedncidcross_ref table.
     * @param $p_iNeesCentralId - project, data file identifier
     * @param $p_iCuratedObjectId - identifier from curated_object table
     * @param $p_strTableSource - table of nees central object 
     * @param $p_strCreatedBy - who is making the insert
     */
    public static function insertCuratedNcIdCrossRef($p_iNeesCentralId, $p_iCuratedObjectId, $p_strTableSource, $p_strCreatedBy){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);

      $strQuery = "insert into ".NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref ".
      			  "(id, neescentral_objectid, curated_entityid, neescentral_table_source, created_by, created_date) ".
      			  "values (curatedncidcross_ref_seq.nextval, :neescentral_objectid, :curated_entityid, :neescentral_table_source, :created_by, sysdate)";
      
      $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  $oDbStatement->bind(":neescentral_objectid", $p_iNeesCentralId);
  	  $oDbStatement->bind(":curated_entityid", $p_iCuratedObjectId);
  	  $oDbStatement->bind(":neescentral_table_source", $p_strTableSource);
  	  $oDbStatement->bind(":created_by", $p_strCreatedBy);
  	  
  	  $bReturn = DbHelper::executeUpdate($oConnection, $oDbStatement);
      
      DbHelper::close($oConnection);
      
      return $bReturn;
    }
    
    public static function getExperiments($p_iProjectId){
  	  $strQuery = "select e.expid, e.name, e.title 
		  		   from ".NeesConfig::ORACLE_SCHEMA.".experiment e, ".
  	  					  NeesConfig::ORACLE_SCHEMA.".project p 
				   where p.projid=:projectId 
				     and e.projid = p.projid
				   order by e.name";
  	  
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  $oDbStatement->bind(":projectId", $p_iProjectId);
  	  
  	  $bReturn = DbHelper::executeQuery($oConnection, $oDbStatement);
      
      DbHelper::close($oConnection);
      
      return $bReturn;
    }
  }//end class
?>  