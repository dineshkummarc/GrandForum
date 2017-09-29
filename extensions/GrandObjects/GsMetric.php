<?php 

/**
 *@package GrandObjects
 */
class GsMetric {

    var $id;
    var $user_id;
    var $start_date;
    var $citation_count;
    var $hindex_5_years;
    var $i10_index_5_years;
    var $hindex;
    var $i10_index;
    var $change_date;
    var $gs_citations = array();

    // constructor
    function GsMetric($data){
        if(count($data)>0){
            $this->id = $data[0]['id'];
            $this->user_id = $data[0]['user_id'];
            $this->start_date = $data[0]['start_date'];
            $this->citation_count = $data[0]['citation_count'];
            $this->hindex_5_years = $data[0]['hindex_5_years'];
            $this->i10_index_5_years = $data[0]['i10_index_5_years'];
            $this->hindex = $data[0]['hindex'];
            $this->i10_index = $data[0]['i10_index'];
            $this->change_date = $data[0]['change_date'];
            $this->gs_citations = $this->getGsCitations();
        }
     }

    /**
     * Returns a new Metric from the given id
     * @param integer $id The id of the metric
     * @return Metric The Metric with the given id. If no 
     * metric exists with that id, it will return an empty metric.
     */
    static function newFromId($id){
        $sql = "SELECT *
                FROM grand_user_gsmetrics
                WHERE `id` = '$id'";
        $data = DBFunctions::execSQL($sql);
        $metric = new GsMetric($data);
        return $metric;
    }

    /**
     * Returns an array of all the recent metrics in the database
     * return Array of metric objects
     */
    static function getAllGsMetrics(){
        $sql = "SELECT v1.id
                FROM grand_user_gsmetrics v1
                WHERE v1.change_date >= (SELECT MAX(v2.change_date)
                                         FROM grand_user_gsmetrics v2
                                         WHERE v1.user_id = v2.user_id)";
        $data = DBFunctions::execSQL($sql);
        $metrics = array();
        foreach($data as $row){
            $metrics[] = GsMetric::newFromId($row['id']);
        }
        return $metrics;
    }

        /**
         * Returns the user's most recent metric in the database
         * return Metric The most recent Metric of the user.
        */      
        static function getUserMetric($id){
            $sql = "SELECT v1.id
                    FROM grand_user_gsmetrics v1
                    WHERE v1.user_id = $id 
                    AND v1.change_date >= (SELECT MAX(v2.change_date)
                                           FROM grand_user_gsmetrics v2
                                           WHERE v1.user_id = v2.user_id)";
            $data = DBFunctions::execSQL($sql);
            $metric = "";
            if(count($data)>0){
                $metric = GsMetric::newFromId($data[0]['id']);
            }
            return $metric;
        }

    function getRecentCitationCount(){
        $citationCount = 0;
        $citationArray = $this->gs_citations;
        $Year = date("Y");
        $count = 0;
        while($count <= 5){
        if(isset($citationArray[$Year])){
            $citationCount += $citationArray[$Year];
        }
        $count++;
            $Year--;
        } 
        return $citationCount;
    }

    /**
     * Returns an array of the users citations count from Google Scholar
     * return Array An array with a key-value pair of [year] => citation count
    */
    function getGsCitations(){
        if(count($this->gs_citations) == 0){
            $sql = "SELECT *
                    FROM grand_gs_citations
                    WHERE user_id = {$this->user_id}";
            $data = DBFunctions::execSQL($sql);
            $newArray = array();
            foreach($data as $row){
                $date = explode('-',$row['year']);
                $year = $date[0];
                $newArray[$year] = $row['count'];
            }
            $this->gs_citations = $newArray;
        }
        return $this->gs_citations;
    }

    /**
     * Returns True if the GsMetric is saved correctly
     * @return boolean True if database accepted new GsMetric
    */
    function create(){
        $me = Person::newFromWGUser();
        if($me->isLoggedIn()){
            DBFunctions::begin();
            $status = DBFunctions::insert('grand_user_gsmetrics',
                                          array('user_id' => $this->user_id,
                                                'start_date' => $this->start_date,
                                                'citation_count' => $this->citation_count,
                                                'hindex_5_years' => $this->hindex_5_years,
                                                'i10_index_5_years' => $this->i10_index_5_years,
                                                'hindex' => $this->hindex,
                                                'i10_index' => $this->i10_index),
                                          true);
            if($status){
                $status = DBFunctions::delete('grand_gs_citations',
                            array('user_id' => $this->user_id),
                            true);

                while($status && list($key, $val) = each($this->gs_citations)){
                    $date = "$key-01-01 00:00:00";
                    $status = DBFunctions::insert('grand_gs_citations',
                                      array('user_id' => $this->user_id,
                                           'year' => $date,
                                           'count' => $val),
                                      true);
                    if(!$status){
                        return false;
                    }
                }
                DBFunctions::commit();
                return $status;
            }
        }
        return false;
    }
}
?>
