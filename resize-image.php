<?php

/*
Copyright (C) 2012 Andrew Baillie

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

	//-------------------------------
	$config['max_width'] = 480;
	$config['max_height'] = 360;
	$config['cache_location'] = $_SERVER['DOCUMENT_ROOT']."/cache/";
	$config['image_quality'] = 80;
	//-------------------------------
		
	// Get the filename...
	$file_created = filemtime( $_GET['image'] );
	$filename = $file_created.'-'.basename( $_GET['image'] );
	
	if ( isset($_GET['w']) && $_GET['w'] != '' ) { $config['max_width'] = $_GET['w']; }
	if ( isset($_GET['h']) && $_GET['h'] != '' ) { $config['max_height'] = $_GET['h']; }
	
	$cache_folder = $config['cache_location'].$config['max_width']."/";
	
	// Create folder if it doesn't exist already
	if ( !file_exists($cache_folder) ) { mkdir( $cache_folder , 0777 ); chmod( $cache_folder , 0777 ); }

	// Create image if it doesn't already exist
	if ( !file_exists($cache_folder.$filename) ) { createImage( $_GET['image'], $config['max_width'], $config['max_height'], $cache_folder, $config['image_quality'], $filename ); }
	
	// Display Photo...
	header("Content-type: image/jpeg");
	readfile( $cache_folder.$filename );
	

	function createImage( $image, $max_width, $max_height, $cache_loc, $img_qua, $filename ) {
	
		$dimensions = GetImageSize($image); 
		$img_width = $dimensions[0];
		$img_height = $dimensions[1];
	
		// Get ratio for resize...
		$x_ratio = $max_width / $img_width;
		$y_ratio = $max_height / $img_height;
	
		// Find the new height and width....
		if ( ($img_width <= $max_width) && ($img_height <= $max_height) ) {
		  $tn_width = $img_width;
		  $tn_height = $img_height;
		}
		else if (($x_ratio * $img_height) < $max_height) {
		  $tn_height = ceil($x_ratio * $img_height);
		  $tn_width = $max_width;
		}
		else {
		  $tn_width = ceil($y_ratio * $img_width);
		  $tn_height = $max_height;
		}
		
		// Now create the new images...
		$src = ImageCreateFromJpeg($image);
		$dst = imagecreatetruecolor($tn_width,$tn_height);
		ImageCopyResampled($dst, $src, 0, 0, 0, 0, $tn_width,$tn_height,$img_width,$img_height);
			
		// Save the image to the cached location....
		ImageJpeg( $dst, $cache_loc.$filename, $img_qua );
		
		// Clean up...
		ImageDestroy($src);
		ImageDestroy($dst);
	}
?>
