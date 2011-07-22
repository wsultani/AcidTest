<?
  // open connection to mysql database
  $user="btc_user";
  $password="btc_user";
  $database="btc_db";
  mysql_connect(localhost,$user,$password);
  @mysql_select_db($database) or die( "Unable to select database");
  $bRetVal = TRUE;
  return $bRetVal;
?>
