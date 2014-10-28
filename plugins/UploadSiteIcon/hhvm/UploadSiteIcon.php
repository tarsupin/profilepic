<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows a user to upload a site icon, generally from the registered site's admin panel.

This allows every site to have a unique appearance rather than being a default "guest" site.
	
	
------------------------------
------ Calling this API ------
------------------------------
	
	// Ideally, this file should already be reduced to 256x256 so that we're not wasting bandwidth
	$pathToSiteIconFile = $_FILES['tmp_name'];		// The path to the site icon that was uploaded.
	
	$packet = array(
		"site_handle"	=> $siteHandle				// The site handle to upload the icon for.
	);
	
	$settings = array(
		"filepath"		=> $pathToSiteIconFile
	);
	
	$response = Connect::to("profile_picture", "SetDefaultPic", $packet, $settings);
	
	
[ Possible Responses ]
	TRUE		// If the site icon was set
	FALSE		// If the site icon wasn't set

*/

class UploadSiteIcon extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	
	
/****** Run the API ******/
	public function runAPI (
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		$success = false;
		
		// Initialize the plugin
		$imageUpload = new ImageUpload($_FILES['fileName']);
		
		// Set your image requirements
		$imageUpload->maxHeight = 300;
		$imageUpload->maxWidth = 300;
		$imageUpload->minHeight = 128;
		$imageUpload->minWidth = 128;
		$imageUpload->maxFilesize = 1024 * 500;	// 1/2 megabyte
		$imageUpload->saveMode = Upload::MODE_OVERWRITE;
			// ... and so forth
		
		// Save the image to a chosen path
		if($imageUpload->validate())
		{
			$success = true;
			$image = new Image($imageUpload->tempPath, $imageUpload->width, $imageUpload->height, $imageUpload->extension);
			
			$eachSize = array("large" => 128, "medium" => 64, "small" => 46);
			
			foreach($eachSize as $size => $dimensions)
			{
				// Determine appropriate directory and filename for the profile pic
				$iconData = ProfilePic::siteData($this->data['uni_id'], $size);
				
				$imageDirectory = APP_PATH . $iconData['image_directory'] . $iconData['main_directory'] . $iconData['second_directory'] . '/';
				
				// Save the image
				$image->autoCrop($dimensions, $dimensions);
				$success = $image->save($imageDirectory . $iconData['filename'] . '.jpg') ? $success : false;
			}
		}
		
		return $success;
	}
	
}