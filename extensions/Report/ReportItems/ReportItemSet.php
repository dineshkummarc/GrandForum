<?php

abstract class ReportItemSet extends AbstractReportItem{

    var $items;
    var $blobIndex;

    // Creates a new ReportItemSet
    function ReportItemSet(){
        $this->items = array();
        $this->blobIndex = "";
        $this->reportCallback = new ReportItemCallback($this);
    }
    
    // Creates an empty tuple, with all values of this object
    function createTuple(){
        $tuple = array('milestone_id' => $this->milestoneId,
                       'project_id' => $this->projectId,
                       'person_id' => $this->personId,
                       'product_id' => $this->productId);
        return $tuple;
    }
    
    // This function must return an array of arrays in the form of array(array('project_id','person_id','milestone_id'))
    abstract function getData();

    function getItems(){
        return $this->items;
    }
    
    function getLimit(){
        $limit = 0;
        foreach($this->items as $item){
            if($item instanceof ReportItemSet){
                if($item->getLimit() > 0){
                    $limit += $item->getLimit();
                }
            }
            else if ($item instanceof TextareaReportItem){
                if($item->getLimit() > 0){
                    $limit += $item->getLimit();
                }
            }
        }
        return $limit;
    }
    
    function getNChars(){
        $nChars = 0;
        foreach($this->items as $item){
            if($item instanceof ReportItemSet){
                if($item->getLimit() > 0){
                    $nChars += $item->getNChars();
                }
            }
            else if ($item instanceof TextareaReportItem){
                if($item->getLimit() > 0){
                    $nChars += $item->getNChars();
                }
            }
        }
        return $nChars;
    }
    
    function getActualNChars(){
        $nChars = 0;
        foreach($this->items as $item){
            if($item instanceof ReportItemSet){
                if($item->getLimit() > 0){
                    $nChars += $item->getActualNChars();
                }
            }
            else if ($item instanceof TextareaReportItem){
                if($item->getLimit() > 0){
                    $nChars += $item->getActualNChars();
                }
            }
        }
        return $nChars;
    }
    
    function getExceedingFields(){
        $nFields = 0;
        foreach($this->items as $item){
            if($item instanceof ReportItemSet){
                if($item->getLimit() > 0){
                    $nFields += $item->getExceedingFields();
                }
            }
            else if ($item instanceof TextareaReportItem){
                if($item->getLimit() > 0){
                    if($item->getActualNChars() > $item->getLimit()){
                        $nFields++;
                    }
                }
            }
        }
        return $nFields;
    }
    
    function getEmptyFields(){
        $nFields = 0;
        foreach($this->items as $item){
            if($item instanceof ReportItemSet){
                if($item->getLimit() > 0){
                    $nFields += $item->getEmptyFields();
                }
            }
            else if ($item instanceof TextareaReportItem){
                if($item->getLimit() > 0){
                    if($item->getActualNChars() == 0){
                        $nFields++;
                    }
                }
            }
        }
        return $nFields;
    }
    
    function setBlobIndex($blobIndex){
        $this->blobIndex = $blobIndex;
    }

    function addReportItem($item){
        $item->setParent($this);
        $this->items[] = $item;
        $item->setPersonId($this->personId);
    }
    
    function save(){
        $errors = array();
        foreach($this->items as $item){
            $errors = array_merge($errors, $item->save());
        }
        return $errors;
    }
    
    function getBlobValue(){
        $values = array();
        foreach($this->items as $item){
            $id = $item->id;
            $secondId = "{$item->personId}_{$item->projectId}_{$item->milestoneId}";
            if($item->id == ""){
                $id = "none";
            }
            $values[$this->id][$secondId][$id] = $item->getBlobValue();
        }
        return $values;
    }
    
    function setBlobValue($values){
        foreach($this->items as $item){
            $id = $item->id;
            $secondId = "{$item->personId}_{$item->projectId}_{$item->milestoneId}";
            if($item->id == ""){
                $id = "none";
            }
            if(isset($values[$this->id][$secondId][$id])){
                $item->setBlobValue($values[$this->id][$secondId][$id]);
            }
        }
    }
    
    // Returns the number of completed values (usually 1, or 0)
    function getNComplete(){
        $nComplete = 0;
        foreach($this->items as $item){
            $nComplete += $item->getNComplete();
        }
        return $nComplete;
    }
    
    // Returns the number of fields which are associated with this AbstractReportItem (usually 1)
    function getNFields(){
        $nFields = 0;
        foreach($this->items as $item){
            $nFields += $item->getNFields();
        }
        return $nFields;
    }
    
    function getNTextareas(){
        $nTextareas = 0;
        foreach($this->items as $item){
            if($item instanceof ReportItemSet){
                $nTextareas += $item->getNTextareas();
            }
            else if($item instanceof TextareaReportItem){
                $nTextareas += 1;
            }
        }
        return $nTextareas;
    }

    function render(){
        foreach($this->items as $item){
            $item->render();
        }
    }
    
    function renderForPDF(){
        foreach($this->items as $item){
            $item->renderForPDF();
        }
    }
}

?>
