<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API is designed to provide the user with a default profile pic until they have created one.
	
	
------------------------------
------ Calling this API ------
------------------------------

	$packet = array(
		"uni_id"		=> $UniID					// The UniID to set the profile pic for.
	,	"title"			=> $_GET['account_name']	// The name of the account you're setting this profile pic for.
	);
	
	$response = Connect::to("profile_picture", "SetDefaultPic", $packet);
	
	
[ Possible Responses ]
	TRUE		// If the default profile pic was set
	FALSE		// If the profile pic wasn't set

*/

class SetDefaultPic extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array("auth");	// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 10000;		// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 8;			// <int> The minimum clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		$success = false;
		
		// Tell the user what type of value they sent
		if(isset($this->data['uni_id']) and isset($this->data['title']))
		{
			// Process the API & Call Data
			$eachSize = array("small", "medium", "large");
			$success = true;
			
			foreach($eachSize as $size)
			{
				// Determine appropriate directory for image upload
				$propicData = ProfilePic::imageData($this->data['uni_id'], $size);
				
				// Prepare File Destination
				$imageDirectory = APP_PATH . $propicData['image_directory'] . $propicData['main_directory'] . $propicData['second_directory'] . '/';
				
				// Save Large Image
				$image = new Image(APP_PATH . "/assets/images/default/default-" . $size . ".png");
				$success = $image->save($imageDirectory . $propicData['filename'] . '.jpg');
			}
			
			// Add the results to the database
			$success = Database::query("REPLACE INTO `avatars` (uni_id, title) VALUES (?, ?)", array($this->data['uni_id'], $this->data['title'])) ? $success : false;
		}
		
		// Respond to the API origin
		return $success;
	}
	
}
