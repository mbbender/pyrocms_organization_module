<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Organization extends Module {

	public $version = '1.0';
 
    public function info() {
        $this->load->language('organization/permission');
		$info = array(
			'name' => array(
				'en'=>'Organizations',
			),
			'description' => array(
				'en'=> 'Groups on a mission to promote better health.'
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'Wisconsin',
            'roles' => array(
                'manage_org', 'create_org', 'delete_org',
                'manage_profile_fields'
            ),
            'sections'=> array(
                'orgs' => array(
                    'name'=>'org:orgs',
                    'uri'=>'admin/organization',
                    'shortcuts'=>array(
                        'create' => array(
                            'name'=>'org:new',
                            'uri'=>'admin/organization/create',
                            'class'=>'add'
                        )
                    )
                ),
                'org_profile' => array(
                    'name'=>'org:profile_fields',
                    'uri'=>'admin/organization/fields',
                    'shortcuts' => array(
                        'create' => array(
                            'name' 	=> 'org:add_field',
                            'uri' 	=> 'admin/organization/fields/create',
                            'class' => 'add'
                        )
                    )
                )
            )
		);

	
		return $info;
	}

	public function install()
	{
        $this->load->driver('Streams');
        $this->load->language('organization/organization');

        $this->dbforge->drop_table('org_orgs');
        $this->dbforge->drop_table('org_profile');
        $this->dbforge->drop_table('org_members_orgs');

        $this->load->driver('Streams');
        $this->streams->utilities->remove_namespace('org');


        if ($this->db->table_exists('data_streams'))
        {
            $this->db->where('stream_namespace', 'org')->delete('data_streams');
        }


        if ( ! $this->streams->streams->add_stream('lang:org:orgs', 'orgs', 'org', 'org_', null)) return false;
        if ( ! $profile_stream_id = $this->streams->streams->add_stream('lang:org:profile_fields', 'profile', 'org', 'org_', null)) return false;
        if ( ! $member_org_stream_id = $this->streams->streams->add_stream('lang:org:member_org','member_org','org','org_',null)) return false;

        //$users_stream = $this->streams->streams->get_stream('users');

        // Add some fields
        $fields = array(
            array(
                'name' => 'Name',
                'slug' => 'name',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 255),
                'assign' => 'orgs',
                'title_column' => true,
                'required' => true,
                'unique' => true
            ),
            array(
                'name' => 'Email',
                'slug' => 'email',
                'namespace' => 'org',
                'type' => 'email',
                'assign' => 'orgs',
                'required' => false
            ),
            array(
                'name' => 'Photo',
                'slug' => 'org_profile_photo',
                'namespace' => 'org',
                'type' => 'image',
                'assign' => 'profile',
                'title_column' => false,
                'required' => false
            ),
            array(
                'name' => 'Description',
                'slug' => 'org_profile_description',
                'namespace' => 'org',
                'type' => 'textarea',
                'assign' => 'profile'
            ),
            array(
                'name' => 'Address',
                'slug' => 'address',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 255),
                'assign' => 'profile'
            ),
            array(
                'name' => 'Phone',
                'slug' => 'phone',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 25),
                'assign' => 'profile'
            ),
            array(
                'name' => 'Fax',
                'slug' => 'fax',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 25),
                'assign' => 'profile'
            ),
            array(
                'name' => 'Website',
                'slug' => 'website',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 150),
                'assign' => 'profile'
            ),
            array(
                'name' => 'Facebook',
                'slug' => 'facebook',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 150),
                'assign' => 'profile'
            ),
            array(
                'name' => 'Twitter',
                'slug' => 'twitter',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 150),
                'assign' => 'profile'
            ),
            array(
                'name' => 'User',
                'slug' => 'user_id',
                'namespace' => 'org',
                'type' => 'relationship',
                'extra' => array('choose_stream' => 'profiles'),
                'assign' => 'member_org'
            ),
            array(
                'name' => 'Organization',
                'slug' => 'org_id',
                'namespace' => 'org',
                'type' => 'relationship',
                'extra' => array('choose_stream' => 'orgs'),
                'assign' => 'member_org'
            ),
            array(
                'name' => 'Role',
                'slug' => 'role',
                'namespace' => 'org',
                'type' => 'choice',
                'extra' => array('choice_type' => 'dropdown','choice_data'=>array('Unapproved','Basic','Org Admin','Banned'),'default_value'=>'Basic'),
                'assign' => 'member_org'
            )
        );


        $this->streams->fields->add_fields($fields);

        $this->streams->streams->update_stream('orgs', 'org', array(
            'view_options' => array(
                'id',
                'name'
            )
        ));

        $this->streams->streams->update_stream('profile', 'org', array(
            'view_options' => array(
                'id',
                'faq_category_title'
            )
        ));

        $org_fields = array(
            'org_profile_id' => array('type' => 'INT', 'constraint' => 11)
        );

        $this->dbforge->add_column('org_orgs', $org_fields);



        // Role can be unapproved, basic, admin
        // Admin can approve unapproved users and edit org profile data.
        /*
        $org_members_orgs_fields = array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'org_id' => array('type' => 'INT', 'constraint' => 11),
            'user_id' => array('type' => 'INT', 'constraint' => 11),
            'role' => array('type' => 'varchar', 'constraint' => 25, 'default'=>'unapproved')
        );
        $this->dbforge->add_field($org_members_orgs_fields);
        $this->dbforge->add_key('id',TRUE);
        $this->dbforge->create_table('org_members_orgs');
        */
        // todo: Make a unique key for org_id,user_id combination


        return true;
	}

	public function uninstall()
	{
        $this->load->driver('Streams');
        $this->streams->utilities->remove_namespace('org');
        $this->dbforge->drop_table('org_orgs');
        $this->dbforge->drop_table('org_profile');
        $this->dbforge->drop_table('org_members_orgs');
		return true;
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