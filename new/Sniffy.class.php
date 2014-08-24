<?php
ini_set('memory_limit','-1');
set_time_limit(0);

class Sniffy
{
    const VERSION = "1";

    private $image;
    private $imageResource;

    private $nibble;
    private $description;

    private $width;
    private $height;

    private $compression;
    private $pixel_compression;

    private $colors;
    private $binary;
    private $ascii;

    public static function encode($image, $compression = 0, $pixel_compression = 0) {
        $instance = new self();

        $instance->image             = $image;
        $instance->compression       = $compression;
        $instance->pixel_compression = $pixel_compression;

        // Correct compression and pixel_compression values if null
        $instance->fixArguments();

        // Get width, height, and image resource
        $instance->getImageInfo();

        // Get image colors
        $instance->getImageColors();

        // Convert colors to hex
        $instance->pixelsToBinary();

        // Binary to Ascii
        $instance->binaryToAscii();

        echo gzcompress($instance->getAscii(), 9);

        return $instance;
    }

    public static function decode($file) {
        
    }

    private function mapping($string) {
        $split = str_split($string);

        $mapping = array(
            '0' => "0000",  # 0000
            '1' => "0001",  # 0001
            '2' => "0010",  # 0010
            '3' => "0011",  # 0011
            '4' => "0100",  # 0100
            '5' => "0101",  # 0101
            '6' => "0110",  # 0110
            '7' => "0111",  # 0111
            '8' => "1000",  # 1000
            '9' => "1001",  # 1001
            'A' => "1010",  # 1010 - Delimiter character
            'B' => "1011",  # 1011 - Frequent character #1
            'C' => "1100",  # 1100 - Frequent character #2
            'D' => "1101",  # 1101 - Frequent character #3
            'E' => "1110",  # 1110 - Frequent character #4
            'F' => "1111"   # 1111 - Frequent character #5


            #255,255,255 => 001001010101001001010101001001010101 (36 bits = 4 bytes and 1 nibble)
        );

        $return = null;
        foreach ($split as $char) {
            $return .= $mapping[strtolower($char)];
        }
        return $return;
    }

    private function fixArguments() {
        if ($this->getCompression() == null) {
            $this->setCompression(0);
        }

        if ($this->getPixelCompression() == null) {
            $this->setPixelCompression($this->compression);
        }
    }

    private function getImageInfo() {
        $imageinfo = getimagesize($this->getImage());

        $this->width  = $imageinfo[0];
        $this->height = $imageinfo[1];

        $this->imageResource = imagecreatefrompng($this->getImage()) or die ("Error, image is invalid!\n");
    }

    private function getImageColors() {
        $im     = $this->getImageResource();
        $colors = array();

        for ($a = 0; $a < $this->getHeight(); $a++) {
            for ($b = 0; $b < $this->getWidth(); $b++) {
                # Adds the index of the colors into the $text array
                $imagecolor = imagecolorsforindex($im, imagecolorat($im, $b, $a));

                // If the next pixel...is relatively, to the threshold, the same as the previous, make them the same color.
                if (($this->withinThreshold($imagecolor["red"], $comp) && $this->withinThreshold($imagecolor["green"], $comp) && $this->withinThreshold($imagecolor["blue"], $comp)) && $comp != 0) {
                    array_push($colors, $prevcolor);
                } else {
                    // Converts pixels to a shade if they are within a threshold (comp)
                    if (abs($imagecolor["red"] - $imagecolor["blue"]) <= $comp_pixel && abs($imagecolor["red"] - $imagecolor["green"]) <= $comp_pixel && $comp_pixel != 0) {
                        $new_color = imagecolorexact($im , $imagecolor["red"] , $imagecolor["red"] , $imagecolor["red"]);
                        $imagecolor = imagecolorsforindex($im, $new_color);
                    }
                    array_push($colors, $imagecolor);
                    $prevcolor = $imagecolor;
                }
            }
        }

        $this->setColors($colors);
    }

    private function pixelsToBinary() {
        $colors = $this->getColors();
        
        foreach ($colors as $color) {
            $binary[] = $this->mapping(sprintf("%02s", dechex($color["red"])) . sprintf("%02s", dechex($color["blue"])) . sprintf("%02s", dechex($color["green"])));
        }
        $this->setBinary($binary);
    }

    private function withinThreshold($val, $threshold) {
        if ($val > $threshold && $val < $threshold) {
            return true;
        }
        return false;
    }

    private function binaryToAscii() {
        $binary = $this->getBinary();
        $ascii = null;

        foreach ($binary as $color) {
            $chunks = explode(" ", chunk_split($color, 8, " "));
            unset($chunks[count($chunks)-1]);
            foreach ($chunks as $chunk) {
                $ascii .= chr($chunk);
            }
        }

        $this->setAscii($ascii);
    }

    // Getters
    public function getImage() { return $this->image; }
    public function getImageResource() { return $this->imageResource; }

    public function getNibble() { return $this->nibble; }
    public function getDescription() { return $this->description; }

    public function getWidth() { return $this->width; }
    public function getHeight() { return $this->height; }

    public function getCompression() { return $this->compression; }
    public function getPixelCompression() { return $this->pixel_compression; }

    public function getColors() { return $this->colors; }
    public function getBinary() { return $this->binary; }
    public function getAscii() { return $this->ascii; }

    // Setters
    public function setImage($val) { $this->image = $val; }
    public function setImageResource($val) { $this->imageResource = $val; }

    public function setNibble($val) { $this->nibble = $val; }
    public function setDescription($val) { $this->description = $val; }

    public function setWidth($val) { $this->width = $val; }
    public function setHeight($val) { $this->height = $val; }

    public function setCompression($val) { $this->compression = $val; }
    public function setPixelCompression($val) { $this->pixel_compression = $val; }

    public function setColors($val) { $this->colors = $val; }
    public function setBinary($val) { $this->binary = $val; }
    public function setAscii($val) { $this->ascii = $val; }
}
?>
