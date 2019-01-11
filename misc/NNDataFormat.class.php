<?php 

/* NNDataFormat Class for NBCS Network Plugin
Last Updated 18 Dec 2018
-------------

Description: 

		
*/
	
namespace misc;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNDataFormat' ) ){
	class NNDataFormat{

	// PROPERTIES
		
		// 
		//public $data;
		public $in;								//Incoming Data String
		public $out; 							//Outgoing Data Array
		private $source;						//The Source Class: Stripe, PayPal, Default. 
		
		private $output_format = array(
			'action' => '',						//Primary Action 
			'service' =>  '', 					//
			'patron' => '', 					//
			'token' => '', 						//
			'data' => array(					//The data key may be replaced with the name of the Primary Action. 
				'type' => '', 					//type of data = reminder (NEEDED?)
				'template' => 0, 				//what notice (template) is being sent?
				'template_vars' => array(),
				'create_date' => '', 			//Create Date or issue Date
				'due_date' => '', 				//Due Date
				'trans_type' => '', 			//Transaction Type, like "charge", "payment", "refund", etc. 
				'trans_status' => '',			//Transaction Status
				'trans_descrip' => '',			//Description of the Transaction
				'currency' => '',				//Currency (only accepting USD)
				'subtotal' => '',		 		//Subtotal before taxes
				'discount' => '',		 		//Discount on Subtotal
				'sales_tax' => '',		 		//Sales Tax
				'gross_amount' => '', 			//Transaction Gross Amount
				'trans_fee' => '',  	 		//Transaction Fee
				'net_amount' => '',		 		//Amount Collected After Fees
				'reference_ID' => '',	 		//Reference ID
				'reference_type' => '',			//Reference Type
				'tp_name' => '', 				//ThirdParty Name, like "stripe" or PayPal 
				'tp_id' => '', 					//ThirdParty Transaction ID
				
				'line_items' => array(
					array(
						'li_id' => '', 			//Item ID
						'li_descrip' => '',		//Description
						'li_qty' => '', 		//Qty
						'unit_price' => '',		//Unit Price
						'li_discount' => '', 	//Discout
						'account' => '', 		//Account
						'li_amount' => '', 		//Amount
					),
					//etc...
				),
				'payee' => array(
					'full_name' => '',			//
					'user_name' => '', 			//
					'display_name' => '', 		//
					'first_name' => '',			//
					'last_name' => '',			//
					'address' => '',			//	
					'address1' => '',			//	
					'city' => '',				//	
					'state' => '',				//	
					'zip' => '',				//	
					'country' => '',			//	
					'email' => '',				//	
					'phone' => '',				//	
					'type' => '',				//paypal, visa, mastercard, etc. 
					'card' => '',				//last4 of 
					'exp' => '',				//expiration date. 
					'on_behalf_of' => '',		//email_address 
					'password' => '',			//?
				),
				'src_data' => '',				//JSON String of Transactional Source Data. 
				'' => '',						// (what else)?
			)
		);
		
		/* private $default_data_map = [
		
		];			// 'nn_value' => '3rd_party_value'
		 */
		

		
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( $data ){
			
			$this->init( $data );
			
		}	
		

	/*
		Name: __Destruct
		Description: 
	*/	
		
		public function __destruct(){
			
			
			
		}	
		



	/*
		Name: Init
		Description: 
	*/	
		
		private function init( $data ){
			
			$this->in = $data;
			
		}	
		
		

	/*
		Name: set_source
		Description: This sets the source of the data. 
	*/	
		
		public function set_source( $source ){
			
			//We may want to set some security checks. 
			
			//Set Source Name Property
			if( !empty( $source ) ){
				
				//All Source Class Names will be formated with first letter cap, all else lower case: ex. Paypal. 
				$source = ucfirst( strtolower( $source ) );
				
				$src_class = 'pymt\\'.$source.'\\NNData'.$source;
				
				$this->source = new $src_class( $this->in );
				
				/* //Convert incoming data to an array. This will vary according on where the data is coming from. 
				$this->data = $this->source->to_array(); */	
			}
		}
		
		
	/*
		Name: format
		Description: 
	*/	
		
		public function format(){
			
			$this->do_formatting();
			
			//final output is $out array. 	
			return ( !empty( $this->out ) )? $this->out : false; 
			
		}
		
		
	/*
		Name: do_formatting
		Description: 
	*/	
		
		private function do_formatting(){
			
			
			//source data in array format
			$data = $this->source->data;
			
			//data_map for source type: paypal, stripe, etc. 
			if( is_object( $this->source ) )			
				$data_map = $this->source->get_data_map();
			
			//the starting point for the final output, a template from $output_format
			$output = $this->output_format;
			

			//a quick check to see that each is set.
			if( empty( $data ) || empty( $data_map ) || empty( $output ) ) 
				return; 
			
			//Map data from source to final format
			$output = $this->do_mapping( $output, $data_map, $data );
			

			//Then remove empty fields from the output array. 
			$output = $this->clean( $output );
			
			
			
			//Base intergrity check of data. 
				//Is there enough incoming data to do something with? 
			
			if( $this->integrity( $output ) )
				$this->out = $output;
			
			
		}
		
		
	
	/*
		Name: integrity
		Description: Checks the integrity of the outputted data. If key fields are in place then return true. 
	*/	
		
		public function integrity( $data ){
			
			$fields = [ 'action', 'service', 'patron', 'token' ];
			
			foreach( $fields as $field ){
				if( empty( $data[ $field ] ) )
					return false;					
			}
			
			return true; 
		}
		
		
		
	/*
		Name: do_mapping
		Description: This method will only look for fields as set in the output_format. If it's not there and not mapped to a field from the source data, it doesn't get processed at this point in time. 
	*/	
		
		public function do_mapping( $output, $data_map, $data){
			//Then for each field in the output format, look for a suitable input. 
			foreach( $output as $o_key => $o_val ){

				//generate potential method name.
				$set_key = 'set_'. $o_key; 
				
				//look for method by $o_key name; 
				if( method_exists( $this, $set_key ) ){ 
					$output[ $o_key ] = $this->$set_key();
					continue;
				}
				
				//if is an array, go recursively deeper. 
				if( is_array( $o_val ) ){
					$output[ $o_key ]  = $this->do_mapping( $o_val, $data_map, $data );
					continue; 
				}
				
				//if no, run data_map on value
				if( isset( $data_map[ $o_key ] ) ){		
					if( ( $found = $this->find_in_source( $data_map[ $o_key ], $data ) ) != false )
						$output[ $o_key ] = $found;
				} 
			}
			
			return ( !empty( $output ) )? $output : '' ; 
			
		}		
		
		
	/*
		Name: set_action
		Description: 
	*/	
		
		public function set_action(){
			
			//Pull the presumable action from the source. 
			$action = $this->source->get_action();
			
			//Does data support requested action?
			
			//How do I figure this out? 
			
			return $action;
			
		}		
				
		
	/*
		Name: set_patron
		Description: How to get PATRON for whom this transaction is set? 
		Send the user ID as a part of the processing data. That the surest way to do this. 
		- How to get that? 
		- On the Cashier Page that you are proposing, the user ID needs to be set, 
			- so login required? 
			- Guest Payments? No, because you only pay when you are receiving account access to something. 
			- Pay on behalf of someone else, yes! Via Email address. 
		- Is it better to pass a system-unique ID or an email address (which is more of a universal identifier)?
			- SYSTEM ID-> Some How Encrypted. 
	*/	
		
		public function set_patron(){
			
			
			$p_value = $this->source->get_patron();
			
			$patron_id = nn_crypt( $p_value, 'd' );
			
			//Check if user exists? 
			$patron = get_user_by( 'id', $patron_id );
			
			//what is the payee email? 
				
			return ( is_object( $patron ) )$patron_id : NULL;
		}		
				
		
	/*
		Name: set_service
		Description: 
	*/	
		
		public function set_service(){
			
			$service = $this->source->get_service();
			
			
			return $service;
		}		
				
		
	/*
		Name: set_token
		Description: 
	*/	
		
		public function set_token(){
			
			$token = $this->source->get_token();
			
			return $token;
		}		
	

		
		
	/*
		Name: set_src_data
		Description: This sets the inbound code to the end of the formatted array. 
	*/	
		
		public function set_src_data(){
			
			return $this->in;
			
		}

	
		
	/*
		Name: set_
		Description: CAREFUL WITH THIS ONE. 
	*/	
		
		public function set__(){
			
			return 5;
		}		
		
		
	/*
		Name: find_in_source
		Description: looking in the source object for the requested value. 
	*/	
		
		public function find_in_source( $key, $data ){
			
			$result = '';
			//If this doesn't return a result. pop off the first word, and look deeper. 
			if( empty( $result = isset( $data[ $key ] )? $data[ $key ] : '' ) ){
				$pos = strpos( $key, '_' );
				$key_one = substr( $key, 0, $pos  );
				$key_two = substr( $key, $pos + 1 );
				$result = isset( $data[ $key_one ][ $key_two ] )? $data[ $key_one ][ $key_two ] : '' ;
			} 
			return ( !empty( $result ) )? $result : false ; 
		}	

		
	/*
		Name: clean
		Description: 
	*/	
		
		public function clean( $data ){
			
			foreach( $data as $key => $val ){
				if( !is_array( $data[ $key ] ) ){
					//unset if empty		
					if( empty( $val ) )
						unset( $data[ $key ] );
				}else{
					$data[ $key ] = $this->clean( $data[ $key ] );
				}
				
			}
			return $data;
		}
		
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}