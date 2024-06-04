<?php
//Start session
session_start();
//Generate random captcha text
function generateRandomString($length = 10) {
    /*
    * @length: The length of the Captcha Text
    */
    $characters = 'a0bcde1fghi2jklm3nopq4rstu5vwx6yzA7BCDE8FGHIJ9KLMN3OPQRS2TUVW1XYZ0';
    $charactersLength = strlen($characters);

    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
//we need 5 characters max
$captcha_txt = generateRandomString(5);
//generate image with a width of 200px and height of 20px
$image = imagecreate(200, 20);
//random background image
$a=array(
"red"=>[255, 0, 0],
"green"=>[0, 155, 6],
"blue"=>[0, 0, 255],
"black"=>[0,0,0]
);

//pick random color from the array
$color_index= (array_rand($a,1));

//allocate background color with indexes from variable $color_index
$bk_clr = imagecolorallocate($image, $a[$color_index][0], $a[$color_index][1], $a[$color_index][2]);
//Final image color, set the text color to white
$img_clr = imagecolorallocate($image, 255, 255, 255); //this will include the background color and the text color

//Draw the image
imagestring($image, 5, 50, 0, $captcha_txt, $img_clr);

//Expiry time of 3 minutes
$expire = gmdate(strtotime('+3 minutes', time())); 
//concatenate the expiry time with the captcha text
$tmp_hash = $captcha_txt.$expire;

$_SESSION['captcha_set'] = TRUE;  //makes sure that the captcha your'e verifying is set
$_SESSION['captcha_token'] =  md5($tmp_hash); //hash the captcha
$_SESSION['captcha_expire'] = $expire; //expiry time

//convert the image string to a png
imagepng($image);
//Free memory
imagedestroy($image); 
?>
