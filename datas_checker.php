<?php

/**
 * Class datas_checker
 * @author FEREGOTTO Romain
 * @link https://github.com/gravity-zero/
 */
class datas_checker
{
    private $errors = [];
    const DISPOSABLE = ["@yopmail", "@ymail", "@jetable", "@trashmail", "@jvlicenses", "@temp-mail", "@emailnax", "@datakop"];

    /**
     * @param $datas
     * @param $check_rules
     * @return array|bool Errors or true
     */
    public function check($datas, $check_rules)
    {
        if($this->array_control($datas, $check_rules))
        {
            foreach($check_rules as $key_control=>$controls)
            {
                foreach ($datas as $data)
                {
                    if(array_key_exists($key_control, $data))
                    {
                        $this->search_method($controls, $data[$key_control], $key_control);
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
        if(!@preg_match("/^[a-zA-Z0-9 'éèùëêûîìàòÀÈÉÌÒÙâôöüïäÏÖÜÄËÂÊÎÔÛ-]+$/", $data)) return false;
        return true;
    }

    private function is_date($data)
    {
        if(!DateTime::createFromFormat('Y-m-d', $data)) return false;
        return true;
    }

    private function is_numeric($data)
    {
        if(!is_numeric($data)) return false;
        return true;
    }

    private function is_int($data)
    {
        if(!is_int((int)$data)) return false;
        return true;
    }

    private function is_email($data)
    {
        if(!filter_var($data, FILTER_VALIDATE_EMAIL)) return false;
        return true;
    }

    private function is_ipadress($data)
    {
        if(!filter_var($data, FILTER_VALIDATE_IP, [FILTER_FLAG_IPV4, FILTER_FLAG_IPV6])) return false;
        return true;
    }

    private function superior_to($data, $superior_to)
    {
        if($data < $superior_to) return false;
        return true;
    }

    private function inferior_to($data, $inferior_to)
    {
        if($data > $inferior_to) return false;
        return true;
    }

    private function lenght_superior($data, $superior_to)
    {
        if(strlen($data) < $superior_to) return false;
        return true;
    }

    private function lenght_inferior($data, $inferior_to)
    {
        if(strlen($data) > $inferior_to) return false;
        return true;
    }

    private function is_string($data)
    {
        if(!is_string($data)) return false;
        return true;
    }

    private function set_error($message, $value=null, $data_name=null, $test_name=null)
    {
        $this->errors[] = ["error_message" => $message, "data_eval" => $value, "data_name" => $data_name, "test_name" => $test_name];
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