# datas_checker

As its name suggests, data checker allows you to quickly check if your element array is valid according to your criteria:

- String
- String: minimum and maximum number of characters
- Date
- Date: greater and less than a date
- Integer
- Float (Number)
- Email address
- Disposable email
- street name
- IP address (IPV4 and IPV6)

PHP version >= 5.4

###Exemple : 

```php 
$datas_to_check = [
                    ["creation_date" => "2016-01-01", "first_name" => "John", "last_name" => "Paul", "id" => 24, "ip" => "192.25.14.2", "email" => "tata@test.com"],
                    ["creation_date" => "q201-01-01", "first_name" => 4, "last_name" => "Paul", "id" => "24", "ip" => "toto", "email" => "tata@test"],
                    ["creation_date" => "2012-05-04", "first_name" => "yes", "last_name" => "true", "id" => "paul", "ip" => "192.25.14.2", "email" => "tata@jetable.org"]
                  ];
        
$control_tests = [
                "creation_date" => ["required", "is_date", "greater_than" => "2016-01-01", "error_message" => "the creation date isn't date or be superior to '2016-01-01'"],
                "first_name" => ["required", "is_string", "lenght_greater" => 2, "error_message" => "the firstname isn't a word or be inferior to 2 characters"],
                "last_name" => ["required", "is_string", "lenght_greater" => 1, "error_message" => "the name isn't a word or be inferior to 1 characters"],
                "id" => ["required", "is_int", "error_message" => "the id doesn't exist or not an integer"],
                "ip" => ["required", "is_ipadress", "error_message" => "the ip address doesn't exist or not a valid ip address"],
                "email" => ["required", "is_email", "disposable_email"]
              ];
                  
$datas_control = new datas_checker();
$isCorrectDatas = $datas_control->check($datas, $control_tests);
```

Here we've got return errors for the first and second row of $datas_to_check.
Has you see you be able to put your own error message. However, the generic message should be sufficient and the error table contains in addition to the message the evaluated data, the data_name, and the failed test name.

###Result :
```php 
    var_dump($isCorrectDatas);
    
    array(
        0 => 
            0 => array(
                    "error_message" => "the creation date isn't date or be superior to '2016-01-01'",
                    "data_eval" => "q201-01-01",
                    "data_name" => "creation_date",
                    "test_name" => "is_date"
                ),
            1 => array(
                    "error_message" => "the creation date isn't date or be superior to '2016-01-01'",
                    "data_eval" => "2012-05-04",
                    "data_name" => "creation_date",
                    "test_name" => "superior_to"
                ),
            2 => array(
                    "error_message" => "the firstname isn't a word or be inferior to 2 characters"
                    "data_eval" => 4
                    "data_name" => "first_name"
                    "test_name" => "is_string"
                ),
            [...],
            5 => array(
                     "error_message" => "Doesn't match the control test IS_EMAIL as excepted"
                     "data_eval" => "tata@test"
                     "data_name" => "email"
                     "test_name" => "is_email"
                ),
            6 => array(
                     "error_message" => "Doesn't match the control test DISPOSABLE_EMAIL as excepted"
                     "data_eval" => "tata@jetable.org"
                     "data_name" => "email"
                     "test_name" => "disposable_email"
                )
    )   
```

I skipped some results to show you the different error messages received, those passed in the check_test array and the standard message. As you can see, you have everything you need to display a suitable message.


### The array methods 

Here are the different methods currently implemented to verify your data set:

  ````
   - required (check null & empty values)
   - is_date
   - greater_than (works with dates, numerics or int values)
   - lower_than (works with dates, numerics or int values)
   - is_string
   - lenght_greater
   - lenght_lower
   - is_ipadress (IPV4 & IPV6)
   - is_email
   - disposable_email
   - street_address
   - is_int
   - is_alphanumeric
   - is_numeric
  ````

Hope this little tool will save you some time to check the validity of your datasets :)
