First Data Global Gateway API CakePHP Plugin Component
======================================================

The Ggapi component simplifies credit card processing using the First Data/Yourpay/Linkpoint Global Gateway API. It supports testing and live configurations, along with local to remote field mapping.

Once configured, all you have to do is feed the component an array of order data (fields used are up to you) and it will map the fields, build the XML string, submit the string, read the response from the gateway, and convert the response back into an array.

Installation
------------

git clone or submodule add to your your app/plugins directory

Requirements
------------
* PHP5 with curl support
* CakePHP - tested on 1.3 only but should work fine with 1.2

Example usage in your controller
--------------------------------

    // attempt the charge (array $data, boolean $testing)
    $response = $this->Ggapi->ggProcess($data, true);

    // update the order table with the response
    if ($response) {
    	if ($response['r_approved'] == 'APPROVED') {
    		// merge the response data with the order data
    		$this->data['Order'] = array_merge($this->data['Order'], $response);
    	} else {
    		// card was DECLINED
    		$error = explode(':', $response['r_error']);
    		$this->Session->setFlash(
    		    'Your credit card was declined. The message was: '.$error[1],
    		    'modal',
    		    array('class' => 'modal error')
    		);
    		$this->redirect(array('controller' => 'orders', 'action' => 'checkout'));
    	}
    } else {
    	// no response from the gateway
    	$this->Session->setFlash(
    	    'There was a problem connecting to our payment gateway, please try again.',
    	    'modal',
    	    array('class' => 'modal error')
    	);
    	$this->redirect(array('controller' => 'orders', 'action' => 'checkout'));
    }

Blog post
---------

Possibly and/or eventually more documentaion and discussion available at
[technokracy.net](http://technokracy.net/2010/07/06/First_Data_Global_Gateway_API_CakePHP_Plugin/)
