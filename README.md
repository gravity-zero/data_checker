# data_checker

As its name suggests, data checker allows you to quickly check if your array elements are valid according to your criteria:

<b>PHP version >= 5.4</b>


### Install with Composer
![](https://github.com/gravity-zero/datas_checker/blob/master/documentation/imgs/composer-logo.png)
```
composer require gravity/datas_checker
 ```

### Require with PSR-4 & Composer
```php
<?php

use Gravity\Datas_checker;

```

### Available methods:

Here are the different methods currently implemented to verify your data set:

  ```
   - error_message //set an error on failure
   - alias //change the default data_name to compose a more explicit message with the error array
   - required //check null & empty values
   - string
   - int
   - numeric
   - date
   - greater_than //works with dates, numerics and int values
   - lower_than //works with dates, numerics and int values
   - contains_special_character //define if your string contains special characters
   - contains_lower //define if your string contains lower case characters
   - contains_upper //define if your string contains upper case characters
   - contains_number //define if your string contains number characters
   - max_length
   - min_length
   - ip_address //IPV4 & IPV6
   - email
   - disposable_email
   - street_address
   - alphanumeric //doesn't match special chars
   - not_alphanumeric //doesn't match special chars
  ```

⚠️ <b>Below are the only methods that take an argument</b> ⚠️
- greater_than => type string or number
- lower_than => type string or number
- max_length => type number
- min_length => type number

#### Example
```php 

    // NUMBER TEST
$control_tests = [
    "myNumberField" => [
        "required",
        "greater_than" => 3
    ]
];
    
    // DATE TEST
$control_tests = [
    "myDateField" => [
        "required",
        "greater_than" => "2016-01-01"
    ]
];    
    // STRING TEST
$control_tests = [
    "myStringField" => [
        "required",
        "max_length" => 8
    ]
];
```

### Steps to build verification array & launch tests
<b>First step</b>, you need to create an array with the fields you want to verify:
```php
$control_tests = [
    "myFirstField",
    "mySecondField"
];
```

<b>Second step</b>, for each field you create a second array contains verification methods you want to use:
```php
$control_tests = [
    "myFirstField" => [
        "required",
        "string"
    ],
    "mySecondField" => [
        "required",
        "int"
    ]
];
```
<b>Third step</b>, create instance of <b>data_checker</b> and use the verify method with your array or object data to verify, and your verification array.

```php
$checker = new Data_checker();
$isCorrectdata = $checker->verify($data_to_check, $control_tests);
```

### Full Example : 

```php 

<?php

use Gravity\Datas_checker;

// Can be an array (like $_POST) or an object
$data_to_check =
        [
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
               
$control_tests =
         [
            "creation_date" => [
                "required",
                "date",
                "greater_than" => "2016-01-01",
                "error_message" => "the creation date isn't a date or be lower than '2016-01-01'"
            ],
            "first_name" => [
                "required",
                "string",
                "min_length" => 4,
                "not_alphanumeric",
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
                "ip_address",
                "alphanumeric",
                "error_message" => "the ip address doesn't exist or not valid ip address"
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
                "alphanumeric",
                "min_length" => 8,
                "contains_int",
                "contains_lower",
                "contains_upper",
                "contains_special_character"
            ]
        ];
                  
$data_control = new data_checker();
$isCorrectdata = $data_control->verify($data_to_check, $control_tests);
```

### Result table:
![](https://github.com/gravity-zero/data_checker/blob/master/documentation/imgs/result_table.png)
### Result php:
```php 
var_dump($isCorrectdata);
    
[ 
      0 => 
          [
            'error_message' => 'the creation date isn\'t date or be lower than \'2016-01-01\'',
            'data_eval' => '2015-31-01',
            'data_name' => 'creation_date',
            'test_name' => 'greater_than',
          ],
      1 => 
          [
            'error_message' => 'the firstname isn\'t a word or be lower than 4 characters',
            'data_eval' => 4,
            'data_name' => 'first_name',
            'test_name' => 'string',
          ],
      2 => 
          [
            'error_message' => 'the firstname isn\'t a word or be lower than 4 characters',
            'data_eval' => 4,
            'data_name' => 'first_name',
            'test_name' => 'min_length',
          ],
      3 => 
          [
            'error_message' => 'the ip adress doesn\'t exist or not valid ip address',
            'data_eval' => 'random string',
            'data_name' => 'ip',
            'test_name' => 'ip_address',
          ],
      4 => 
          [
            'error_message' => 'Doesn\'t match the control test EMAIL as excepted',
            'data_eval' => 'tata@test',
            'data_name' => 'email',
            'test_name' => 'email',
          ],
      5 => 
          [
            'error_message' => 'Doesn\'t match the control test ALPHANUMERIC as excepted',
            'data_eval' => '4D#I3',
            'data_name' => 'captcha',
            'test_name' => 'alphanumeric'
          ],
      7 =>
          [
            'error_message' => 'Doesn\'t match the control test NOT_ALPHANUMERIC as excepted',
            'data_eval' => 'azerty123',
            'data_name' => 'password',
            'test_name' => 'not_alphanumeric'
          ],
      6 => 
          [
            'error_message' => 'Doesn\'t match the control test CONTAINS_UPPER as excepted',
            'data_eval' => 'azerty123',
            'data_name' => 'password',
            'test_name' => 'contains_upper',
          ],
      7 => 
          [
            'error_message' => 'Doesn\'t match the control test CONTAINS_SPECIAL_CHARACTER as excepted',
            'data_eval' => 'azerty123',
            'data_name' => 'password',
            'test_name' => 'contains_special_character',
          ]
]   
```

As you can see, you have everything you need to display a suitable message.

Hope this little tool will save you some time to check the validity of your dataets :)
