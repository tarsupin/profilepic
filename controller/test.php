<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Try to force a login to Profile Pic if possible
if(!Me::$loggedIn)
{
	Me::redirectLogin("/login", URL::unifaction_com());
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

/*
echo "<img src='" . ProfilePic::image(1, "huge") . "' />";
echo "<img src='" . ProfilePic::image(1, "large") . "' />";
*/

/*
for($a = 300;$a < 553;$a++)
{
	// Determine appropriate directory and filename for the profile pic
	$hugeData = ProfilePic::imageData($a, "huge");
	$largeData = ProfilePic::imageData($a, "large");
	
	$hugeFile = APP_PATH . $hugeData['image_directory'] . $hugeData['main_directory'] . $hugeData['second_directory'] . '/' . $hugeData['filename'] . '.' . $hugeData['ext'];
	$largeFile = APP_PATH . $largeData['image_directory'] . $largeData['main_directory'] . $largeData['second_directory'] . '/' . $largeData['filename'] . '.' . $largeData['ext'];
	
	if(File::exists($largeFile))
	{
		copy($largeFile, $hugeFile);
	}
}
*/

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");