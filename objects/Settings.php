<?php

if (preg_match("#" . basename(__FILE__) . "#", $_SERVER["PHP_SELF"])) {
    header("Location: /");
    die();
}
/*
 * Migs settings object
 */

class MigsPaymentGatewaySettings {
    var $url = "";
    var $secure_secret = "";
    var $accesscode = "";
    var $merchant = "";
    var $salt_1 = "";
    var $salt_2 = "";
    var $return_url = "";
    var $return_post_id = 0;
    var $empty_data = "";
    var $noerror_data = "";
    var $currency = "$";

    function updateSettings() {
        MigsPaymentGatewaySettings::checkSaltLenght(strlen($this->salt_1), "Salt 1");
        MigsPaymentGatewaySettings::checkSaltLenght(strlen($this->salt_2), "Salt 2");
        update_option(MyConstants::PREFIX . "_url", $this->url);
        update_option(MyConstants::PREFIX . "_secure_secret", $this->secure_secret);
        update_option(MyConstants::PREFIX . "_accesscode", $this->accesscode);
        update_option(MyConstants::PREFIX . "_merchant", $this->merchant);
        update_option(MyConstants::PREFIX . "_salt_1", $this->salt_1);
        update_option(MyConstants::PREFIX . "_salt_2", $this->salt_2);
        update_option(MyConstants::PREFIX . "_empty_data", $this->empty_data);
        update_option(MyConstants::PREFIX . "_noerror_data", $this->noerror_data);
        update_option(MyConstants::PREFIX . "_return_post_id", intval($this->return_post_id));
        update_option(MyConstants::PREFIX . "_currency", $this->currency);
        do_action(MyConstants::PREFIX . "_settings_update", $this);
    }

    function getBankSettings() {
        $this->url = get_option(MyConstants::PREFIX . "_url");
        $this->secure_secret = get_option(MyConstants::PREFIX . "_secure_secret");
        $this->accesscode = get_option(MyConstants::PREFIX . "_accesscode");
        $this->merchant = get_option(MyConstants::PREFIX . "_merchant");
        $this->salt_1 = get_option(MyConstants::PREFIX . "_salt_1");
        $this->salt_2 = get_option(MyConstants::PREFIX . "_salt_2");
        $this->return_post_id = get_option(MyConstants::PREFIX . "_return_post_id");
        $this->return_url = self::getReturnConfigPermalinks();
        $this->empty_data = get_option(MyConstants::PREFIX . "_empty_data");
        $this->noerror_data = get_option(MyConstants::PREFIX . "_noerror_data");
        $this->currency = get_option(MyConstants::PREFIX . "_currency");
        do_action(MyConstants::PREFIX . "_get_settings", $this);
        return $this;
    }

    function getReturnConfigPermalinks() {
        $permalinks = self::getDefaultConfigPermalinks();
        if ($this->return_post_id > 0) {
            if (intval($this->return_post_id)) {
                $post = get_post($this->return_post_id);
                if ($post) {
                    $permalinks = get_permalink($post->ID) . "?action=py";
                }
            }
        }
        return $permalinks;
    }
	
	function checkSaltLenght($value, $name){
		$allowed = array(16, 24, 32);
		if (!in_array(strlen($this->salt_1), $allowed)) {
			echo $name . " length must be 16 or 32 or 64";
			die();
		}
	}
	
    function getDefaultConfigPermalinks() {
        return get_option("siteurl") . "/?action=py";
    }

    function getHashedPassword($extra = "") {
        $md5HashData = $this->salt_1;
        $md5HashData .= $this->secure_secret;
        $md5HashData .= $this->salt_2;
        if (!empty($extra)) {
            $md5HashData .= $extra;
        }
        return md5($md5HashData);
    }

    function isEmpty() {
        return ((empty($this->secure_secret)) && (empty($this->accesscode)) && (empty($this->merchant)));
    }
    static function getCurrency(){
        $currency = get_option(MyConstants::PREFIX . "_currency");
        if (empty($currency)){
            $currency = "$";
        }
        return $currency;
    }
}
