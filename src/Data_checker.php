<?php

/**
 * Class data_checker
 * @link https://github.com/gravity-zero/data_checker
 */
namespace Gravity;

class Data_checker
{
    private array $errors = [];
    public string $alias;
    const DISPOSABLE = ["@yopmail", "@ymail", "@jetable", "@trashmail", "@jvlicenses", "@temp-mail", "@emailnax", "@datakop"];

    /**
     * @param array|object $data
     * @param array $check_rules
     * @return bool|array Errors array or true
     */
    public function verify(array|object $data,array $check_rules): bool|array
    {
        $data = (array)$data; // cast object as array

        if($this->array_control($data, $check_rules))
        {
            foreach($check_rules as $control_name=>$controls)
            {
                $this->alias = array_key_exists("alias", $controls) ? $controls["alias"] : "";

                if(array_key_exists($control_name, $data))
                {
                    if(!in_array("required", $controls))
                    {
                        if(!empty($data[$control_name]))
                        {
                            $this->search_method($controls, $data[$control_name], $control_name);
                        }
                    }else{
                        $this->search_method($controls, $data[$control_name], $control_name);
                    }
                }elseif(in_array("required", $controls)){
                    if(array_key_exists("error_message", $controls) && $controls['error_message'])
                    {
                        $this->set_error($controls["error_message"]);
                    }else{
                        $this->set_error("The field ". $control_name . " is required but was not found");
                    }
                }
            }
        }

        return count($this->get_errors()) > 0 ? $this->get_errors() : true;
    }

    /**
     * @param $controls
     * @param $data
     * @param $key_checked
     */
    private function search_method($controls, $data, $key_checked): void
    {
        foreach($controls as $key=>$value)
        {
            if(is_string($key) && $key !== "error_message" && method_exists($this, $key))
            {
                if(!$this->$key($data, $value))
                {
                    if(array_key_exists("error_message", $controls) && $controls['error_message'])
                    {
                        $this->set_error($controls["error_message"], $data, $key_checked, $key);
                    } else {
                        $this->set_error("Doesn't match the control test ". strtoupper($key) . " as excepted", $data, $key_checked, $key);
                    }
                }
            }elseif(method_exists($this, $value)){
                if(!$this->$value($data))
                {
                    if(array_key_exists("error_message", $controls) && $controls['error_message'])
                    {
                        $this->set_error($controls["error_message"], $data, $key_checked, $value);
                    } else {
                        $this->set_error("Doesn't match the control test ". strtoupper($value) . " as excepted", $data, $key_checked, $value);
                    }
                }
            }
        }
    }

    private function required($data): bool
    {
        if(!isset($data) || empty($data)) return false;
        return true;
    }

    private function disposable_email($data): bool
    {
        $domain = explode(".", strstr($data, "@"));
        if(in_array($domain[0], self::DISPOSABLE)) return false;

        return true;
    }

    private function street_address($data): bool
    {
        if (!@preg_match("/^[a-zA-Z0-9 'éèùëêûîìàòÀÈÉÌÒÙâôöüïäÏÖÜÄËÂÊÎÔÛ-]+$/", $data)) return false;
        return true;
    }

    private function date($data): bool
    {
        if(!\DateTime::createFromFormat('Y-m-d', $data)) return false;
        return true;
    }

    private function numeric($data): bool
    {
        if(!is_numeric($data)) return false;
        return true;
    }

    private function int($data): bool
    {
        if(!is_int($data) && !(is_string($data) && filter_var($data, FILTER_VALIDATE_INT))) return false;
        return true;
    }

    private function email($data): bool
    {
        if(!filter_var($data, FILTER_VALIDATE_EMAIL)) return false;
        return true;
    }

    private function ip_address($data): bool
    {
        if(!filter_var($data, FILTER_VALIDATE_IP, [FILTER_FLAG_IPV4, FILTER_FLAG_IPV6])) return false;
        return true;
    }

    private function greater_than($data, $greater_than): bool
    {
        if($data < $greater_than) return false;
        return true;
    }

    private function lower_than($data, $lower_than): bool
    {
        if($data > $lower_than) return false;
        return true;
    }

    private function min_length($data, $length_greater): bool
    {
        if(strlen($data) < $length_greater) return false;
        return true;
    }

    private function max_length($data, $length_lower): bool
    {
        if(strlen($data) > $length_lower) return false;
        return true;
    }

    private function string($data): bool
    {
        if(!is_string($data)) return false;
        return true;
    }

    private function contains_upper($data): bool
    {
        $upper = false;
        foreach($this->str_split($data) as $char)
        {
            if(ctype_upper($char) && !is_numeric($char)) return true;
        }
        return false;
    }

    private function contains_lower($data): bool
    {
        foreach($this->str_split($data) as $char)
        {
            if(ctype_lower($char) && !is_numeric($char)) return true;
        }
        return false;
    }

    private function contains_number($data): bool
    {
        foreach($this->str_split($data) as $char)
        {
            if(is_numeric($char)) return true;
        }
        return false;
    }

    private function alphanumeric($data): bool
    {
        if (!ctype_alnum($data)) return false;
        return true;
    }

    private function not_alphanumeric($data): bool
    {
        if (ctype_alnum($data)) return false;
        if (!preg_match("/^[a-zA-Z éèùëêûîìàòÀÈÉÌÒÙâôöüïäÏÖÜÄËÂÊÎÔÛ'-]+$/", $data)) return false;
        return true;
    }

    private function contains_special_character($data): bool
    {
        if(!preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $data)) return false;
        return true;
    }

    private function str_split($data)
    {
        if((int)PHP_VERSION >= 8) return str_split($data);

        return explode("", $data);
    }

    private function set_error($message, $value=null, $data_name=null, $test_name=null)
    {
        $this->errors[] = ["error_message" => $message, "data_eval" => !empty($value) ? $value : "EMPTY", "data_name" => !empty($this->alias) ? $this->alias : $data_name, "test_name" => $test_name];
    }

    public function get_errors(): array
    {
        return $this->errors;
    }

    private function array_control($data, $check_rules): bool
    {
        if(is_array($data) && is_array($check_rules)) return true;

        $this->set_error("Datas or Rules checker aren't an array");
        return false;
    }

}
