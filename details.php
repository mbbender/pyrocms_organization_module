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
			'menu' => 'wisconsin',
            'roles' => array(
                'manage_org', 'create_org', 'delete_org',
                'manage_profile_fields'
            ),
            'sections'=> array(
                'orgs' => array(
                    'name'=>'org:orgs',
                    'uri'=>'admin/organization',
                    'shortcuts'=>array(

                    )
                )

            )
		);

        if($this->permission_m->has_role('create_org','organization')){
            $info['sections']['orgs']['shortcuts']['create'] = array(
                'name'=>'org:new',
                'uri'=>'admin/organization/create',
                'class'=>'add'
            );
        }
        if($this->permission_m->has_role('manage_profile_fields','organization')){
            $info['sections']['org_profile']= array(
                'name'=>'org:profile_fields',
                'uri'=>'admin/organization/fields',
                'shortcuts' => array(
                    'create' => array(
                        'name' 	=> 'org:add_field',
                        'uri' 	=> 'admin/organization/fields/create',
                        'class' => 'add'
                    )
                )
            );
        }

	
		return $info;
	}

	public function install()
	{
        $this->load->driver('Streams');
        $this->load->language('organization/organization');

        $this->dbforge->drop_table('org_orgs');
        $this->dbforge->drop_table('org_profiles');
        $this->dbforge->drop_table('org_orgs_profiles');

        $this->load->driver('Streams');
        $this->streams->utilities->remove_namespace('org');


        if ($this->db->table_exists('data_streams'))
        {
            $this->db->where('stream_namespace', 'org')->delete('data_streams');
        }


        if ( ! $this->streams->streams->add_stream('lang:org:orgs', 'orgs', 'org', 'org_', null)) return false;
        if ( ! $profile_stream_id = $this->streams->streams->add_stream('lang:org:profile_fields', 'profiles', 'org', 'org_', null)) return false;

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
                'name' => 'Admins',
                'slug' => 'admins',
                'namespace' => 'org',
                'type' => 'multiple',
                'extra' => array('choose_stream' => $this->streams->streams->get_stream('profiles','users')->id,'choose_ui'=>'multi'),
                'assign' => 'orgs'
            ),
            array(
                'name' => 'Members',
                'slug' => 'members',
                'namespace' => 'org',
                'type' => 'multiple',
                'extra' => array('choose_stream' => $this->streams->streams->get_stream('profiles','users')->id,'choose_ui'=>'multi'),
                'assign' => 'orgs'
            ),
            array(
                'name' => 'Photo',
                'slug' => 'org_profile_photo',
                'namespace' => 'org',
                'type' => 'image',
                'assign' => 'profiles',
                'title_column' => false,
                'required' => false
            ),
            array(
                'name' => 'Description',
                'slug' => 'org_profile_description',
                'namespace' => 'org',
                'type' => 'textarea',
                'assign' => 'profiles'
            ),
            array(
                'name' => 'Address',
                'slug' => 'address',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 255),
                'assign' => 'profiles'
            ),
            array(
                'name' => 'Phone',
                'slug' => 'phone',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 25),
                'assign' => 'profiles'
            ),
            array(
                'name' => 'Fax',
                'slug' => 'fax',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 25),
                'assign' => 'profiles'
            ),
            array(
                'name' => 'Website',
                'slug' => 'website',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 150),
                'assign' => 'profiles'
            ),
            array(
                'name' => 'Facebook',
                'slug' => 'facebook',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 150),
                'assign' => 'profiles'
            ),
            array(
                'name' => 'Twitter',
                'slug' => 'twitter',
                'namespace' => 'org',
                'type' => 'text',
                'extra' => array('max_length' => 150),
                'assign' => 'profiles'
            )
        );


        $this->streams->fields->add_fields($fields);

        $this->streams->streams->update_stream('orgs', 'org', array(
            'view_options' => array(
                'id',
                'name',
                'email'
            )
        ));

        $this->streams->streams->update_stream('profiles', 'org', array(
            'view_options' => array(
                'id',
                'description',
                'phone'
            )
        ));

        $org_fields = array(
            'org_profile_id' => array('type' => 'INT', 'constraint' => 11)
        );

        $this->dbforge->add_column('org_orgs', $org_fields);

        // Add default user group for Org Admins and set permissions to only Organization management
        // Not tested, but decided this was not the responsibility of this module. - MBB
        /*
        $this->db->select('name')->from('groups')->where('name=org-admin');
        $query = $this->db->get();
        if($query->num_rows() == 0){
            $data = array(
                'name' => 'org-admin',
                'description' => 'Organization Admin'
            );
            $this->db->insert('groups',$data);
            $group_id = $this->db->insert_id();

            $data = array(
                'group_id' => $group_id,
                'module' =>'organization',
                'roles' => '{"manage_org":"1"}'
            );
            $this->db->set('group_id',$group_id);
            $this->db->set('module','organization');
            $this->db->set('roles','{"manage_org":"1"}',FALSE);
            $this->db->insert('permissions');
        }
        */
        return true;
	}

	public function uninstall()
	{
        $this->load->driver('Streams');
        $this->streams->utilities->remove_namespace('org');
        $this->dbforge->drop_table('org_orgs');
        $this->dbforge->drop_table('org_profiles');
        $this->dbforge->drop_table('org_orgs_profiles');
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