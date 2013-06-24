<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * Organization
 *
 * @author   Bender Dev Team
 */
class Organization_m extends MY_Model
{
	
	
	public function __construct() {
		parent::__construct();

		$this->_table = 'organization';
		$this->load->helper('date');
	}

	/**
	 * Insert a new organization into the database
	 *
	 * @param array $input The data to insert
	 * @return string
	 */
	public function insert($input = array(), $skip_validation = false)
     {
		
	   return	parent::insert(array(
				'org_name'=>$input['org_name'],
				'org_email'=>$input['org_email'],
                'org_admins'=>$input['org_admins'],
                'org_photo'=>$input['org_photo'],
                'org_description'=>$input['org_description'],
                'org_address'=>$input['org_address'],
                'org_phone'=>$input['org_phone'],
                'org_fax'=>$input['org_fax'],
                'org_website'=>$input['org_website'],
                'org_facebook'=>$input['org_facebook'],
                'org_twitter'=>$input['org_twitter'],
				'created_date'=>now(),
				'slug'=>url_title(strtolower(convert_accented_characters($input['org_name'])))
			));

		
	}


	/**
	 * Update an existing organization
	 *
	 * @param int $id The ID of the organization
	 * @param array $input The data to update
	 * @return bool
	 */
	public function update($id, $input, $skip_validation = false) {
		return parent::update($id, array(
				'org_name'=>$input['org_name'],
				'org_email'=>$input['org_email'],
                'org_admins'=>$input['org_admins'],
                'org_photo'=>$input['org_photo'],
                'org_description'=>$input['org_description'],
                'org_address'=>$input['org_address'],
                'org_phone'=>$input['org_phone'],
                'org_fax'=>$input['org_fax'],
                'org_website'=>$input['org_website'],
                'org_facebook'=>$input['org_facebook'],
                'org_twitter'=>$input['org_twitter'],
				'slug'=>url_title(strtolower(convert_accented_characters($input['org_name'])))
			));
	}

	/**
	 * Callback method for validating the title
	 *
	 * @param string $title The organization name to validate
	 * @param int $id The id to check
	 * @return mixed
	 */
	public function check_title($title = '', $id = 0) {
		return (bool) $this->db->where('org_name', $title)
		->where('id != ', $id)
		->from($this->_table)
		->count_all_results();
	}

	
	/*
	* Remove organization
	* @access public
	* @param int question id 
	*/
	public function delete_organization( $id = 0)
	{
		if($id==0) // id specified ?
		{
			return false;
		}
		
		return $this->db->delete($this->_table, array('id'=>$id)); // just remove the organization
	}

	/**
	* Get organization info for slug
	* @accees public
	* @param organization slug and int organization id
	*/
	public function get_organization_slug($slug = '', $org_id = 0)
	{
		if(empty($slug)) // id Specified?
        {
		  return false;
        }
		return $this->db->get_where($this->_table, array('slug'=>$slug, 'id'=>$org_id))->row();
		
	}
    
   	/**
	* Get Admin list
	* @accees public
	* return admins array
	*/
	public function get_admins_list()
	{
		// get admin list group=1 
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('profiles', 'profiles.user_id = users.id');

        $query = $this->db->get();
		$array = array();
		if ($query->num_rows() > 0 ) {
			foreach ($query->result() as $row)
             {
				$array[$row->id] =  $row->display_name;
			}
		}
		return $array;		
		
	}
}