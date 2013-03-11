<?php

class JungAPI extends API{

    var $year = 2012;
    var $startDate = REPORTING_END;
    var $endDate = REPORTING_END;

    function JungAPI(){
        $this->addGET("nodeType", true, "", "all");
        $this->addGET("edgeType", true, "", "all");
        $this->addGET("year", true, "", "2012");
	}

    function processParams($params){
        $_GET['nodeType'] = mysql_real_escape_string($_GET['nodeType']);
        $_GET['edgeType'] = mysql_real_escape_string($_GET['edgeType']);
        $_GET['year'] = mysql_real_escape_string($_GET['year']);
    }

	function doAction(){
	    header("Content-type: application/json");
	    $this->outputJSON();
		exit;
	}
	
	function outputJSON(){
        $json = array();
        $nodeType = $_GET['nodeType'];
        $edgeType = $_GET['edgeType'];
        $this->year = $_GET['year'];
        $this->startDate = $_GET['year'].REPORTING_CYCLE_END_MONTH;
        $this->endDate = $_GET['year'].REPORTING_CYCLE_END_MONTH;
        
        $nodes = array();
        $edges = array();
        $metas = array();
        switch($nodeType){
            case 'all':
                $pnis = Person::getAllPeopleDuring(PNI, $this->startDate, $this->endDate);
                $cnis = Person::getAllPeopleDuring(CNI, $this->startDate, $this->endDate);
                $hqps = Person::getAllPeopleDuring(HQP, $this->startDate, $this->endDate);
                $tmpNodes = array_merge($pnis, $cnis, $hqps);
                $nodes = $pnis;
                foreach($tmpNodes as $p){
                    $found = false;
                    foreach($nodes as $node){
                        if($node->getId() == $p->getId()) $found = true;
                    }
                    if(!$found) $nodes[] = $p;
                }
                break;
            case 'pni':
                $nodes = Person::getAllPeopleDuring(PNI, $this->startDate, $this->endDate);
                break;
            case 'cni':
                $nodes = Person::getAllPeopleDuring(CNI, $this->startDate, $this->endDate);
                break;
            case 'hqp':
                $nodes = Person::getAllPeopleDuring(HQP, $this->startDate, $this->endDate);
                break;
        }
        foreach($nodes as $key => $node){
            $projects = $node->getProjectsDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
            if(count($projects) == 0){
                unset($nodes[$key]);
            }
        }
        switch($edgeType){
            case 'all':
                $edges = array_merge($this->getWorksWithEdges($nodes),
                                     $this->getCoProduceEdges($nodes),
                                     $this->getCoSuperviseEdges($nodes));
                break;
            case 'coproduce':
                $edges = $this->getCoProduceEdges($nodes);
                break;
            case 'cosup':
                $edges = $this->getCoSuperviseEdges($nodes);
                break;
            case 'workswith':
                $edges = $this->getWorksWithEdges($nodes);
                break;
        }
        
        $metas = $this->getMetas($nodes, $edges);
        
        $json['nodes'] = array();
        foreach($nodes as $node){
            $json['nodes'][] = $node->getName();
        }
        $json['edges'] = $edges;
        $json['metas'] = $metas;
        echo json_encode($json);
        exit;
	}
	
	function getMetas($nodes, $edges){
	    $metas = array();
	    foreach($nodes as $person){
	        $tuple = array();
	        
	        $projects = $person->getProjectsDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	        $projectsAlready = array();
	        foreach($projects as $p){
	            if(!isset($projectsAlready[$p->getId()])){
	                $projectsAlready[$p->getId()] = true;
	            }
	        }
	        $products = $person->getPapersAuthored('all', "2010".REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH, true);
	        $projectsByProduct = array();
	        $nProductsWith1University = array();
	        $nProductsWith2Universities = array();
	        $nProductsWith3Universities = array();
	        $nProductsWith4OrMoreUniversities = array();
	        foreach($products as $product){
	            $pProjects = $product->getProjects();
	            $universities = array();
	            foreach($pProjects as $proj){
	                $projectsByProduct[$proj->getName()] = true;
	            }
	            foreach($product->getAuthors() as $author){
	                $uni = $author->getUniversityDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	                $universities[$uni['university']] = true;
	            }
	            if(count($universities) == 1){
	                $nProductsWith1University[$product->getId()] = true;
	            }
	            if(count($universities) == 2){
	                $nProductsWith2Universities[$product->getId()] = true;
	            }
	            if(count($universities) == 3){
	                $nProductsWith3Universities[$product->getId()] = true;
	            }
	            if(count($universities) >= 4){
	                $nProductsWith4OrMoreUniversities[$product->getId()] = true;
	            }
	        }
            
	        $tuple['nProjects'] = (string)count($projectsAlready);
	        $tuple['nProjectsByProduct'] = (string)count($projectsByProduct);
	        $tuple['nProductsWith1University'] = (string)count($nProductsWith1University);
	        $tuple['nProductsWith2Universities'] = (string)count($nProductsWith2Universities);
	        $tuple['nProductsWith3Universities'] = (string)count($nProductsWith3Universities);
	        $tuple['nProductsWith4OrMoreUniversities'] = (string)count($nProductsWith4OrMoreUniversities);

	        if($person->isRoleDuring(HQP, $this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH) &&
	           !$person->isRoleDuring(PNI, $this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH) &&
	           !$person->isRoleDuring(CNI, $this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH)){
	            $tuple['nHQP'] = "";
	            $tuple['nWorksWith'] = "";
	            $tuple['totalAllocationUpToNow'] = "";
	        }
	        else{
	            $worksWith = $person->getRelationsDuring(WORKS_WITH, $this->startDate, $this->endDate);
	            $hqps = $person->getHQPDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	            $totalHqps = $person->getHQP(true);
	            $budgets = array();
	            $totalAllocated = 0;
	            for($i=2010;$i<=$this->year;$i++){
	                $allocated = $person->getAllocatedBudget($i-1);
	                if($allocated != null){
	                    $value = $allocated->copy()->rasterize()->where(COL_TOTAL)->select(ROW_TOTAL)->toString();
	                    $totalAllocated += (int)str_replace(',', '', str_replace('$', '', $value));
	                }
	            }
	            $tuple['nCurrentHQP'] = (string)count($hqps);
	            $tuple['nTotalHQP'] = (string)count($totalHqps);
	            $tuple['nCurrentWorksWith'] = (string)count($worksWith);
	            $tuple['totalAllocationUpToNow'] = (string)$totalAllocated;
	        }
	        
	        $connectedDisciplines = array();
	        foreach($edges as $edge){
	            if($edge['a'] == $person->getName()){
	                $b = Person::newFromName($edge['b']);
	                $disc = $b->getDisciplineDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	                $connectedDisciplines[$disc] = true;
	            }
	            else if($edge['b'] == $person->getName()){
	                $a = Person::newFromName($edge['a']);
	                $disc = $a->getDisciplineDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	                $connectedDisciplines[$disc] = true;
	            }
	        }
	        
	        $tuple['nProductsUpToNow'] = (string)count($products);
	        $tuple['discipline'] = $person->getDisciplineDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	        $tuple['nConnectedDisciplines'] = count($connectedDisciplines);
	        
	        $metas[] = $tuple;
	    }
	    return $metas;
	}
	
	function getCoProduceEdges($nodes){
	    $edges = array();
	    $ids = array();
	    foreach($nodes as $node){
	        $ids[$node->getId()] = true;
	    }
	    foreach($nodes as $person){
	        $products = $person->getPapersAuthored('all', $this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH, true);
	        foreach($products as $product){
	            $authors = $product->getAuthors();
	            foreach($authors as $auth){
	                if(isset($ids[$auth->getId()]) && $person->getId() != $auth->getId()){
	                    $edges[] = array('a' => $person->getName(), 'b' => $auth->getName());
	                }
	            }
	        }
	    }
	    return $edges;
	}
	
	function getCoSuperviseEdges($nodes){
	    if($_GET['nodeType'] == 'hqp'){
	        return array();
	    }
	    $edges = array();
	    $ids = array();
	    foreach($nodes as $node){
	        $ids[$node->getId()] = true;
	    }
	    foreach($nodes as $person){
	        $hqps = $person->getHQPDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	        foreach($hqps as $hqp){
	            $sups = $hqp->getSupervisorsDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
	            foreach($sups as $sup){
	                if(isset($ids[$sup->getId()]) && $person->getId() != $sup->getId()){
	                    $edges[] = array('a' => $person->getName(), 'b' => $sup->getName());
	                }
	            }
	        }
	    }
	    return $edges;
	}
	
	function getWorksWithEdges($nodes){
	    $edges = array();
	    $ids = array();
	    foreach($nodes as $node){
	        $ids[$node->getId()] = true;
	    }
	    foreach($nodes as $person){
	        $relations = $person->getRelationsDuring(WORKS_WITH, $this->startDate, $this->endDate);
	        $alreadyDone = array();
	        foreach($relations as $relation){
	            if(isset($ids[$relation->getUser2()->getId()]) && 
	               !isset($alreadyDone[$relation->getUser2()->getId()]) &&
	               $person->getId() != $relation->getUser2()->getId()){
	                $edges[] = array('a' => $relation->getUser1()->getName(), 'b' => $relation->getUser2()->getName());
	                $alreadyDone[$relation->getUser2()->getId()] = true;
	            }
	        }
	    }
	    return $edges;
	}
	
	function isLoginRequired(){
		return false;
	}
}

?>
