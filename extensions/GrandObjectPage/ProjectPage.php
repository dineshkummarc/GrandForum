<?php
require_once('ProjectPage/ProjectVisualisationsTab.php');
autoload_register('GrandObjectPage/ProjectPage');

$projectPage = new ProjectPage();
$wgHooks['ArticleViewHeader'][] = array($projectPage, 'processPage');
$wgHooks['SkinTemplateTabs'][] = array($projectPage, 'showTabs');

class ProjectPage {

    function processPage($article, $outputDone, $pcache){
        global $wgOut, $wgTitle, $wgUser, $wgRoles, $wgServer, $wgScriptPath;
        
        $me = Person::newFromId($wgUser->getId());
        if(!$wgOut->isDisabled()){
            $name = $article->getTitle()->getNsText();
            $title = $article->getTitle()->getText();
            $project = Project::newFromName($name);
            
            $wgOut->addScript("<script type='text/javascript'>
                function stripAlphaChars(id){
                    var str = $('#' + id).attr('value');
                    var out = new String(str); 
                    out = out.replace(/[^0-9]/g, ''); 
                    if(out > 100){
                        out = 100;
                    }
                    else if(out < 0){
                        out = 0;
                    }
                    $('#' + id).attr('value', out);
                }
            </script>");
            
            
            if($name == ""){
                $split = explode(":", $name);
                if(count($split) > 1){
                    $title = $split[1];
                }
                else{
                    $title = "";
                }
                $name = $split[0];
            }
            if($title != "Main"){
                if($wgTitle->getText() == "Mail Index"){
                    TabUtils::clearActions();
                }
                return true;
            }
            
            $isLead = false;
            if($project != null){
                if($me->isRoleAtLeast(MANAGER)){
                    $isLead = true;
                }
                if(!$isLead){
                    $isLead = $me->leadershipOf($project->getName());
                }
            }
            
            $isMember = $me->isMemberOf($project);
            
            $isLead = ( $isLead && (!FROZEN || $me->isRoleAtLeast(STAFF)) );
            $isMember = ($isMember && (!FROZEN || $me->isRoleAtLeast(STAFF)) );
            $edit = (isset($_POST['edit']) && $isLead);
            
            // Project Exists and it is the right Namespace
            if($project != null && $project->getName() != null){
                TabUtils::clearActions();
                $wgOut->clearHTML();
                $wgOut->setPageTitle($project->getFullName());
                
                $visibility = array();
                if(!$project->isDeleted()){
                    $visibility['edit'] = $edit;
                    $visibility['isLead'] = $isLead;
                    $visibility['isMember'] = $isMember;
                }
                else{
                    $visibility['edit'] = false;
                    $visibility['isLead'] = false;
                    $visibility['isMember'] = false;
                }
                
                $tabbedPage = new TabbedPage("project");
                $tabbedPage->addTab(new ProjectMainTab($project, $visibility));
                $tabbedPage->addTab(new ProjectMilestonesTab($project, $visibility));
                $tabbedPage->addTab(new ProjectDashboardTab($project, $visibility));
                $tabbedPage->addTab(new ProjectBudgetTab($project, $visibility));
                $tabbedPage->addTab(new ProjectVisualisationsTab($project, $visibility));
                $tabbedPage->showPage();
                
                $wgOut->output();
                $wgOut->disable();
            }
        }
        return true;
    }
    
    // Adds the tabs for the user's projects
    function showTabs($skin, &$content_actions){
        global $wgServer, $wgScriptPath, $wgArticle, $wgUser, $wgRoles, $wgOut;
        if($wgArticle != null){
            $name = $wgArticle->getTitle()->getNsText();
            $title = $wgArticle->getTitle()->getText();
            if($name == ""){
                $split = explode(":", $name);
                if(count($split) > 1){
                    $title = $split[1];
                }
                else{
                    $title = "";
                }
                $name = $split[0];
            }
            if($title != "Main"){
                return true;
            }
            $me = Person::newFromId($wgUser->getId());
            if($me->isMemberOf(Project::newFromName($name))){
                $content_actions = array();
                foreach($me->getProjects() as $proj){
                    if($name != $proj->getName()){
                        $class = false;
                    }
                    else{
                        $class = "selected";
                    }
                    $content_actions[] = array (
                         'class' => $class,
                         'text'  => $proj->getName(),
                         'href'  => "{$proj->getUrl()}",
                        );
                }
            }
        }
        return true;
    }
}
?>
