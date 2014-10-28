<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Try to force a login to Profile Pic if possible
if(!Me::$loggedIn)
{
	Me::redirectLogin("/login", URL::unifaction_com());
}

// Process profile pic upload (if applicable)
$success = false;

if(Form::submitted("propic-upload"))
{
	// Initialize the image upload plugin
	$imageUpload = new ImageUpload($_FILES['image']);
	
	// Set your image requirements
	$imageUpload->maxHeight = 1400;
	$imageUpload->maxWidth = 1800;
	$imageUpload->maxFilesize = 1024 * 3000;			// 3 megabytes max
	$imageUpload->saveMode = Upload::MODE_OVERWRITE;
	
	// Save the image to a chosen path
	if($imageUpload->validate())
	{
		$success = true;
		$image = new Image($imageUpload->tempPath, $imageUpload->width, $imageUpload->height, $imageUpload->extension);
		
		$eachSize = array("large" => 128, "medium" => 64, "small" => 46);
		
		foreach($eachSize as $size => $dimensions)
		{
			// Determine appropriate directory and filename for the profile pic
			$propicData = ProfilePic::imageData(Me::$id, $size);
			
			$imageDirectory = APP_PATH . $propicData['image_directory'] . $propicData['main_directory'] . $propicData['second_directory'] . '/';
			
			// Save the image
			$image->autoCrop($dimensions, $dimensions);
			$success = $image->save($imageDirectory . $propicData['filename'] . '.jpg') ? $success : false;
		}
	}
	
	// Display Profile Pic Alert
	if($success)
	{
		Database::query("REPLACE INTO avatars (uni_id, title) VALUES (?, ?)", array(Me::$id, $_POST['title']));
		Alert::success("Avatar Uploaded", "Your avatar has been successfully updated.");
	}
	else
	{
		Alert::error("Avatar Issue", "There was an error uploading your avatar.");
	}
}

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Display the page
echo '
<div id="content" class="content-open">' . Alert::display();

// Show your current profile pic
if($propicTitle = Database::selectValue("SELECT title FROM avatars WHERE uni_id=? LIMIT 1", array(Me::$id)))
{
	echo '
	<div style="display:inline-block; padding:30px; vertical-align:top;">
		<img src="' . ProfilePic::image(Me::$id, "large") . '?t=' . time() . '" />
		<h3 style="text-align:center;">My Profile Picture</h3>
		<p style="text-align:center; font-weight:strong;">' . $propicTitle . '</p>
	</div>';
}
else
{
	$propicTitle = "";
}

// Create the image upload form
if(!$success)
{
	echo '
	<div style="display:inline-block; padding:30px; vertical-align:top;">
	<h3>Update Your Profile Picture</h3>
	<form class="uniform" action="/" method="post" enctype="multipart/form-data">' . Form::prepare("propic-upload") . '
		<p>Select Your Profile Image: <input type="file" name="image" /></p>
		<p>Title: <input type="text" name="title" value="' . $propicTitle . '" placeholder="Main" maxlength="16" /></p>
		<p><input type="submit" name="submit" value="Update Your Profile Picture"></p>
	</form>
	</div>';
}
else
{
	echo '
	<div style="display:inline-block; padding:30px; vertical-align:top;">
	<h3>You avatar is ready!</h3>
	<a class="button" href="/">Upload New Avatar</a>
	<a class="button" href="' . URL::unifaction_com() . '">Return to My UniFaction</a>
	</div>';
}

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");