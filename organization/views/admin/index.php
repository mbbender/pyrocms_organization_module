<section class="title">
	<h4>Organizations List</h4>
</section>

<section class="item">
	<div class="content">	

	<?php echo form_open('admin/organization/action');?>
	
		<?php echo form_hidden('redirect', uri_string()) ?>
	
		<div id="filter-stage">		
			<?php echo $this->load->view('admin/tables/organization') ?>		
		</div>

		<div class="table_action_buttons">	
		  <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))) ?>			
		</div>

	<?php echo form_close();?>
	
	</div>
</section>