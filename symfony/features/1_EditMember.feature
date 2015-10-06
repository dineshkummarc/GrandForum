Feature: EditMember
    In order to edit user's roles/projects
    As a User I need to be able to request role/project changes
    As an Admin I need to be able to accept role/project changes

    Scenario: Staff Making HQP a candidate
        Given I am logged in as "Staff.User1" using password "Staff.Pass1"
        When I follow "Edit Roles"
        And I select "HQP User4" from "names"
        And I press "Next"
        And I follow "SubRolesTab"
        And I check "candidate"
        And I press "Submit Request"
        Then I should see "is now a candidate user"
        And "hqp.user4@behat-test.com" should not be subscribed to "test-hqps"
        
    Scenario: Staff Making Candidate-HQP a full HQP
        Given I am logged in as "Staff.User1" using password "Staff.Pass1"
        When I follow "Edit Roles"
        And I select "HQP User4" from "names"
        And I press "Next"
        And I follow "SubRolesTab"
        And I uncheck "candidate"
        And I press "Submit Request"
        Then I should see "is now a full user"
        And "hqp.user4@behat-test.com" should be subscribed to "test-hqps"
        
    Scenario: NI trying to Edit Sub-Roles (should not be able to)
        Given I am logged in as "NI.User1" using password "NI.Pass1"
        When I follow "Edit Roles"
        And I select "HQP User1" from "names"
        And I press "Next"
        Then I should not see "Sub-Roles" 

    Scenario: Two different requests happening at the same time, and then accepted
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "Edit Roles"
        And I select "NI User1" from "names"
        And I press "Next"
        And I click by css "#ProjectsTab"
        And I check "p_wpNS_Phase2Project3"
        And I check "p_wpNS_Phase2Project4"
        And I press "Submit Request"
        And I follow "Edit Roles"
        And I select "NI User1" from "names"
        And I press "Next"
        And I click by css "#ProjectsTab"
        And I check "p_wpNS_Phase2Project5"
        And I press "Submit Request"
        And I follow "status_notifications"
        And I follow "User Role Request"
        And I press "Accept"
        Then I should see "added to Phase2Project5"
        And I should not see "removed"
        When I follow "status_notifications"
        And I follow "User Role Request"
        And I press "Accept"
        Then I should see "added to Phase2Project3"
        And I should see "added to Phase2Project4"
        And I should not see "removed"
        
    Scenario: NI Removed from 3 projects but in two separate requests, and then accepted
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "Edit Roles"
        And I select "NI User1" from "names"
        And I press "Next"
        And I click by css "#ProjectsTab"
        And I uncheck "p_wpNS_Phase2Project3"
        And I uncheck "p_wpNS_Phase2Project4"
        And I press "Submit Request"
        And I follow "Edit Roles"
        And I select "NI User1" from "names"
        And I press "Next"
        And I click by css "#ProjectsTab"
        And I uncheck "p_wpNS_Phase2Project5"
        And I press "Submit Request"
        And I follow "status_notifications"
        And I follow "User Role Request"
        And I press "Accept"
        Then I should see "removed from Phase2Project5"
        And I should not see "added"
        When I follow "status_notifications"
        And I follow "User Role Request"
        And I press "Accept"
        Then I should see "removed from Phase2Project3"
        And I should see "removed from Phase2Project4"
        And I should not see "added"

    Scenario: NI Editing HQP's projects
        Given I am logged in as "NI.User1" using password "NI.Pass1"
        When I follow "Edit Roles"
        And I select "HQP User1" from "names"
        And I press "Next"
        And I follow "ProjectsTab"
        And I check "p_wpNS_Phase2Project2"
        And I press "Submit Request"
        Then I should see "+Phase2Project2"
        
    Scenario: NI Inactivating HQP
        Given I am logged in as "NI.User1" using password "NI.Pass1"
        When I follow "Edit Roles"
        And I select "HQP ToBeInactivated" from "names"
        And I press "Next"
        And I uncheck "role_HQP"
        And I press "Submit Request"
        Then I should see "The user HQP ToBeInactivated has been requested to have the following role changes:"
        And I should see "-HQP"
        
    Scenario: Admin Accepting request
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "status_notifications"
        And I follow "User Role Request"
        And I press "Accept"
        Then I should see "removed from HQP"
        And I press "Accept"
        Then I should see "added to Phase2Project2"
        
    Scenario: Admin Adding PL (Make sure PL is also added to project, and subscribed to mailing list)
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "Edit Roles"
        And I select "NI User3" from "names"
        And I press "Next"
        And I follow "LeadershipTab"
        And I check "pl_Phase2Project5"
        And I press "Submit Request"
        Then I should see "is now a project leader of Phase2Project5"
        When I go to "index.php/Phase2Project5:Main"
        Then I should see "User3, NI"
        When I go to "index.php/NI:NI.User3?tab=projects"
        Then I should see "Phase2Project5"
        And "ni.user3@behat-test.com" should be subscribed to "test-leaders"
        
    Scenario: Admin Removing PL (Make sure that PL is also removed from the mailing list)
        Given I am logged in as "Admin.User1" using password "Admin.Pass1"
        When I follow "Edit Roles"
        And I select "NI User3" from "names"
        And I press "Next"
        And I follow "LeadershipTab"
        And I uncheck "pl_Phase2Project5"
        And I press "Submit Request"
        Then I should see "is no longer a project leader of Phase2Project5"
        And "ni.user3@behat-test.com" should not be subscribed to "test-leaders"
        
    Scenario: PL Editing RMC project members (Should see RMC who are also NI, but not people who are only RMC)
        Given I am logged in as "PL.User1" using password "PL.Pass1"
        When I follow "Edit Roles"
        Then I should see "RMC User1"
        But I should not see "RMC User2"
        When I select "RMC User1" from "names"
        And I press "Next"
        Then I should see "Phase2Project1"
        
    Scenario: PL Editing Champ project members
        Given I am logged in as "PL.User1" using password "PL.Pass1"
        When I follow "Edit Roles"
        Then I should see "Champ User1"
