<html><head><meta charset="UTF-8"></head><body>
<?php
  include "SDatabase.php";

  $helper = new SQLHelper("localhost", "root","","database_name");

  echo $helper->exportToSDatabaseFormatString();

?>
</body></html>
