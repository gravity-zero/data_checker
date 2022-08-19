<?php

/**
 * Class datas_checker
 * @link https://github.com/gravity-zero/datas_checker
 */
class Datas_checker
{
    private $errors = [];
    public $alias;
    const DISPOSABLE = ["@yopmail", "@ymail", "@jetable", "@trashmail", "@jvlicenses", "@temp-mail", "@emailnax", "@datakop"];

    /**
     * @param array $datas
     * @param array $check_rules
     * @return bool|array Errors or true
     */
    public function verify(array|object $datas,array $check_rules): bool|array
    {
        $datas = (array)$datas; // cast object as array

        if($this->array_control($datas, $check_rules))
        {
            foreach($check_rules as $control_name=>$controls)
            {
                $this->alias = array_key_exists("alias", $controls) ? $controls["alias"] : "";

                if(array_key_exists($control_name, $datas))
                {
                    if(!in_array("required", $controls))
                    {
                        if(!empty($datas[$control_name]))
                        {
                            $this->search_method($controls, $datas[$control_name], $control_name);
                        }
                    }else{
                        $this->search_method($controls, $datas[$control_name], $control_name);
                    }
                }elseif(in_array("required", $controls)){
                    $this->set_error("The field ". $control_name . " is required but was not found");
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
    private function search_method($controls, $data, $key_checked)
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

    private function required($data)
    {
        if(!isset($data) || empty($data)) return false;
        return true;
    }

    private function disposable_email($data)
    {
        $domain = explode(".", strstr($data, "@"));
        if(in_array($domain[0], self::DISPOSABLE)) return false;

        return true;
    }

    private function street_address($data)
    {
        if (!@preg_match("/^[a-zA-Z0-9 'éèùëêûîìàòÀÈÉÌÒÙâôöüïäÏÖÜÄËÂÊÎÔÛ-]+$/", $data)) return false;
        return true;
    }

    private function date($data)
    {
        if(!DateTime::createFromFormat('Y-m-d', $data)) return false;
        return true;
    }

    private function numeric($data)
    {
        if(!is_numeric($data)) return false;
        return true;
    }

    private function int($data)
    {
        if(!is_int((int)$data)) return false;
        return true;
    }

    private function email($data)
    {
        if(!filter_var($data, FILTER_VALIDATE_EMAIL)) return false;
        return true;
    }

    private function ip_address($data)
    {
        if(!filter_var($data, FILTER_VALIDATE_IP, [FILTER_FLAG_IPV4, FILTER_FLAG_IPV6])) return false;
        return true;
    }

    private function greater_than($data, $greater_than)
    {
        if($data < $greater_than) return false;
        return true;
    }

    private function lower_than($data, $lower_than)
    {
        if($data > $lower_than) return false;
        return true;
    }

    private function min_length($data, $length_greater)
    {
        if(strlen($data) < $length_greater) return false;
        return true;
    }

    private function max_length($data, $length_lower)
    {
        if(strlen($data) > $length_lower) return false;
        return true;
    }

    private function string($data)
    {
        if(!is_string($data)) return false;
        return true;
    }

    private function contains_upper($data)
    {
        $upper = false;
        foreach(str_split($data) as $char)
        {
            if(ctype_upper($char) && !is_numeric($char)) return true;
        }
        return false;
    }

    private function contains_lower($data)
    {
        foreach(str_split($data) as $char)
        {
            if(ctype_lower($char) && !is_numeric($char)) return true;
        }
        return false;
    }

    private function contains_number($data)
    {
        foreach(str_split($data) as $char)
        {
            if(is_numeric($char)) return true;
        }
        return false;
    }

    private function alphanumeric($data)
    {
        if (!ctype_alnum($data)) return false;
        return true;
    }

    private function not_alphanumeric($data)
    {
        if (!preg_match("/^[a-zA-Z éèùëêûîìàòÀÈÉÌÒÙâôöüïäÏÖÜÄËÂÊÎÔÛ'-]+$/", $data)) return false;
        return true;
    }

    private function contains_special_character($data)
    {
        if(!preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $data)) return false;
        return true;
    }

    private function set_error($message, $value=null, $data_name=null, $test_name=null)
    {
        $this->errors[] = ["error_message" => $message, "data_eval" => !empty($value) ? $value : "EMPTY", "data_name" => !empty($this->alias) ? $this->alias : $data_name, "test_name" => $test_name];
    }

    public function get_errors()
    {
        return $this->errors;
    }

    private function array_control($datas, $check_rules)
    {
        if(is_array($datas) && is_array($check_rules)) return true;

        $this->set_error("Datas or Rules checker aren't an array");
        return false;
    }

}
