<?php

autoload_register('GrandObjectPage/PersonPage');

$wgHooks['UnknownAction'][] = 'PersonProfileTab::getPersonCloudData';
$wgHooks['UnknownAction'][] = 'PersonVisualizationsTab::getTimelineData';
$wgHooks['UnknownAction'][] = 'PersonVisualizationsTab::getDoughnutData';
$wgHooks['UnknownAction'][] = 'PersonVisualizationsTab::getChordData';
$wgHooks['UnknownAction'][] = 'PersonVisualTab::getSurveyData';

$wgHooks['ArticleViewHeader'][] = 'PersonPage::processPage';
$wgHooks['userCan'][] = 'PersonPage::userCanExecute';
$wgHooks['SubLevelTabs'][] = 'PersonPage::createSubTabs';

class PersonPage {

    function userCanExecute(&$title, &$user, $action, &$result){
        $name = $title->getNSText();
        if($name == "HQP"){
            $result = $user->isLoggedIn();
        }
        return true;
    }

    function processPage($article, $outputDone, $pcache){
        global $wgOut, $wgUser, $wgRoles, $wgServer, $wgScriptPath, $wgTitle, $wgRoleValues, $config, $wgImpersonating, $wgDelegating;
        $result = true;
        self::userCanExecute($wgTitle, $wgUser, "read", $result);
        if(!$result){
            permissionError();
        }
        $me = Person::newFromId($wgUser->getId());
        $nsText = ($article != null) ? str_replace("_", " ", $article->getTitle()->getNsText()) : "";
        if(!isset($wgRoleValues[$nsText])){
            // Namespace is not a role namespace
            return true;
        }
        if(!$wgOut->isDisabled()){
            $role = $nsText;
            $name = $article->getTitle()->getText();
            if($role == ""){
                $split = explode(":", $name);
                if(count($split) > 1){
                    $name = $split[1];
                }
                else{
                    $name = "";
                }
                $role = $split[0];
            }
            $person = Person::newFromName($name);
            if((array_search($role, $wgRoles) !== false || $role == INACTIVE || 
                                                           $role == PL || $role == 'PL') && 
               $person->getName() != null && 
               $person != null && ($person->isRole($role) || $person->isRole($role."-Candidate"))){
                TabUtils::clearActions();
                $supervisors = $person->getSupervisors(true);
                
                $isMe = ($person->isMe() ||
                        $me->isRoleAtLeast(STAFF));
                $isSupervisor = false;
                foreach($supervisors as $supervisor){
                    if($supervisor->getName() == $me->getName()){
                        $isSupervisor = true;
                        break;
                    }
                }
                $isChampion = $person->isRole(CHAMP);
                if($isChampion){
                    $creators = $person->getCreators();
                    foreach($creators as $creator){
                        if($creator->getId() == $me->getId()){
                            $isSupervisor = true;
                        }
                    }
                    foreach($person->getProjects() as $project){
                        if(($project->isSubProject() && $me->leadershipOf($project->getParent())) || $me->leadershipOf($project)){
                            $isSupervisor = true;
                        }
                    }
                }
                foreach($me->getThemeProjects() as $project){
                    if($person->isMemberOf($project)){
                        $isSupervisor = true;
                        break;
                    }
                }
                $isSupervisor = ( $isSupervisor || ($me->isRoleAtLeast(MANAGER)) );
                $isMe = ( $isMe || ($me->isRoleAtLeast(MANAGER)) );
                $edit = ((isset($_GET['edit']) || isset($_POST['edit'])) && ($isMe || $isSupervisor));
                $edit = ( $edit && ($me->isRoleAtLeast(MANAGER)) );
                
                $post = ((isset($_POST['submit']) && $_POST['submit'] == "Save Profile"));
                $post = ( $post && ($me->isRoleAtLeast(MANAGER)) );
                $wgOut->clearHTML();
                
                //Adding support for GET['edit']
                if(isset($_GET['edit'])){
                    $_POST['edit'] = true;
                    $_POST['submit'] = "Edit Main";
                }
                
                // Start the PersonPage
                $visibility = array();
                $visibility['edit'] = $edit;
                $visibility['isMe'] = $isMe;
                $visibility['isSupervisor'] = $isSupervisor;
                $visibility['isChampion'] = $isChampion;
                
                self::showTitle($person, $visibility);

                if(isset($_GET['startRange']) && isset($_GET['endRange']) && 
                   $me->getId() == $person->getId() && 
                   !$wgImpersonating && !$wgDelegating){
                    // Value of profile start/end fields to be updated
                    $me->profileStartDate = $_GET['startRange'];
                    $me->profileEndDate = $_GET['endRange'];
                    $me->update();
                }
                // Set the value of the Profile start/end fields
                if(!isset($_GET['startRange']) && !isset($_GET['endRange']) && $me->getId() == $person->getId()){
                    $startRange = ($me->getProfileStartDate() != "0000-00-00") ? $me->getProfileStartDate() : CYCLE_START;
                    $endRange   = ($me->getProfileEndDate()   != "0000-00-00") ? $me->getProfileEndDate()   : CYCLE_END;
                }
                else{
                    $startRange = (isset($_GET['startRange'])) ? $_GET['startRange'] : CYCLE_START;
                    $endRange   = (isset($_GET['endRange']))   ? $_GET['endRange']   : CYCLE_END;
                }
                $tabbedPage = new TabbedPage("person");
                
                $tabbedPage->addTab(new PersonProfileTab($person, $visibility));
                if($person->isRole(NI)){
                    $tabbedPage->addTab(new PersonFECTab($person, $visibility));
                }
                if($config->getValue('networkName') == 'AGE-WELL' && ($person->isRole(HQP) || $person->isRole(HQP."-Candidate"))){
                    $tabbedPage->addTab(new HQPEpicTab($person, $visibility));
                }
                if($wgUser->isLoggedIn() && $person->isRoleDuring(HQP, '0000-00-00 00:00:00', '2030-00-00 00:00:00')){
                    $tabbedPage->addTab(new HQPExitTab($person, $visibility));
                }
                if($config->getValue('projectsEnabled')){
                    $tabbedPage->addTab(new PersonProjectTab($person, $visibility));
                }
                if($wgUser->isLoggedIn() && $person->isRole(NI) || $person->isRole(HQP) || $person->wasLastRole(HQP)){
                    if($visibility['isMe']){
                        $tabbedPage->addTab(new PersonPublicationsTab($person,$visibility, 'Award', $startRange, $endRange));
                    }
                    $tabbedPage->addTab(new PersonPublicationsTab($person,$visibility, 'Publication', $startRange, $endRange));
                    $tabbedPage->addTab(new PersonPublicationsTab($person,$visibility, 'Presentation', $startRange, $endRange));
                    if($visibility['isMe']){
                        $tabbedPage->addTab(new PersonPublicationsTypesTab($person,$visibility, 'Activity', $startRange, $endRange));
                    }
                }
                if($wgUser->isLoggedIn() && $person->isRole(NI) && $visibility['isMe']){
                    $tabbedPage->addTab(new PersonGrantsTab($person, $visibility, $startRange, $endRange));
                    //$tabbedPage->addTab(new PersonContributionsTab($person, $visibility));
                }
                if($wgUser->isLoggedIn() && $person->isRole(NI) && $visibility['isMe']){
                    $tabbedPage->addTab(new PersonCoursesTab($person,$visibility, $startRange, $endRange));
                }
                if($wgUser->isLoggedIn() && $person->isRole(NI) && $visibility['isMe']){
                    $tabbedPage->addTab(new PersonGradStudentsTab($person, $visibility, $startRange, $endRange));
                }
                if($person->isRole(HQP) || $person->wasLastRole(HQP)){
                    $tabbedPage->addTab(new PersonRelationsTab($person, $visibility));
                }
                if(($wgUser->isLoggedIn() && $person->isRole(NI) || $person->isRole(HQP) || $person->wasLastRole(HQP)) && $visibility['isMe']){
                    $tabbedPage->addTab(new PersonPublicationsTab($person,$visibility, 'Patent/Spin-Off', $startRange, $endRange));
                }
                if($wgUser->isLoggedIn() && $person->isRole(NI) && $visibility['isMe']){
                    $tabbedPage->addTab(new PersonCitationsTab($person, $visibility));
                }
                //$tabbedPage->addTab(new PersonProductsTab($person, $visibility));
                if($config->getValue('projectsEnabled')){
                    $tabbedPage->addTab(new PersonDashboardTab($person, $visibility));
                }
                /*if(isExtensionEnabled('AllocatedBudgets') && $person->isRoleAtLeast(NI) && !$person->isRole(AR)){
                    $tabbedPage->addTab(new PersonBudgetTab($person, $visibility));
                }*/
                if(isExtensionEnabled('Acknowledgements')){
                    $tabbedPage->addTab(new PersonAcknowledgementTab($person, $visibility));
                }
                if($config->getValue('networkName') != 'Faculty of Science'){
                    $tabbedPage->addTab(new PersonVisualizationsTab($person, $visibility));
                }
                //$tabbedPage->addTab(new PersonVisualTab($person,$visibility));
                //$tabbedPage->addTab(new PersonDataQualityTab($person, $visibility));
                $tabbedPage->showPage();

                self::showTitle($person, $visibility);
                $wgOut->output();
                $wgOut->disable();
            }
            else if($person != null && 
                    $person->getName() != null && 
                    !$person->isRole($role)){
                TabUtils::clearActions();
                // User Exists, but it is probably the wrong Namespace
                $wgOut->clearHTML();
                $wgOut->setPageTitle("User Does Not Exist");
                $roles = $person->getRoles();
                $wgOut->addHTML("There is no user '$role:$name'");
                if(isset($roles[0])){
                    $wgOut->addHTML("<br />
                                    Did you mean <a href='$wgServer$wgScriptPath/index.php/{$roles[0]->getRole()}:{$person->getName()}'>{$roles[0]->getRole()}:{$person->getName()}</a>?");
                }
                $wgOut->output();
                $wgOut->disable();
            }
            else if(array_search($role, $wgRoles) !== false && $wgTitle->getText() != "Mail Index" && strstr($wgTitle->getText(), "MAIL ") === false){
                // User does not exist
                TabUtils::clearActions();
                $wgOut->clearHTML();
                $wgOut->setPageTitle("User Does Not Exist");
                $wgOut->addHTML("There is no user '$role:$name'");
                $wgOut->output();
                $wgOut->disable();
            }
            else if($wgTitle->getText() == "Mail Index"){
                TabUtils::clearActions();
            }
        }
        return true;
    }
    
    /**
     * Displays the title for this person
     */
    function showTitle($person, $visibility){
        global $wgOut;
        $wgOut->setPageTitle($person->getReversedName());
        $wgOut->addHTML("<script type='text/javascript'>
            $('.custom-title').hide();
        </script>");
    }
    
    static function createSubTabs(&$tabs){
        global $wgUser, $wgServer, $wgScriptPath, $wgTitle;
        $me = Person::newFromWgUser();
        if($me->isLoggedIn()){
            $selected = ($me->isRole($wgTitle->getNSText()) && $me->getName() == $wgTitle->getText()) ? "selected" : "";
            $tabs['Profile']['subtabs'][] = TabUtils::createSubTab($me->getNameForForms(), $me->getUrl(), $selected);
        }
        return true;
    }
}
?>
