Feature: Create new tenant
  As a user
  I want to be able to list tenants

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
    When I list the tenants
    Then I should see the following entries on the tenant list:
      | name        | code        | isActive | status              | domainEmail     |
      | my homespot | my-homespot | true     | ready               | @myhomespot.com |
      | The Store   | thestore    | false    | ready_for_migration | @thestore.com   |
