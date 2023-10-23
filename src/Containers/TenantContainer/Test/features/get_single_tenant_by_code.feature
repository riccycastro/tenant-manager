Feature: Get a single tenant by code
  As a user
  I want to be able to get a single tenant by its code

  Scenario:
    Given I am authenticated as system user
    And I have a tenant with:
      | field        | value           |
      | name         | my homespot     |
      | code         | my-homespot     |
      | domain email | @myhomespot.com |
      | status       | ready           |
      | is active    | true            |
    And I have a tenant with:
      | field        | value               |
      | name         | The Store           |
      | code         | thestore            |
      | domain email | @thestore.com       |
      | status       | ready_for_migration |
      | is active    | false               |
    When get the tenant with code "thestore"
    Then I should see the following tenant:
      | name        | code        | isActive | status              | domainEmail     |
      | The Store   | thestore    | false    | ready_for_migration | @thestore.com   |
