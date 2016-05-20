<?php 
/** @docbloc */

namespace sqlite;

/**
 * DBIntrd SQLite3 database interface
 */
class DBIntrd {
  /**
   * table var
   * @var text
   */
  static $table = "";
  /**
   * initDB
   * @return object
   */
  function initDB() {
    global $db_path;
    $db = new SQLite3($db_path);
    if (!$db) die ("db error..");
    return $db;
  }
  /**
   * stopDB
   * @return void
   */
  function stopDB($db) {
    $db->close();
  }
  /**
   * queryDB
   * @return object
   */
  function queryDB($query) {
    global $debug;
    if ($debug) echo "<br><span style='font-size:9px; color:green;'> <b>DEBUG_SQL</b>: ".$query."</span>"; //debug queries
    $db=$this->initDB(); 
    //vd($query); //debug
    //DIE;
    $stmt = $db->prepare($query);
    $results = $stmt->execute();
    while ((strpos($query,"SELECT")!==false or strpos($query,"PRAGMA")!==false) 
      and $row = $results->fetchArray(SQLITE3_ASSOC)) { //fetcharray without int index
        $rows[]=$row;
    }
    $this->stopDB($db);
    if (isset($rows)) return $rows;
    if ($results) return $results;
  }
  /**
   * formatInsertDataDB
   * @return object
   */
  function formatInsertDataDB($object){
    $array=(array)$object;
    $array="( ".implode(', ',array_keys($array))." ) VALUES ( '".implode('\', \'',$array)."'  )"; //implode array to an valid INSERT format
    return $array;
  }
  /**
   * formatUpdateDataDB
   * @return object
   */
  function formatUpdateDataDB($object){
    $array=(array)$object;
    $array = implode(', ', array_map(function ($v, $k) { return $k . '=' . "'".$v."'"; }, $array, array_keys($array))); //implode array to a valid UPDATE format
    return $array;
  }
}

/**
 * data interface
 */
class data extends DBIntrd {
  function __construct($table,$id=false,$childs=false) {
    DBIntrd::$table=$table; //stores table name at parent class
    if (is_array($id)){
      $this->id="";
      foreach($id as $key=>$value){
        $this->{$key}=$value;
      }
      return $this; 
    }else{
      $this->id=$id;
    }
    $this->childs=$childs;
    if(!$this->id){
      $data = $this->getColumns(); //fetch table columns if id is not defined
      if ($data) {
        foreach ($data as $column){
          $this->{$column} = "";
        }
      }
    } else if($this->id=="all"){
      $data = $this->getData(true); //fetch all table data if all is set
      if ($data) {
        foreach ($data as $column=>$value){
          $this->{$column} = $value;
        }
      }
    } else if(strpos($this->id,"custom")!==false){
      $custom=explode("custom:",$this->id);
      $custom=$custom[1];
      $data = $this->getData(false,$filter=false,$custom); //fetch customized data
      if ($data) {
        foreach ($data as $column=>$value){
          $this->{$column} = $value;
        }
      }
    } else if(strpos($this->id,"filter")!==false){
      $filter=explode("filter:",$this->id);
      $filter=$filter[1];
      $data = $this->getData(false,$filter); //fetch filtered data
      //$this->filtered = json_decode(json_encode($data), FALSE);
      if ($data) {
        foreach ($data as $column=>$value){
          $this->{$column} = $value;
        }
      }
    } else if($this->id){
      $data = $this->getData(); //fetch table data if id is set
      if ($data) {
        foreach ($data as $column=>$value){
          $this->{$column} = $value;
        }
      }
    }
  }
  /**
   * getColumns
   * @return object
   */
  function getColumns() { 
    $results=$this->queryDB("PRAGMA table_info('".DBIntrd::$table."')");
    foreach($results as $column){
      $columns[]=$column["name"];
    }
    return $columns;
  } 
  /**
   * getData
   * @return object
   */
  function getData($all=false,$filter=false,$custom=false) { 
    //vd($this);
    //die;
    if($all){
      $results=$this->queryDB("SELECT * FROM ".DBIntrd::$table);
    }
    if($custom){
      $results=$this->queryDB($custom); 
    }
    if($filter){
      if (strpos($filter,"|")!==false){
        $filter=explode("|",$filter);
        //vd($filter);
        $results=$this->queryDB("SELECT * FROM ".DBIntrd::$table." WHERE ".$filter[0]."='".$filter[1]."'");
      }else{
        $results=$this->queryDB("SELECT * FROM ".DBIntrd::$table." WHERE ".$filter); //special filter case
      }
      //vd($results);
      //die;
      /* trying to do child objects w/ a SINGLE QUERY.. no success..
      $query="
        SELECT products.id as product_id, products.name as product_name, networks.name as network_name 
          FROM networks 
            JOIN ".DBIntrd::$table." 
              ON products.networks_id = networks.id  
      "; 
      $results=$this->queryDB($query);
      */
    }
    if(!isset($results)){
      $results=$this->queryDB("SELECT * FROM ".DBIntrd::$table." WHERE id='".$this->id."'");
      $results=$results; //return only first matched entry
    }
    if($this->childs){ //propagating SELECT to childs (if u know a best way to do this at SINGLE SQL query, please commit..)
      foreach($results as $mainkey=>$value){
        // vd($results);
        // die;
        foreach($value as $key=>$prop){
          if(strpos($key,"_id")!==false){
            $child_data=explode("_",$key);
            $child_table=$child_data[0];
            $child_id=$prop;
            $child=$this->queryDB("SELECT * FROM ".$child_table." WHERE id='".$child_id."'");
            if (isset($child_data)) unset($child_data);
            $child_data[$child_id]=$child[0];
            $results[$mainkey][$key]=$child_data;
          }
        }
      }
    }
    if (isset($this->childs)) unset($this->childs);
    if (isset($this->id)) unset($this->id); //removing all flag
    $results=json_decode(json_encode($results), FALSE);
    return $results; 
  } 
  /**
   * save (UPDATE OR INSERT)
   * @return object
   */
  function save($update=false) { 
    if(!$update){
      unset($this->id); //popping id to INSERT
      unset($this->childs); //popping childs flag to INSERT
      $data=$this->formatInsertDataDB($this);
      $result=$this->queryDB("INSERT INTO ".DBIntrd::$table." $data ;");
      return $result;
    }else{
      //echo"ok";
      $data=$this->formatUpdateDataDB($this->{0});
      $this->id=$this->{0}->id;
      $result=$this->queryDB("UPDATE ".DBIntrd::$table." SET $data WHERE id = '$this->id';");
      return $result;
    }
  }
}

?>
