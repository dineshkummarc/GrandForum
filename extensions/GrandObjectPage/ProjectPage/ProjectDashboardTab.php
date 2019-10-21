<?php

class ProjectDashboardTab extends AbstractEditableTab {

    var $project;
    var $visibility;

    function ProjectDashboardTab($project, $visibility){
        parent::AbstractTab("Dashboard");
        $this->project = $project;
        $this->visibility = $visibility;
        if(isset($_GET['showDashboard'])){
            echo $this->showDashboard($this->project, $this->visibility);
            exit;
        }
    }
    
    function tabSelect(){
        return "_.defer(function(){
            $('select.chosen').chosen();
            $('button[value=\"Edit Dashboard\"]:not(#editTopResearchOutcomes):not(#editTechnologyEvaluationAdoption)').css('display', 'none');
            $('button[value=\"Save Dashboard\"]:not(#editTopResearchOutcomes):not(#editTechnologyEvaluationAdoption)').css('display', 'none');
            $('div#dashboard input[value=\"Cancel\"]:not(#cancelTopResearchOutcomes):not(#cancelTechnologyEvaluationAdoption)').css('display', 'none');
        });";
    }
    
    function handleEdit(){
        global $config;
        if($this->canEdit() && isset($_POST['top_products']) && is_array($_POST['top_products'])){
            DBFunctions::delete('grand_top_products',
                                array('type' => EQ('PROJECT'),
                                      'obj_id' => EQ($this->project->getId())));
            foreach($_POST['top_products'] as $product){
                if($product != ""){
                    $exploded = explode("_", $product);
                    $type = $exploded[0];
                    $productId = $exploded[1];
                    DBFunctions::insert('grand_top_products',
                                        array('type' => 'PROJECT',
                                              'obj_id' => $this->project->getId(),
                                              'product_type' => $type,
                                              'product_id' => $productId));
                }
            }
            if($config->getValue('projectTechEnabled')){
                $this->project->technology = array(
                    'response1' => $_POST['response1'],
                    'response2' => $_POST['response2'],
                    'response3' => $_POST['response3']
                );
                $this->project->saveTechnology();
            }
        }
    }
    
    function canEdit(){
        return ($this->project->userCanEdit() && !$this->project->isSubProject());
    }
    
    function generateBody(){
        global $wgServer, $wgScriptPath, $config;
        $me = Person::newFromWgUser();
        if($me->isRoleAtLeast(HQP) && ($me->isMemberOf($this->project) || !$me->isSubRole("UofC"))){
            if(!$this->project->isSubProject()){
                $this->showTopProducts($this->project, $this->visibility);
                if($config->getValue('projectTechEnabled')){
                    $this->showTechnologyEvaluationAdoption($this->project, $this->visibility);
                }
            }
            $this->html .= "<div id='ajax_dashboard'><br /><span class='throbber'></span></div>";
            $this->html .= "<script type='text/javascript'>
            $.get('{$this->project->getUrl()}?showDashboard', function(response){
                $('#ajax_dashboard').html(response);
            });
            _.defer(function(){
                $('button[value=\"Edit Dashboard\"]:not(#editTopResearchOutcomes):not(#editTechnologyEvaluationAdoption)').css('display', 'none');
            });</script>";
        }
        return $this->html;
    }
    
    function generateEditBody(){
        global $config;
        if(!$this->project->isSubProject()){
            $this->showEditTopProducts($this->project, $this->visibility);
            if($config->getValue('projectTechEnabled')){
                $this->showEditTechnologyEvaluationAdoption($this->project, $this->visibility);
            }
        }
        $this->html .= "<div id='ajax_dashboard'><br /><span class='throbber'></span></div>";
        $this->html .= "<script type='text/javascript'>
            $.get('{$this->project->getUrl()}?showDashboard', function(response){
                $('#ajax_dashboard').html(response);
            });
            _.defer(function(){
                $('select.chosen:visible').chosen();
                $('select.chosen').each(function(i, el){
                    var prevVal = $(el).val();
                    if(prevVal != ''){
                        $('option[value=' + prevVal + ']', $('select.chosen').not(el)).prop('disabled', true);
                    }
                    $('select.chosen').trigger('chosen:updated');
                    $(el).change(function(e, p){
                        var id = $(this).val();
                        if(prevVal != ''){
                            $('option[value=' + prevVal + ']', $('select.chosen').not(this)).prop('disabled', false);
                        }
                        if(id != ''){
                            $('option[value=' + id + ']', $('select.chosen').not(this)).prop('disabled', true);
                        }
                        $('select.chosen').trigger('chosen:updated');
                        prevVal = id;
                    });
                });
                $('button[value=\"Save Dashboard\"]:not(#editTopResearchOutcomes):not(#editTechnologyEvaluationAdoption)').css('display', 'none');
                $('div#dashboard input[value=\"Cancel\"]:not(#cancelTopResearchOutcomes):not(#cancelTechnologyEvaluationAdoption)').css('display', 'none');
            });
        </script>";
    }
    
    private function optGroup($products, $category, $value){
        $html = "";
        $plural = Inflect::pluralize($category);
        $html .= "<optgroup label='$plural'>";
        $count = 0;
        foreach($products as $product){
            if($product instanceof Contribution && $category == "Contribution"){
                $selected = ($value == $product->getId()) ? "selected='selected'" : "";
                $year = $product->getStartYear();
                $html .= "<option value='CONTRIBUTION_{$product->getId()}' $selected>($year) {$product->getTitle()}</option>";
                $count++;
            }
            else if($product instanceof Paper && $category == $product->getCategory()){
                $selected = ($value == $product->getId()) ? "selected='selected'" : "";
                $year = substr($product->getDate(), 0, 4);
                $html .= "<option value='PRODUCT_{$product->getId()}' $selected>($year) {$product->getType()}: {$product->getTitle()}</option>";
                $count++;
            }
        }
        $html .= "</optgroup>";
        if($count > 0){
            return $html;
        }
        return "";
    }
    
    private function selectList($project, $value){
        $productStructure = Product::structure();
        $categories = @array_keys($productStructure['categories']);
        $allProducts = array_merge($project->getPapers('all', "0000-00-00", "2100-01-01"),
                                   $project->getContributions());
        $products = array();
        foreach($allProducts as $product){
            $date = $product->getDate();
            $products[$date."_{$product->getId()}"] = $product;
        }
        ksort($products);
        $products = array_reverse($products);
        $html = "<select class='chosen' name='top_products[]' style='max-width:800px;'>";
        $html .= "<option value=''>---</option>";
        foreach($categories as $category){
            $html .= $this->optGroup($products, $category, $value);
        }
        $html .= $this->optGroup($products, "Contribution", $value);
        $html .= "</select><br />";
        return $html;
    }
    
    function showEditTopProducts($project, $visibility){
        global $config;
        $this->html .= "<h2>Top Research Outcomes</h2>";
        $this->html .= "<small>Select up to {$config->getValue('nProjectTopProducts')} research outcomes that you believe showcase the productivity of {$project->getName()} the greatest.  The order that you specify them in does not matter.  The ".strtolower(Inflect::pluralize($config->getValue('productsTerm')))." will be sorted in descending order by date.  These top ".strtolower(Inflect::pluralize($config->getValue('productsTerm')))." will be shown in the annual report. ie:
        <ul>
            <li>Publication in a high-impact journal</li>
            <li>Major partnerships or collaborations</li>
            <li>Licensing a product</li>
            <li>High profile awards</li>
            <li>Formation of a start-up company</li>
        </ul>
        </small>";
        $products = $project->getTopProducts();
        $i = 0;
        foreach($products as $product){
            $this->html .= $this->selectList($project, $product->getId());
            $i++;
        }
        for($i; $i < $config->getValue('nProjectTopProducts'); $i++){
            $this->html .= $this->selectList($project, "");
        }
        $this->html .= "<br /><button id='editTopResearchOutcomes' type='submit' value='Save Dashboard' name='submit'>Save</button>
                        <input id='cancelTopResearchOutcomes' type='submit' value='Cancel' name='submit' />";
    }
    
    function showEditTechnologyEvaluationAdoption($project, $visibility){
        if(!$visibility['isLead']){
            return;
        }
        $technology = $project->getTechnology();
        $options = array("No",
                         "Yes, only evaluated",
                         "Yes, only adopted",
                         "Yes, both evaluated and adopted");
        $blankSelected = ($technology['response1'] == "") ? "selected='selected'" : "";
        $response1 = "<select name='response1'>
                        <option value='' $blankSelected>---</option>";
                        foreach($options as $option){
                            $selected = @($technology['response1'] == $option) ? "selected='selected'" : "";
                            $response1 .= "<option value='$option' $selected>$option</option>";
                        }
        $response1 .= "</select>";
        $options = array("Level 1",
                         "Level 2",
                         "Level 3",
                         "Level 4",
                         "Level 5",
                         "Level 6",
                         "Level 7",
                         "Level 8",
                         "Level 9");
        $blankSelected = ($technology['response3'] == "") ? "selected='selected'" : "";
        $response3 = "<select name='response3'>
                        <option value='' $blankSelected>---</option>";
                        foreach($options as $option){
                            $selected = @($technology['response3'] == $option) ? "selected='selected'" : "";
                            $response3 .= "<option value='$option' $selected>$option</option>";
                        }
        $response3 .= "</select>";
        $this->html .= "<h2>Technology Evaluation/Adoption</h2>
                        <b>Have your research group evaluated and/or adopted any new technology since the beginning of the project?</b><br />
                        {$response1}
                        <div id='tech_yes' style='display:none;'>
                            <br />
                            <b>Please provide the name of the technology your research group has evaluated/adopted:</b><br />
                            <input type='text' name='response2' value='{$technology['response2']}' size='50' />
                            <br /><br />
                            <b>Based on the definitions provided by Innovation Canada in the link below, please indicate the Technology Readiness Level (TRL) of the technology your research group has evaluated/adopted:</b><br />
                            {$response3}<br />
                            <small>Innovation Canada info: <a target='_blank' href='https://www.ic.gc.ca/eic/site/080.nsf/eng/00002.html'>https://www.ic.gc.ca/eic/site/080.nsf/eng/00002.html</a></small>
                        </div>
                        <script type='text/javascript'>
                            $('[name=response1]').change(function(){
                                if($(this).val() != 'No' && $(this).val() != ''){
                                    $('#tech_yes').show();
                                }
                                else{
                                    $('#tech_yes').hide();
                                }
                            });
                            $('[name=response1]').change();
                        </script>";
        $this->html .= "<br /><button id='editTechnologyEvaluationAdoption' type='submit' value='Save Dashboard' name='submit'>Save</button>
                        <input id='cancelTechnologyEvaluationAdoption' type='submit' value='Cancel' name='submit' />";
    }
    
    function showTopProducts($project, $visibility){
        global $config;
        $products = $project->getTopProducts();
        if(!$visibility['isLead'] && count($products) == 0){
            return;
        }
        $this->html .= "<h2>Top Research Outcomes</h2>";
        $date = date('M j, Y', strtotime($project->getTopProductsLastUpdated()));
        if(count($products) > 0){
            $this->html .= "<table class='dashboard' cellspacing='1' cellpadding='3' rules='all' frame='box' style='max-width: 800px;'>
                                <tr>
                                    <td align='center'><b>Year</b></td>
                                    <td align='center'><b>Category</b></td>
                                    <td align='center'><b>".$config->getValue('productsValue')."</b></td>
                                </th>";
            foreach($products as $product){
                if($product instanceof Contribution){
                    $year = $product->getStartYear();
                    $category = "Contribution";
                    $citation = "<a href='{$product->getUrl()}'>{$product->getTitle()}</a>";
                }
                else{
                    $year = substr($product->getDate(), 0, 4);
                    $category = $product->getCategory();
                    $citation = $product->getCitation();
                }
                if($year == "0000"){
                    $year = "";
                }
                if($year == YEAR){
                    $year = "<b><u>$year</u></b>";
                }
                $this->html .= "<tr>
                                    <td align='center'>{$year}</td>
                                    <td>{$category}</td>
                                    <td>{$citation}</td>
                                </tr>";
            }
            $this->html .= "</table><i>Last updated on: $date</i><br />";
        }
        else{
            $this->html .= "You have not entered any <i>Top Research Outcomes</i> yet<br />";
        }
        if($this->canEdit()){
            $this->html .= "<button id='editTopResearchOutcomes' type='submit' value='Edit Dashboard' name='submit'>Edit Top Research Outcomes</button>";
        }
    }
    
    function showTechnologyEvaluationAdoption($project, $visibility){
        if(!$visibility['isLead']){
            return;
        }
        $technology = $project->getTechnology();
        $this->html .= "<h2>Technology Evaluation/Adoption</h2>
                        <b>Have your research group evaluated and/or adopted any new technology since the beginning of the project?</b><br />
                        {$technology['response1']}";
        if($technology['response1'] != "" && $technology['response1'] != "No"){
            $this->html .= "
                        <br /><br />
                        <b>Please provide the name of the technology your research group has evaluated/adopted:</b><br />
                        {$technology['response2']}
                        <br /><br />
                        <b>Based on the definitions provided by Innovation Canada in the link below, please indicate the Technology Readiness Level (TRL) of the technology your research group has evaluated/adopted:</b><br />
                        {$technology['response3']}";
        }
        if($this->canEdit()){
            $this->html .= "<br /><br /><button id='editTechnologyEvaluationAdoption' type='submit' value='Edit Dashboard' name='submit'>Edit Technology</button>";
        }
    }
    
    function showDashboard($project, $visibility){
        global $wgOut, $config;
        $me = Person::newFromWgUser();
        $html = "";
        if($me->isLoggedIn()){
            $html .= "<h2>Dashboard</h2>";
            $html .= "<div id='dashboardAccordion'>";
            $html .= "<h3><a href='#'>Overall</a></h3>";
            $html .= "<div style='overflow: auto;'>";
            $dashboard = new DashboardTable(PROJECT_PUBLIC_STRUCTURE, $project);
            if(!$visibility['isLead']){
                $dashboard->filterCols(HEAD, array('Contributions'));
            }
            $html .= $dashboard->render(false, false);
            $html .= "</div>";
            $startYear = YEAR;
            if($project->deleted){
                $startYear = substr($project->getDeleted(), 0, 4)-1;
            }
            $phaseDates = $config->getValue("projectPhaseDates");
            for($i=$startYear; $i >= max(substr($phaseDates[1], 0, 4), substr($project->getCreated(), 0, 4)) - 1; $i--){
                $html .= "<h3><a href='#'>$i/".substr($i+1,2,2)."</a></h3>";
                $html .= "<div style='overflow: auto;'>";
                $dashboard = new DashboardTable(PROJECT_PUBLIC_STRUCTURE, $project, "$i-04-01", ($i+1)."-03-31");
                if(!$visibility['isLead']){
                    $dashboard->filterCols(HEAD, array('Contributions'));
                }
                $html .= $dashboard->render(false, false);
                $html .= "</div>";
            }
            $html .="</div>";
            $html .= "<script type='text/javascript'>
                $('#dashboardAccordion').accordion({autoHeight: false,
                                                    collapsible: true});
            </script>";
        }
        return $html;
    }

}    
    
?>
