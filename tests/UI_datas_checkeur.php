<?php
require_once "../Datas_checker.php";

$datas1 = [
            "creation_date" => "2017-01-01",
            "first_name" => "John",
            "last_name" => "Paul",
            "id" => 24,
            "ip" => "192.25.14.8",
            "email" => "tata@test.com",
            "test" => "random string",
            "captcha" => "25EC5"
        ];

$datas2 = [
            "creation_date" => "2015-31-01",
            "first_name" => 4,
            "last_name" => "Paul",
            "id" => "24",
            "ip" => "random string",
            "email" => "tata@test",
            "test" => "random string",
            "captcha" => "4D#I3",
            "password" => "azerty123"
        ];

$datas3 = [
            "creation_date" => "2018-01-01",
            "first_name" => "random string",
            "last_name" => "true",
            "id" => "paul",
            "ip" => "192.25.14.2",
            "email" => "tata@jetable.org",
            "test" => "",
            "captcha" => "4DEI3",
            "password" => "12345"
        ];

$datas4 = new stdClass();
$datas4->creation_date = "2018-01-01";
$datas4->first_name = "lo4";
$datas4->last_name = "Bob";
$datas4->id = 4;
$datas4->ip = "::1";
$datas4->email = "tootoo@test.com";
$datas4->password = "ThisIsP4\$\$w0rd";
$datas4->captcha = "4DEI3";


/* French Version */

/*$check_test = [
        "creation_date" => [
                                "required",
                                "date",
                                "greater_than" => "2016-01-01",
                                "error_message" => "la date de création est inférieur à la date"
                            ],
        "first_name" => [
                            "required",
                            "string",
                            "min_length" => 4,
                            "not_alphanumeric",
                            "error_message" => "le prénom n'est pas un mot ou est inférieur à 4 lettres"
                        ],
        "last_name" => [
                            "required",
                            "string",
                            "min_length" => 1,
                            "error_message" => "le nom n'est pas un mot ou est inférieur à 1 lettres"
                        ],
        "id" => [
                    "required",
                    "int",
                    "error_message" => "l'id est manquant ou n'est pas un chiffre"
                ],
        "ip" => [
                    "required",
                    "ip_address",
                    "alphanumeric",
                    "error_message" => "l'adresse ip est manquante ou n'est pas une ip valide"
                ],
        "email" => [
                        "required",
                        "email",
                        "disposable_email"
                    ],
        "test" => [
                        "required",
                        "string",
                        "alias" => "Alias_test"
                  ],
        "password" => [
                        "required",
                        "alphanumeric",
                        "min_length" => 8,
                        "contains_int",
                        "contains_lower",
                        "contains_upper",
                        "contains_special_characters"
                  ]
        ];*/

/* English Version */

$check_test = [
    "creation_date" => [
        "required",
        "date",
        "greater_than" => "2016-01-01",
        "error_message" => "the creation date isn't date or be superior to '2016-01-01'"
    ],
    "first_name" => [
        "required",
        "string",
        "min_length" => 4,
        "error_message" => "the firstname isn't a word or be inferior to 4 characters"
    ],
    "last_name" => [
        "required",
        "string",
        "min_length" => 1,
        "not_alphanumeric",
        "error_message" => "the name isn't a word or be inferior to 1 characters"
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
        "error_message" => "the ip adress doesn't exist or not valid ip address"
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
        "not_alphanumeric",
        "contains_number",
        "contains_lower",
        "contains_upper",
        "contains_special_character"
    ]
];


function printTable($infos, $array)
{
    $table = "<h3>TEST ".$infos."</h3>";
    $table .= "<table class='error_recap'>";
    $table .= "<tr>";
    $table .= "<th>Data Name</th>";
    $table .= "<th>Error Message</th>";
    $table .= "<th>Data Evaluated</th>";
    $table .= "<th>Test Fail</th>";
    $table .= "</tr>";
    $table .= "<tbody>";

    for($i=0; $i < count($array); $i++)
    {
        $error = $array[$i];
        $table .= "<tr>";
        $table .= "<td>".$error["data_name"]."</td>";
        $table .= "<td>".$error["error_message"]."</td>";
        $table .= "<td>".$error["data_eval"]."</td>";
        $table .= "<td>".$error["test_name"]."</td>";
        $table .= "</tr>";
    }
    $table .= "</tbody>";
    $table .= "</table>";

    echo $table;
}

?>

    <style>
        body{
            background-color: grey;
        }

        h3{
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
        }

        table {
            width:70%;
            border-collapse:collapse;
            margin-left: auto;
            margin-right: auto;
        }

        th, td {
            border: 1px solid black;
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }

        .error_recap {
            background-color: rgb(255, 77, 77);
            color: black;
        }

        .error_recap th {
            background-color: red;
        }

    </style>
    <body>


<?php

$checker = new datas_checker();
$isCorrectDatas = $checker->verify($datas1, $check_test);
unset($checker);
if(is_array($isCorrectDatas))
{
    printTable("- 1", $isCorrectDatas);
}

$checker = new datas_checker();
$isCorrectDatas = $checker->verify($datas2, $check_test);
unset($checker);
if(is_array($isCorrectDatas))
{
    printTable("- 2", $isCorrectDatas);
}

$checker = new datas_checker();
$isCorrectDatas = $checker->verify($datas3, $check_test);
unset($checker);
if(is_array($isCorrectDatas))
{
    printTable("- 3", $isCorrectDatas);
}

$checker = new datas_checker();
$isCorrectDatas = $checker->verify($datas4, $check_test);
unset($checker);
if(is_array($isCorrectDatas))
{
    printTable("- Object - 4", $isCorrectDatas);
}

?>
    </body>



