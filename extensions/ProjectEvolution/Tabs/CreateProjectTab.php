<?php

class CreateProjectTab extends ProjectTab {

    function CreateProjectTab(){
        parent::ProjectTab("Create");
    }
    
    static function createForm($pre){
    
        $form = new FormContainer("{$pre}_form_container");
    
        $table = new FormTable("{$pre}_form_table");
        
        $acronymRow = new FormTableRow("{$pre}_acronym_row");
        $acronymRow->append(new Label("{$pre}_acronym_label", "Acronym", "The acronym/name for the project ie. MEOW", VALIDATE_NOT_NULL));
        $acronymRow->append(new TextField("new_acronym", "Acronym", "", 12, VALIDATE_NOT_NULL));
        
        $fullNameRow = new FormTableRow("{$pre}_full_name_row");
        $fullNameRow->append(new Label("{$pre}_full_name_label", "Full Name", "The project's full name ie. Media Enabled Organizational Worldflow", VALIDATE_NOT_NULL));
        $fullNameRow->append(new TextField("new_full_name", "Full Name", "", 40, VALIDATE_NOT_NULL));
        
        $proposedRow = new FormTableRow("{$pre}_proposed_row");
        $proposedRow->append(new Label("{$pre}_proposed_label", "Proposed?", "Whether or not this project is proposed(loi) or not", VALIDATE_NOT_NULL));
        $proposedRow->append(new RadioBox("{$pre}_proposed", "Proposed?", "No", array("Yes", "No"), VALIDATE_NOT_NULL));
        
        $descRow = new FormTableRow("{$pre}_description_row");
        $descRow->append(new Label("{$pre}_description_label", "Description", "The description of the project", VALIDATE_NOTHING));
        $descRow->append(new TextareaField("{$pre}_description", "Description", "", VALIDATE_NOTHING));
        
        $themeFieldSet = new FieldSet("{$pre}_themes_set", "Themes");
        $themeTable = new FormTable("{$pre}_themes_table");
        
        $theme1Row = new FormTableRow("{$pre}_theme1_row");
        $theme1Row->append(new Label("{$pre}_theme1_label", "AnImage", "", VALIDATE_NOTHING));
        $theme1Row->append(new PercentField("{$pre}_theme1", "AnImage", "", VALIDATE_NOTHING));
        
        $theme2Row = new FormTableRow("{$pre}_theme2_row");
        $theme2Row->append(new Label("{$pre}_theme2_label", "GamSim", "", VALIDATE_NOTHING));
        $theme2Row->append(new PercentField("{$pre}_theme2", "GamSim", "", VALIDATE_NOTHING));
        
        $theme3Row = new FormTableRow("{$pre}_theme3_row");
        $theme3Row->append(new Label("{$pre}_theme3_label", "nMEDIA", "", VALIDATE_NOTHING));
        $theme3Row->append(new PercentField("{$pre}_theme3", "nMEDIA", "", VALIDATE_NOTHING));
        
        $theme4Row = new FormTableRow("{$pre}_theme4_row");
        $theme4Row->append(new Label("{$pre}_theme4_label", "SocLeg", "", VALIDATE_NOTHING));
        $theme4Row->append(new PercentField("{$pre}_theme4", "SocLeg", "", VALIDATE_NOTHING));
        
        $theme5Row = new FormTableRow("{$pre}_theme5_row");
        $theme5Row->append(new Label("{$pre}_theme5_label", "TechMeth", "", VALIDATE_NOTHING));
        $theme5Row->append(new PercentField("{$pre}_theme5", "TechMeth", "", VALIDATE_NOTHING));
        
        $themeTable->append($theme1Row);
        $themeTable->append($theme2Row);
        $themeTable->append($theme3Row);
        $themeTable->append($theme4Row);
        $themeTable->append($theme5Row);
        $themeFieldSet->append($themeTable);
        
        $table->append($acronymRow);
        $table->append($fullNameRow);
        $table->append($proposedRow);
        $table->append($descRow);
        
        $form->append($table);
        $form->append($themeFieldSet);
        
        return $form;
    }
    
    function generateBody(){
        global $wgUser, $wgServer, $wgScriptPath;
        $form = self::createForm('new');
        $this->html = $form->render();
        return $this->html;
    }
    
    function handleEdit(){
        global $wgMessages;
        $form = self::createForm('new');
        $errors = $form->validate();
        
        if(count($errors) == 0){
            // Call the API
            $form->getElementById("new_acronym")->setPOST("acronym");
            $form->getElementById("new_full_name")->setPOST("fullName");
            $form->getElementById("new_proposed")->setPOST("proposed");
            $form->getElementById("new_description")->setPOST("description");
            $form->getElementById("new_theme1")->setPOST("theme1");
            $form->getElementById("new_theme2")->setPOST("theme2");
            $form->getElementById("new_theme3")->setPOST("theme3");
            $form->getElementById("new_theme4")->setPOST("theme4");
            $form->getElementById("new_theme5")->setPOST("theme5");
            //APIRequest::doAction('CreateProject', true);
            $form->reset();
        }
        return implode("<br />\n", $errors);
    }
}    
    
?>
