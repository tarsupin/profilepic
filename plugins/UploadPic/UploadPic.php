<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows a user to upload a profile pic to this site from Auth.
	
	
------------------------------
------ Calling this API ------
------------------------------
	
	$pathToPicFile = $_FILES['tmp_name'];			// The path to the profile picture that was uploaded.
	
	$packet = array(
		"uni_id"		=> $UniID					// The UniID to upload the profile picture for.
	);
	
	$settings = array(
		"filepath"		=> $pathToPicFile
	);
	
	$response = Connect::to("profile_picture", "SetDefaultPic", $packet, $settings);
	
	
[ Possible Responses ]
	TRUE		// If the default profile pic was set
	FALSE		// If the profile pic wasn't set

*/

class Uploadpic extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array("unifaction");		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 10000;		// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 8;			// <int> The minimum clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		$success = false;
		
		// Initialize the plugin
		$imageUpload = new ImageUpload($_FILES['fileName']);
		
		// Set your image requirements
		$imageUpload->maxHeight = 1200;
		$imageUpload->maxWidth = 1600;
		$imageUpload->maxFilesize = 1024 * 2000;	// 2 megabytes
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
				// Determine appropriate directory and filename for the avatar
				$picData = ProfilePic::imageData($this->data['uni_id'], $size);
				
				$imageDirectory = APP_PATH . $picData['image_directory'] . $picData['main_directory'] . $picData['second_directory'] . '/';
				
				// Save the image
				$image->autoCrop($dimensions, $dimensions);
				$success = $image->save($imageDirectory . $picData['filename'] . '.jpg') ? $success : false;
			}
		}
		
		return $success;
	}
	
}
