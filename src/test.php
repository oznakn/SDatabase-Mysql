<html><head><meta charset="UTF-8"></head><body>
<?php
  include "SDatabase.php";

  $helper = new SQLHelper("localhost", "database_username","database_password","database_name");

  echo $helper->exportToSDatabaseFormatString();

?>
</body></html>
