<div class="wrap">
	<?php
		if(isset($message)){
	?>
		<div id="message" class="updated"><?php echo $message; ?></div>
	<?php }	?>
	
	<h2>Chọn thể loại cho game</h2>
	<form id="wpse-list-table-form" method="post" action="<?php echo admin_url('admin.php?page=list_of_game&amp;noheader=true');?>">
		<?php 

			$requestIds = $_GET['requestId'];

			if($_GET['action'] == 'bulk_action'){
				echo "Chú ý: Phần chọn thể loại này sẽ chỉ áp dụng cho những game tạo mới, những game cập nhật sẽ giữ nguyên thể loại cũ!";

				foreach($requestIds as $requestId)
				{
				  echo '<input type="hidden" name="requestId[]" value="'. $requestId. '">';
				  echo '<input type="hidden" name="action" value="bulk_game" />';
				}
			}else{
				echo '<input type="hidden" name="requestId" value="'. $requestIds . '">';
				echo '<input type="hidden" name="action" value="addgame" />';
			}
		?>
	
		<table class="form-table select-category">
		    <tr valign="top">
		    <th scope="row">Thể loại</th>
		    <td><?php wp_category_checklist();?></td>
		    </tr>
		</table>

		<?php submit_button("Phân phối game này"); ?>
  	</form>
</div>
