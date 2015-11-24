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
	    'pi_version'      => '1.4.0',
	    'pi_author'       => 'Richard Whitmer/Godat Design, Inc.',
	    'pi_author_url'   => 'https://github.com/Panchesco/gdcat',
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
			public $br_desc			= 'yes';
			public $image_man			= '';
		
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
				
				$this->br_desc	= strtolower(ee()->TMPL->fetch_param('br_desc','yes'));
				
				$this->image_man	= strtolower(ee()->TMPL->fetch_param('image_man',''));

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
					 $row = $query->row();
					 
					  // "cat_" style names
					 $this->category = $row;
					 
					 if($this->br_desc == 'yes')
					 {
					 	$this->category->cat_description = nl2br($this->category->cat_description);
					 }
					 
					 // ee channel category vars
					 
					 $this->category->category_id = $row->cat_id;
					 $this->category->category_name = $row->cat_name;
					 $this->category->category_url_title = $row->cat_url_title;
					 $this->category->category_description = $row->cat_description;
					 $this->category->category_image = $this->fileurl($row->cat_image);;
					 
					 // Get the image file url
					 if($this->category->cat_image)
					 {
						 $this->category->cat_image = $this->fileurl($row->cat_image);
					 }
					 
					 
				 } else {
					 
					 $this->category				= new stdClass();
					 $this->category->group_id		= '';
					 $this->category->cat_id		= '';
					 $this->category->cat_name		= '';
					 $this->category->cat_url_title	= '';
					 $this->category->cat_description	= '';
					 $this->category->cat_image		= '';
					 $this->category->cat_order		= '';
					 
				 }
		 			
			 }
			 
			 // ------------------------------------------------------------------------
			 
			 			 
			 /**
				 * Returned category db row parsed for template.
				 */
				 public function category()
				 {
					 if($this->category->cat_id !== '')
					 {
						 
						 foreach($this->category as $key=>$row)
						 {
								$data[$key] = $row;
						 }
						 
						 return ee()->TMPL->parse_variables(ee()->TMPL->tagdata,array($data));
					 } else {
						 return ee()->TMPL->no_results();
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
			 * Return data about a category and its parent categories
			 */
			 public function line()
			 {
				 $data = array();
				 
				 $sort = ee()->TMPL->fetch_param('sort','desc');
				 $cat_url_title = ee()->TMPL->fetch_param('cat_url_title',0);
				 $group_id = ee()->TMPL->fetch_param('group_id',0);
				 
				 $query = $this->category_line($group_id,$cat_url_title);
				 
				 while($query !== FALSE && $query->num_rows()!==0)
				 {
				 
				 		$data[] = $query->row_array();
				
				 		$query = ee()->db->where('cat_id',$query->row()->parent_id)
				 						->limit(1)
				 						->get('categories');
				 
				 }
				 
				 if(strtolower($sort)=='desc')
				 {
				 	$data = array_reverse($data);
				 }
				 
				 if( count($data) != 0)
				 {
					 		
					 		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $data);
				 
				 	} else {
					 
					 		return ee()->TMPL->no_results();
				 }
				 
			 }
			
			// ------------------------------------------------------------------------

			/**
			 *	Return the parent id for a given category_id
			 *  @param $id integer
			 *	@return object
			 */
			private function parent_id($id)
			{
										$query = ee()->db
												->select('*')
												->where('parent_id',$id)
												->limit(1)
												->get('categories');
												
										if($query->num_rows()==1)
										{
											return $query->row();
										} else {
											return FALSE;
										}
				
			}
			 
			// ------------------------------------------------------------------------
			
			/**
			 *	Return the db row for a given category_id
			 *	@param $group_id integer
			 *  @param $cat_url_title string
			 *	@return object
			 */
			private function category_line($group_id=0,$cat_url_title='')
			{
										$query = ee()->db
												->where('group_id',$group_id)
												->where('cat_url_title',$cat_url_title)
												->limit(1)
												->get('categories');
												
										if($query->num_rows()==1)
										{
											return $query;
										} else {
											return FALSE;
										}
				
			}
			 
			// ------------------------------------------------------------------------
			
			/**
				* Adapted from: https://devot-ee.com/add-ons/parse-filedirectories
				* Parse a file field directory 
				* @param string
				* @return string
				*/
				private function fileurl($url)
				{
							$file_dir = '';
							$file_name = '';

							// Figure out what the full URL should be
							if (preg_match('/{filedir_([0-9]+)}/', $url, $matches))
							{
								$file_dir = $matches[1];
								$file_name = str_replace($matches[0], '', $url);
								
								$query = ee()->db->select('url')
												->from('upload_prefs')
												->where('id',$file_dir)
												->get();

										// Output the full URL
										if( $this->image_man != '')
										{
											return $query->row('url'). '_' . $this->image_man . '/' . $file_name;
										} else {
											return $query->row('url').$file_name;
										}
							}

				}
				
			// ------------------------------------------------------------------------	
			
			/**
			 *	Return plugin usage documentation.
			 *	@return string
			 */
			public function usage()
			{
				
					ob_start();  ?>
					 
					SINGLE TAGS:
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
					br_desc - yes/no - Apply nl2br to cat_description? 
					image_man - Image manipulation to output with cat_image.
					
					
					
					TAGS PAIRS:
					----------------------------------------------------------------------------
					{exp:gdcat:category group_id="1" cat_url_title="category-url-title" image_man="thumbs"}
						 {cat_id}
						 {category_id}
						 {site_id
						 {group_id}
						 {parent_id}
						 {cat_name}
						 {category_name}
						 {cat_url_title}
						 {category_url_title}
						 {cat_description}
						 {category_description}
						 {cat_image}
						 {category_image}
						 {cat_order}
						 {category_order}
					{/exp:gdcat:line}
					

					{exp:gdcat:line group_id="1" cat_url_title="category-url-title"}
						 {cat_id}
						 {category_id}
						 {site_id
						 {group_id}
						 {parent_id}
						 {cat_name}
						 {category_name}
						 {cat_url_title}
						 {category_url_title}
						 {cat_description}
						 {category_description}
						 {cat_image}
						 {category_image}
						 {cat_order}
						 {category_order}
					{/exp:gdcat:line}

					<?php
					 $buffer = ob_get_contents();
					 ob_end_clean();
					
					return $buffer;
					
			}
		
		
	}
/* End of file pi.gdcat.php */
/* Location: ./system/expressionengine/third_party/gdcat/pi.gdcat.php */