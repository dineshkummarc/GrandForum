<?php

$wgHooks['UnknownAction'][] = 'PersonProfileTab::getPersonCloudData';

class PersonProfileTab extends AbstractEditableTab {

    var $person;
    var $visibility;

    function PersonProfileTab($person, $visibility){
        parent::AbstractEditableTab("Profile");
        $this->person = $person;
        $this->visibility = $visibility;
    }

    function generateBody(){
        global $wgUser;
        $this->person->getLastRole();
        $this->html .= "<table width='100%' cellpadding='0' cellspacing='0'>";
        $this->html .= "</td><td id='firstLeft' width='60%' valign='top'>";
        $this->showContact($this->person, $this->visibility);
        if($this->person->getProfile() != ""){
            $this->html .= "<h2 style='margin-top:0;padding-top:0;'>Profile</h2>";
            $this->showProfile($this->person, $this->visibility);
            $this->html .= "<br />";
        }
        $this->html .= $this->showTable($this->person, $this->visibility);
        $extra = array();
        if($this->person->isRole(NI) || 
           $this->person->isRole(HQP) || 
           $this->person->isRole(EXTERNAL)){
            // Only show the word cloud for 'researchers'
            $extra[] = $this->showCloud($this->person, $this->visibility);
        }
        $extra[] = $this->showDoughnut($this->person, $this->visibility);
        $extra[] = $this->showTwitter($this->person, $this->visibility);
        
        
        // Delete extra widgets which have no content
        foreach($extra as $key => $e){
            if($e == ""){
                unset($extra[$key]);
            }
        }
        $this->html .= "</td><td id='firstRight' valign='top' width='40%' style='padding-top:15px;padding-left:15px;'>".implode("<hr />", $extra)."</td></tr>";
        $this->html .= "</table>";
        $this->showCCV($this->person, $this->visibility);
        return $this->html;
    }
    
    function generateEditBody(){
        $this->html .= "<table>";
        $this->showEditPhoto($this->person, $this->visibility);
        $this->html .= "</td><td style='padding-right:25px;' valign='top'>";
        $this->showEditContact($this->person, $this->visibility);
        $this->html .= "</table>";
        $this->html .= "<h2>Profile</h2>";
        $this->showEditProfile($this->person, $this->visibility);
    }
    
    function canEdit(){
        return ($this->visibility['isMe'] || 
                $this->visibility['isSupervisor']);
    }
    
    function handleEdit(){
        $this->handleContactEdit();
        $_POST['user_name'] = $this->person->getName();
        $_POST['type'] = "public";
        $_POST['profile'] = str_replace("'", "&#39;", $_POST['public_profile']);
        $_POST['profile'] = @str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['profile']));
        APIRequest::doAction('UserProfile', true);
        $_POST['type'] = "private";
        $_POST['profile'] = @str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['private_profile']));
        APIRequest::doAction('UserProfile', true);
        Person::$cache = array();
        Person::$namesCache = array();
        Person::$idsCache = array();
        $this->person = Person::newFromId($this->person->getId());
    }
    
    function handleContactEdit(){
        global $wgImpersonating;
        $error = "";
        if(!$wgImpersonating && isset($_FILES['photo']) && $_FILES['photo']['tmp_name'] != ""){
            $type = $_FILES['photo']['type'];
            $size = $_FILES['photo']['size'];
            $tmp = $_FILES['photo']['tmp_name'];
            if($type == "image/jpeg" ||
               $type == "image/pjpeg" ||
               $type == "image/gif" || 
               $type == "image/png"){
                if($size <= 1024*1024*5){
                    //File is OK to upload
                    $fileName = "Photos/".str_replace(".", "_", $this->person->getName()).".jpg";
                    move_uploaded_file($tmp, $fileName);
                    
                    if($type == "image/jpeg" || $type == "image/pjpeg"){
                        $src_image = @imagecreatefromjpeg($fileName);
                    }
                    else if($type == "image/png"){
                        $src_image = @imagecreatefrompng($fileName);
                    }
                    else if($type == "image/gif"){
                        $src_image = @imagecreatefromgif($fileName);
                    }
                    if($src_image != false){
                        imagealphablending($src_image, true);
                        imagesavealpha($src_image, true);
                        $src_width = imagesx($src_image);
                        $src_height = imagesy($src_image);
                        $dst_width = 100;
                        $dst_height = 132;
                        $dst_image = imagecreatetruecolor($dst_width, $dst_height);
                        imagealphablending($dst_image, true);
                        
                        imagesavealpha($dst_image, true);
                        imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
                        imagedestroy($src_image);
                        
                        imagejpeg($dst_image, $fileName, 100);
                        imagedestroy($dst_image);
                    }
                    else{
                        //File is not an ok filetype
                        $error .= "The file you uploaded is not of the right type.  It should be either gif, png or jpeg";
                    }
                }
                else{
                    //File size is too large
                    $error .= "The file you uploaded is too large.  It should be smaller than 5MB.<br />";
                }
            }
            else{
                //File is not an ok filetype
                $error .= "The file you uploaded is not of the right type.  It should be either gif, png or jpeg.<br />";
            }
        }
        if($error == ""){
            // Insert the new data into the DB
            $_POST['user_name'] = $this->person->getName();
            $_POST['twitter'] = @$_POST['twitter'];
            $_POST['website'] = @$_POST['website'];
            $_POST['nationality'] = @$_POST['nationality'];
            $_POST['email'] = @$_POST['email'];
            $_POST['university'] = @$_POST['university'];
            $_POST['department'] = @$_POST['department'];
            $_POST['title'] = @$_POST['title'];
            $_POST['gender'] = @$_POST['gender'];
            if($this->visibility['isChampion']){
                $_POST['partner'] = @$_POST['org'];
                $_POST['title'] = @$_POST['title'];
                $_POST['department'] = @$_POST['department'];
                $api = new UserPartnerAPI();
                $api->doAction(true);
            }
            else{
                $api = new UserUniversityAPI();
                $api->processParams(array());
                $api->doAction(true);
            }
            $api = new UserTwitterAccountAPI();
            $api->doAction(true);
            $api = new UserWebsiteAPI();
            $api->doAction(true);
            $api = new UserNationalityAPI();
            $api->doAction(true);
            $api = new UserEmailAPI();
            $api->doAction(true);
            $api = new UserGenderAPI();
            $api->doAction(true);
        }
        
        //Reset the cache to use the changed data
        unset(Person::$cache[$this->person->id]);
        unset(Person::$cache[$this->person->getName()]);
        Person::$idsCache = array();
        Person::$namesCache = array();
        $this->person = Person::newFromId($this->person->id);
        return $error;
    }
    
    /*
     * Displays the profile for this user
     */
    function showProfile($person, $visibility){
        global $wgUser;
        $this->html .= "<p style='text-align:justify;'>".nl2br($person->getProfile($wgUser->isLoggedIn()))."</p>";
    }
    
    /**
     * Displays the twitter widget for this user
     */
    function showTwitter($person, $visibility){
        $html = "";
        if($person->getTwitter() != ""){
            $html = <<<EOF
                <br />
                <div id='twitter' style='display: block; width: 100%; text-align: right;'>
                    <div>
                        <a class="twitter-timeline" width="100%" height="400" href="https://twitter.com/{$person->getTwitter()}" data-screen-name="{$person->getTwitter()}" data-widget-id="553303321864196097">Tweets by @{$person->getTwitter()}</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </div>
                </div>
                <script type='text/javascript'>
                    // Adds a bit of responsiveness to the profile
                    /*setInterval(function(){
                        if($("#bodyContent").width() < 1000){
                            $('#twitter').css('display', 'block');
                            $('#twitter').css('width', '100%');
                            $('#twitter div').css('width', '100%');
                            $('#twitter div').css('max-width', '');
                            $('#twitter iframe').css('width', '133%');
                        }
                        else{
                            $('#twitter').css('display', 'inline-block');
                            $('#twitter').css('width', '50%');
                            $('#twitter div').css('max-width', 225);
                            $('#twitter iframe').css('width', 300);
                        }
                    }, 100);*/
                </script>
EOF;
        }
        return $html;
    }
    
    function showEditProfile($person, $visibility){
        global $config;
        $this->html .= "<table>
                            <tr>
                                <td align='right' valign='top'><b>{$config->getValue('networkName')} Website:</b></td>
                                <td><textarea style='width:600px; height:150px;' name='public_profile'>{$person->getProfile(false)}</textarea></td>
                            </tr>
                            <tr>
                                <td align='right' valign='top'><b>{$config->getValue('siteName')}:</b></td>
                                <td><textarea style='width:600px; height:150px;' name='private_profile'>{$person->getProfile(true)}</textarea></td>
                            </tr>
                        </table>";
    }
    
    function showCloud($person, $visibility){
        global $wgServer, $wgScriptPath, $wgTitle, $wgOut, $wgUser;
        $dataUrl = "$wgServer$wgScriptPath/index.php/{$wgTitle->getNSText()}:{$wgTitle->getText()}?action=getPersonCloudData&person={$person->getId()}";
        $wordle = new Wordle($dataUrl, true, '$("#personProducts_wrapper input").val(text); $("#personProducts_wrapper input").trigger("keyup")');
        $wordle->width = "100%";
        $wordle->height = 232;
        $wgOut->addScript("<script type='text/javascript'>
                                $(document).ready(function(){
                                    onLoad{$wordle->index}();
                                });
                          </script>");
        return $wordle->show();
    }
    
    static function getPersonCloudData($action, $article){
	    global $wgServer, $wgScriptPath;
	    if($action == "getPersonCloudData"){
	        $text = "";
	        $person = Person::newFromId($_GET['person']);
	        $text .= $person->getProfile()."\n";
	        
	        $products = $person->getPapers("all", false, 'both', false, 'Public');
	        foreach($products as $product){
	            $text .= $product->getTitle()."\n";
	            $text .= $product->getDescription()."\n";
	        }
	        CommonWords::$commonWords[] = strtolower($person->getFirstName());
	        CommonWords::$commonWords[] = strtolower($person->getLastName());
	        $data = Wordle::createDataFromText($text);
	        $data = array_slice($data, 0, 75);
            header("Content-Type: application/json");
            echo json_encode($data);
            exit;
        }
        return true;
	}
	
	function showDoughnut($person, $visibility){
        global $wgServer, $wgScriptPath, $wgTitle, $wgOut, $wgUser;
        $dataUrl = "$wgServer$wgScriptPath/index.php/{$wgTitle->getNSText()}:{$wgTitle->getText()}?action=getDoughnutData&person={$person->getId()}";
        $fn = '$("#personProducts_wrapper input").val(text); $("#personProducts_wrapper input").trigger("keyup")';
        $doughnut = new Doughnut($dataUrl, true, $fn);
        $wgOut->addScript("<script type='text/javascript'>
                                $(document).ready(function(){
                                    if(!$('#vis{$doughnut->index}').is(':visible')){
                                        var interval = setInterval(function(){
                                            if($('#vis{$doughnut->index}').is(':visible')){
                                                $('#vis{$doughnut->index}').doughnut('{$doughnut->url}', true, function(text){ {$fn} });
                                                clearInterval(interval);
                                            }
                                        }, 100);
                                    }
                                });
                          </script>");
        return $doughnut->show();
    }
    
    function showChord($person, $visibility){
        global $wgServer, $wgScriptPath, $wgTitle, $wgOut;
        $dataUrl = "$wgServer$wgScriptPath/index.php/{$wgTitle->getNSText()}:{$wgTitle->getText()}?action=getChordData&person={$person->getId()}";
        $html = "<div style='position:absolute; right:0; display:inline-block; text-align:right;'>";
        $chord = new Chord($dataUrl);
        $chord->width = 226;
        $chord->height = 226;
        $chord->options = false;
        $html .= $chord->show();
        $html .= "</div>";
        $wgOut->addScript("<script type='text/javascript'>
                                $(document).ready(function(){
                                    $('#vis{$chord->index}').hide();
                                    var maxWidth = {$chord->width};
                                    var width = -1;
                                    var height = {$chord->height};
                                    var lastWidth = -1;
                                    setInterval(function(){
                                        var leftWidth = $('#firstLeft').width();
                                        var cardWidth = $('#firstLeft div#card').width();
                                        var widthDiff = leftWidth - cardWidth;
                                        newWidth = Math.min(maxWidth, widthDiff);
                                        if($('#vis{$chord->index}').is(':visible') && width != newWidth){
                                            width = newWidth;
                                            height = width;
                                            if(width < 100){
                                                // Too small, just don't show it anymore
                                                $('#vis{$chord->index}').empty();
                                            }
                                            else{
                                                _.defer(function(){
                                                    $('#vis{$chord->index}').empty();
                                                    $('#vis{$chord->index}').show();
                                                    render{$chord->index}(width, height);
                                                });
                                            }
                                            lastWidth = $('#firstLeft').width();
                                        }
                                    }, 100);
                                });
                          </script>");
        return $html;
    }
    
    /**
     * Shows a table of this Person's products, and is filterable by the
     * visualizations which appear above it.
     */
    function showTable($person, $visibility){
        $me = Person::newFromWgUser();
        $products = $person->getPapers("all", false, 'both', true, "Public");
        $string = "";
        if(count($products) > 0){
            $string = "<table id='personProducts' rules='all' frame='box'>
                <thead>
                    <tr>
                        <th>Title</th><th>Date</th><th>Universities</th><th>Authors</th>
                    </tr>
                </thead>
                <tbody>";
            foreach($products as $paper){
                $projects = array();
                foreach($paper->getProjects() as $project){
                    $projects[] = "{$project->getName()}";
                }

                $names = array();
                foreach($paper->getAuthors() as $author){
                    if($author->getId() != 0 && $author->getUrl() != ""){
                        $names[] = "<a href='{$author->getUrl()}'>{$author->getNameForForms()}</a>";
                    }
                    else{
                        $names[] = $author->getNameForForms();
                    }
                }
                
                $string .= "<tr>";
                $string .= "<td><a href='{$paper->getUrl()}'>{$paper->getTitle()}</a><span style='display:none'>{$paper->getDescription()}".implode(", ", $projects)."</span></td>";
                $string .= "<td style='white-space: nowrap;'>{$paper->getDate()}</td>";
                $string .= "<td>".implode(", ", $paper->getUniversities())."</td>";
                $string .= "<td>".implode(", ", $names)."</td>";
                
                $string .= "</tr>";
            }
            $string .= "</tbody>
                </table>
                <script type='text/javascript'>
                    $('#personProducts').dataTable({
                        'order': [[ 1, 'desc' ]],
                        'autoWidth': false
                    });
                </script>";
        }
        return $string;
    }
   
    /*
     * Displays the profile for this user
     */
    function showCCV($person, $visibility){
        global $wgUser, $wgServer, $wgScriptPath;
        if(isExtensionEnabled('CCVExport')){
            $me = Person::newFromWgUser();
            if(($person->isRole(NI)) && $me->getId() == $person->getId()){
                $this->html .= "<a class='button' href='$wgServer$wgScriptPath/index.php/Special:CCVExport?getXML'>Download CCV</a>";
            }
        }
    }
    
    /*
     * Displays the photo for this person
     */
    function showPhoto($person, $visibility){
        $this->html .= "<tr><td style='padding-right:25px;' valign='top'>";
        if($person->getPhoto() != ""){
            $this->html .= "<img src='{$person->getPhoto()}' alt='{$person->getName()}' />";
        }
        $this->html .= "<div id=\"special_links\"></div>";
    }
    
    function showEditPhoto($person, $visibility){
        $this->html .= "<tr><td style='padding-right:25px;' valign='top' colspan='2'>";
        $this->html .= "<img src='{$person->getPhoto()}' alt='{$person->getName()}' />";
        $this->html .= "<div id=\"special_links\"></div>";
        $this->html .= "</td></tr>";
        $this->html .= "<tr><td style='padding-right:25px;' valign='top'><table>
                            <tr>
                                <td align='right'><b>Upload new Photo:</b></td>
                                <td><input type='file' name='photo' /></td>
                            </tr>
                            <tr>
                                <td></td><td><small><li>The image will be scaled to 100x132.</li>
                                                    <li>Max file size is 5MB</li>
                                                    <li>File type must be <i>gif</i>, <i>png</i> or <i>jpeg</i></li></small></td>
                            </tr>
                            <tr>
                                <td align='right'><b>Website URL:</b></td>
                                <td><input type='text' size='30' name='website' value='".str_replace("'", "&#39;", $person->getWebsite())."' /></td>
                            </tr>
                            <tr>
                                <td align='right'><b>Twitter Account:</b></td>
                                <td><input type='text' name='twitter' value='".str_replace("'", "&#39;", $person->getTwitter())."' /></td>
                            </tr>
                        </table></td>";
    }
    
   /*
    * Displays the contact information for this person
    */
    function showContact($person, $visibility){
        global $wgOut, $wgUser, $wgTitle, $wgServer, $wgScriptPath;
        $this->html .= "<div style='white-space: nowrap;position:relative;height:229px;'>";
        $this->html .= <<<EOF
            <div id='card' style='min-height:142px;display:inline-block;vertical-align:top;'></div>
            <script type='text/javascript'>
                $(document).ready(function(){    
                    var person = new Person({$person->toJSON()});
                    var card = new LargePersonCardView({el: $("#card"), model: person});
                    card.render();
                });
            </script>
EOF;
        $this->html .= $this->showChord($person, $visibility);
        $this->html .= "</div>";
    }
    
    function showEditContact($person, $visibility){
        global $wgOut, $wgUser;
        $university = $person->getUniversity();
        $nationality = "";
        if($visibility['isMe'] || $visibility['isSupervisor']){
            if($person->isRoleDuring(HQP, "0000", "9999") ||
               $person->isRoleDuring(NI, "0000", "9999")){
                $canSelected = ($person->getNationality() == "Canadian") ? "selected='selected'" : "";
                $amerSelected = ($person->getNationality() == "American") ? "selected='selected'" : "";
                $immSelected = ($person->getNationality() == "Landed Immigrant" || $person->getNationality() == "Foreign") ? "selected='selected'" : "";
                $visaSelected = ($person->getNationality() == "Visa Holder") ? "selected='selected'" : "";
                $nationality = "<tr>
                    <td align='right'><b>Nationality:</b></td>
                    <td>
                        <select name='nationality'>
                            <option value='Canadian' $canSelected>Canadian</option>
                            <option value='American' $amerSelected>American</option>
                            <option value='Landed Immigrant' $immSelected>Landed Immigrant</option>
                            <option value='Visa Holder' $visaSelected>Visa Holder</option>
                        </select>
                    </td>
                </tr>";
            }
            
            $blankSelected = ($person->getGender() == "") ? "selected='selected'" : "";
            $maleSelected = ($person->getGender() == "Male") ? "selected='selected'" : "";
            $femaleSelected = ($person->getGender() == "Female") ? "selected='selected'" : "";
            $gender = "<tr>
                <td align='right'><b>Gender:</b></td>
                <td>
                    <select name='gender'>
                        <option value='' $blankSelected>----</option>
                        <option value='Male' $maleSelected>Male</option>
                        <option value='Female' $femaleSelected>Female</option>
                    </select>
                </td>
            </tr>";
            $partnerHTML = "";
            if($visibility['isChampion']){
                $partners = array();
                foreach(Partner::getAllPartners() as $partner){
                    $partners[] = $partner->getOrganization();
                }
                $wgOut->addScript("<script type='text/javascript'>
                var partners = [\"".implode("\",\n\"", $partners)."\"];
                
                $(document).ready(function(){
                    $('#partner').autocomplete({
                        source: partners
                    });
                });</script>");
                $partner = str_replace("'", "&#39;", $person->getPartnerName());
                $partnerHTML = "<tr>
                                 <td align='right'><b>Partner:</b></td>
                                 <td>
                                    <input type='text' value='{$partner}' id='partner' name='partner' />
                                 </td>
                                 </tr>
                                 ";
            }
        }
        $this->html .= "<table>
                            <tr>
                                <td align='right'><b>Email:</b></td>
                                <td><input size='30' type='text' name='email' value='".str_replace("'", "&#39;", $person->getEmail())."' /></td>
                            </tr>
                            {$nationality}
                            {$gender}";
                            
        if($person->isRole(CHAMP)){
            $titles = array_merge(array(""), Person::getAllPartnerTitles());
            $organizations = array_merge(array(""), Person::getAllPartnerNames());
            $depts = array_merge(array(""), Person::getAllPartnerDepartments());
            $titleCombo = new ComboBox('title', "Title", $person->getPartnerTitle(), $titles);
            $orgCombo = new ComboBox('org', "Institution", $person->getPartnerName(), $organizations);
            $deptCombo = new ComboBox('department', "Department", $person->getPartnerDepartment(), $depts);
            $orgCombo->attr('style', 'max-width: 250px;');
            $deptCombo->attr('style', 'max-width: 250px;');
            $titleCombo->attr('style', 'max-width: 250px;');
            $this->html .= "<tr>
                                <td align='right'><b>Title:</b></td>
                                <td>{$titleCombo->render()}
                                </td>
                            </tr>
                            <tr>
                                <td align='right'><b>Institution:</b></td>
                                <td>{$orgCombo->render()}
                                </td>
                            </tr>
                            <tr>
                                <td align='right'><b>Department:</b></td>
                                <td>{$deptCombo->render()}
                                </td>
                            </tr>";
        }
        else{
            $universities = new Collection(University::getAllUniversities());
            $uniNames = $universities->pluck('name');
            $positions = Person::getAllPositions();
            $myPosition = "";
            foreach($positions as $key => $position){
                if($university['position'] == $position){
                    $myPosition = $key;
                }
            }
            $departments = Person::getAllDepartments();
            $organizations = array_unique(array_merge($uniNames, Person::getAllPartnerNames()));
            sort($organizations);
            $positionCombo = new ComboBox('title', "Title", $myPosition, $positions);
            $orgCombo = new ComboBox('university', "Institution", $university['university'], $organizations);
            $departmentCombo = new ComboBox('department', "Department", $university['department'], $departments);
            $positionCombo->attr('style', 'max-width: 250px;');
            $departmentCombo->attr('style', 'max-width: 250px;');
            $this->html .= "<tr>
                                <td align='right'><b>Title:</b></td>
                                <td>{$positionCombo->render()}
                                </td>
                            </tr>
                            <tr>
                                <td align='right'><b>Institution:</b></td>
                                <td>{$orgCombo->render()}
                                </td>
                            </tr>
                            <tr>
                                <td align='right'><b>Department:</b></td>
                                <td>{$departmentCombo->render()}</td>
                            </tr>";
        }
        $this->html .= "</table>";
    }
    
}
?>
