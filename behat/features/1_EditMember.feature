Feature: EditMember

    Scenario: PNI Editing HQP's projects
        Given I am logged in as "PNI.User1" using password "PNI.Pass1"
        When I follow "Edit Member"
        And I select "HQP User1" from "names"
        And I press "Next"
        And I follow "ProjectsTab"
        And I check "p_wpNS_Phase2Project2"
        And I press "Submit Request"
        Then I should see "+Phase2Project2"
        
    Scenario: Admin Accepting request
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "lnk-notifications"
        And I follow "User Role Request"
        And I press "Accept"
        Then I should see "added to Phase2Project2"
        
    Scenario: Admin Adding PL (Make sure PL is also added to project)
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "Edit Member"
        And I select "PNI User3" from "names"
        And I press "Next"
        And I follow "LeadershipTab"
        And I check "pl_Phase2Project5"
        And I press "Submit Request"
        Then I should see "is now a project leader of Phase2Project5"
        When I go to "index.php/Phase2Project5:Main"
        Then I should see "Leader: User3, PNI"
        When I go to "index.php/PNI:PNI.User3?tab=projects"
        Then I should see "Phase2Project5"
        
    Scenario: PL Editing RMC project members (Should see RMC who are also PNI, but not people who are only RMC)
        Given I am logged in as "PL.User1" using password "PL.Pass1"
        When I follow "Edit Member"
        Then I should see "RMC User1"
        But I should not see "RMC User2"
        When I select "RMC User1" from "names"
        And I press "Next"
        Then I should see "Phase2Project1"
        But I should not see "PNI"
