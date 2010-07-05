<?php
class GgapiComponent extends Object {

	public function initialize($controller) {
		$this->controller = $controller;
		Configure::load('Ggapi.settings');
	}

	public function ggprocess($order, $customer) {
		// get required settings and config from config/settings.php
		$settings = Configure::read('Ggapi.settings');
		$config = Configure::read('Ggapi.config');

		// field mapping
		$request = $this->__ggprep($order, $customer);

		// combine the non-variable settings from config file with order data
		$request = array_merge($request, $settings);

		// converts the array into the GG API formatted XML file
		$request = $this->__buildXML($request);

		// post the XML file, hopefully get a response
		$response = $this->__postXML($config['url'], $config['port'], $config['key'], $request);

		// check the response, convert to array if it's valid
		if ($response) {
			$response = $this->__decodeXML($response);
			if (is_array($response)) {
				return $response;
			}
		}
		return false;
	}

	// matches local fields to GG API fields (field map in config/settings.php)
 	private function __ggprep($order, $customer) {
		$data = array();
		$fields = Configure::read('Ggapi.fields');
		$args = func_get_args();
		foreach ($args as $arg) {
			foreach ($arg as $k => $v) {
				if (isset($fields[$k])) {
					if (!is_array($fields[$k])) {
						$data[$fields[$k]] = $v;
					} else {
						foreach ($fields[$k] as $ik => $iv) {
							if (isset($fields[$k][$ik])) {
								$data[$iv] = $v[$ik];
							}
						}
					}
				}
			}
		}
		return $data;
	}

	// returns the XML response or false
	private function __postXML($url, $port, $key, $xml) {
	    $defaults = array(
	        CURLOPT_POST => 1,
	        CURLOPT_HEADER => 0,
	        CURLOPT_URL => $url,
			CURLOPT_PORT => $port,
	        CURLOPT_FRESH_CONNECT => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FORBID_REUSE => 1,
	        CURLOPT_TIMEOUT => 15,
	        CURLOPT_POSTFIELDS => $xml,
			CURLOPT_SSLCERT => $key,
			CURLOPT_SSL_VERIFYHOST => 0, // testing only
			CURLOPT_SSL_VERIFYPEER => 0 // testing only
	    );

	    $ch = curl_init();
	    curl_setopt_array($ch, $defaults);
	    if (!$result = curl_exec($ch)) {
			return false;
	    }

	    curl_close($ch);
	    return $result;
	}

	// converts the XML response into an associative array
	private function __decodeXML($xml_response) {
		preg_match_all ("/<(.*?)>(.*?)\</", $xml_response, $out, PREG_SET_ORDER);
		$n = 0;
		$result = array();
		while (isset($out[$n])) {
			$result[$out[$n][1]] = strip_tags($out[$n][0]);
			$n++;
		}
		return $result;
	}

	// from GG API php sample code lphp.php, vintage 2003.
	private function __buildXML($pdata) {

		### ORDEROPTIONS NODE ###
		$xml = "<order><orderoptions>";

		if (isset($pdata["ordertype"]))
			$xml .= "<ordertype>" . $pdata["ordertype"] . "</ordertype>";

		if (isset($pdata["result"]))
			$xml .= "<result>" . $pdata["result"] . "</result>";

		$xml .= "</orderoptions>";


		### CREDITCARD NODE ###
		$xml .= "<creditcard>";

		if (isset($pdata["cardnumber"]))
			$xml .= "<cardnumber>" . $pdata["cardnumber"] . "</cardnumber>";

		if (isset($pdata["cardexpmonth"]))
			$xml .= "<cardexpmonth>" . $pdata["cardexpmonth"] . "</cardexpmonth>";

		if (isset($pdata["cardexpyear"]))
			$xml .= "<cardexpyear>" . $pdata["cardexpyear"] . "</cardexpyear>";

		if (isset($pdata["cvmvalue"]))
			$xml .= "<cvmvalue>" . $pdata["cvmvalue"] . "</cvmvalue>";

		if (isset($pdata["cvmindicator"]))
			$xml .= "<cvmindicator>" . $pdata["cvmindicator"] . "</cvmindicator>";

		if (isset($pdata["track"]))
			$xml .= "<track>" . $pdata["track"] . "</track>";

		$xml .= "</creditcard>";


		### BILLING NODE ###
		$xml .= "<billing>";

		if (isset($pdata["name"]))
			$xml .= "<name>" . $pdata["name"] . "</name>";

		if (isset($pdata["company"]))
			$xml .= "<company>" . $pdata["company"] . "</company>";

		if (isset($pdata["address1"]))
			$xml .= "<address1>" . $pdata["address1"] . "</address1>";
		elseif (isset($pdata["address"]))
			$xml .= "<address1>" . $pdata["address"] . "</address1>";

		if (isset($pdata["address2"]))
			$xml .= "<address2>" . $pdata["address2"] . "</address2>";

		if (isset($pdata["city"]))
			$xml .= "<city>" . $pdata["city"] . "</city>";

		if (isset($pdata["state"]))
			$xml .= "<state>" . $pdata["state"] . "</state>";

		if (isset($pdata["zip"]))
			$xml .= "<zip>" . $pdata["zip"] . "</zip>";

		if (isset($pdata["country"]))
			$xml .= "<country>" . $pdata["country"] . "</country>";

		if (isset($pdata["userid"]))
			$xml .= "<userid>" . $pdata["userid"] . "</userid>";

		if (isset($pdata["email"]))
			$xml .= "<email>" . $pdata["email"] . "</email>";

		if (isset($pdata["phone"]))
			$xml .= "<phone>" . $pdata["phone"] . "</phone>";

		if (isset($pdata["fax"]))
			$xml .= "<fax>" . $pdata["fax"] . "</fax>";

		if (isset($pdata["addrnum"]))
			$xml .= "<addrnum>" . $pdata["addrnum"] . "</addrnum>";

		$xml .= "</billing>";


		## SHIPPING NODE ##
		$xml .= "<shipping>";

		if (isset($pdata["sname"]))
			$xml .= "<name>" . $pdata["sname"] . "</name>";

		if (isset($pdata["saddress1"]))
			$xml .= "<address1>" . $pdata["saddress1"] . "</address1>";

		if (isset($pdata["saddress2"]))
			$xml .= "<address2>" . $pdata["saddress2"] . "</address2>";

		if (isset($pdata["scity"]))
			$xml .= "<city>" . $pdata["scity"] . "</city>";

		if (isset($pdata["sstate"]))
			$xml .= "<state>" . $pdata["sstate"] . "</state>";
		elseif (isset($pdata["state"]))
			$xml .= "<state>" . $pdata["sstate"] . "</state>";

		if (isset($pdata["szip"]))
			$xml .= "<zip>" . $pdata["szip"] . "</zip>";
		elseif (isset($pdata["sip"]))
			$xml .= "<zip>" . $pdata["zip"] . "</zip>";

		if (isset($pdata["scountry"]))
			$xml .= "<country>" . $pdata["scountry"] . "</country>";

		if (isset($pdata["scarrier"]))
			$xml .= "<carrier>" . $pdata["scarrier"] . "</carrier>";

		if (isset($pdata["sitems"]))
			$xml .= "<items>" . $pdata["sitems"] . "</items>";

		if (isset($pdata["sweight"]))
			$xml .= "<weight>" . $pdata["sweight"] . "</weight>";

		if (isset($pdata["stotal"]))
			$xml .= "<total>" . $pdata["stotal"] . "</total>";

		$xml .= "</shipping>";


		### TRANSACTIONDETAILS NODE ###
		$xml .= "<transactiondetails>";

		if (isset($pdata["oid"]))
			$xml .= "<oid>" . $pdata["oid"] . "</oid>";

		if (isset($pdata["ponumber"]))
			$xml .= "<ponumber>" . $pdata["ponumber"] . "</ponumber>";

		if (isset($pdata["recurring"]))
			$xml .= "<recurring>" . $pdata["recurring"] . "</recurring>";

		if (isset($pdata["taxexempt"]))
			$xml .= "<taxexempt>" . $pdata["taxexempt"] . "</taxexempt>";

		if (isset($pdata["terminaltype"]))
			$xml .= "<terminaltype>" . $pdata["terminaltype"] . "</terminaltype>";

		if (isset($pdata["ip"]))
			$xml .= "<ip>" . $pdata["ip"] . "</ip>";

		if (isset($pdata["reference_number"]))
			$xml .= "<reference_number>" . $pdata["reference_number"] . "</reference_number>";

		if (isset($pdata["transactionorigin"]))
			$xml .= "<transactionorigin>" . $pdata["transactionorigin"] . "</transactionorigin>";

		if (isset($pdata["tdate"]))
			$xml .= "<tdate>" . $pdata["tdate"] . "</tdate>";

		$xml .= "</transactiondetails>";


		### MERCHANTINFO NODE ###
		$xml .= "<merchantinfo>";

		if (isset($pdata["configfile"]))
			$xml .= "<configfile>" . $pdata["configfile"] . "</configfile>";

		if (isset($pdata["keyfile"]))
			$xml .= "<keyfile>" . $pdata["keyfile"] . "</keyfile>";

		if (isset($pdata["host"]))
			$xml .= "<host>" . $pdata["host"] . "</host>";

		if (isset($pdata["port"]))
			$xml .= "<port>" . $pdata["port"] . "</port>";

		if (isset($pdata["appname"]))
			$xml .= "<appname>" . $pdata["appname"] . "</appname>";

		$xml .= "</merchantinfo>";



		### PAYMENT NODE ###
		$xml .= "<payment>";

		if (isset($pdata["chargetotal"]))
			$xml .= "<chargetotal>" . $pdata["chargetotal"] . "</chargetotal>";

		if (isset($pdata["tax"]))
			$xml .= "<tax>" . $pdata["tax"] . "</tax>";

		if (isset($pdata["vattax"]))
			$xml .= "<vattax>" . $pdata["vattax"] . "</vattax>";

		if (isset($pdata["shipping"]))
			$xml .= "<shipping>" . $pdata["shipping"] . "</shipping>";

		if (isset($pdata["subtotal"]))
			$xml .= "<subtotal>" . $pdata["subtotal"] . "</subtotal>";

		$xml .= "</payment>";


		### CHECK NODE ###


		if (isset($pdata["voidcheck"]))
		{
			$xml .= "<telecheck><void>1</void></telecheck>";
		}
		elseif (isset($pdata["routing"]))
		{
			$xml .= "<telecheck>";
			$xml .= "<routing>" . $pdata["routing"] . "</routing>";

			if (isset($pdata["account"]))
				$xml .= "<account>" . $pdata["account"] . "</account>";

			if (isset($pdata["bankname"]))
				$xml .= "<bankname>" . $pdata["bankname"] . "</bankname>";

			if (isset($pdata["bankstate"]))
				$xml .= "<bankstate>" . $pdata["bankstate"] . "</bankstate>";

			if (isset($pdata["ssn"]))
				$xml .= "<ssn>" . $pdata["ssn"] . "</ssn>";

			if (isset($pdata["dl"]))
				$xml .= "<dl>" . $pdata["dl"] . "</dl>";

			if (isset($pdata["dlstate"]))
				$xml .= "<dlstate>" . $pdata["dlstate"] . "</dlstate>";

			if (isset($pdata["checknumber"]))
				$xml .= "<checknumber>" . $pdata["checknumber"] . "</checknumber>";

			if (isset($pdata["accounttype"]))
				$xml .= "<accounttype>" . $pdata["accounttype"] . "</accounttype>";

			$xml .= "</telecheck>";
		}


		### PERIODIC NODE ###

		if (isset($pdata["startdate"]))
		{
			$xml .= "<periodic>";

			$xml .= "<startdate>" . $pdata["startdate"] . "</startdate>";

			if (isset($pdata["installments"]))
				$xml .= "<installments>" . $pdata["installments"] . "</installments>";

			if (isset($pdata["threshold"]))
						$xml .= "<threshold>" . $pdata["threshold"] . "</threshold>";

			if (isset($pdata["periodicity"]))
						$xml .= "<periodicity>" . $pdata["periodicity"] . "</periodicity>";

			if (isset($pdata["pbcomments"]))
						$xml .= "<comments>" . $pdata["pbcomments"] . "</comments>";

			if (isset($pdata["action"]))
				$xml .= "<action>" . $pdata["action"] . "</action>";

			$xml .= "</periodic>";
		}


		### NOTES NODE ###

		if (isset($pdata["comments"]) || isset($pdata["referred"]))
		{
			$xml .= "<notes>";

			if (isset($pdata["comments"]))
				$xml .= "<comments>" . $pdata["comments"] . "</comments>";

			if (isset($pdata["referred"]))
				$xml .= "<referred>" . $pdata["referred"] . "</referred>";

			$xml .= "</notes>";
		}

		$xml .= "</order>";

		return $xml;
	}



}