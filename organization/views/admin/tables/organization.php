<?php if ( ! empty($organization_list)): ?>

	<table border="0" class="table-list" cellspacing="0">
		<thead>
			<tr>
				<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')) ?></th>
				<th >Name</th>
				<th >Email</th>
				<th>Address</th>
				<th >Phone</th>
				<th>Website</th>
   	            <th>Action</th>
			</tr>
		</thead>
	
		<tfoot>
			<tr>
				<td colspan="7">
					<div class="inner"><?php $this->load->view('admin/partials/pagination') ?></div>
				</td>
			</tr>
		</tfoot>
	
		<tbody>
			<?php foreach ($organization_list as $orgnization): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $orgnization->id) ?></td>
					<td>
						<?php echo $orgnization->org_name?>
					</td>
				
					<td>
						<?php echo $orgnization->org_email;?>
					</td>
					
					<td>
					   <?php echo $orgnization->org_address;?>
					</td>
                    <td>
					   <?php echo $orgnization->org_address;?>
					</td>
				
					<td><?php echo $orgnization->org_website;?></td>
					
					<td class="align-center buttons buttons-small">
											
						<?php echo anchor('admin/organization/edit/'.$orgnization->id, lang('global:edit'), 'class="button edit"') ?>
						<?php echo anchor('admin/organization/delete/'.$orgnization->id, lang('global:delete'), array('class'=>'confirm button delete')) ?>
						
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	
<?php else: ?>

	<div class="no_data">No Organization Registered Yet</div>

<?php endif ?>
