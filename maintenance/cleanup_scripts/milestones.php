<?php
include_once('../commandLine.inc');

//change the schema
$sql = "ALTER TABLE `grand_milestones_people` CHANGE COLUMN `person_id` `user_id` INT(11) NOT NULL";
DBFunctions::execSQL($sql, true);

echo "ALL DONE!\n";

?>
