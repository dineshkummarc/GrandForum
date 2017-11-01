<?php

class UserGsmsBulkUploadAPI extends API{

    function UserGsmsBulkUploadAPI(){
    }

    function processParams($params){

    }

    function extract_excel_data($contents){
	$i = 0;
	$data_array = array();
	foreach($contents as $row){
	    if($i == 0){
		$i++;
		continue; //fix this to check instead of just skipping
	    }
	    elseif($row[2] == ""){
		break;
	    }
		//set student
	    $student_name = $row[2]." ".$row[1];
	    $array_info = array();
	    $array_info['name'] = $student_name;
	    $array_info['department'] = $row[0];
	    $array_info['gsms_id'] = $row[3];
	    $array_info['student_id'] = $row[4];
	    $array_info['cs_app'] = $row[5];
	    $array_info['date_of_birth'] = $row[6];
	    $array_info['email'] = $row[7];
	    $array_info['academic_year'] = $row[8];
	    $array_info['term'] = $row[9];
	    $array_info['program'] = $row[10];
	    $array_info['subplan_name'] = $row[11];
	    $array_info['degree'] = $row[12];
	    $array_info['program_name'] = $row[13];
            $array_info['admission_program_name'] = $row[14];
            $array_info['submitted_date'] = $row[15];
            $array_info['gender'] = $row[16];
            $array_info['country_of_birth'] = $row[17];
            $array_info['country_of_citizenship'] = $row[18];
            $array_info['application_type'] = $row[19];
            $array_info['folder'] = $row[20];
            $array_info['education_history'] = $row[21];
            $array_info['department_gpa'] = $row[22];
            $array_info['gpa_scale'] = $row[23];
            $array_info['normalized_gpa'] = $row[24];
            $array_info['fgsr_gpa'] = $row[25];
            $array_info['gpa_scale'] = $row[26];
            $array_info['normalized_gpa'] = $row[27];
            $array_info['epl_test'] = $row[28];
            $array_info['epl_score'] = $row[29];
            $array_info['epl_listen'] = $row[30];
            $array_info['epl_write'] = $row[31];
            $array_info['epl_read'] = $row[32];
            $array_info['epl_speaking'] = $row[33];
            $array_info['funding_note'] = $row[34];
            $array_info['department_decision'] = $row[35];
            $array_info['fgsr_decision'] = $row[36];
            $array_info['decision_response'] = $row[37];
            $array_info['general_notes'] = $row[38];
	    $data_array[] = $array_info;
	}
	return $data_array;
    }

    function readXLS($file){
        $dir = dirname(__FILE__);
        require_once($dir . '/../../Classes/PHPExcel/IOFactory.php');
        $objReader = PHPExcel_IOFactory::createReaderForFile($file);
        $class = get_class($objReader);
        if($class != "PHPExcel_Reader_Excel5" && $class != "PHPExcel_Reader_Excel2007" && $class != "PHPExcel_Reader_HTML" && $class != "PHPExcel_Reader_CSV"){
            return false;
        }
        $objReader->setReadDataOnly(true);
        $obj = $objReader->load($file);
        $obj->setActiveSheetIndex(0);
        $cells = $obj->getActiveSheet()->toArray();
        return $cells;
    }

    function doAction($noEcho=false){
        global $wgUser, $wgServer, $wgScriptPath, $wgRoles, $config, $wgLang;
        $user = Person::newFromId($wgUser->getId());
        if(!$user->isRoleAtLeast(MANAGER)){
            return;
        }   
        $xls = $_FILES['gsms_outcome'];
        if(isset($xls['type']) &&
            ($xls['type'] == "application/vnd.ms-excel" || $xls['type'] == "application/octet-stream" || $xls['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
             || $xls['type'] == 'text/csv')&&
            $xls['size'] > 0 ){
            $error = "";
            $success = array();
            $errors = array();
            $xls_cells = $this->readXLS($xls['tmp_name']);
	    if($xls_cells === false){
		$errors[] = "Please upload a .xls or .csv file";
	    }
            $data = $this->extract_excel_data($xls_cells);
	    foreach($data as $student){
		$student_obj = Person::newFromGSMSId($student['gsms_id']);
                if($student_obj == null){
                    $errors[] = "<b>{$student['name']}</b> failed.  Student not found.";
                    continue;
                }	
		$student_id = $student_obj->getId();
		$student_name = $student['name'];
		  //check if student exists
		if($student_id != 0){
                   $update = false;
                  //check if update or new
                    $gsms_sheet = GsmsData::newFromUserId($student_id);
                              $gsms_sheet->gender = $student['gender'];
                              $gsms_sheet->gsms_id = $student['gsms_id'];
                              $gsms_sheet->date_of_birth = $student['date_of_birth'];
                              $gsms_sheet->program_name = $student['program_name'];
                              $gsms_sheet->country_of_birth = $student['country_of_birth'];
                              $gsms_sheet->country_of_citizenship = $student['country_of_citizenship'];
                              $gsms_sheet->applicant_type = $student['applicant_type'];
                              $gsms_sheet->education_history = $student['education_history'];
                              $gsms_sheet->department = $student['department'];
                              $gsms_sheet->epl_test = $student['epl_test'];
                              $gsms_sheet->epl_score = $student['epl_score'];
                              $gsms_sheet->epl_listen = $student['epl_listen'];
                              $gsms_sheet->epl_write = $student['epl_write'];
                              $gsms_sheet->epl_read = $student['epl_read'];
                              $gsms_sheet->epl_speaking = $student['epl_speaking'];
                              $gsms_sheet->cs_app = $student['cs_app'];
                              $gsms_sheet->academic_year = $student['academic_year'];
                              $gsms_sheet->term = $student['term'];
                              $gsms_sheet->subplan_name = $student['subplan_name'];
                              $gsms_sheet->program = $student['program'];
                              $gsms_sheet->degree_code = $student['degree_code'];
                              $gsms_sheet->admission_program_name = $student['admission_program_name'];
                              $gsms_sheet->submitted_date = $student['submitted_date'];
                              $gsms_sheet->folder = $student['folder'];
                              $gsms_sheet->department_gpa = $student['department_gpa'];
                              $gsms_sheet->department_gpa_scale = $student['department_gpa_scale'];
                              $gsms_sheet->department_normalized_gpa = $student['department_normalized_gpa'];
                              $gsms_sheet->fgsr_gpa = $student['fgsr_gpa'];
                              $gsms_sheet->fgsr_gpa_scale = $student['fgsr_gpa_scale'];
                              $gsms_sheet->fgsr_normalized_gpa = $student['fgsr_normalized_gpa'];
                              $gsms_sheet->funding_note = $student['funding_note'];
                              $gsms_sheet->department_decision = $student['department_decision'];
                              $gsms_sheet->fgsr_decision = $student['fgsr_decision'];
                              $gsms_sheet->decision_response = $student['decision_response'];
                              $gsms_sheet->general_notes = $student['general_notes'];
                    if($gsms_sheet->user_id == ""){
                        $gsms_sheet->user_id = $student_id;
                        $gsms_sheet->create();
                    }
                    else{
                        $gsms_sheet->update();
                    }
		    $success[] = $student_name;
                    DBFunctions::commit();
		}
		else{
			$errors[] = "<b>{$student['name']}</b> failed.  Student not found.";
                        $error_count= $error_count+1;
		}
		
        }
	}
	else{
                $errors[] = "Please upload a .xls or .csv file";
	}
        //$success = (count($success) > 0) ? "<ul><li>".implode("</li><li>", $success)."</li></ul>" : "";
	if(count($errors) == 0){
            $success = "All students successfully updated.";
	}
	else{
            $success = (count($success) > 0) ? (count($success)) . " students were updated." : "";

	}
        if(count($errors) < 5){
            $errors = (count($errors) > 0) ? "<ul><li>".implode("</li><li>", $errors)."</li></ul>" : "";
        }
        else{
            $errors =(count($errors))." students could not be found.";
        }

	

        DBFunctions::commit();
                echo <<<EOF
                <html>
                    <head>
                        <script type='text/javascript'>
                            parent.ccvUploaded("$success", "$errors");
                        </script>
                    </head>
                </html>
EOF;
        exit;
    }

   function isLoginRequired(){
       return true;
   }
}
?>
