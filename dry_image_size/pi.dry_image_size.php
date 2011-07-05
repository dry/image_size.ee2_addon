<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Resize Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Greg Salt <greg@purple-dogfish.co.uk>
 * @link		http://www.purple-dogfish.co.uk	
 */

$plugin_info = array(
	'pi_name'		=> 'Image Size',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Greg Salt',
	'pi_author_url'	=> 'http://www.purple-dogfish.co.uk',
	'pi_description'=> 'Choose the size of an embedded image you want to display in custom field',
	'pi_usage'		=> Dry_image_size::usage()
);


class Dry_image_size {

	public	$return_data;

	private $data;
	private $images = array();
	private $size_folder;

   	private $EE;

	const DS = DIRECTORY_SEPARATOR;

	public function __construct()
	{
		$this->EE =& get_instance();

		$this->data = $this->EE->TMPL->tagdata;
		$size = $this->EE->TMPL->fetch_param('size');

		if ($size == '')
		{
			return $this->return_data = $this->data;
		}
		else
		{
			$this->size_folder = '_'.$size;
		}

		$this->extract_images($this->data);

		if (count($this->images))
		{
			$this->process_sizes();
		}

		$this->return_data = $this->data;
	}

	private function replace_image($image, $src)
	{
		// Remove the width and height attributes
		// We could replace these but why waste DB queries...
		$img_attr = preg_replace('/width="[0-9]+"/U', '', $image[2]);
		$img_attr = preg_replace('/height="[0-9]+"/U', '', $img_attr);

		$new_link = sprintf('<img src="%s" %s />', $src, $img_attr);
		return $this->data = str_replace($image[0], $new_link, $this->data);
	}

	private function process_sizes()
	{
		foreach($this->images AS $image)
		{
			$srcparts = explode(self::DS, $image[1]);
			$file = array_pop($srcparts);
			$srcparts[] = $this->size_folder;
			$src = implode(self::DS, $srcparts).self::DS.$file;
			$this->data = $this->replace_image($image, $src);
		}
	}

	private function extract_images($str)
	{
		preg_match_all('/<img\ssrc="(.+)"(.+)\/>/U', $str, $matches, PREG_SET_ORDER);
		$this->images = $matches;
	}

	public static function usage()
	{
		ob_start();
?>

This plugin allows you to embed images in entries using the standard image selection button and then output a specific size of that image as defined in your file upload preferences.
<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.dry_image_size.php */
/* Location: /system/expressionengine/third_party/dry_image_size/pi.dry_image_size.php */
