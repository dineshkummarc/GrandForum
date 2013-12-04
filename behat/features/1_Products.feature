Feature: Products

    Scenario: Adding a new Publication
        Given I am logged in as "PNI.User1" using password "PNI.Pass1"
        When I follow "Add/Edit Publication"
        And I click by css "#addpublication"
        And I fill in "title" with "New Publication"
        And I press "Create"
        And I select "PNI User1" from "rightauthors"
        And I press "moveLeftauthors"
        And I fill in "description" with "This is a description"
        And I select "Proceedings Paper" from "type"
        And I select "January" from "month"
        And I select "4" from "day"
        And I select "2013" from "year"
        And I check "projects_Phase2Project1"
        And I press "Create Publication"
        Then I should see "New Publication"
        And I should see "Jan 4, 2013"
        And I should see "Phase2Project1"
