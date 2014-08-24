<?php
ini_set('memory_limit', '2048M');
$file = file_get_contents($argv[1]);
$contents = gzuncompress($file);
#echo "Version:\t" . $contents[0] . "\n";
$chars = array(1 => "!", "@", "$", "%", "^", "&", "*", "(", ")", "_", "+", "-", "=", "{", "}", ";", "?", "/", "~", "`", "[", "]");

$start = strpos($contents, "|");
$end = strpos($contents, "|", $start+1);
$prev = $end;
$coords = substr($contents, $start+1, ($end-2));
$coords_explode = explode("x", $coords);

$width = $coords_explode[0];
$height = $coords_explode[1];
#echo "\nWidth:\t\t" . $width . "\nHeight:\t\t" . $height . "\n\n";

$endchar = strpos($contents, "#");
$vars = substr($contents, $prev+1, $endchar-$prev-1);
#$prev = $endchar;
$numofvars = substr_count($vars, "|");

for ($z = 0; $z < $numofvars; $z++) {
	$start = strpos($contents, "|", $prev);
	$end = strpos($contents, "|", $start+1);
	$prev = $end;
	$charmap[] = substr($contents, $start+1, $end-$start-1);
}

if ($charmap != null) {
	foreach ($charmap as $key => $rep) {
		$contents = str_replace($chars[$key+1], $rep, $contents);
	}
}

$img = imagecreatetruecolor($width,$height);
$colors = array();

$total_colors = (substr_count($contents, "|")-2);
$times = 0;
while (++$times <= $total_colors) {
	$start = $prev;
	$end = strpos($contents, "|", $start+1);
	$prev = $end;
	$sub = substr($contents, $start+1, $end-$start-1);

	// If String doesn't contain :, it is 1 - 20 chars repetition
	if (strpos($sub, ":") == null) {
		//$sub_exp = explode(",", $sub);
		$str_split = str_split($sub);
		$sub_exp = array($str_split[0] . $str_split[1], $str_split[2] . $str_split[3], $str_split[4] . $str_split[5]);
		$timeschar = timeschar($sub);
		for ($a = 0; $a<$timeschar; $a++) {
			// Shade
			if (strlen($sub) <= 3) {
				$colors[] = imagecolorclosest($img, hexdec($sub), hexdec($sub), hexdec($sub));
			} else {
				$colors[] = imagecolorclosest($img, hexdec($sub_exp[0]), hexdec($sub_exp[1]), hexdec($sub_exp[2]));
			}
		}
	} else {
		#echo $sub . "\n";
		$occ = explode(":", $sub);
		$sub = $occ[0];
		$occ = $occ[1];
		if (strlen($sub) <= 2) {
			for ($b = 0; $b < $occ; $b++) {
				$colors[] = imagecolorclosest($img, hexdec($sub), hexdec($sub), hexdec($sub));
			}
		} else {
			for ($b = 0; $b < $occ; $b++) {
				$str_split = str_split($sub);
				$sub_exp = array($str_split[0] . $str_split[1], $str_split[2] . $str_split[3], $str_split[4] . $str_split[5]);
				$colors[] = imagecolorclosest($img, hexdec($sub_exp[0]), hexdec($sub_exp[1]), hexdec($sub_exp[2]));
			}
		}
	}

	#print_r($colors);
	#die;

}
$size = count($colors);
$it = 0;
for ($col = 0; $col < $height; $col++) {
	for ($row = 0; $row < $width; $row++) {
		imagesetpixel($img, $row, $col, $colors[($col*$width)+$row]);
	}
}

/*
while (++$it < $size) {
	#echo $col . " x " . ($it-($col*$height)) . "\n";
	imagesetpixel($img, $row, ($it-($row*$width)) , $colors[$it]);
	if ($it % $width == 0) {
		$row++;
		#echo "\n";
	}
}
*/

header('Content-Disposition: Attachment;filename=image.png'); 
header('Content-type: image/png'); 
imagepng($img);
imagedestroy($img);

function timeschar($string) {
	if (strpos($string, 'H')) {
		return 2;
	}
	else if (strpos($string, 'I')) {
		return 3;
	}
	else if (strpos($string, 'J')) {
		return 4;
	}
	else if (strpos($string, 'K')) {
		return 5;
	}
	else if (strpos($string, 'L')) {
		return 6;
	}
	else if (strpos($string, 'M')) {
		return 7;
	}
	else if (strpos($string, 'N')) {
		return 8;
	}
	else if (strpos($string, 'O')) {
		return 9;
	}
	else if (strpos($string, 'P')) {
		return 10;
	}
	else if (strpos($string, 'Q')) {
		return 11;
	}
	else if (strpos($string, 'R')) {
		return 12;
	}
	else if (strpos($string, 'S')) {
		return 13;
	}
	else if (strpos($string, 'T')) {
		return 14;
	}
	else if (strpos($string, 'U')) {
		return 15;
	}
	else if (strpos($string, 'V')) {
		return 16;
	}
	else if (strpos($string, 'W')) {
		return 17;
	}
	else if (strpos($string, 'X')) {
		return 18;
	}
	else if (strpos($string, 'Y')) {
		return 19;
	}
	else if (strpos($string, 'Z')) {
		return 20;
	} else {
		return 1;
	}
}
?>