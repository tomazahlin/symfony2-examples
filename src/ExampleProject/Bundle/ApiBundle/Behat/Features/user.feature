@user
Feature: Usage of the user section of the API

  @get @guest @error
  Scenario: Cannot get any user when not authenticated
    Given I am not authenticated
    When I send a GET request to "/users/1"
    Then the response code should be 401

  @get @auth @success
  Scenario: Users can get any profile when authenticated
    Given I am authenticated
    When I send a GET request to "/users/1"
    Then the response code should be 200
    And the response should be json entity
    And the entity should contain "id, username, email"

  @get @auth @error
  Scenario: Try to get unexisting user
    Given I am authenticated
    When I send a GET request to "/users/0"
    Then the response code should be 404

  @patch @auth @validation @success
  Scenario Outline: Users can update their own data
    Given I am authenticated as user with id 1
    When I send a PATCH request to "/users/1" with body:
      """
      {"id":1, "<key>":<value>}
      """
    Then the response code should be 200
    And the response should be json entity

    Examples:
      | key        | value              |
      |  username  |  "rubye76.unused"  |
      |  username  |  "rubye76"         |

  @patch @auth @error
  Scenario Outline: Users cannot update data of other users
    Given I am authenticated as user with id 1
    When I send a PATCH request to "/users/2" with body:
      """
      {"id":1, "<key>":<value>}
      """
    Then the response code should be 401

    Examples:
      | key        | value            |
      |  username  |  "carole.ferry"  |
