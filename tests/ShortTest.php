<?php

use PHPUnit\Framework\TestCase;
use Gravity\Data_checker;

class ShortTest extends \PHPUnit\Framework\TestCase
{
    private array $control;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->control = [
            "creation_date" => [
                "required",
                "date",
                "greater_than" => "2016-01-01",
                "error_message" => "the creation date isn't date or be lower than '2016-01-01'"
            ],
            "first_name" => [
                "required",
                "string",
                "min_length" => 4,
                "error_message" => "the firstname isn't a word or be lower than 4 characters"
            ],
            "last_name" => [
                "required",
                "string",
                "min_length" => 1,
                "error_message" => "the name isn't a word or be lower than 1 characters"
            ],
            "id" => [
                "required",
                "int",
                "error_message" => "the id doesn't exist or not an integer"
            ],
            "ip" => [
                "required",
                "string",
                "ip_address",
                "error_message" => "l'ip n'est pas présente mais elle devrait l'être"
            ],
            "email" => [
                "required",
                "email",
                "disposable_email"
            ],
            "captcha" => [
                "required",
                "string",
                "alphanumeric",
                "min_length" => 5,
                "max_length" => 5
            ],
            "test" => [
                "required",
                "string",
                "alias" => "Alias_test"
            ],
            "password" => [
                "required",
                "min_length" => 8,
                "contains_number",
                "contains_lower",
                "contains_upper",
                "contains_special_character"
            ]
        ];
    }

    public function testValidationSuccess()
    {
        $data = [
            "creation_date" => "2017-01-01",
            "first_name" => "John",
            "last_name" => "Paul",
            "id" => 24,
            "ip" => "192.25.14.8",
            "email" => "tata@test.com",
            "test" => "random string",
            "captcha" => "25EC5",
            "password" => "azert_@Dy123"
        ];


        $checker = new Data_checker();
        $result = $checker->verify($data, $this->control);
        $this->assertTrue($result);
    }

    public function testValidationFailure()
    {
        $data = [
            "creation_date" => "2015-01-01", // Invalid date
            "first_name" => "Jo", // Too short
            "last_name" => "", // Missing
            "id" => "abc", // Not an integer
            "email" => "invalid_email", // Not a valid email
            "test" => "random string",
            "captcha" => "123", // Too short
            "password" => "short" // Too short and missing required characters
        ];

        $checker = new Data_checker();
        $result = $checker->verify($data, $this->control);
        // Assuming the result is an array of error messages
        $this->assertIsArray($result);

        $expectedMessages = [
            "the creation date isn't date or be lower than '2016-01-01'",
            "the firstname isn't a word or be lower than 4 characters",
            "the name isn't a word or be lower than 1 characters",
            "l'ip n'est pas présente mais elle devrait l'être",
            "Doesn't match the control test EMAIL as excepted",
            "Doesn't match the control test MIN_LENGTH as excepted",
            "Doesn't match the control test MIN_LENGTH as excepted",
            "Doesn't match the control test MIN_LENGTH as excepted",
            "Doesn't match the control test CONTAINS_NUMBER as excepted",
            "Doesn't match the control test CONTAINS_UPPER as excepted",
            "Doesn't match the control test CONTAINS_SPECIAL_CHARACTER as excepted"
        ];

        $actualMessages = array_map(function($error) {
            return $error['error_message'];
        }, $result);

        foreach ($expectedMessages as $expectedMessage) {
            $this->assertContains($expectedMessage, $actualMessages);
        }
    }
}