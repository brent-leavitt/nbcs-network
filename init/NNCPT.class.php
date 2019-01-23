<?php 
/*
// NB_NET CPT

Description: This class initializes CPT that are declared at a network level. 

	The Network has only one network wide CPT: Guides (AKA Enrollment steps, or service actions.) 

	Not so: 
		Receipts,
		Invoices,
		Notices,
		Notice Templates
		Records
	
Planning: 
	Guides are linked to Enrollment tokens (multiples) and are set from within the meta for the specific guide. Will call the options table from database and allow for the selection of enrollment tokens to apply to each guide. 


*/

namespace init;

use \modl\NNCPT as CPT;

class NNCPT{
	
	public $post_types = array(
		'guide',
		'receipt',
		'invoice',
		'notice',
		'noticetemplate',
		'record',
	);
	
	public function __construct( ){
		//$this->setup();
		
	}
	
	public function setup(){
		//Define specific CPTs for use across the network. 

		//Guide
		$guide	= new CPT( 
			array( 
				'post_type'=>'guide',
				'description'=>'enrollment actions or services used for assigning behaviours to tokens',
				/* 'menu_pos'=>53,*/
				'menu_icon'=>'index-card', 
				'hierarchical' => false,
				'exclude_from_search' => true,
				'supports' => array( 
					'title', 
					'editor', 
					'comments', 
					'author', 
					'revisions' 
				)
			) 
		);
		
		$receipt =  new CPT( 
			array( 
				'post_type'=>'receipt',
				'description'=>'receipt of transactions processed',
				/* 'menu_pos'=>53,*/
				'menu_icon'=>'cart', 
				'hierarchical' => false,
				'exclude_from_search' => false,
				'supports' => array( 
					'title', 
					'editor', 
					'revisions' 
				)
			) 
		);
		
		$receipt->set_status( [ 'complete' ] );
		
		$invoice =  new CPT( 
			array( 
				'post_type'=>'invoice',
				'description'=>'finacial transactions to be received',
				/* 'menu_pos'=>53,*/
				'menu_icon'=>'media-spreadsheet', 
				'hierarchical' => false,
				'exclude_from_search' => true,
				'supports' => array( 
					'title', 
					'editor', 
					'comments', 
					'author', 
					'revisions' 
				)
			) 
		);
		
		$invoice->set_status( [ 'issued' ] );
		
		$notice =  new CPT( 
			array( 
				'post_type'=>'notice',
				'description'=>'',
				/* 'menu_pos'=>53,*/
				'menu_icon'=>'email', 
				'hierarchical' => false,
				'exclude_from_search' => true,
				'supports' => array( 
					'title', 
					'editor', 
					'comments', 
					'author', 
					'revisions' 
				)
			) 
		);

		$notice->set_status( [ 'sent', 'posted', 'delivered', 'opened', 'read', 'clicked' ] );
		
		$noticetemplate =  new CPT( 
			array( 
				'post_type'=>'noticetemplate',
				'description'=>'',
				/* 'menu_pos'=>53,*/
				'menu_icon'=>'admin-page', 
				'hierarchical' => false,
				'exclude_from_search' => true,
				'supports' => array( 
					'title', 
					'editor', 
					'comments', 
					'author', 
					'revisions' 
				)
			) 
		);	
		
		$record =  new CPT( 
			array( 
				'post_type'=>'record',
				'description'=>'',
				/* 'menu_pos'=>53,*/
				'menu_icon'=>'backup', 
				'hierarchical' => false,
				'exclude_from_search' => true,
				'supports' => array( 
					'title', 
					'editor', 
					'comments', 
					'author', 
					'revisions' 
				)
			) 
		);		
		$record->set_status( [ 'recorded' ] );
		
		add_filter( 'rwmb_meta_boxes', array( $this, 'set_meta_boxes') );
	}
	
	
	private function set_meta_boxes( $meta_boxes ) {
		$prefix = NN_PREFIX;
		
		//For Guides(
		//(Replace below code when ready)
		/* [OLD]
			$meta_boxes[] = array(
			'id' => 'crm_posts',
			'title' => esc_html__( 'User', 'nbcs-crm' ),
			'post_types' => array( 'post' ),
			'context' => 'side',
			'priority' => 'high',
			'autosave' => true,
			'fields' => array(
				array(
					'id' => $prefix . 'user',
					'type' => 'user',
					'field_type' => 'select_advanced',
				),
				array(
					'id' => $prefix . 'contact',
					'name' => esc_html__( 'Type of Contact', 'nbcs-crm' ),
					'type' => 'select',
					'placeholder' => esc_html__( 'Select an Item', 'nbcs-crm' ),
					'options' => array(
						'form_inquiry' => 'Form Inquiry',
						'chat' => 'Chat',
						'email_sent' => 'Email Sent',
						'email_received' => 'Email Received',
						'phone_sent' => 'Phone Call Made',
						'phone_received' => 'Phone Call Received',
						'admin_note' => 'Admin Note',
						'automated' => 'Automated',
						
					),
				),
				
			),
		); */
		

		return $meta_boxes;
	}

	
	public function set_caps(){
		
		foreach( $this->post_types as $pt ){
			
			//I need a list of all the capabilities to add to the admin. 
			$cpt = new CPT( array( 'post_type' => $pt ) );
			
			
			
			//Then I need to add_caps to the admin: 
			 $admin = get_role( 'administrator' );
			 
			 foreach( $caps as $cap ){
				 
				 $admin->add_cap( $cap );
			 }

		}
		
		
		
	}
	
	public function remove(){
		
		$types = $this->post_types;
		foreach( $types as $type )
			unregister_post_type( $type );
		
	}
}