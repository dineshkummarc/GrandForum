<?php

$publicationPage = new PublicationPage();

$wgHooks['ArticleViewHeader'][] = array($publicationPage, 'processPage');
$wgHooks['SkinTemplateContentActions'][] = array($publicationPage, 'removeTabs');


$publicationTypes = array("Proceedings Paper" => "an article written for submission to a workshop, symposium, or conference",
                          "Collections Paper" => "an article written as part of a collection in a specified subject area",
                          "Journal Paper" => "an article written for submission to a periodical in a specified subject area", 
                          "Journal Abstract" => "the summary abstract of a longer journal paper",
                          "Book" => "a published monograph of non-trivial length in a specified subject area",
                          "Edited Book" => "a compilation of chapters written by specialists",
                          "Book Chapter" => "a chapter written as part of a book",
                          "Book Review" => "critique of the content of a published book",
                          "Review Article" => "review of a publicly-available work or event",
                          "White Paper" => "an authoritative report that helps a reader understand, solve, or decide an issue",
                          "Magazine/Newspaper Article" => "appearing in a publicly-available format",
                          "PHD Thesis" => "as awarded by an accredited institution",
                          "Masters Thesis" => "as awarded by an accredited institution",
                          "Bachelors Thesis" => "as awarded by an accredited institution",
                          "Tech Report" => "internal to the author&#39s institution",
                          "Poster" => "as presented at a workshop, symposium, or conference",
                          "Manual" => "an instructional user guide",
                          "Misc" => "any item not fitting the other listed types:\nenter its type in the text box provided");

$activityTypes = array("Panel" => "panel",
                       "Tutorial" => "tutorial",
                       "Event Organization" => "event"); 

                       
$artifactTypes = array("Repository" => "repository",
                       "Open Software" => "open software",
                       "Patent" => "patent",
                       "Device/Machine" => "device/machine",
                       "Aesthetic Object" => "asthetic object",
                       "Misc" => "misc");


$presentationTypes = array( "2MM"=>"2MM",
                            "RNotes"=>"RNotes",
                            "WIP"=>"WIP",
                            "Misc"=>"Misc"
                            );

$pressTypes = array("University Press" => "university press",
                    "Provincial News" => "provincial news",
                    "National News" => "national news",
                    "International News" => "international news",
                    "Misc" => "misc");

$awardTypes = array("Award" => "award");

$types = array("Artifact" => $artifactTypes,
               "Publication" => $publicationTypes,
               "Activity" => $activityTypes,
               "Press" => $pressTypes,
               "Award" => $awardTypes,
               "Presentation" => $presentationTypes);

$refereedTypes = array("Proceedings Paper", "Collections Paper", "Journal Paper", "Book Chapter", "Book Review", "Review Article",
                       "White Paper", "Poster", "Misc");

$optionDefs = array("Address" => "the city, country of the publisher",
                    "Book Title" => "the title of the book",
                    "Department" => "the name of the department in which the author was enrolled",
                    "DOI" => "Digital Object Identifier",
                    "Edition" => "the name or number of the edition in which this item appears",
                    "Editor" => "the name of the editor of this item",
                    "Editors" => "the names of the editors of this publication",
                    "Event Title" => "the official name of the event",
                    "Event Location" => "the city, country where the event took place",
                    "How Published" => "the form of publication (text, video, etc.)",
                    "ISBN" => "International Standard Book Number",
                    "ISSN" => "International Standard Serial Number",
                    "Journal Title" => "the name of the journal in which the item appears",
                    "Magazine/Newspaper Title" => "the name of the publication in which this item appears",
                    "Number" => "the issue number of the journal in which this article appears",
                    "Pages" => "the item's page range in the publication",
                    "Published In" => "the name of the publication in which this item appears",
                    "Publisher" => "the name of the publishing company",
                    "Series" => "the name of the series in which this item appears",
                    "Venue" => "where this item appeared",
                    "Note" => "any additional information",
                    "University" => "the name of the university where this thesis was awarded",
                    "URL" => "link to a copy of this item\nDO NOT LINK TO ITEMS RESTRICTED BY COPYRIGHT",
                    "Volume" => "the volume number of the journal in which the article appears",
                    "" => ""
);

class PublicationPage {

    function processPage($article, $outputDone, $pcache){
        global $wgOut, $wgUser, $wgRoles, $wgServer, $wgScriptPath, $types, $refereedTypes;
        
        $me = Person::newFromId($wgUser->getId());
        if(!$wgOut->isDisabled()){
            $name = $article->getTitle()->getNsText();
            $title = $article->getTitle()->getText();
            
            if($name == ""){
                $split = explode(":", $title);
                if(count($split) > 1){
                    $title = $split[1];
                }
                else{
                    $title = "";
                }
                $name = $split[0];
            }
            if(!($name == "Activity" || $name == "Press" || $name == "Award" || $name == "Publication" || $name == "Artifact" || $name == "Presentation")){
                return true;
            }
            
            //Switching to use the actual product_id in the URL to identify the publication a.k.a. 'Product'
            if(is_numeric($title)){
                $product_id = $title;
                $paper = Paper::newFromId($product_id);
            }
            else{
                $title = str_replace("_", " ", $title);
                $paper = Paper::newFromTitle(str_replace(":", "&#58;", $title));
                $product_id = $paper->getId();
            }
            
            if($paper->getTitle() != null && isset($_GET['create'])){
                unset($_GET['create']);
                $_GET['edit'] = "true";
            }
            
            $create = isset($_GET['create']);
            $create = ( $create && (!FROZEN || $me->isRoleAtLeast(STAFF)) );
            $edit = (isset($_GET['edit']) || $create);
            $edit = ( $edit && (!FROZEN || $me->isRoleAtLeast(STAFF)) );
            
            $post = (isset($_POST['submit']) && ($_POST['submit'] == "Save $name" || $_POST['submit'] == "Create $name"));
            $post = ( $post && (!FROZEN || $me->isRoleAtLeast(STAFF)) );
            
            if(($name == "Activity" || $name == "Press" || $name == "Award" || $name == "Publication" || $name == "Artifact" || $name == "Presentation") && 
               (($paper->getTitle() != null && $paper->getCategory() == $name) || $create)){
               if(!$me->isRoleAtLeast(INACTIVE) && 
                  ($paper->getCategory() != "Publication" || 
                   ($paper->getCategory() == "Publication" && $paper->getStatus() != "Published"))){ // Check that the user is logged in
                    $wgOut->clearHTML();
                        
                    $wgOut->setPageTitle("Permission error");
                    $wgOut->addHTML("You must be logged in to view this page.");
                    
                    $wgOut->output();
                    $wgOut->disable();
                    return;
                }
                $category = $name;
                $authorTitle = self::getAuthorTitle($category);
                if($post){
                    // The user has submitted the form
                    self::proccessPost($category);
                    if(!$create){
                        header("Location: $wgServer$wgScriptPath/index.php/$category:".str_replace("?", "%3F", str_replace("&#39;", "'", $title)));
                    }
                    else{
                        header("Location: $wgServer$wgScriptPath/index.php/$category:".str_replace("?", "%3F", $title));
                    }
                }
                $wgOut->clearHTML();
                if(!$create){
                    $wgOut->setPageTitle(str_replace("&#39;", "'", $paper->getTitle()));
                }
                else{
                    $wgOut->setPageTitle($title);
                }
                if($edit){
                    $misc_types = Paper::getAllMiscTypes($paper->getCategory());
                    $wgOut->addScript("<script type='text/javascript' src='$wgServer$wgScriptPath/scripts/switcheroo.js'></script>");
                    $wgOut->addScript('<script type="text/javascript">
                    var oldAttr = Array();
                    
                    var misc_types = ["'.implode("\",\n\"", $misc_types).'"];

                    $(document).ready(function(){
                        var type = "'.$paper->getType().'";
                        if(type == "Paper"){
                            type = "Proceedings Paper";
                        }
                        showHideAttr(type);
                    });
                    
                    function showHideAttr(type){
                        $.each($("tr.attr"), function(index, value){
                            if($(value).attr("name").toLowerCase() != "date"){
                                oldAttr[$(value).attr("name").toLowerCase()] = $(value).detach();
                            }
                        });
                        var category = "'.$name.'";
                        $("input[name=misc_type]").remove();
                        switch(category){
                            case "Activity":
                                switch(type){
                                    default:
                                    case "Panel":
                                        addAttr("Conference");
                                        addAttr("Location");
                                        addAttr("Organizing Body");
                                        addAttr("URL");
                                        break;
                                    case "Tutorial":
                                        addAttr("Conference");
                                        addAttr("Location");
                                        addAttr("Organizing Body");
                                        addAttr("URL");
                                        break;
                                    case "Event Organization":
                                        addAttr("Conference");
                                        addAttr("Location");
                                        addAttr("Organizing Body");
                                        addAttr("URL");
                                        break;
                                }
                                break;
                            case "Artifact":
                                switch(type){
                                    case "Repository":
                                        addAttr("URL");
                                        break;
                                    case "Open Software":
                                        addAttr("URL");
                                        break;
                                    case "Patent":
                                        addAttr("Number");
                                        break;
                                    case "Device/Machine":
                                        break;
                                    case "Aesthetic Object":
                                        break;
                                    default:
                                    case "Misc":
                                        $("select[name=type]").parent().append("<input type=\'text\' name=\'misc_type\' value=\''.str_replace("Misc: ", "", $paper->getType()).'\' />");
                                        $("input[name=misc_type]").autocomplete({
                                            source: misc_types
                                        });
                                        break;
                                }
                                break;
                            case "Press":
                                switch(type){
                                    case "University Press":
                                        addAttr("URL");
                                        break;
                                    case "Provincial News":
                                        addAttr("URL");
                                        break;
                                    case "National News":
                                        addAttr("URL");
                                        break;
                                    case "International News":
                                        addAttr("URL");
                                        break;
                                    default:
                                    case "Misc":
                                        $("select[name=type]").parent().append("<input type=\'text\' name=\'misc_type\' value=\''.str_replace("Misc: ", "", $paper->getType()).'\' />");
                                        $("input[name=misc_type]").autocomplete({
                                            source: misc_types
                                        });
                                        addAttr("URL");
                                        break;
                                }
                                break;
                            case "Award":
                                switch(type){
                                    default:
                                    case "Award":
                                        addAttr("URL");
                                        break;
                                }
                                break;
                            case "Presentation":
                                switch(type){
                                    default:
                                        addAttr("Conference");
                                        addAttr("Location");
                                        addAttr("Organizing Body");
                                        addAttr("URL");
                                        break;
                                }
                                break;
                            case "Publication":
                                switch(type){
                                    case "Book":
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Edited Book":
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Book Chapter":
                                        addAttrDefn('.$this->get_defn("Book Title").');
                                        addAttrDefn('.$this->get_defn("Editors").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Book Review":
                                        addAttrDefn('.$this->get_defn("Book Title").');
                                        addAttrDefn('.$this->get_defn("Published In").');
                                        addAttrDefn('.$this->get_defn("Volume").');
                                        addAttrDefn('.$this->get_defn("Number").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Review Article":
                                        addAttrDefn('.$this->get_defn("Published In").');
                                        addAttrDefn('.$this->get_defn("Volume").');
                                        addAttrDefn('.$this->get_defn("Number").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "White Paper":
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Editor").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("URL");
                                        break;
                                    case "Magazine/Newspaper Article":
                                        addAttrDefn('.$this->get_defn("Magazine/Newspaper Title").');
                                        addAttrDefn('.$this->get_defn("Volume").');
                                        addAttrDefn('.$this->get_defn("Number").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Journal Paper":
                                        addAttrDefn('.$this->get_defn("Journal Title").');
                                        addAttrDefn('.$this->get_defn("Volume").');
                                        addAttrDefn('.$this->get_defn("Number").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Journal Abstract":
                                        addAttrDefn('.$this->get_defn("Journal Title").');
                                        addAttrDefn('.$this->get_defn("Volume").');
                                        addAttrDefn('.$this->get_defn("Number").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        break;
                                    case "Proceedings Paper":
                                        addAttrDefn("Event Title", "the name of the event");
                                        addAttrDefn('.$this->get_defn("Event Location").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Collections Paper":
                                        addAttrDefn('.$this->get_defn("Book Title").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttrDefn('.$this->get_defn("Address").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "PhD Thesis":
                                        addAttrDefn('.$this->get_defn("University").');
                                        addAttrDefn('.$this->get_defn("Department").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Masters Thesis":
                                        addAttrDefn('.$this->get_defn("University").');
                                        addAttrDefn('.$this->get_defn("Department").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Bachelors Thesis":
                                        addAttrDefn('.$this->get_defn("University").');
                                        addAttrDefn('.$this->get_defn("Department").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Poster":
                                        addAttrDefn('.$this->get_defn("Event Title").');
                                        addAttrDefn('.$this->get_defn("Event Location").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Tech Report":
                                        addAttrDefn('.$this->get_defn("University").');
                                        addAttrDefn('.$this->get_defn("Department").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    case "Manual":
                                        addAttrDefn('.$this->get_defn("Volume").');
                                        addAttrDefn('.$this->get_defn("Edition").');
                                        addAttrDefn('.$this->get_defn("Series").');
                                        addAttrDefn('.$this->get_defn("Pages").');
                                        addAttrDefn('.$this->get_defn("Publisher").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                    default:
                                    case "Misc":
                                        $("select[name=type]").parent().append("<input type=\'text\' name=\'misc_type\' value=\''.str_replace("Misc: ", "", $paper->getType()).'\' />");
                                        $("input[name=misc_type]").autocomplete({
                                            source: misc_types
                                        });
                                        addAttrDefn('.$this->get_defn("Venue").');
                                        addAttrDefn('.$this->get_defn("How Published").');
                                        addAttrDefn('.$this->get_defn("Note").');
                                        addAttr("ISBN");
                                        addAttr("ISSN");
                                        addAttr("DOI");
                                        addAttr("URL");
                                        break;
                                break;
                            }
                        }
                    }
                    
                    function placeInHidden(){
                        $.each($("#authors").children(), function(index, val){
                            $("#hiddenAuthors").append("<input class=\'auth\' type=\'hidden\' name=\'authors[]\' value=\'" + val.innerHTML + "\' />");
                        });
                    }
                    
                    function addAttr(attr){
                        if(typeof oldAttr[attr.toLowerCase()] == "undefined"){
                            var newAttr = document.createElement("tr");
                            newAttr.setAttribute("name", attr.toLowerCase());
                            newAttr.setAttribute("class", "attr");
                            newAttr.innerHTML="<td align=\"right\"><b>" + attr + ":</b></td><td><input type=\"text\" size=\"50\" name=\"" + attr.toLowerCase() + "\" /></td>";
                            $("#attributes").append(newAttr);
                        }
                        else{
                            $("#attributes").append(oldAttr[attr.toLowerCase()]);
                        }
                    }

                    function addAttrDefn(attr, defn){
                        if(typeof oldAttr[attr.toLowerCase()] == "undefined"){
                            var newAttr = document.createElement("tr");
                            newAttr.setAttribute("name", attr.toLowerCase());
                            newAttr.setAttribute("class", "attr");
                            newAttr.setAttribute("title", defn); 
                            newAttr.innerHTML="<td align=\"right\"><b>" + attr + ":</b></td><td><input type=\"text\" size=\"50\" name=\"" + attr.toLowerCase() + "\" /></td>";
                            $("#attributes").append(newAttr);
                        }
                        else{
                            $("#attributes").append(oldAttr[attr.toLowerCase()]);
                        }
                    }

                    function disableEnterKey(e){
                        var key;
                        
                        if(window.event){
                            key = window.event.keyCode;     //IE
                        }
                        else{
                            key = e.which;     //firefox
                        }
                        if(key == 13){      // ENTER
                            return false;
                        }
                        else{
                            return true;
                        }
                    }
                    
			        </script>');
				}
                
                if($edit){
                    if($create){
                        $wgOut->addHTML("<form action='$wgServer$wgScriptPath/index.php/{$category}:".str_replace("?", "%3F", str_replace("'", "&#39;", $title))."?create' method='post'>
                                        <input type='hidden' name='title' value='".str_replace("'", "&#39;", $title)."' /><input type='hidden' name='product_id' value='$product_id' />");
                    }
                    else{
                        $wgOut->addHTML("<form action='$wgServer$wgScriptPath/index.php/{$category}:{$product_id}?edit' method='post'>
                                            <input type='hidden' name='title' value='{$paper->getTitle()}' /><input type='hidden' name='product_id' value='$product_id' /><div style='font-weight:bold; font-size:14px;padding: 10px 0;'>Change Title: <input type='text' value='{$paper->getTitle()}' name='new_title' size='80' /></div>");
                    }
                }
                $wgOut->addWikiText("== {$authorTitle}s ==
                                     __NOEDITSECTION__\n");
                $authors = $paper->getAuthors();
                $i = 1;
                $nAuthors = count($authors);
                $authorNames = array();
                if(!$create){
                    foreach($authors as $author){
                        $authorNames[] = $author->getName();
                    }
                }
                if($edit){
                    $allPeople = Person::getAllPeople('all');
                    foreach($allPeople as $person){
                        if(array_search($person->getName(), $authorNames) === false){
                            $list[] = $person->getName();
                        }
                    }
                    $wgOut->addHTML("<div class='switcheroo' name='{$authorTitle}' id='authors'>
                                            <div class='left'><span>".implode("</span>\n<span>", $authorNames)."</span></div>
                                            <div class='right'><span>".implode("</span>\n<span>", $list)."</span></div>
                                        </div>");
                }
                else{
                    $texts = array();
                    foreach($authors as $author){
                        if($author->getRoles() != null){
                            $texts[] = "<a href='{$author->getUrl()}'>{$author->getNameForForms()}</a>";
                        }
                        else{
                            $texts[] = $author->getNameForForms();
                        }
                    }
                    $wgOut->addHTML(implode(", ", $texts));
                }
                
                if($category == "Publication"){
                    if($edit || !$edit && $paper->getDescription() != ""){
                        $wgOut->addWikiText("== Abstract ==
                                            __NOEDITSECTION__\n");
                    }
                }
                else if($category == "Artifact" || $category == "Activity" || $category == "Press" || $category == "Award" || $category == "Presentation"){
                    if($edit || !$edit && $paper->getDescription() != ""){
                        $wgOut->addWikiText("== Description ==
                                            __NOEDITSECTION__\n");
                    }
                }
                if($edit){
                    $wgOut->addHTML("<textarea style='height:175px; width:650px;' name='description'>{$paper->getDescription()}</textarea>");
                }
                else{
                    $wgOut->addWikiText($paper->getDescription());
                }
                
                $date = $paper->getDate();
                $wgOut->addWikiText("== $category Information ==
                                     __NOEDITSECTION__\n");
                $wgOut->addHTML("<table border=0 id='attributes'>");
                if($category == "Publication"){
                    if($edit){
                        $submittedSelected = ($paper->getStatus() == "Submitted") ? " selected='selected'" : "";
                        $toappearSelected = ($paper->getStatus() == "To Appear") ? " selected='selected'" : "";
                        $revisionSelected = ($paper->getStatus() == "Under Revision") ? " selected='selected'" : "";
                        $publishedSelected = ($paper->getStatus() == "Published") ? " selected='selected'" : "";
                        $toAppearSelected = ($paper->getStatus() == "To Appear") ? " selected='selected'" : "";
                        $rejectedSelected = ($paper->getStatus() == "Rejected") ? " selected='selected'" : "";
                        $wgOut->addHTML("<tr title='the publication status of the paper'>
                                            <td align='right'><b>Status:</b></td>
                                            <td>
                                                <select name='status'>
                                                    <option$submittedSelected title='draft submitted to venue, not yet accepted'>Submitted</option>
                                                    <option$toappearSelected title='Accepted/In Press/To Appear: will be published, not yet released.'>To Appear</option>
                                                    <option$revisionSelected title='accepted and undergoing final changes'>Under Revision</option>
                                                    <option$publishedSelected title='appears in specified venue'>Published</option>
                                                    <option$rejectedSelected title='was not accepted for publication'>Rejected</option>
                                                </select>
                                                <a href='#' title='Mouse-over options to see definitions'
                                                            onclick='return false'><img src='../skins/common/images/q_white.png'></a>
                                            </td>
                                        </tr>");
                    }
                    else{
                        $wgOut->addHTML("<tr>
                                            <td align='left'><b>Status:</b><br /></td>
                                            <td>{$paper->getStatus()}<br /></td>
                                        </tr>");
                    }
                }
                else if($category == "Artifact"){
                    if($edit){
                        $peerReviewed = ($paper->getStatus() == "Peer Reviewed") ? " checked='checked'" : "checked='checked'";
                        $notPeerReviewed = ($paper->getStatus() == "Not Peer Reviewed") ? " checked='checked'" : "";
                        $wgOut->addHTML("<tr>
                                            <td align='right' valign='top'><b>Status:</b><br /><br /></td>
                                            <td>
                                                <input type='radio' name='status' value='Peer Reviewed'$peerReviewed /> Peer Reviewed<br />
                                                <input type='radio' name='status' value='Not Peer Reviewed'$notPeerReviewed /> Not Peer Reviewed<br /></br />
                                            </td>
                                        </tr>");
                    }
                    else{
                        $wgOut->addHTML("<tr>
                                            <td valign='top'><b>Status:</b><br /><br /></td>
                                            <td>{$paper->getStatus()}<br /><br /></td>
                                        </tr>");
                    }
                }

                $align = ($edit) ? "align='right'" : "align='left'";
 
                $wgOut->addHTML("<tr title='the type of publication''>
                                        <td $align><b>Type:</b></td>");
                $type = $paper->getType();
                if($create){
                    $type = "Misc";
                }
                $currType = "";
                if($edit){
                    $wgOut->addHTML("<td><select onChange='showHideAttr(this.value);' name='type'>");
                    foreach($types[$category] as $pType => $pDescription){
                        $selected = "";
                        if($type == $pType || (strstr($type, "Misc") !== false && strstr($pType, "Misc") !== false)){
                            $selected = " selected='selected'";
                        }
                        $wgOut->addHTML("<option$selected title='$pDescription'>$pType</option>");
                    }
                    $wgOut->addHTML("</select></td>");
                }
                else{
                    $wgOut->addHTML("<td>".str_replace("Misc: ", "", $paper->getType())."</td>");
                }
                $wgOut->addHTML("</tr>");

                if($category == "Publication"){
                    if($edit){
                        $arr = $paper->getData();
                        $value = (isset($arr['peer_reviewed']))? $arr['peer_reviewed'] : "";
                        $peerReviewed = ($value == "Yes") ? " checked='checked'" : "";
                        $notPeerReviewed = ($value == "No" || $value == "") ? " checked='checked'" : "";
                        $wgOut->addHTML("<tr title='whether or not published in a peer-reviewed publication'>
                                             <td align='right' valign='top'><b>Peer Reviewed:</b><br /><br /></td>
                                             <td>
                                                 <input type='radio' name='peer_reviewed' value='Yes'$peerReviewed /> Yes<br />
                                                 <input type='radio' name='peer_reviewed' value='No'$notPeerReviewed /> No<br />
                                             </td>
                                          </tr>");
                    } else {
                        $arr = $paper->getData();
                        $value = (isset($arr['peer_reviewed']))? $arr['peer_reviewed'] : "";
                        $this->addAttrRow("Peer Reviewed", $value);
                    }
                }

                if($edit){
                    $today = getdate();

                    if($category != "Publication"){
                        $today['year'] -= 2;
                    }
                    
                    $wgOut->addHTML("<tr title='the date of publication'>
                                        <td align='right'><b>Date:</b></td>
                                        <td>".$this->date_picker($date, 2000, $today['year'] + 2)."</td>
                                    </tr>");
                }
                else{
                    $exploded = explode("-", $date);
                    
                    $month = $exploded[1];
                    $day = $exploded[2];
                    $year = $exploded[0];
                    if($year == "0000"){
                        // Year is unknown, don't display the date at all
                        $html = "";
                    }
                    else if($month == "00"){
                        // Month is unknown, only display the year
                        $dateTime = new DateTime("$year-01-01");
                        $html = $dateTime->format('Y');
                    }
                    else if($day == "00"){
                        $dateTime = new DateTime("$year-$month-01");
                        $html = $dateTime->format('M, Y');
                    }
                    else{
                        $dateTime = new DateTime("$year-$month-$day");
                        $html = $dateTime->format('M j, Y');
                    }
                    if($html != ""){
                        $wgOut->addHTML("<tr>
                                            <td><b>Date:</b></td>
                                            <td>$html</td>
                                        </tr>");
                    }
                }
                
                if($category == "Publication"){
                    $data = $paper->getData();
                    if(!isset($data['isbn'])){
                        $this->addAttrRow("ISBN","");
                    }
                    if(!isset($data['issn'])){
                        $this->addAttrRow("ISSN","");
                    }
                    if(!isset($data['doi'])){
                        $this->addAttrRow("DOI","");
                    }
                    if(!isset($data['url'])){
                        $this->addAttrRow("URL", "");
                    }
                }
                else if($category == "Artifact"){
                    //Nothing extra
                }
                else if($category == "Press"){
                    $data = $paper->getData();
                    if(!isset($data['url'])){
                        $this->addAttrRow("URL", "");
                    }
                }
                else if($category == "Award"){
                    $data = $paper->getData();
                    if(!isset($data['url'])){
                        $this->addAttrRow("URL", "");
                    }
                }
                else if($category == "Presentation"){
                    if($edit){
                        $invitedSelected = ($paper->getStatus() == "Invited") ? "checked='checked'" : "";
                        $notinvitedSelected = ($paper->getStatus() == "Not Invited") ? "checked='checked'" : "";
                        if($invitedSelected == "" && $notinvitedSelected == ""){
                            $notinvitedSelected = "checked='checked'";
                        }
                        $wgOut->addHTML("<tr>
                                            <td align='right'><b>Sub-Type:</b><br /><br /></td>
                                            <td>
                                                <input type='radio' name='status' value='Not Invited' $notinvitedSelected /> Not Invited<br />
                                                <input type='radio' name='status' value='Invited' $invitedSelected /> Invited
                                                </br />
                                            </td>
                                        </tr>");
                    }
                    else{
                        $wgOut->addHTML("<tr>
                                            <td><b>Status:</b></td>
                                            <td>{$paper->getStatus()}</td>
                                        </tr>");
                    }
                }
                else if($category == "Activity"){
                    $data = $paper->getData();
                    if(!isset($data['conference'])){
                        $this->addAttrRow("Conference", "");
                    }
                    if(!isset($data['location'])){
                        $this->addAttrRow("Location", "");
                    }
                    if(!isset($data['organizing_body'])){
                        $this->addAttrRow("Organizing Body", "");
                    }
                    if(!isset($data['url'])){
                        $this->addAttrRow("URL", "");
                    }
                }

                if(!$create){
                    foreach($paper->getData() as $attr => $value){
                        if ($attr == 'peer_reviewed'){  // want to guarantee placement as per $edit page
                            continue;
                        }
                        $attr = ucwords(str_replace("_", " ", $attr));
                        if($attr == "Isbn" ||
                           $attr == "Doi" ||
                           $attr == "Issn" ||
                           $attr == "Url"){
                            $attr = strtoupper($attr);   
                        }
                        $this->addAttrRow($attr, $value);
                    }
                }

                if($me->isLoggedIn()){
                    $rmcYears = $paper->getReportedYears('RMC');
                    $nceYears = $paper->getReportedYears('NCE');
                    $rmc = (count($rmcYears) > 0) ? implode(", ", $rmcYears) : "Never";
                    $nce = (count($nceYears) > 0) ? implode(", ", $nceYears) : "Never";

                    $reported = "<tr><td><b>Reported to RMC:</b></td><td>{$rmc}</td></tr><tr><td><b>Reported to NCE:</b></td><td>{$nce}</td></tr>";
                    $wgOut->addHTML($reported);
                }

                $wgOut->addHTML("</table>");
                $projects = $paper->getProjects();
                if($edit || !$edit && count($projects) > 0){
                    $wgOut->addWikiText("== Related Projects ==
                                         __NOEDITSECTION__\n");
                }
                
                $pProjects = array();
                if(!$create){
                    foreach($projects as $project){
                        $pProjects[] = $project->getName();
                    }
                }
                if($edit){
                    $projs = Project::orderProjects(Project::getAllProjects());
		            $pArray = array();
		            foreach($projs as $project){
		                $pArray[] = $project->getName();
		            }
		            $wgOut->addHTML("<table border='0' cellspacing='2'><tr>");
		            $i = 0;
		            foreach($pArray as $project){
                        if($i % 3 == 0){
		                    $wgOut->addHTML("</tr><tr>\n");
	                    }
	                    if(array_search($project, $pProjects) !== false){
	                        $wgOut->addHTML("<td style='min-width:150px;' valign='top'><input type='checkbox' name='projects[]' value='$project' checked='checked' /> $project<br /></td>\n");
	                    }
	                    else {
	                        $wgOut->addHTML("<td style='min-width:150px;' valign='top'><input type='checkbox' name='projects[]' value='$project' /> $project</td>\n");
	                    }
	                    $i++;
	                }
	                $wgOut->addHTML("</table>");
	                if(count($projects) > 0){
	                    foreach($projects as $project){
	                        // Add any deleted projects so that they remain as part of this project
	                        if($project->deleted){
	                            $wgOut->addHTML("<input style='display:none;' type='checkbox' name='projects[]' value='{$project->getName()}' checked='checked' />");
	                        }
	                    }
	                }
                }
                else{
                    $projectList = array();
                    if(count($projects) > 0){
                        foreach($projects as $project){
                            if(!$project->deleted){
                                $projectList[] = "<a href='{$project->getUrl()}'>{$project->getName()}</a>";
                            }
                        }
                    }
                    $wgOut->addHTML(implode(", ", $projectList));
                }
                $wgOut->addHTML("<br />");
                if($wgUser->isLoggedIn()){
                    if($create){
                        $wgOut->addHTML("<input type='submit' name='submit' value='Create $category' />");
                        $wgOut->addHTML("</form>");
                    }
                    else if($edit){
                        $wgOut->addHTML("<input type='submit' name='submit' value='Save $category' />");
                        $wgOut->addHTML("</form>");
                    }
                    else if( (!FROZEN || $me->isRoleAtLeast(STAFF)) ){
                        $wgOut->addHTML("<input type='button' name='edit' value='Edit $category' onClick='document.location=\"$wgServer$wgScriptPath/index.php/$category:".$paper->getId()."?edit\";' />");
                    }
                }
                $wgOut->output();
                $wgOut->disable();
            }
            else if($name == "Publication"){
                $wgOut->clearHTML();
                
                $wgOut->setPageTitle("Publication Does Not Exist");
                $wgOut->addHTML("The publication '$title' does not exist. <a href='$wgServer$wgScriptPath/index.php/Publication:$title?create'>Click Here</a> to create the publication.");
                
                $wgOut->output();
                $wgOut->disable();
            }
            else if($name == "Artifact"){
                $wgOut->clearHTML();
                
                $wgOut->setPageTitle("Artifact Does Not Exist");
                $wgOut->addHTML("The artifact '$title' does not exist. <a href='$wgServer$wgScriptPath/index.php/Artifact:$title?create'>Click Here</a> to create the artifact.");
                
                $wgOut->output();
                $wgOut->disable();
            }
            else if($name == "Activity"){
                $wgOut->clearHTML();
                
                $wgOut->setPageTitle("Activity Does Not Exist");
                $wgOut->addHTML("The activity '$title' does not exist. <a href='$wgServer$wgScriptPath/index.php/Activity:$title?create'>Click Here</a> to create the activity.");
                
                $wgOut->output();
                $wgOut->disable();
            }
            else if($name == "Press"){
                $wgOut->clearHTML();
                
                $wgOut->setPageTitle("Press Article Does Not Exist");
                $wgOut->addHTML("The press article '$title' does not exist. <a href='$wgServer$wgScriptPath/index.php/Press:$title?create'>Click Here</a> to create the article.");
                
                $wgOut->output();
                $wgOut->disable();
            }
            else if($name == "Award"){
                $wgOut->clearHTML();
                
                $wgOut->setPageTitle("Award Does Not Exist");
                $wgOut->addHTML("The Award '$title' does not exist. <a href='$wgServer$wgScriptPath/index.php/Award:$title?create'>Click Here</a> to create the award.");
                
                $wgOut->output();
                $wgOut->disable();
            }
            else if($name == "Presentation"){
                $wgOut->clearHTML();
                
                $wgOut->setPageTitle("Presentation Does Not Exist");
                $wgOut->addHTML("The Presentation '$title' does not exist. <a href='$wgServer$wgScriptPath/index.php/Presentation:$title?create'>Click Here</a> to create the presentation.");
                
                $wgOut->output();
                $wgOut->disable();
            }
        }
        return true;
    }
    
    function getAuthorTitle($category){
        switch($category){
            case "Publication":
                return "Author";
                break;
            case "Artifact":
                return "Author";
                break;
            case "Activity":
                return "Person";
                break;
            case "Presentation":
                return "Person";
                break;
            case "Press":
                return "Mentioned&nbsp;Name";
                break;
            case "Award":
                return "Recipient";
                break;
        }
    }
    
    function proccessPost($category){
        $_POST['date'] = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
        $_POST['abstract'] = $_POST['description'];
        if(!isset($_POST['authors'])){
            $_POST['authors'] = " ";
        }
        else{
            $_POST['authors'] = @array_unique($_POST['authors']);
        }
        if(!isset($_POST['projects']) || $_POST['projects'] == null){
            $_POST['projects'] = " ";
        }
        switch($category){
            case "Activity":
                switch($_POST['type']){
                    default:
                    case "Panel":
                        $api = new PanelAPI(true);
                        break;
                    case "Tutorial":
                        $api = new TutorialAPI(true);
                        break;
                    case "Event Organization":
                        $api = new EventOrganizationAPI(true);
                        break;
                }
                break;
            case "Presentation":
                switch($_POST['type']){
                    default:
                    case "2MM":
                        $api = new TwoMMAPI(true);
                        break;
                    case "WIP":
                        $api = new WIPAPI(true);
                        break;
                    case "RNotes":
                        $api = new RNotesAPI(true);
                        break;
                    case "Misc":
                        $api = new MiscPresAPI(true);
                        break;
                }
                break;
            case "Press":
                switch($_POST['type']){
                    default:
                    case "University Press":
                        $api = new UniversityPressAPI(true);
                        break;
                    case "Provincial News":
                        $api = new ProvincialPressAPI(true);
                        break;
                    case "National News":
                        $api = new NationalPressAPI(true);
                        break;
                    case "International News":
                        $api = new InternationalPressAPI(true);
                        break;
                    case "Misc":
                        $api = new PressAPI(true);
                        break;
                }
                break;
            case "Award":
                switch($_POST['type']){
                    default:
                    case "Award":
                        $api = new AwardsAPI(true);
                        break;
                }
                break;
            case "Artifact":
                switch($_POST['type']){
                    default:
                    case "Repository":
                        $api = new RepositoryAPI(true);
                        break;
                    case "Open Software":
                        $api = new SoftwareAPI(true);
                        break;
                    case "Patent":
                        $api = new PatentAPI(true);
                        break;
                    case "Device/Machine":
                        $api = new DeviceAPI(true);
                        break;
                    case "Aesthetic Object":
                        $api = new AestheticObjectAPI(true);
                        break;
                    case "Misc":
                        $api = new ArtifactAPI(true);
                        break;
                }
                break;
            case "Publication":
                switch($_POST['type']){
                    case "Book":
                        $api = new BookAPI(true);
                        break;
                    case "Edited Book":
                        $api = new EditedBookAPI(true);
                        break;
                    case "Book Chapter":
                        $api = new BookChapterAPI(true);
                        break;
                    case "Book Review":
                        $api = new BookReviewAPI(true);
                        break;
                    case "Review Article":
                        $api = new ReviewArticleAPI(true);
                        break;
                    case "White Paper":
                        $api = new WhitePaperAPI(true);
                        break;
                    case "Magazine/Newspaper Article":
                        $api = new MagazineAPI(true);
                        break;
                    case "Journal Paper":
                        $api = new JournalPaperAPI(true);
                        break;
                    case "Journal Abstract":
                        $api = new JournalAbstractAPI(true);
                        break;
                    case "Proceedings Paper":
                        $api = new ProceedingsPaperAPI(true);
                        break;
                    case "Collections Paper":
                        $api = new CollectionAPI(true);
                        break;
                    case "PhD Thesis":
                        $api = new PHDThesisAPI(true);
                        break;
                    case "Masters Thesis":
                        $api = new MastersThesisAPI(true);
                        break;
                    case "Bachelors Thesis":
                        $api = new BachelorsThesisAPI(true);
                        break;
                    case "Poster":
                        $api = new PosterAPI(true);
                        break;
                    case "Tech Report":
                        $api = new TechReportAPI(true);
                        break;
                    case "Manual":
                        $api = new ManualAPI(true);
                        break;
                    default:
                    case "Misc":
                        $api = new MiscAPI(true);
                        break;
                break;
            }
        }
        $api->doAction();
    }
    
    function date_picker($date, $startyear=NULL, $endyear=NULL){
        $newDate = explode("-", $date);
        $year = @$newDate[0];
        $month = @$newDate[1];
        $day = @$newDate[2];
        if($startyear==NULL){
            $startyear = date("Y")-100;
        }
        if($endyear==NULL){
            $endyear=date("Y")+50;
        }

        $months=array('','January','February','March','April','May',
        'June','July','August', 'September','October','November','December');

        // Month dropdown
        $html="<select name=\"month\">";

        if($month == "00"){
            $html.="<option selected value='00'>--</option>";
        }
        else{
            $html.="<option value='00'>--</option>";
        }
        for($i=1;$i<=12;$i++){
            $selected = "";
            if($month == $i){
                $selected = "selected='selected'";
            }
            $html.="<option $selected value='$i'>$months[$i]</option>";
        }
        $html.="</select> ";
       
        // Day dropdown
        $html.="<select name=\"day\">";
        if($day == "00"){
            $html.="<option selected value='00'>--</option>";
        }
        else{
            $html.="<option value='00'>--</option>";
        }
        for($i=1;$i<=31;$i++){
            $selected = "";
            if($day == $i){
                $selected = "selected='selected'";
            }
            $html.="<option $selected value='$i'>$i</option>";
        }
        $html.="</select> ";

        // Year dropdown
        $html.="<select name=\"year\">";
        if($year == "0000"){
            $html.="<option selected value='0000'>----</option>";
        }
        else{
            $html.="<option value='0000'>----</option>";
        }
        for($i=$endyear;$i>=$startyear;$i--){
            $selected = "";
            if($year == $i){
                $selected = "selected='selected'";
            }     
            $html.="<option $selected value='$i'>$i</option>";
        }
        $html.="</select> ";

        return $html;
    }


    function get_defn($key){
        global $optionDefs;
        return "\"".$key."\", \"".$optionDefs[$key]."\"";
    }

    
    function addAttrRow($attr, $value){
        global $wgOut, $wgUser, $optionDefs;
        $me = Person::newFromId($wgUser->getId());
        $edit = (isset($_GET['edit']) || isset($_GET['create']));
        $edit = ( $edit && (!FROZEN || $me->isRoleAtLeast(STAFF)) );
        
        if($edit || !$edit && $value != ""){
            $align = ($edit) ? "align='right'" : "align='left'";
            $wgOut->addHTML("<tr class='attr' name='$attr'>
                                <td $align><b>$attr:</b></td>");
        }
        if($edit){
            if (isset($optionDefs[$attr])){
                $wgOut->addHTML("<tr class='attr' name='$attr' title='$optionDefs[$attr]'>
                                   <td align='right'><b>$attr:</b></td>");
            } else {
                $wgOut->addHTML("<tr class='attr' name='$attr'>
                                   <td align='right'><b>$attr:</b></td>");
            }
        }
        if($edit){
            $wgOut->addHTML("<td><input size='50' name='".strtolower($attr)."' type='text' value='$value' /></td>");
        }
        else{
            if($value != ""){
                if($attr == "URL"){
                    $wgOut->addHTML("<td><a target='_blank' href='$value'>$value</a></td>");
                }
                else{
                    $wgOut->addHTML("<td>$value</td>");
                }
            }
        }
        $wgOut->addHTML("</tr>");
    }
    
    function removeTabs(&$content_actions){
        global $wgArticle, $wgRoles;
        if($wgArticle != null){
            $name = $wgArticle->getTitle()->getNsText();
            $title = $wgArticle->getTitle()->getText();
            if($name == ""){
                $split = explode(":", $title);
                if(count($split) > 1){
                    $title = $split[1];
                }
                else{
                    $title = "";
                }
                $name = $split[0];
            }
            if($name == "Publication" || $name == "Artifact" || $name == "Activity" || $name == "Press" || $name == "Presentation"){
                unset($content_actions['protect']);
                unset($content_actions['watch']);
                unset($content_actions['unwatch']);
                unset($content_actions['create']);
                unset($content_actions['history']);
                unset($content_actions['delete']);
                unset($content_actions['talk']);
                unset($content_actions['move']);
                unset($content_actions['edit']);
                return false;
            }
        }
        return true;
    }
}
?>
