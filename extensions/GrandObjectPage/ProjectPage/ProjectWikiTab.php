<?php

class ProjectWikiTab extends AbstractTab {

    var $project;
    var $visibility;

    function ProjectWikiTab($project, $visibility){
        parent::AbstractTab("Wiki");
        $this->project = $project;
        $this->visibility = $visibility;
    }
    
    function generateBody(){
        global $wgUser, $wgServer, $wgScriptPath;
        $project = $this->project;
        $me = Person::newFromWgUser();
        $edit = $this->visibility['edit'];
        
        if(!($wgUser->isLoggedIn() && $me->isMemberOf($project)) && !$me->isRoleAtLeast(MANAGER)){
            return $this->html;
        }
        
        $this->html .= "<h2>Create New Page</h2>
        <div>
            <table>
                <tr>
                    <td><b>Title:</b></td><td><input id='newPageTitle' type='text' name='title' size='40' /></td><td><a class='button' id='createPageButton'>Create Page</a></td>
                </tr>
            </table>
        </div>
        <script type='text/javascript'>
            $('#createPageButton').click(function(){
                clearWarning();
                var title = $('#newPageTitle').val().trim();
                if(title == ''){
                    addWarning('The title must not be empty');
                }
                else if(title.indexOf('%') !== -1 ||
                        title.indexOf('[') !== -1 ||
                        title.indexOf(']') !== -1 ||
                        title.indexOf('{') !== -1 ||
                        title.indexOf('}') !== -1){
                    addWarning('The title must not contain the following characters: <b>%</b>, <b>[</b>, <b>]</b>, <b>{</b>, <b>}</b>');
                }
                else{ 
                    document.location = '$wgServer$wgScriptPath/index.php/{$project->getName()}:' + title + '?action=edit';
                }
            });
        </script>";
        
        $pages = $this->project->getWikiPages();
        $this->html .= "<h2>Search</h2><table id='projectWikiPages' style='background:#ffffff;' cellspacing='1' cellpadding='3' frame='box' rules='all'><thead><tr bgcolor='#F2F2F2'><th>Page Title</th><th>Last Edited</th><th>Last Edited By</th></tr></thead>\n";
        $this->html .= "<tbody>\n";
        foreach($pages as $page){
            if($page->getTitle()->getText() != "Main"){
                $this->html .= "<tr>\n";
                $revId = $page->getRevIdFetched();
                $revision = Revision::newFromId($revId);
			    $date = $revision->getTimestamp();
			    $year = substr($date, 0, 4);
			    $month = substr($date, 4, 2);
			    $day = substr($date, 6, 2);
			    $hour = substr($date, 8, 2);
			    $minute = substr($date, 10, 2);
			    $second = substr($date, 12, 2);
			    $editor = Person::newFromId($revision->getRawUser());
                $this->html .= "<td><a href='$wgServer$wgScriptPath/index.php/{$project->getName()}:{$page->getTitle()->getText()}'>{$page->getTitle()->getText()}</a></td>\n";
                $this->html .= "<td>{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}</td>\n";
                $this->html .= "<td><a href='{$editor->getUrl()}'>{$editor->getReversedName()}</a></td>\n";
                $this->html .= "</tr>\n";
            }
        }
        $this->html .= "</tbody></table>";
        $this->html .= "<script type='text/javascript'>
            $('#projectWikiPages').dataTable({'iDisplayLength': 100});
        </script>";
        return $this->html;
    }

}    
    
?>
