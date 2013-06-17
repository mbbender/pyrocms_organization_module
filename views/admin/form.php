<section class="title">
	<h4>Organization</h4>
</section>

	<section class="item">
	<div class="content">
	<?php echo form_open($this->uri->uri_string(), 'class="form_inputs"') ?>

		<?php echo form_hidden('id', $organization->id) ?>
		<ul class="fields">
			
			<li>
				<label for="org_name">Name:</label>
				<div class="input">
					<?php echo form_input('org_name', isset($organization->org_name) ? $organization->org_name : '', 'maxlength="100"') ?>
				</div>
			</li>
            <li>
				<label for="org_email">Email:</label>
				<div class="input">
					<?php echo form_input('org_email', isset($organization->org_email) ? $organization->org_email:'', 'maxlength="100"') ?>
				</div>
			</li>
			
			<li>
				<label for="org_admin">Organization Admin:</label>
				<div class="input">
					<?php echo form_dropdown('org_admins', $admins,  isset($organization->org_admins) ? $organization->org_admins : '') ?>
				</div>
			</li>
			

			<li>
				<label for="user_website">Photo:</label>
				<div class="input">
					<input type="file" name="org_photo" size="20" />
				</div>
			</li>
            
            <li>
				<label for="user_website">Description:</label>
				<div class="input">
					<?php echo form_textarea(array('name'=>'org_description', 'value' => isset($organization->org_description) ? $organization->org_description:'', 'rows' => 5)) ?>
				</div>
			</li>

			<li>
				<label for="user_email">Address:</label>
				<div class="input">
					<?php echo form_input('org_address', isset($organization->org_address) ? $organization->org_address:''); ?>
				</div>
			</li>
            <li>
				<label for="user_email">Phone:</label>
				<div class="input">
					<?php echo form_input('org_phone', isset($organization->org_phone) ? $organization->org_phone:'');?>
				</div>
			</li>
            <li>
				<label for="user_email">Fax:</label>
				<div class="input">
					<?php echo form_input('org_fax', isset($organization->org_fax) ? $organization->org_fax:''); ?>
				</div>
			</li>
            <li>
				<label for="user_email">Website:</label>
				<div class="input">
					<?php echo form_input('org_website', isset($organization->org_website) ? $organization->org_website : ''); ?>
				</div>
			</li>
            <li>
				<label for="user_email">Facebook:</label>
				<div class="input">
					<?php echo form_input('org_facebook', isset($organization->org_facebook) ? $organization->org_facebook : ''); ?>
				</div>
			</li>
            <li>
				<label for="user_email">Twitter:</label>
				<div class="input">
					<?php echo form_input('org_twitter', isset($organization->org_twitter) ? $organization->org_twitter : ''); ?>
				</div>
			</li>
		</ul>

		<div class="buttons float-right padding-top">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )) ?>
		</div>

	<?php echo form_close() ?>
	</div>
</section>