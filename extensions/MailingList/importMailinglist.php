<?php
require_once("common.php");

$sql = "select projectid, mailListName from wikidev_projects";
$dbr = wfGetDB(DB_READ);
$result = $dbr->query($sql);

$mailmanArchivesPaths = array();
while ($row = $dbr->fetchObject($result)) {
	$mailmanArchivesPaths[$row->projectid] = $wdMailmanArchives . "/" . $row->mailListName;
}

$existing = getExistingMIDs();
$mapping = getAddressMapping(); 

foreach ($mailmanArchivesPaths as $proj_id => $mailmanArchivesPath) {
	$allMessages = array();
	
	foreach (glob("$mailmanArchivesPath/*.txt") as $filename) { //TODO is that specific enough?
		$messages = parseMailArchive($filename, $proj_id);
		if (count($messages) > 0) {
			$allMessages[] = $messages;
		}
	}

	if (count($allMessages) == 0) {
		print "No new messages found.\n";
	}

	else {
		$dbw = wfGetDB(DB_MASTER);
		$count = 0;
		foreach ($allMessages as $messages) {
			insertNonPrefix($dbw, 'wikidev_messages', $messages);
			$count += count($messages);
		}
		print "Imported $count new messages.\n";
	}
}

function getExistingMIDs() {
	$dbr = wfGetDB(DB_READ);
	$result = $dbr->select(' wikidev_messages', 'mid_header');
	
	$existing = array();
	
	while ($row = $dbr->fetchObject($result)) {
		$existing[$row->mid_header] = true;
	}
	
	return $existing;
}

function parseMailArchive($filename, $proj_id) {
	global $mapping, $existing;
	$text = file_get_contents($filename);
	
	$pattern = "/From: (.*?) \((.*?)\)\nDate: (.*?)\nSubject: \[.*?\] (.*?)\n.*?(References: (.*?)\n)?Message-ID: <(.*?)>\n\n(.*?)(\n\nFrom|$)/s";
	preg_match_all($pattern, $text, $matches);
	$messages = array();
	$parentMapping = array();
	list($addresses, $names, $dates, $subjects, $refids, $mids, $bodies) = array($matches[1], $matches[2], $matches[3], $matches[4], $matches[6],  $matches[7], $matches[8]);
		
	for ($i = 0; $i < count($mids); $i++) {
		
		if (isset($existing[$mids[$i]])) {
			continue;
		}
		
		$fromAddr = $addresses[$i];
		$fromAddr = str_replace(" at ", "@", $fromAddr);
		$fromAddrA = explode("@", $fromAddr);
		
		$userTable = getTableName("user");
		
		$sql = "SELECT DISTINCT u.user_name as user_name
				FROM $userTable u 
				WHERE LOWER(CONVERT(u.user_email USING latin1)) LIKE CONCAT(LOWER('{$fromAddrA[0]}'), '%')";
		$dbr = wfGetDB(DB_READ);
		$result = $dbr->query($sql);
		$data = array();
		while ($row = $dbr->fetchRow($result)) {
			$data[] = $row;
		}
		
		if(count($data) > 0){
			$username = $data[0]['user_name'];
		}
		else{
		    $username = "";
		}
		
		$refid = $mids[$i];
		
		if (trim($refids[$i]) != "") {
			$curRefids = preg_split("/\s+/", $refids[$i]);
			preg_match("/<(.*)>/", $curRefids[0], $refidMatches);
			$refid = $refidMatches[1];
			
			//sometimes the first reference is not actually the original message in the thread
			if (isset($parentMapping[$refid])) {
				$refid = $parentMapping[$refid];
			}
			
			$parentMapping[$mids[$i]] = $refid;
		}
		$date = $dates[$i];
		$date = strftime("%Y-%m-%d %H:%M:%S", strtotime($date));
		
		$messages[] = array(
		'project_id' => $proj_id,
		'body' => $bodies[$i],
		'author' => $names[$i], 
		'user_name' => $username, 
		'address' => $fromAddr, 
		'date' => $date, 
		'subject' => $subjects[$i], 
		'mid_header' => $mids[$i], 
		'refid_header' => $refid, 
		);
	}

	return $messages;
}

?>
