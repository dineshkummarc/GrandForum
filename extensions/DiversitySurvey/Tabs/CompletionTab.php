<?php

class CompletionTab extends AbstractTab {

    function CompletionTab(){
        parent::AbstractTab("Completion");
    }

    function generateBody(){
        $people = Person::getAllPeople();
        $this->html  = "<table id='peopleTable' class='wikitable' frame='box' rules='all' style='width:100%'>";
        $this->html .= "<thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Institution</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Completed?</th>
                                <th>Decline?</th>
                                <th>Q1</th>
                                <th>Q2</th>
                                <th>Q3</th>
                                <th>Q4</th>
                                <th>Q5</th>
                                <th>Q6</th>
                                <th>Q7</th>
                                <th>Q8</th>
                                <th>Q9</th>
                                <th>Q10</th>
                            </tr>
                         </thead>
                         <tbody>";
        foreach($people as $person){
            if(DiversitySurvey::isEligible($person)){
                $diversity = Diversity::newFromUserId($person->getId());
                if($diversity->canView()){
                    $complete = ($diversity->isComplete()) ? "Yes" : "No";
                
                    // Roles
                    $roles = array();;
                    foreach($person->getRoles() as $role){
                        $roles[] = $role->getRole();
                    }
                    $roles = array_unique($roles);
                
                    // Race
                    $race = implode(", ", $diversity->getRaces());
                    
                    // Gender
                    $gender = implode(", ", $diversity->getGenders());
                    
                    // Orientation
                    $orientation = implode(", ", $diversity->getOrientations());
                    
                    $decline = ($diversity->decline == 0) ? "No" : "Yes";
                    
                    $this->html .= "<tr>
                                        <td>{$person->getNameForForms()}</td>
                                        <td>{$person->getEmail()}</td>
                                        <td>".implode(", ", $roles)."</td>
                                        <td>{$person->getUni()}</td>
                                        <td>{$person->getDepartment()}</td>
                                        <td>{$person->getPosition()}</td>
                                        <td>{$complete}</td>
                                        <td>
                                            <div style='min-width:200px;max-height: 200px; overflow-y: auto;'>
                                                {$decline}<br />
                                                {$diversity->reason}
                                            </div>
                                        </td>
                                        <td>{$diversity->birth}</td>
                                        <td>{$diversity->indigenous}</td>
                                        <td>
                                            {$diversity->disability}<br />
                                            {$diversity->disabilityVisibility}
                                        </td>
                                        <td>{$diversity->minority}</td>
                                        <td>{$race}</td>
                                        <td>{$diversity->racialized}</td>
                                        <td>{$diversity->immigration}</td>
                                        <td>{$gender}</td>
                                        <td>{$orientation}</td>
                                        <td><div style='min-width:200px;max-height: 200px; overflow-y: auto;'>{$diversity->comments}</div></td>
                                     </tr>";
                }
            }
        }
        $this->html .= "</tbody>
                        </table>";
        $this->html .= "<script type='text/javascript'>
            $('#peopleTable').dataTable({
                'aLengthMenu': [[-1], ['All']],
                'iDisplayLength': -1,
                'dom': 'Blfrtip',
                'buttons': [
                    'excel', 'pdf'
                ]
            });
        </script>";
    }
}
?>
