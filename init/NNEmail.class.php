<?php

/*
Email Settitngs Initialization Class - Initial Class Psuedo Code for NBCS Network Plugin
Last Updated 9 Nov 2018
-------------

  Description: This class initializes email settings for use accross the network.


---

*/

namespace init;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NNEmail' ) ){
	class NNEmail{
		
		
		//Parameters
		
		public $default_sender = array( 
			'email'	=> 'office@trainingdoulas.com',
			'name' 	=> 'New Beginnings'
			
		); 	
		
		
		
		//Methods
		
		/*
		
		Name: __construct
		Description: 
		
		*/
		
		public function __construct(){
			
			$this->init();
			
			
			
		}
		
		
		/*
		Name: init
		Description: 
		
		*/
		
		public function init(){
			
			$this->set_default();
			
			$this->add_filters();
		
		}
			
		/*
		Name: __
		Description: 
		
		*/
		
		public function add_filters(){
			
			//Email Settings. 
			add_filter( 'wp_mail_from', function( $email ) {
				return $this->default_sender[ 'email' ];
			});
			
			//
			add_filter( 'wp_mail_from_name', function( $name ) {
				return $this->default_sender[ 'name' ];
			});
			
			
		}
			
		/*
		Name: set_default
		Description: 
		
		*/
		
		public function set_default(){
			
			if( !empty( $email = get_option( 'admin_email' ) ){
				
				$user = get_user_by( 'email', $email );
				
			
				if( !empty( $user ) ){
					
					$first_name = $user->first_name;
					
					$this->default_sender[ 'name' ] = $user->first_name. ' at New Beginnings ';
					
					$this->default_sender[ 'email' ] = $email;
					
					return true;
					
				}
			}
			return false;
			
		}
			
		/*
		Name: __
		Description: 
		
		*/
		
		public function __(){
			
			
		}
		
		
		
		
	}
}




?>


