<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Structure Sidebar Accessory Class for EE2
*
* @package NDG Sidebar Structure
* @author Nico De Gols <nicodegols@me.com>
* @copyright Copyright (c) 2010 Nico De Gols
*/

class Ndg_sidebarstructure_acc {

	var $name			= 'NDG Sidebar Structure';
	var $id				= 'ndg_sidebarstructure';
	var $version		= '1.0';
	var $description	= 'An EE2 Accessory which adds the "Structure" addon tree in the control panel sidebar';
	var $sections		= array();

	var $structure;
	var $installed = FALSE;

	/**
	 * Constructor
	 */
	function Ndg_sidebarstructure_acc()
	{		
		$this->EE =& get_instance();
		
		$results = $this->EE->db->query("SELECT module_id FROM ".$this->EE->db->dbprefix('modules')." WHERE module_name = 'Structure'");

	    if ($results->num_rows > 0)
	        $this->installed = TRUE;
	
		if ($this->installed === FALSE)
			return;
			
	}

	// --------------------------------------------------------------------

	/**
	 * Update
	 */
	function update()
	{
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Sections
	 *
	 * Set content for the accessory
	 *
	 * @access	public
	 * @return	void
	 */
	function set_sections()
	{
		if ($this->installed === FALSE)
		{
			$this->sections['Not Installed'] = "Structure is not installed.";
		}
		else
		{			
			include(APPPATH.'third_party/structure/mod.structure'.EXT);

			$this->structure = new Structure();
			$sidebarstructure_data['data'] 			= $this->structure->get_data();
			$sidebarstructure_data['theme_url']		= $this->EE->config->item('theme_folder_url') . 'third_party/structure';

			//save the original view path, and set to othe Structure package view folder
			$orig_view_path = $this->EE->load->_ci_view_path;
			$this->EE->load->_ci_view_path = APPPATH.'third_party/structure/views/';
		
			//code using the Structure view files
			$index = str_replace(array("\r", "\n"), ' ', $this->EE->load->view('index', $sidebarstructure_data, TRUE));

			//then return the view path to the application's original view path
			$this->EE->load->_ci_view_path = $orig_view_path;
		

			$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$sidebarstructure_data['theme_url']}/css/structure-new.css'>
				<style type='text/css'>
					#sidebarContent #sidebarStructure .addEdit{  margin:0 !important;}
					#sidebarContent #sidebarStructure .addEditLabel{display:none;}
					#sidebarContent #sidebarStructure {overflow:hidden;}
					#sidebarContent #sidebarStructure p.main-container{width:100%;}
				</style>
			
			");
			$this->EE->cp->add_to_foot('
			<script type="text/javascript">
			$("#accessoryTabs > ul > li > a.ndg_sidebarstructure").parent("li").remove()
			$(document).ready( function() {
				$("#sidebarContent").prepend(\'<div id="sidebarStructure" style="margin-top:10px">'.$index.'</div>\');
				$("#sidebarContent #sidebarStructure").find("img").hover( function(){
				      $(this).css("cursor","default");
				   });
			})
			</script>');
		}
		
		
	}

}
// END CLASS

/* End of file acc.ndg_sidebarstructure.php */
/* Location: ./system/expressionengine/third_party/ndg_sidebarstructure/acc.ndg_sidebarstructure.php */