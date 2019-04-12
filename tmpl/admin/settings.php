<?php 
if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }
?>

	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'NN Plugin Settings', NN_TD ); ?></h2>
		
		<?php settings_errors(); ?>
		
		<?php 
		//Incoming array is has tabs already set as setting groups. Take top level info for tab name/values. 		
		
			$settings = new init\NNSettings(); //Add a settings page
			echo $settings->render();
		?>
			
	</div><!-- /.wrap -->