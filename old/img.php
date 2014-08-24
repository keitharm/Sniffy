<?php
ini_set('memory_limit', '2048M');    

# Set up an array for the characters that are decoded
$text = array();
$image = $argv[1];
$comp = $argv[2];
$comp_pixel = $argv[3];

if ($argv[2] == null) {
	$comp = 0;
}
if ($argv[3] == null) {
	$comp_pixel = $comp;
}
# Creates an array filled with information regarding the image
$imageinfo = getimagesize($image);

# Height in pixels
$height = $imageinfo[1];

# Width in pixels
$width = $imageinfo[0];

# Creates a new image from url
$im = @imagecreatefrompng($image) or die ("<center><font color='red'>Error, image is invalid!</font></center>");

# Finds the index of the color of each pixel in the image
for ($j = 0; $j < $height; $j++) {
    for ($i = 0; $i < $width; $i++) {
        # Adds the index of the colors into the $text array
        $imagecolor = imagecolorsforindex($im, imagecolorat($im, $i, $j));

        // If the next pixel...is relatively, to the threshold, the same as the previous, make them the same color.
        if (($imagecolor[red] > ($prevcolor[red]-$comp) && $imagecolor[red] < ($prevcolor[red]+$comp)) && ($imagecolor[green] > ($prevcolor[green]-$comp) && $imagecolor[green] < ($prevcolor[green]+$comp)) && ($imagecolor[blue] > ($prevcolor[blue]-$comp) && $imagecolor[blue] < ($prevcolor[blue]+$comp)) && $comp != 0 && $comp != null) {
        	array_push($text, $prevcolor);
        } else {
        	// Converts pixels to a shade if they are within a threshold (comp)
        	if (abs($imagecolor[red] - $imagecolor[blue]) <= $comp_pixel && abs($imagecolor[red] - $imagecolor[green]) <= $comp_pixel && $comp_pixel != 0 && $comp_pixel != null) {
        		$new_color = imagecolorexact($im , $imagecolor[red] , $imagecolor[red] , $imagecolor[red]);
        		$imagecolor = imagecolorsforindex($im, $new_color);
        	}
        	array_push($text, $imagecolor);
        	$prevcolor = $imagecolor;
        }
    }
}
$values = array();
for ($a = 0; $a < count($text); $a++) {
	// If it is a shade
	if ($text[$a][red] == $text[$a][green] && $text[$a][red] == $text[$a][blue]) {
		array_push($values, dechex($text[$a][red]));
	} else {
		$redp = dechex($text[$a][red]);
		$greenp = dechex($text[$a][green]);
		$bluep = dechex($text[$a][blue]);
		if (strlen($redp) == 1) {
			$redp = "0" . $redp;
		}
		if (strlen($greenp) == 1) {
			$greenp = "0" . $greenp;
		}
		if (strlen($bluep) == 1) {
			$bluep = "0" . $bluep;
		}
		array_push($values, $redp . $greenp . $bluep);
	}
}
$output = "";
$size = count($values);
$pos = 0;
while ($pos < $size) {
	$same = 1;
	while ($values[$pos] == $values[++$pos]) {
		$same++;
	}
	if ($same == 1) {
		$output .= $values[$pos-1] . "|";
	}
	else if ($same == 2) {
		$output .= $values[$pos-1] . "H|";
	}
	else if ($same == 3) {
		$output .= $values[$pos-1] . "I|";
	}
	else if ($same == 4) {
		$output .= $values[$pos-1] . "J|";
	}
	else if ($same == 5) {
		$output .= $values[$pos-1] . "K|";
	}
	else if ($same == 6) {
		$output .= $values[$pos-1] . "L|";
	}
	else if ($same == 7) {
		$output .= $values[$pos-1] . "M|";
	}
	else if ($same == 8) {
		$output .= $values[$pos-1] . "N|";
	}
	else if ($same == 9) {
		$output .= $values[$pos-1] . "O|";
	}
	else if ($same == 10) {
		$output .= $values[$pos-1] . "P|";
	}
	else if ($same == 11) {
		$output .= $values[$pos-1] . "Q|";
	}
	else if ($same == 12) {
		$output .= $values[$pos-1] . "R|";
	}
	else if ($same == 13) {
		$output .= $values[$pos-1] . "S|";
	} 
	else if ($same == 14) {
		$output .= $values[$pos-1] . "T|";
	}
	else if ($same == 15) {
		$output .= $values[$pos-1] . "U|";
	}
	else if ($same == 16) {
		$output .= $values[$pos-1] . "V|";
	}
	else if ($same == 17) {
		$output .= $values[$pos-1] . "W|";
	}
	else if ($same == 18) {
		$output .= $values[$pos-1] . "X|";
	}
	else if ($same == 19) {
		$output .= $values[$pos-1] . "Y|";
	}
	else if ($same == 20) {
		$output .= $values[$pos-1] . "Z|";
	} else {
		$output .= $values[$pos-1] . ":" . $same . "|";
	}
}
/*
$count = array_count_values($values);
foreach ($count as $color => $times) {
    $output .= $color . ":" . $times . "|";
}
*/
#echo $output;
#die;
$com_ex = explode("|", $output);
$new = array_count_values($com_ex);
asort($new);
$new = array_reverse($new);

$chars = array(1 => "!", "@", "$", "%", "^", "&", "*", "(", ")", "_", "+", "-", "=", "{", "}", ";", "?", "/", "~", "`", "[", "]");
foreach($new as $key => $value) {
	if ($d == count($chars)) {
		break;
	}
	if (strlen($key) > 2) {
		$d++;
		$charmap .= $key . "|";
		$output = str_replace("|" . $key . "|", "|" . $chars[$d] . "|", $output);
		$output = str_replace("|" . $key, "|" . $chars[$d], $output);
	}
}
$result = "1|" . $width . "x" . $height . "|" . $charmap . "#" . $output;
echo gzcompress($result, 9);
?>