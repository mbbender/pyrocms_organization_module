<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin controller
 *
 * @author	Bender Dev Team
 * @package 	PyroCMS 
 * @category	Modules
 */
class Admin extends Admin_Controller
{

    protected $section = 'orgs';

    //--------------------------------------------------------------------------    
    /**
     * Constructor method
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        // Load the required libraries, models, etc
        $this->lang->load('organization');
        $this->load->helper('html');
        $this->load->driver('Streams');
        $this->load->model('permissions/permission_m');

        // Validate User is Admin or Org Admin with privileges to this organization.

    }

   /*
	* show all organization for admin
	* @access public
	*/

    public function index()
    {
        role_or_die('organization', 'manage_org');

        // The extra array is where most of our
        // customization options go.
        $extra = array();

        // The title can be a string, or a language
        // string, prefixed by lang:
        $extra['title'] = 'lang:org:orgs';

        // We can customize the buttons that appear
        // for each row. They point to our own functions
        // elsewhere in this controller. -entry_id- will
        // be replaced by the entry id of the row.

        $extra['buttons'] = array();

        if($this->permission_m->has_role(array('manage_org'),'organization')){
            array_push($extra['buttons'],
                array(
                    'label' => lang('global:edit'),
                    'url' => 'admin/organization/edit/-entry_id-'
                )
            );
        }

        if($this->permission_m->has_role(array('delete_org'),'organization')){
            array_push($extra['buttons'],
                array(
                    'label' => lang('global:delete'),
                    'url' => 'admin/organization/delete/-entry_id-',
                    'confirm' => true
                )
            );
        }

        // In this example, we are setting the 5th parameter to true. This
        // signals the function to use the template library to build the page
        // so we don't have to. If we had that set to false, the function
        // would return a string with just the form.
        $this->streams->cp->entries_table('orgs', 'org', 10, 'admin/organization/index', true, $extra);
    }

    //--------------------------------------------------------------------------

    /*
	* Create organization
	* @access public
	*/
	public function create()
	{
        role_or_die('organization', 'create_org');

        // Extra validation for basic data
        //$this->validation_rules['name']['rules'] .= '|required';

        // Get the profile fields validation array from streams
        $org_validation = $this->streams->streams->validation_array('orgs', 'org');
        $profile_validation = $this->streams->streams->validation_array('profiles', 'org');

        // Set the validation rules
        $this->form_validation->set_rules(array_merge($profile_validation, $org_validation));



        // keep non-admins from creating admin accounts. If they aren't an admin then force new one as a "user" account
        //$group_id = ($this->current_user->group !== 'admin' and $group_id == 1) ? 2 : $group_id;

        // Get user profile data. This will be passed to our
        // streams insert_entry data in the model.
        $assignments = $this->streams->streams->get_assignments('profiles', 'org');
        $profile_data = array();

        foreach ($assignments as $assign)
        {
            $profile_data[$assign->field_slug] = $this->input->post($assign->field_slug);
        }

        // Some stream fields need $_POST as well.
        $profile_data = array_merge($profile_data, $_POST);



        if ($this->form_validation->run() !== false)
        {

            if ($profile_id = $this->streams->entries->insert_entry($profile_data, 'profiles', 'org'))
            {
                $extra['org_profile_id'] = $profile_id;

                if ($org_id = $this->streams->entries->insert_entry($profile_data, 'orgs', 'org',array(),$extra))
                {
                    $this->session->set_flashdata('success', sprintf($this->lang->line('org:submit_success'), $this->input->post('title')));

                }
                else
                {
                    $this->session->set_flashdata('error', lang('org:submit_error'));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('org:submit_error'));
            }

            // Redirect back to the form or main page
            ($this->input->post('btnAction') == 'save_exit') ? redirect('admin/organization') : redirect('admin/organization/create');
        }
        else
        {
            // Go through all the known fields and get the post values
            if ($_POST)
            {
                $member = (object) $_POST;
            }
        }


        if ( ! isset($member))
        {
            $member = new stdClass();
        }


        $stream_fields = $this->streams_m->get_stream_fields($this->streams_m->get_stream_id_from_slug('orgs', 'org'));
        // Set Values
        $org_values = $this->fields->set_values($stream_fields, null, 'new');
        // Run stream field events
        $this->fields->run_field_events($stream_fields, array(), $org_values);


        $stream_fields = $this->streams_m->get_stream_fields($this->streams_m->get_stream_id_from_slug('profiles', 'org'));
        // Set Values
        $values = $this->fields->set_values($stream_fields, null, 'new');
        // Run stream field events
        $this->fields->run_field_events($stream_fields, array(), $values);

        $assign_admin_view_extra = array(
            'return' => 'admin/organization',
            'success_message' => lang('org:add_admin_success'),
            'failure_message' => lang('org:add_admin_failure'),
            'title' => 'lang:org:add_admin',
        );
        $this->template
            ->title($this->module_details['name'], lang('org:add_title'))
            ->set('organization', $member)
            ->set('org_fields', $this->streams->fields->get_stream_fields('orgs', 'org', $org_values))
            ->set('profile_fields', $this->streams->fields->get_stream_fields('profiles', 'org', $values))
            ->build('admin/form');



		
	}
    /**
	 * Edit method, edit an existing organization
	 * 
	 * @param int id The ID of the organization to edit
	 * @return void
	 */
	public function edit($id = 0)
	{
        role_or_die('organization', 'manage_org');

        $id or redirect('admin/organization');

        $org = $this->streams->entries->get_entry($id,'orgs','org');
        $profile_fields = $this->streams->entries->get_entry($org->org_profile_id,'profiles','org');

        // Get the validation for our custom blog fields.
        $org_validation = $this->streams->streams->validation_array('orgs', 'org','edit',array(), $id);
        $profile_validation = $this->streams->streams->validation_array('profiles', 'org', 'edit', array(), $org->org_profile_id);

        // Merge and set our validation rules
        $this->form_validation->set_rules(array_merge($org_validation, $profile_validation));

        if ($this->form_validation->run())
        {

            if ($this->streams->entries->update_entry($id, $_POST, 'orgs', 'org') && $this->streams->entries->update_entry($org->org_profile_id, $_POST, 'profiles', 'org'))
            {
                $this->session->set_flashdata(array('success' => sprintf(lang('org:edit_success'), $this->input->post('name'))));

                // Blog article has been updated, may not be anything to do with publishing though
                Events::trigger('org_updated', $id);

            }
            else
            {
                $this->session->set_flashdata('error', lang('org:edit_error'));
            }

            // Redirect back to the form or main page
            ($this->input->post('btnAction') == 'save_exit') ? redirect('admin/organization') : redirect('admin/organization/edit/'.$id);
        }

        // Go through all the known fields and get the post values
        $org_entry = $this->streams->entries->get_entry($id, 'orgs', 'org', false, false);
        $org_assignments = $this->streams->streams->get_assignments('orgs', 'org');
        $org_data = array();
        foreach ($org_assignments as $assign)
        {
            if (isset($_POST[$assign->field_slug]))
            {
                $org_data[$assign->field_slug] = $this->input->post($assign->field_slug);
            }
            elseif (isset($org->{$assign->field_slug}))
            {
                $org_data[$assign->field_slug] = $org->{$assign->field_slug};
            }
        }

        $assignments = $this->streams->streams->get_assignments('profiles', 'org');
        $profile_data = array();
        foreach ($assignments as $assign)
        {
            if (isset($_POST[$assign->field_slug]))
            {
                $profile_data[$assign->field_slug] = $this->input->post($assign->field_slug);
            }
            elseif (isset($profile_fields->{$assign->field_slug}))
            {
                $profile_data[$assign->field_slug] = $profile_fields->{$assign->field_slug};
            }
        }

        $stream_fields = $this->streams_m->get_stream_fields($this->streams_m->get_stream_id_from_slug('orgs', 'org'));
        // Set Values
        $values = $this->fields->set_values($stream_fields, null, 'edit');
        // Run stream field events
        $this->fields->run_field_events($stream_fields, array(), $values);


        $stream_fields = $this->streams_m->get_stream_fields($this->streams_m->get_stream_id_from_slug('profiles', 'org'));
        // Set Values
        $values = $this->fields->set_values($stream_fields, null, 'edit');
        // Run stream field events
        $this->fields->run_field_events($stream_fields, array(), $values);



        $this->template
            ->title($this->module_details['name'], lang('org:edit_title'))
            ->set('organization', $org)
            ->set('org_fields', $this->streams->fields->get_stream_fields('orgs', 'org', $org_data, $id))
            ->set('profile_fields',$this->streams->fields->get_stream_fields('profiles', 'org', $profile_data, $org->org_profile_id))
            ->build('admin/form');
	}
    /**
	 * Delete method, deletes an existing organization (obvious isn't it?)
	 * 
	 * @param int id The ID of the organization to edit
	 * @return void
	 */
	public function delete($id = 0)
	{
        role_or_die('organization', 'delete_org');

        $org = $this->streams->entries->get_entry($id,'orgs','org');
        $this->streams->entries->delete_entry($org->org_profile_id, 'profiles', 'org');
        $this->streams->entries->delete_entry($id, 'orgs', 'org');
        $this->session->set_flashdata('error', sprintf(lang('org:deleted'),$org->name));

        redirect('admin/organization/');
	}


}