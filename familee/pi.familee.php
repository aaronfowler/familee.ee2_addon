<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================
 This ExpressionEngine plugin was created by Aaron Fowler
 http://twitter.com/adfowler
=====================================================

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.

=====================================================
 File: pi.familee.php
-----------------------------------------------------
 Purpose: to output an unordered list of forward/reverse relationship links with no duplicates. 
=====================================================
*/

$plugin_info = array(
	'pi_name'			=> 'Familee - Playa 4 version',
	'pi_version'		=> '2.0.3',
	'pi_author'			=> 'Aaron Fowler',
	'pi_author_url'		=> 'http://twitter.com/adfowler',
	'pi_description'	=> 'Outputs an unordered list of forward/reverse relationship links with no duplicates.',
	'pi_usage'			=> Familee::usage()
);

/**
 * Familee
 * 
 * Outputs an unordered list of 
 * forward/reverse relationship links with no duplicates
 *
 * @version 2.0.2
 *
 */
class Familee {
	
	public $return_data = '';
	
	/** 
	 * Constructor
	 * @access public
	 * @return void
	 */
	function Familee()
	{
		$this->EE =& get_instance();
		
		// Allowed orderby/sort parameters
		$allowable_orderby = array('entry_id', 'title', 'entry_date');
		$allowable_sort = array('asc', 'desc');
		
		// Fetch params
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		$path = $this->EE->TMPL->fetch_param('path');
		$orderby = 'entry_date';
		$sort = 'DESC';
		
		// Parameter "entry_id" is required
		if ($entry_id === FALSE)
		{
			echo 'Parameter "entry_id" of exp:familee tag must be defined!<br><br>';
		}

		// The value of the parameter "entry_id" must be a number
		if (is_numeric($entry_id) === FALSE)
		{
			echo 'The value of the parameter "entry_id" of exp:familee tag must be a number!<br><br>';
		}
		
		
		if ($entry_id !== FALSE)
		{
			$channel_sql = '';
			if ($this->EE->TMPL->fetch_param('channel_id'))
			{
				$channel_ids = explode('|', $this->EE->TMPL->fetch_param('channel_id'));
				foreach ($channel_ids as $cid)
				{
					if(is_numeric($cid) === FALSE)
					{
						echo 'All "channel_id" parameters of exp:familee tag must be numbers!<br><br>';
					}
					else
					{
						if ($channel_sql == '')
						{
							$channel_sql = 'AND (channel_id=' . $cid;
						}
						else
						{
							$channel_sql .= ' OR channel_id=' . $cid;
						}
					}
				}
				if ($channel_sql != '')
				{
					$channel_sql .= ')';
				}
			}
			
			foreach ($allowable_orderby as $param)
			{
				if (strtolower($this->EE->TMPL->fetch_param('orderby')) == $param)
				{
					$orderby = strtolower($this->EE->TMPL->fetch_param('orderby'));
				}
			}
			
			foreach ($allowable_sort as $param)
			{
				if (strtolower($this->EE->TMPL->fetch_param('sort')) == $param)
				{
					$sort = strtolower($this->EE->TMPL->fetch_param('sort'));
				}
			}
			
			// Find all of the entry_ids related to the current entry
			$relations = '';
			
			$sql = "SELECT child_entry_id, parent_entry_id
			FROM exp_playa_relationships 
			WHERE parent_entry_id=" . $entry_id . " OR child_entry_id=" . $entry_id;
			// Perform SQL query
			$results = $this->EE->db->query($sql);
			foreach ($results->result_array() as $row)
			{
				$relations .= $row['child_entry_id'] . ',';
				$relations .= $row['parent_entry_id'] . ',';
			}
			
			if ($relations != '')
			{
				// Pull channel_title data for the related entries
				$sql = "SELECT DISTINCT entry_id, title, url_title, status, entry_date 
				FROM exp_channel_titles 
				WHERE status != 'closed' " . $channel_sql . " 
				AND entry_id IN (" . substr($relations, 0, -1) . ") 
				ORDER BY " . $orderby . " " . $sort;
			
				// Perform SQL query
				$results = $this->EE->db->query($sql);
			
				foreach ($results->result_array() as $row)
				{
					if ($entry_id != $row['entry_id'])
					{
						$this->return_data .= '<li><a href="' . $path;
						$this->return_data .= (strtolower($this->EE->TMPL->fetch_param('include_entry_id')) == 'yes') ? $row['entry_id'] . '/' : '';
						$this->return_data .= $row['url_title'] . '">' . $row['title'] . '</a></li>';
						
					}
				}
			}
			
			// Build the unordered list and return it
			if ($this->return_data !== '')
			{
				$class = $this->EE->TMPL->fetch_param('class') ? ' class="' . $this->EE->TMPL->fetch_param('class') . '"' : '';
				$id = $this->EE->TMPL->fetch_param('id') ? ' id="' . $this->EE->TMPL->fetch_param('id') . '"' : '';
				$html_start = $this->EE->TMPL->fetch_param('html_start') . '<ul' . $class . $id . '>';
				$html_end = '</ul>' . $this->EE->TMPL->fetch_param('html_end');
				$this->return_data = $html_start . $this->return_data . $html_end;
			}
		}
	} // END FUNCTION
  
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------
// This function describes how the plugin is used.
	function usage()
	{
		ob_start(); 
		?>

		PARAMETERS:

		1) entry_id - This is the only required parameter. Allows you to specify entry id number.

		2) channel_id - Allows you to limit relationships to within one or more channels. Separate multiple channels with a pipe character.

		3) orderby='entry_date' - Options are 'entry_date', 'title', or 'entry_id'

		4) sort='DESC' - Options are 'ASC' or 'DESC'

		5) path - Prepend a path to the returned url.

		6) class - Add a class attribute to the <ul> tag.

		7) id - Add an id attribute to the <ul> tag.

		6) html_start - Add HTML before the opening <ul> tag.

		7) html_end - Add HTML after the closing </ul> tag.

		EXAMPLE OF USAGE:

		{exp:familee entry_id="{entry_id}" channel_id="1|2|3" orderby="title" sort="asc" path="/{segment_2}/" class="nav" id="article-nav" html_start="<h4>Related Links</h4>" html_end="<p>That's all, folks!</p>"}


		<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 
		return $buffer;
	} // END USAGE

} // END CLASS

/* End of file pi.familee.php */
/* Location: ./system/expressionengine/third_party/familee/pi.familee.php */