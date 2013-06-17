<section class="title">
    <?php if ($this->method === 'create'): ?>
        <h4><?php echo lang('org:new') ?></h4>
        <?php echo form_open_multipart(uri_string(), 'class="crud" autocomplete="off"') ?>

    <?php else: ?>
        <h4><?php echo sprintf(lang('org:edit'), $organization->name) ?></h4>
        <?php echo form_open_multipart(uri_string(), 'class="crud"') ?>
        <?php echo form_hidden('row_edit_id', isset($organization->row_edit_id) ? $organization->row_edit_id : $organization->id); ?>
    <?php endif ?>
</section>

<section class="item">
    <div class="content">

        <div class="tabs">

            <ul class="tab-menu">
                <li><a href="#user-basic-data-tab"><span><?php echo lang('org:basic_data_label') ?></span></a></li>
                <li><a href="#user-profile-fields-tab"><span><?php echo lang('org:profile_data_label') ?></span></a></li>
                <?php if($this->current_user->group === 'admin' || $this->current_user->group === 'org-admin'):?>
                    <li><a href="#org-admin-tab"><span><?php echo lang('org:membership') ?></span></a></li>
                <?php endif;?>
            </ul>

            <!-- Content tab -->
            <div class="form_inputs" id="user-basic-data-tab">
                <fieldset>
                    <ul>
                        <?php foreach($org_fields as $field): ?>
                            <?php if($field['field_slug'] === 'admins' && $this->current_user->group !== 'admin') continue; ?>

                            <li>
                                <label for="<?php echo $field['field_slug'] ?>">
                                    <?php echo (lang($field['field_name'])) ? lang('profile:'.$field['field_name']) : $field['field_name'];  ?>
                                    <?php if ($field['required']){ ?> <span>*</span><?php } ?>
                                </label>
                                <div class="input">
                                    <?php
                                    if($field['field_slug'] === 'email'){ echo '<input type="text" name="email" value="'.$field['value']['email_address'].'" id="email"/>';}
                                    else { echo $field['input']; }
                                    ?>
                                </div>
                            </li>
                        <?php endforeach ?>

                    </ul>
                </fieldset>
            </div>

            <div class="form_inputs" id="user-profile-fields-tab">

                <fieldset>
                    <ul>
                        <?php foreach($profile_fields as $field): ?>
                            <li>
                                <label for="<?php echo $field['field_slug'] ?>">
                                    <?php echo (lang($field['field_name'])) ? lang('profile:'.$field['field_name']) : $field['field_name'];  ?>
                                    <?php if ($field['required']){ ?> <span>*</span><?php } ?>
                                </label>
                                <div class="input">
                                    <?php echo $field['input'] ?>
                                </div>
                            </li>
                        <?php endforeach ?>

                    </ul>
                </fieldset>
            </div>


            <div class="form_inputs" id="org-admin-tab">
                <fieldset>
                    <ul>
                        <li><h4>Manage Membership Coming Soon</h4></li>

                    </ul>
                </fieldset>
            </div>

        </div>

        <div class="buttons">
            <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel') )) ?>
        </div>

        <?php echo form_close() ?>

    </div>
</section>