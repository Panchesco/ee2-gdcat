<?php 

/**
 *	Gdcat Class
 *
 *	@package		ExpressionEngine
 *	@author			Richard Whitmer/Godat Design, Inc.
 *	@copyright		(c) 2014, Godat Design, Inc.
 *	@license		
 *
 *	Permission is hereby granted, free of charge, to any person obtaining a copy
 *	of this software and associated documentation files (the "Software"), to deal
 *	in the Software without restriction, including without limitation the rights
 *	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *	copies of the Software, and to permit persons to whom the Software is
 *	furnished to do so, subject to the following conditions:
 *	The above copyright notice and this permission notice shall be included in all
 *	copies or substantial portions of the Software.
 *	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *	SOFTWARE.
 *	
 *	@link			http://godatdesign.com
 *	@since			Version 2.9
 */
 
 // ------------------------------------------------------------------------

/**
 * Good Cat Plugin
 *
 * @package			ExpressionEngine
 * @subpackage		third_party
 * @category		Plugin
 * @author			Richard Whitmer/Godat Design, Inc.
 * @copyright		Copyright (c) 2014, Godat Design, Inc.
 * @link			http://godatdesign.com
 */
  
 // ------------------------------------------------------------------------

	$plugin_info = array(
	    'pi_name'         => 'Good Cat',
	    'pi_version'      => '1.0',
	    'pi_author'       => 'Richard Whitmer/Godat Design, Inc.',
	    'pi_author_url'   => 'http://godatdesign.com/',
	    'pi_description'  => '
	    Use one or more properties of a category to easily find other properties
	    and make them available in templates as a single tag, independent
	    of the native exp:channel:category tag pair.
	    ',
	    'pi_usage'        => Gdcat::usage()
	);
	

	class  Gdcat {
		
			public $return_data		= "";
			public $cat_id 			= FALSE;
			public $cat_name		= FALSE;
			public $cat_url_title	= FALSE;
			public $group_id		= FALSE;
			public $category;
		
			public function __construct()
			{

				if(ee()->TMPL->fetch_param('group_id'))
				{
				    $this->group_id				= ee()->TMPL->fetch_param('group_id');
				}

				if(ee()->TMPL->fetch_param('cat_id'))
				{
				    $this->cat_id			= ee()->TMPL->fetch_param('cat_id');
				}
				
				if(ee()->TMPL->fetch_param('cat_name'))
				{
				    $this->cat_name			= ee()->TMPL->fetch_param('cat_name');
				}
				
				if(ee()->TMPL->fetch_param('cat_url_title'))
				{
				    $this->cat_url_title	= ee()->TMPL->fetch_param('cat_url_title');
				}
				
				$this->find_category();
				
			}
			
			// ------------------------------------------------------------------------

			/**
			 *	Use the provided parameters to fetch the category
			 *	from the exp_categories table.
			 */
			 public function find_category()
			 {
			 
			 	$where	= array();
			 	
				 
				 if($this->group_id)
				 {
					 $where['group_id']		= $this->group_id;
				 }
				 
				 if($this->cat_id)
				 {
					 $where['cat_id']	= $this->cat_id;
				 }
				 
				 if($this->cat_name)
				 {
					 $where['cat_name']	= $this->cat_name;
				 }
				 
				 if($this->cat_url_title)
				 {
					 $where['cat_url_title']	= $this->cat_url_title;
				 }
				 
				 $query =  ee()->db->select("group_id,cat_id,cat_name,cat_url_title,cat_description,cat_image,cat_order")
				 			->where($where)
				 			->get('categories');
				 			
				 			
				 if($query->num_rows()==1)
				 {
					 $this->category = $query->row();
					 
				 } else {
					 
					 $this->category				= new stdClass();
					 $this->category->group_id		= '';
					 $this->category->cat_id		= '';
					 $this->category->cat_name		= '';
					 $this->category->cat_url_title	= '';
					 $this->category->description	= '';
					 $this->category->cat_image		= '';
					 $this->category->cat_order		= '';
					 
				 }
		 			
			 }
			 
			 // ------------------------------------------------------------------------
			 
			 
			/**
			 *	Return the group_id from the category property object.
			 *	@return integer
			 */
			 public function group_id()
			{
				return $this->category->group_id;
			}
			
			// ------------------------------------------------------------------------
			
			/**
			 *	Return the category id from the category property object.
			 *	@return integer
			 */
			 public function id()
			{
				return $this->category->cat_id;
			}
			
			// ------------------------------------------------------------------------
			
			/**
			 *	Return the cateogry name from the category property object.
			 *	@return string
			 */
			public function name()
			{
				return $this->category->cat_name;
			}
			
			// ------------------------------------------------------------------------
			
			/**
			 *	Return the category url_title from the category property object.
			 *	@return string
			 */
			 public function url_title()
			{
				return $this->category->cat_url_title;
			}
			
			// ------------------------------------------------------------------------
			
			/**
			 *	Return the category description from the category property object.
			 *	@return string
			 */
			public function description()
			{
			
				return $this->category->cat_description;
			}
			
			// ------------------------------------------------------------------------
			 
			/**
			 *	Return the category image data from the category property object.
			 *	@return string
			 */
			 public function image()
			{
				return $this->category->cat_image;
			}
			
			// ------------------------------------------------------------------------
			
			/**
			 *	Return the category order from the category property object.
			 *	@return integer
			 */
			 public function order()
			{
				return $this->category->cat_desciption;
			}
			
			// ------------------------------------------------------------------------
			 

			/**
			 *	Return plugin usage documentation.
			 *	@return string
			 */
			public function usage()
			{
				
					ob_start();  ?>
					 
					TAGS:
					----------------------------------------------------------------------------
					{exp:gdcat:group_id [parameters]} 
					{exp:gdcat:url_title [parameters]}
					{exp:gdcat:name [parameters]} 
					{exp:gdcat:id [parameters]}
					{exp:gdcat:description [parameters]} 
					{exp:gdcat:image [parameters]}
					{exp:gdcat:order [parameters]}
					
					PARAMETERS: 
					All are optional, but query will return
					the first result it finds meeting the criteria set in the parameters, 
					so whenever possible using the group_id and either the cat_id or 
					cat_url_title is recommended.
					----------------------------------------------------------------------------
					group_id
					cat_id
					cat_name
					cat_url_title
					

					<?php
					 $buffer = ob_get_contents();
					 ob_end_clean();
					
					return $buffer;
					
			}
		
		
	}
/* End of file pi.gdcat.php */
/* Location: ./system/expressionengine/third_party/gdcat/pi.gdcat.php */
