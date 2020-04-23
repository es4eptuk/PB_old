<?php
//надо закрыть доступ

// задание максимальной ширины и высоты
$width = 500;
$height = 500;
$tpm_path = "";
    if (  0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        if(isset($_FILES['file'])) {
        $category  = $_POST['category'];
        $vendor = $_POST['vendor'];
        @mkdir("img/catalog/".$category);
        @mkdir("img/catalog/".$category."/thumb");
        $tmp = explode(".", $_FILES['file']['name']);
        $ras = end($tmp);
       // echo $_FILES['file']['name'];
        $name = 'img/catalog/'.$category.'/' . $vendor.'.jpg';
        $thumb = 'img/catalog/'.$category.'/thumb/' . $vendor.'.jpg';
         
         if(file_exists($name)) {
             echo $name;
             unlink($name);
         }
         if(file_exists($thumb)) {
            // echo $thumb;
             unlink($thumb);
         }
         
        $image_mime = image_type_to_mime_type(exif_imagetype($_FILES['file']['tmp_name']));
        //echo $image_mime;
        
        if($image_mime=='image/png') {
            tojpg($_FILES['file']['tmp_name'],$name);
            move_uploaded_file($tpm_path, $name);
        }
        else {
            //tojpg($_FILES['file']['tmp_name'],$name);
            move_uploaded_file($_FILES['file']['tmp_name'], $name);
        }

        imageresize($name,$name,500,500,100);
        imageresize($thumb,$name,80,80,75);
        }
    }
    
    
   function imageresize($outfile,$infile,$neww,$newh,$quality) {
    $im=imagecreatefromjpeg($infile);
    $k1=$neww/imagesx($im);
    $k2=$newh/imagesy($im);
    $k=$k1>$k2?$k2:$k1;

    $w=intval(imagesx($im)*$k);
    $h=intval(imagesy($im)*$k);

    $im1=imagecreatetruecolor($w,$h);
    imagecopyresampled($im1,$im,0,0,0,0,$w,$h,imagesx($im),imagesy($im));

    imagejpeg($im1,$outfile,$quality);
    imagedestroy($im);
    imagedestroy($im1);
    }
    
    function tojpg($filePath,$name) {
        $image = imagecreatefrompng($filePath);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file 
        imagejpeg($bg, $name , $quality);
        //$tpm_path = $filePath. ".jpg";
        imagedestroy($bg);
    }

?>