@mod @mod_wiki @core_completion
Feature: View activity completion information in the Wiki activity
  In order to have visibility of wiki completion requirements
  As a student
  I need to be able to view my wiki completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity       | wiki          |
      | course         | C1            |
      | idnumber       | mh1           |
      | name           | Music history |
      | completion     | 2             |
      | completionview | 1             |
    And I am on the "Music history" "wiki activity" page logged in as teacher1
    And I click on "Create page" "button"

  Scenario: View automatic wiki completion conditions as a teacher and confirm all tabs display conditions
    When I am on the "Music history" "wiki activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And I select "Edit" from the "jump" singleselect
    And "Music history" should have the "View" completion condition
    And I select "Comments" from the "jump" singleselect
    And "Music history" should have the "View" completion condition
    And I select "Map" from the "jump" singleselect
    And "Music history" should have the "View" completion condition
    And I select "Files" from the "jump" singleselect
    And "Music history" should have the "View" completion condition
    And I select "Administration" from the "jump" singleselect
    And "Music history" should have the "View" completion condition

  Scenario: A students can complete a wiki activity by viewing it
    When I am on the "Music history" "wiki activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: A student can manually mark the wiki activity as done but a teacher cannot
    Given I am on the "Music history" "wiki activity" page logged in as teacher1
    And I am on the "Music history" "wiki activity editing" page
    And I expand all fieldsets
    And I press "Unlock completion settings"
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    # Student view.
    When I am on the "Music history" "wiki activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
