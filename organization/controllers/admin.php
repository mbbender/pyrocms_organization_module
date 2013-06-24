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

    
    protected $validation_rules = array(
		array(
			'field' => 'org_name',
			'label' => 'Name',
			'rules' => 'trim|required|max_length[100]|callback__check_name'
		),
        array(
			'field' => 'org_email',
			'label' => 'Email',
			'rules' => 'trim|required|max_length[100]|valid_email'
		),
        array(
			'field' => 'org_admins',
            'label' => 'Organization Admin',
			'rules' => 'trim|required'			
		),
		array(
			'field' => 'org_address',
            'label' => 'Address',
			'rules' => 'trim|required'			
		),
        array(
			'field' => 'org_phone',
            'label' => 'Phone'			
		),
        array(
			'field' => 'org_fax',
            'label' => 'Fax'			
		),
        array(
			'field' => 'org_website',
            'label' => 'Website',			
		),
        array(
			'field' => 'org_facebook',
            'label' => 'Facebook'		
		),
         array(
			'field' => 'org_twitter',
            'label' => 'Twitter'		
		),
		array(
			'field' => 'id',
			'rules' => 'trim|is_numeric'			
		),
	);
     var $limit = 2; // change the limit for display the pagination results
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
            $this->load->library('form_validation');
            $this->load->model('organization_m');
            $this->load->helper('html');
            $this->lang->load('organization');
            // Set the validation rules
	       	$this->form_validation->set_rules($this->validation_rules);

    }

   /*
	* show all organization for admin
	* @access public
	*/

    public function index()
    {
		
        // Create pagination links
		$total_rows = $this->organization_m->count_all();
		$pagination = create_pagination('admin/organization/index', $total_rows, $this->limit, 4);
		
		// Using this data, get the relevant results
		$organization_list = $this->organization_m->order_by('id', 'desc')->limit($pagination['limit'])->get_all();
	//	print_r($organization_list);
       // die;
		$this->template
            ->title('Organization List')
            ->set('organization_list', $organization_list)
            ->set('pagination', $pagination)
            ->build('admin/index');
    }

    //--------------------------------------------------------------------------

    /*
	* Create organization
	* @access public
	*/
	public function create()
	{
		
		
		$this->form_validation->set_rules($this->validation_rules); // validate the form values

		if ($this->form_validation->run()) {
			
			if ($id = $this->organization_m->insert($_POST)) { // make the post values
				
				$this->session->set_flashdata('success', 'Added Successfully');
			}
			else {
				$this->session->set_flashdata('error', 'Error occured. Please try again or later');
			}

			redirect('admin/organization/index');
		}
		
		$organization = new stdClass();
		 // Get admin list
		$admins = $this->organization_m->get_admins_list();		
		// Loop through each validation rule
		foreach ($this->validation_rules as $rule)
		{
			$organization->{$rule['field']} = set_value($rule['field']);
		}			
			
		$this->template
            ->set('organization', $organization)
            ->set('admins', $admins)
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
		// Get the task
		$organization = $this->organization_m->get($id);
        // Get admin list
		$admins = $this->organization_m->get_admins_list();
		// ID specified?
		$organization or redirect('admin/organization/index');

		$this->form_validation->set_rules('id', 'ID', 'trim|required|is_numeric');
		
		// Validate the results
		if ($this->form_validation->run())
		{		
			$this->organization_m->update($id, $_POST)
				? $this->session->set_flashdata('success', 'Successfully Updated' )
				: $this->session->set_flashdata('error', 'Some error occured');
			
			redirect('admin/organization/index');
		}
		

		$this->template
			->title($this->module_details['name'], 'Edit Organization')
			->set('organization', $organization)
            ->set('admins', $admins)
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
		$id_array = (!empty($id)) ? array($id) : $this->input->post('action_to');
		
		// Delete multiple
		if (!empty($id_array))
		{
			$deleted = 0;
			$to_delete = 0;
			$deleted_ids = array();
			foreach ($id_array as $id)
			{
				if ($this->organization_m->delete($id))
				{
					$deleted++;
					$deleted_ids[] = $id;
				}
				else
				{
					$this->session->set_flashdata('error', 'Error occured while deleting organizations');
				}
				$to_delete++;
			}
			
			if ( $deleted > 0 )
			{
				$this->session->set_flashdata('success', 'Deleted Successfully');
			}
						
		}		
		else
		{
			$this->session->set_flashdata('error', 'Please make sure selection');
		}
		
		redirect('admin/organization/index');
	}
    /**
	 * Callback method that checks the title of the organization
	 *
	 * @param string title The title to check
	 * @return bool
	 */
	public function _check_name($name = '')
	{
		$id = $this->input->post('id');
		if ($this->organization_m->check_title($name, $id)) {
			$this->form_validation->set_message('_check_name', 'Organization name already exist');
			return FALSE;
		}

		return TRUE;
	}
}