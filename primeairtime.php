<?php

define('MAIN_ROOTB', realpath(dirname(__file__)) . '/');
require_once MAIN_ROOTB . "http.php";
class primeairtime extends http
{

    private $key;
    public $id;
    public $service_id; 
    public $account;
    public $iso;
    public $cref;
    public $type;
    public $sortcode;
    public $option;

    public function __construct()
    {
        $Logger = new Logger(array('path' => MAIN_ROOT . '/log/'));
        $Logger->enable_exception();

        if (self::config('log_error')) {
            $Logger->enable_error();
            $Logger->enable_display_error(self::config('display_error'));
            $Logger->enable_fatal();
            $Logger->enable_method_file(true);
        } else {
            $Logger->enable_display_error(self::config('display_error'));
        }


        $this->key = self::auth_key();
    }
    
        public function set_option($option)
    {
        $this->option = $option;
        return $this;
    }

    public function set_id($id)
    {
        $this->id = $id;
        return $this;
    }
    
  
    public function set_sort_code($sortcode)
    {
        $this->sortcode = $sortcode;
        return $this;
    }

    public function set_service_id($service_id)
    {
        $this->service_id = $service_id;
        return $this;
    }

    public function set_account($account)
    {
        $this->account = $account;
        return $this;
    }

    public function set_iso($iso)
    {
        $this->iso = $iso;
        return $this;
    }

    public function set_product_id($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    public function set_type($type)
    {
        $this->type = $type;
        return $this;
    }
    public function set_ref($cref)
    {
        $this->cref = $cref;
        return $this;
    }

    public function set_key($key)
    {
        //return $this->key;
    }
    

    public function get_key()
    {
        return $this->key;
    }


    public function reauth_key()
    {
        $this->key = self::revalidate_key();
        return $this->key;
    }


    public function accounts()
    {
        return self::mobifin_post("accounts/me/pins", "", false);
    }


    public function status()
    {
        return self::mobifin_post("status", "", false);
    }


    public function batch($id = "")
    {

        if (!empty($id)) {
            $this->id = $id;
        }

        if (!empty($this->id)) {
            return self::mobifin_post("topup/batch/" . $this->id, "", false);
        }
        return self::mobifin_post("topup/batch", "", false);
    }

    public function log($cref = "")
    {
        if (!empty($cref)) {
            $this->cref = $cref;
        }
        return self::mobifin_post("topup/log/byref/" . $this->cref, "", false);
    }

    
    public function topup($type = true)
    {
        $this->type = "datatopup";
        if ($type) {
            $this->type = "topup";
        }
        return $this;
    }

    public function info($numbers)
    {
        $array_post = $numbers[0];

        if (count($numbers) > 1) {
            $array_post = array("numbers" => $numbers);
            return self::mobifin_post("topup/info", json_encode($array_post), true);
        } else {
            return self::mobifin_post($this->type . "/info/" . $array_post, "", false);
        }

    }


    public function exec($numbers)
    {
        $numbers = json_decode($numbers, true);
        if (!isset($numbers['numbers'])) {

            return self::mobifin_post($this->type . "/exec/" . $numbers['msisdn'],
                json_encode($numbers), true);
        } else {
            return self::mobifin_post($this->type . "/exec", json_encode($numbers), true);
        }

    }



    public function products($page)
    {
        return self::mobifin_post("products/$this->type/page/$page", "", false);
    }



    public function billpay($parameter = "")
    {

        if (empty($this->iso) && empty($this->service_id) && empty($this->account)) {
            
            return self::mobifin_post("billpay", "", false);
        }


        if (!empty($this->iso) && empty($this->service_id)) {
            return self::mobifin_post("billpay/country/$this->iso", "", false);
        }

        if (!empty($this->iso) && !empty($this->service_id)) {
            return self::mobifin_post("billpay/country/$this->iso/$this->service_id",
                "", false);
        }

        if (!empty($this->service_id) && !empty($this->product_id) && empty($this->
            iso) && empty($this->account)) {
            if (empty($parameter)) {
                return self::mobifin_post("billpay/$this->service_id/$this->product_id", "", false);
            }

            return self::mobifin_post("billpay/$this->service_id/$this->product_id/validate",
                '{"meter" : "' . $parameter . '"}', true);

        }

        if (!empty($this->account)) {
            
            
            if($this->service_id == "electricity"){
            return self::mobifin_post("billpay/$this->account",$parameter, true);
            }else{
               return self::mobifin_post("billpay/$this->service_id/".$this->product_id."/".$this->option,$parameter, true); 
            }
        }

    }




    public function ft($account="")
    {
        $this->account = $account;
        return $this;
    }
    
            public function banks()
    {
        return self::mobifin_post("ft/banks", "", false);
    }
    
            public function access()
    {
        return self::mobifin_post("ft/check_access", "", false);
    }
    
        public function loogup()
    {
        return self::mobifin_post("ft/lookup/$this->sortcode/$this->account", "", false);
    }

        public function transfer($parameter)
    {
        return self::mobifin_post("ft/transfer/$this->sortcode/$this->account", $parameter, true);
    }

  

    public function sms($parameter)
    {

        return self::mobifin_post("sms/$this->account", $parameter, true);
    }


    //security to prevent miss use / abuse
    //log transaction and implement duplicate transaction


}
?>