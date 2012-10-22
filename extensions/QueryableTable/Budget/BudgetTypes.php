<?php

autoload_register('QueryableTable/Budget/Cells');
autoload_register('QueryableTable/Budget/Arrays');

//CellTypes
////Basic Types
define('MONEY', 0);
define('PERC', 1);
define('TOTAL', 2);
define('ROW_TOTAL', 3);
define('COL_TOTAL', 4);
define('ROW_SUM', 5);
define('COL_SUM', 6);
define('CUBE_ROW_TOTAL', 25);
define('CUBE_COL_TOTAL', 26);
define('CUBE_TOTAL', 27);
////Validation Types
define('V_PROJ', 50);
define('V_PERS', 51);
define('V_PERS_NOT_NULL', 52);

$cellTypes[MONEY] = "MoneyCell";
$cellTypes[PERC] = "PercCell";
$cellTypes[TOTAL] = "TotalCell";
$cellTypes[ROW_TOTAL] = "RowTotalCell";
$cellTypes[COL_TOTAL] = "ColTotalCell";
$cellTypes[ROW_SUM] = "RowSumCell";
$cellTypes[COL_SUM] = "ColSumCell";
$cellTypes[CUBE_ROW_TOTAL] = "CubeRowTotalCell";
$cellTypes[CUBE_COL_TOTAL] = "CubeColTotalCell";
$cellTypes[CUBE_TOTAL] = "CubeTotalCell";
$cellTypes[V_PROJ] = "VProjCell";
$cellTypes[V_PERS] = "VPersCell";
$cellTypes[V_PERS_NOT_NULL] = "VPersNotNullCell";

//Budget Structures
define('SUPPLEMENTAL_STRUCTURE', 1);
define('REPORT_STRUCTURE', 2);
define('REPORT2_STRUCTURE', 3);

$budgetStructures = array();
$budgetStructures[SUPPLEMENTAL_STRUCTURE] =
    array(array(HEAD1,  V_PERS_NOT_NULL, BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PROJ, V_PROJ, V_PROJ, V_PROJ, V_PROJ, V_PROJ, BLANK,  BLANK,  BLANK),
          array(NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA),
          array(NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA),
          array(NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA),
          array(NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA),
          array(HEAD1,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  HEAD1,  HEAD1,  HEAD1),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD1,  COL_SUM,COL_SUM,COL_SUM,COL_SUM,COL_SUM,COL_SUM,ROW_SUM,PERC,   PERC)
    );
    
$budgetStructures[REPORT2_STRUCTURE] =
    array(array(HEAD1,  V_PERS_NOT_NULL, BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PROJ, V_PROJ, V_PROJ, V_PROJ, V_PROJ, V_PROJ, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, BLANK,  BLANK,  BLANK),
          array(NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA,     NA),
          array(HEAD1,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  HEAD1,  HEAD1,  HEAD1),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD1,  COL_SUM,COL_SUM,COL_SUM,COL_SUM,COL_SUM,COL_SUM,ROW_SUM,PERC,   PERC)
    );
         
$budgetStructures[REPORT_STRUCTURE] =
    array(array(HEAD1,  V_PERS_NOT_NULL,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PROJ, V_PROJ, V_PROJ, V_PROJ, V_PROJ, V_PROJ, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, BLANK,  BLANK,  BLANK),
          array(HEAD1,  V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, V_PERS, BLANK,  BLANK,  BLANK),
          //YEAR1
          array(BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD1,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  HEAD1,  HEAD1,  HEAD1),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD1,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          //YEAR2
          array(BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD1,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  HEAD1,  HEAD1,  HEAD1),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD1,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          //YEAR3
          array(BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD1,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  HEAD1,  HEAD1,  HEAD1),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD2,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK,  BLANK),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD3,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
          array(HEAD1,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  MONEY,  ROW_SUM,PERC,   PERC),
    );
?>
