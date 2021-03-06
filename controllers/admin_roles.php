<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin User Fields
 *
 * Manage custom organization fields.
 *
 * @author 		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Modules\Users\Controllers
 */
class Admin_roles extends Admin_Controller {

	protected $section = 'org_profile';

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

        $this->lang->load('organization');
        $this->load->helper('html');
        $this->load->driver('Streams');

		// If they cannot administer profile fields,
		// then they can't access anythere here.
		role_or_die('organization', 'manage_profile_fields');
	}

	// --------------------------------------------------------------------------
	
	/**
	 * List out profile fields
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function index()
	{
		$buttons = array(
			array(
				'url'		=> 'admin/organization/roles/edit/-assign_id-',
				'label'		=> $this->lang->line('global:edit')
			),
			array(
				'url'		=> 'admin/organization/roles/delete/-assign_id-',
				'label'		=> $this->lang->line('global:delete'),
				'confirm'	=> true
			)
		);

		$this->template->title(lang('org:profile_fields'));

		$this->streams->cp->assignments_table(
								'member_org',
								'org',
								Settings::get('records_per_page'),
								'admin/organization/fields/index',
								true,
								array('buttons' => $buttons));
	}

	// --------------------------------------------------------------------------

	/**
	 * Create a new profile field
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function create()
	{
		$extra['title'] 		= lang('streams:new_field');
		$extra['show_cancel'] 	= true;
		$extra['cancel_uri'] 	= 'admin/organization/fields';

		$this->streams->cp->field_form('profile', 'org', 'new', 'admin/organization/fields', null, array(), true, $extra);
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete a profile field
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function delete()
	{
		if ( ! $assign_id = $this->uri->segment(5))
		{
			show_error(lang('streams:cannot_find_assign'));
		}
	
		// Tear down the assignment
		if ( ! $this->streams->cp->teardown_assignment_field($assign_id))
		{
		    $this->session->set_flashdata('notice', lang('org:profile_delete_failure'));
		}
		else
		{
		    $this->session->set_flashdata('success', lang('org:profile_delete_success'));
		}
	
		redirect('admin/organization/fields');
	}

	// --------------------------------------------------------------------------

	/**
	 * Edit a profile field
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function edit()
	{
		if ( ! $assign_id = $this->uri->segment(5))
		{
			show_error(lang('streams:cannot_find_assign'));
		}

		$extra['title'] 		= lang('streams:edit_field');
		$extra['show_cancel'] 	= true;
		$extra['cancel_uri'] 	= 'admin/organization/fields';

		$this->streams->cp->field_form('profile', 'org', 'edit', 'admin/organization/fields', $assign_id, array(), true, $extra);
	}
}