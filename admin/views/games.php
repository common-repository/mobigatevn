<div class="wrap">
	<?php
		if(isset($message)){
	?>
		<div id="message" class="updated"><?php echo $message; ?></div>
	<?php }	?>
	
	<h2>List games</h2>
	<form id="wpse-list-table-form" method="post" action="<?php echo admin_url('admin.php?page=list_of_game&amp;noheader=true');?>">
		<?php
			// $game_list_table->search_box( 'search', 'search_id' );
	  		$game_list_table->display(); 
	  	?>
  	</form>
</div>
