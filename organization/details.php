<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Organization extends Module {

	public $version = '1.0';
 
    public function info() {
		$info = array(
			'name' => array(
				'en'=>'Organization',
			),
			'description' => array(
				'en'=> 'All about organizations.'
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content',
		);
		

		$info['shortcuts'][] = array(
			'name' => 'org:org_list',
			'uri' => 'admin/organization/index',
			'class' => ''
		);
		$info['shortcuts'][] = array(
			'name' => 'add_new_org',
			'uri' => 'admin/organization/create',
			'class' => 'add'
		);
	
		return $info;
	}

	public function install()
	{
        $this->dbforge->drop_table('organization');
        $this->dbforge->drop_table('organization_users');

		$tables = array(
			'organization' => array(
				'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
				'org_name' => array('type' => 'VARCHAR', 'constraint' => 200, 'key'=>true),
				'slug' => array('type' => 'VARCHAR', 'constraint' => 200),
				'org_email' => array('type' => 'VARCHAR', 'constraint' => 200),               
				'org_admins' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'org_photo' => array('type' => 'VARCHAR', 'constraint' => 200),
                'org_description' => array('type' => 'text'), 
                'org_address' => array('type' => 'VARCHAR', 'constraint' => 200),
                'org_phone' => array('type' => 'VARCHAR', 'constraint' => 200),
                'org_fax' => array('type' => 'VARCHAR', 'constraint' => 200),
                'org_website' => array('type' => 'VARCHAR', 'constraint' => 200),
                'org_facebook' => array('type' => 'VARCHAR', 'constraint' => 255),
                'org_twitter' => array('type' => 'VARCHAR', 'constraint' => 255),
				'created_date' => array('type' => 'INT', 'constraint' => 11),
			),
			'organization_users' => array(
				'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
				'organization_id' => array('type' => 'INT', 'constraint' => 11),
				'user_id' => array('type' => 'INT', 'constraint' => 11),
			));
        return $this->install_tables($tables);
	}

	public function uninstall()
	{
		$this->dbforge->drop_table('organization');
        $this->dbforge->drop_table('organization_users');
		return TRUE;
	}

	public function upgrade($old_version)
	{
		// Nothing here yet.
		return TRUE;
	}

	public function help()
	{
		// Return a string containing help info
		// You could include a file and return it here.
		return "No documentation has been added for this module.";
	}
}
/* End of file details.php */