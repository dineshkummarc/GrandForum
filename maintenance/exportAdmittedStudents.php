<?php

require_once('commandLine.inc');

/* Example:
sid 1549275
term F17
surname Golestan-Irani
firstname Shadan
middlename
gender Male
email shgolestan@ut.ac.ir
citizenship Iran
immigration INT
degree PhD
specialization
status FT
*/

$wgUser = User::newFromId(1); // Admin user
$people = Person::getAllPeople(CI);

$dir = dirname(__FILE__);

$outdir = "{$dir}/outputAdmittedStudents";
$filenames = [];

@mkdir($outdir);
$peopleSoFar = 0;
$nPeople = count($people);
for($y=2017;$y<=YEAR;$y++){
    foreach($people as $person){
	    $gsms = $person->getGSMS($y);
	    if ($gsms->id != null) {
		    $sop = $gsms->getSOP();
		    // Only export Admitted students
		    if ($sop->getFinalAdmit() == "Admit") {
			    $array = $gsms->toArray();
			    if ($array['additional']['term'] == "Fall Term") {
				    $year = explode("/", $array['additional']['academic_year'])[0];
				    $term = "F" . substr($year, 2); // '2018' becomes '18'
			    } else if ($array['additional']['term'] == "Winter Term") {
				    $year = explode("/", $array['additional']['academic_year'])[1];
				    $term = "W" . $year;
			    }
			
			    if ($array['additional']['gender'] == "M") {
				    $gender = "Male";
			    } else if ($array['additional']['gender'] == "F") {
				    $gender = "Female";
			    } else {
				    $gender = $array['additional']['gender'];
			    }

                $array['student_id'] = str_pad($array['student_id'], 7, "0", STR_PAD_LEFT);

			    $output = array(
				    "sid " . $array['student_id'],
				    "term " . $term,
				    "surname " . $person->getLastName(),
				    "firstname " . $person->getFirstName(),
				    "middlename " . $person->getMiddleName(),
				    "gender " . $gender,
				    "email " . $array['student_data']['email'],
				    "citizenship " . $array['additional']['country_of_citizenship_full'],
				    "immigration " . $array['additional']['immigration'],
				    "degree " . $array['degree'],
				    "specialization " . $array['area'],
				    "status " . $array['ftpt']
			    );
			    if ($array['student_id'] != 0) {
				    $loc = $outdir . "/" . $array['student_id'];
				    array_push($filenames, $array['student_id']);
			    } else {
				    $f = "gsms" . $array['gsms_id'];
				    $loc = $outdir . "/" . $f;
				    array_push($filenames, $f);
			    }
			    $found = false;
			    if(!file_exists($loc)){
			        $found = true;
			    }
			    file_put_contents($loc, implode("\n", $output) . "\n");
			    if($found){
			        $command = "ssh -T -i /home/srvadmin/srvadmin docsdb@csora-app.cs.ualberta.ca < {$outdir}/{$array['student_id']}";
			        echo "{$command}\n";
			        system("{$command}");
			    }
		    }
	    }
	    //show_status(++$peopleSoFar, $nPeople);
    }
}



// Copy the files to the GradDB server
/*exec("scp -i graddb.pem" . $outdir . "/* docsdb@csora-app:/local/oracle3/cshome/docsdb/graddb/Data/Applicants/AppFiles/");
$loadCommand = "/local/oracle3/cshome/docsdb/graddb/Data/Applicants/load_applicant_file";
$commandToRun = "";
foreach($filenames as $f) {
	$commandToRun .= $loadCommand . " " . $f . "; ";
}
//var_dump($commandToRun);
if (count($filenames) != 0){
	exec("ssh -i graddb.pem docsdb@csora-app '" . $commandToRun . "'");
}
*/