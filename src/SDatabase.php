<?php
  class SQLHelper {
    public $mDBPlace;
    public $mDBUser;
    public $mDBPassword;
    public $mDBName;

    public function SQLHelper($databasePlace, $databaseUser, $databasePassword, $databaseName) {
      $this->mDBPlace    = $databasePlace;
      $this->mDBUser     = $databaseUser;
      $this->mDBPassword = $databasePassword;
      $this->mDBName     = $databaseName;
    }

    private function getCon($dbName) {
      $connect = mysqli_connect($this->mDBPlace , $this->mDBUser , $this->mDBPassword);
      mysqli_select_db($connect, $dbName);
      mysqli_query($connect , "SET NAMES UTF8");
      return $connect;
    }

    private function getInformationCon() {
      return $this->getCon("information_schema");
    }

    private function getDBCon() {
      return $this->getCon($this->mDBName);
    }

    private function queryToList($connect, $query) {
      $list = array();
      $answer = mysqli_query($connect, $query);
      echo mysqli_error($connect);
      while($result = mysqli_fetch_array($answer)) {
        array_push($list, $result);
      }
      return $list;
    }

    private function getTableList() {
      return $this->queryToList($this->getInformationCon(), "SELECT TABLE_NAME,TABLE_SCHEMA FROM TABLES WHERE TABLE_SCHEMA = '$this->mDBName'");
    }

    private function getTableColumnList($tableName) {
      return $this->queryToList($this->getInformationCon(), "SELECT TABLE_NAME,TABLE_SCHEMA,COLUMN_NAME,EXTRA FROM COLUMNS WHERE TABLE_NAME = '$tableName' AND TABLE_SCHEMA = '$this->mDBName'");
    }

    private function getTableItems($tableName) {
      return $this->queryToList($this->getDBCon(),  "SELECT * FROM $tableName");
    }

    public function exportToSDatabaseFormatString() {
      $string = "";
      $tableList = $this->getTableList();

      for($i = 0; $i < count($tableList); $i++) {
        $string .= "{['" . $tableList[$i]["TABLE_NAME"] . "'=";

        $columnList = $this->getTableColumnList($tableList[$i]["TABLE_NAME"]);
        for($j = 0; $j < count($columnList); $j++) {
          $string .= "'" . $columnList[$j]["COLUMN_NAME"] . "'";

          if($columnList[$j]["EXTRA"] == "auto_increment") {
            $string .= ":ai";
          }

          if($j < count($columnList) - 1) {
            $string .= ",";
          }
        }
        $string .= "];";

        $itemsList = $this->getTableItems($tableList[$i]["TABLE_NAME"]);
        for($k = 0; $k < count($itemsList); $k++) {
          $string .= "(";

          for($u = 0; $u < count($columnList); $u++) {
            $string .= "'" . $itemsList[$k][$columnList[$u]["COLUMN_NAME"]] ."'";
            if($u < count($columnList) - 1) {
              $string .= ",";
            }
          }
          $string .= ");";
        }
        $string .= "};";
      }
      return $string;
    }
  }
?>
