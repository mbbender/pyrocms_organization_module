<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Example Plugin
 *
 * Quick plugin to demonstrate how things work
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Addon\Plugins
 * @copyright	Copyright (c) 2009 - 2010, PyroCMS
 */
class Plugin_Organization extends Plugin
{
	public $version = '1.0.0';

	public $name = array(
		'en'	=> 'Organization'
	);

	public $description = array(
		'en'	=> 'Work with organizations via this plugin.'
	);

	/**
	 * Returns a PluginDoc array that PyroCMS uses 
	 * to build the reference in the admin panel
	 *
	 * All options are listed here but refer 
	 * to the Blog plugin for a larger example
	 *
	 * @return array
	 */
	public function _self_doc()
	{
		$info = array(
			'hello' => array(
				'description' => array(// a single sentence to explain the purpose of this method
					'en' => 'A simple "Hello World!" example.'
				),
				'single' => true,// will it work as a single tag?
				'double' => false,// how about as a double tag?
				'variables' => '',// list all variables available inside the double tag. Separate them|like|this
				'attributes' => array(
					'name' => array(// this is the name="World" attribute
						'type' => 'text',// Can be: slug, number, flag, text, array, any.
						'flags' => '',// flags are predefined values like asc|desc|random.
						'default' => 'World',// this attribute defaults to this if no value is given
						'required' => false,// is this attribute required?
					),
				),
			),
            'members' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Display members of an organization'
                ),
                'single' => false,// single tag or double tag (tag pair)
                'double' => true,
                'variables' => 'display_name',// the variables available inside the double tags
                'attributes' => array(// an array of all attributes
                    'category' => array(// the attribute name. If the attribute name is used give most common values as separate attributes
                        'type' => 'slug',// Can be: slug, number, flag, text, any. A flag is a predefined value.
                        'flags' => '',// valid flag values that the plugin will recognize. IE: asc|desc|random
                        'default' => '',// the value that it defaults to
                        'required' => false,// is this attribute required?
                    ),
                    'limit' => array(
                        'type' => 'number',
                        'flags' => '',
                        'default' => '',
                        'required' => false,
                    ),
                    'offset' => array(
                        'type' => 'number',
                        'flags' => '',
                        'default' => '0',
                        'required' => false,
                    ),
                    'order-by' => array(
                        'type' => 'column',
                        'flags' => '',
                        'default' => 'created_on',
                        'required' => false,
                    ),
                    'order-dir' => array(
                        'type' => 'flag',
                        'flags' => 'asc|desc|random',
                        'default' => 'asc',
                        'required' => false,
                    ),
                ),
            ),
		);
	
		return $info;
	}

	/**
	 * Hello
	 *
	 * Usage:
	 * {{ example:hello }}
	 *
	 * @return string
	 */
	function hello()
	{
		$name = $this->attribute('name', 'World');
		
		return 'Hello '.$name.'!';
	}

    /**
     * Member List
     *
     * Creates a list of members for an organization. Must set org_id
     *
     * Usage:
     * {{ organization:members org_id="1" }}
     *		<h2>{{ display_name }}</h2>
     * {{ /organization:members }}
     *
     * @param	array
     * @return	array
     */
    public function members()
    {
        $org_id =$this->attribute('org_id');
        if( empty($org_id) ) return "org_id attribute is required.";

        $this->load->driver('Streams');


        $this->db->select('org_profiles.*,org_orgs.*,profiles.*');
        $this->db->from('org_members_orgs_profiles');
        $this->db->join('org_orgs','org_orgs.id = org_members_orgs_profiles.row_id');
        $this->db->join('org_profiles','org_profiles.id = org_orgs.org_profile_id');
        $this->db->join('profiles','org_members_orgs_profiles.profiles_id = profiles.id');
        $this->db->where('org_members_orgs_profiles.row_id', $org_id);
        $query = $this->db->get();

        // Get our members.
        $members['entries'] = $query->result();

        if ($members['entries'])
        {
            // Process members.
            // Each member needs some special treatment.
            foreach ($members['entries'] as &$member)
            {
                // Full URL for convenience.
                $member->url = site_url('user/'.$member->id);
            }
        }

        // {{ entries }} Bypass.
        // However, users can use {{ entries }} if using pagination.
        $loop = false;

        if (preg_match('/\{\{\s?entries\s?\}\}/', $this->content()) == 0)
        {
            $members = $members['entries'];
            $loop = true;
        }

        // Return our content.
        return $this->streams->parse->parse_tag_content($this->content(), $members, 'orgs', 'org', $loop);
    }
}

/* End of file example.php */