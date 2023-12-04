Feature: Create tenant
  As a user
  I want to be able to create a new tenant

  Scenario:
    Given I am authenticated as system user
    When I create a new tenant with:
      | field        | value           |
      | name         | my homespot     |
      | code         | my-homespot     |
      | domain email | @myhomespot.com |
    Then I should see that it was created successfully
