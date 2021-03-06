<?php

define("REPORTING_YEAR_REAL", YEAR); // Hard-coded year for the reporting period
if(isset($_GET['reportingYear']) && 
   ((preg_match("/.*Special:Report.*/", $_SERVER["REQUEST_URI"]) !== false && isset($_GET['ticket']) && isset($_GET['report'])) || 
     preg_match("/.*Special:CreatePDF.*/", $_SERVER["REQUEST_URI"]) !== false)){
    define("REPORTING_YEAR", str_replace("'", "", $_GET['reportingYear']));
}
else{
    define("REPORTING_YEAR", REPORTING_YEAR_REAL);
}

define("REPORTING_CYCLE_START_MONTH", CYCLE_START_MONTH);
define("REPORTING_NCE_START_MONTH", NCE_START_MONTH);
define("REPORTING_START_MONTH", START_MONTH);
define("REPORTING_END_MONTH", END_MONTH);
define("REPORTING_CYCLE_END_MONTH_ACTUAL", CYCLE_END_MONTH_ACTUAL);
define("REPORTING_CYCLE_END_MONTH", CYCLE_END_MONTH);
define("REPORTING_PRODUCTION_MONTH", PRODUCTION_MONTH);
define("REPORTING_RMC_REVISED_MONTH", RMC_REVISED_MONTH);
define("REPORTING_RMC_MEETING_MONTH", RMC_MEETING_MONTH);
define("REPORTING_NCE_END_MONTH", NCE_END_MONTH);
define("REPORTING_NCE_PRODUCTION_MONTH", NCE_PRODUCTION_MONTH);

define("REPORTING_CYCLE_START", REPORTING_YEAR.REPORTING_CYCLE_START_MONTH); // Start of internal reporting cycle (Used for range queries)
define("REPORTING_NCE_START", REPORTING_YEAR.REPORTING_NCE_START_MONTH); // Start of NCE reporting cycle
define("REPORTING_START", REPORTING_YEAR.REPORTING_START_MONTH); // Start of reporting period
define("REPORTING_END", REPORTING_YEAR.REPORTING_END_MONTH); // End of reporting period for HQP, NIs and Projects
define("REPORTING_CYCLE_END_ACTUAL", REPORTING_YEAR.REPORTING_CYCLE_END_MONTH_ACTUAL); // End of internal reporting cycle (Used for range queries)
define("REPORTING_CYCLE_END", (REPORTING_YEAR+1).REPORTING_CYCLE_END_MONTH); // End of internal reporting cycle (Used for range queries)
define("REPORTING_PRODUCTION", (REPORTING_YEAR+1).REPORTING_PRODUCTION_MONTH); // Production of NI and Project reports
define("REPORTING_RMC_REVISED", (REPORTING_YEAR+1).REPORTING_RMC_REVISED_MONTH); // RMC when evaluator reports can be revised
define("REPORTING_RMC_MEETING", (REPORTING_YEAR+1).REPORTING_RMC_MEETING_MONTH); // RMC meeting for fund allocation
define("REPORTING_NCE_END", (REPORTING_YEAR+1).REPORTING_NCE_END_MONTH); // End of NCE reporting cycle
define("REPORTING_NCE_PRODUCTION", (REPORTING_YEAR+1).REPORTING_NCE_PRODUCTION_MONTH); // Production of NCE report

?>
