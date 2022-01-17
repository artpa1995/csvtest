
<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
  
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
  
if (isset($_POST['submit'])) {
   
    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'product/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      
    if(isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
      
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);
      
        if('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
  
        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
  
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        // echo "<pre>";
        // print_r($sheetData);die;
        
        if (!empty($sheetData)) {
           
            for ($i=1; $i<count($sheetData); $i++) { //skipping first row
                $u = 0;
                $ProductID = !empty($sheetData[$i][0]) ? $sheetData[$i][0] : "0";
                $ProductCategory =!empty($sheetData[$i][1]) ? $sheetData[$i][1] : "0";
                $Deeplink = !empty(urlencode($sheetData[$i][2])) ? urlencode($sheetData[$i][2]) : "0";
                $ProductName = !empty($sheetData[$i][3]) ? $sheetData[$i][3] : "0";
                $ImageUrl =  !empty(urlencode($sheetData[$i][4])) ?urlencode($sheetData[$i][4]) : "0";
                $ImageUrl2 = !empty(urlencode($sheetData[$i][5])) ? urlencode($sheetData[$i][5]): "0";
                $ImageUrl3 = !empty(urlencode($sheetData[$i][6])) ? urlencode($sheetData[$i][6]) : "0";
                $ImageUrl4 = !empty(urlencode($sheetData[$i][7])) ? urlencode($sheetData[$i][6]) : "0";
                $ProductDescription = !empty($sheetData[$i][8]) ? $sheetData[$i][8] : "0";
                $BrandName = !empty($sheetData[$i][9]) ? $sheetData[$i][9] : "0";
                $Price = !empty($sheetData[$i][10]) ? $sheetData[$i][10] : "0";
                $PreviousPrice =  !empty($sheetData[$i][11]) ? $sheetData[$i][11] : "0";
                $AvailableSizes =   !empty($sheetData[$i][12]) ?  strval($sheetData[$i][12]) : "0";
                $Color = !empty(strval($sheetData[$i][13])) ? strval($sheetData[$i][13]) : "0";
                $EAN = !empty(strval($sheetData[$i][14])) ? strval($sheetData[$i][14]) : "0";
                $Gender = !empty(strval($sheetData[$i][15])) ? strval($sheetData[$i][15]) : "0";
                $ProductQuantity = !empty(strval($sheetData[$i][16])) ? strval($sheetData[$i][16]) : "0";
                $Stylekey = !empty(strval($sheetData[$i][17])) ? strval($sheetData[$i][17]) : "0";
                $BestPerformer = !empty(strval($sheetData[$i][18])) ? strval($sheetData[$i][18]) : "0";
                $StandardSize = !empty(strval($sheetData[$i][19])) ? strval($sheetData[$i][19]) : "0";
                $Kollektion = !empty(strval($sheetData[$i][20])) ? strval($sheetData[$i][20]) : "0";
                $Merchant = !empty(strval($sheetData[$i][21])) ? strval($sheetData[$i][21]) : "0";
                $ProductScore = !empty(urlencode(strval($sheetData[$i][22]))) ? strval($sheetData[$i][22]) : "0";
                $matches = [];
                preg_match_all('/\s*([^\s].+)\s*:\s*([^\s].+)\s*(;|$)/U', $ProductDescription, $matches);
                $descriptions = array_combine($matches[1], $matches[2]);
                $keywords = preg_split("/\s*>\s*/", $ProductCategory);
                $kk = $keywords;
                $category = end($keywords);
                array_pop($keywords);
                $kkk = $kk;

                for ($j=0; $j<count($kkk); $j++) { 
                  
                    $cat =  $kk[0];
                    $catchild = $kk[1];
                    $parentcat =  array_shift($kk);
               
                    $select = $db->query("SELECT * FROM `category` WHERE `Category` = '$cat' ");
                    if(!empty($select)){
                        $row = mysqli_fetch_assoc($select);
                    }
                    if(empty($row)){
                            $tt = $db->query("INSERT INTO category( category, parentCategory, childCategory) VALUES('$cat','$parentcat', '$catchild')");
                    }

                }

                $select = $db->query("SELECT * FROM `products` WHERE `ProductID` = '$ProductID' ");
                if(!empty($select)){
                    $row = mysqli_fetch_assoc($select);
                }

                if(empty($row)){
                    $t = $db->query("INSERT INTO products(ProductID, ProductCategory, Deeplink, ProductName, ImageUrl, ImageUrl2, ImageUrl3, ImageUrl4, BrandName, EAN, Stylekey, BestPerformer, Kollektion, Merchant, ProductScore)
                    VALUES('$ProductID', '$category', '$Deeplink', '$ProductName',  '$ImageUrl', '$ImageUrl2', '$ImageUrl3', '$ImageUrl4', '$BrandName', '$EAN', '$Stylekey', '$BestPerformer', '$Kollektion', '$Merchant', '$ProductScore')");
                }else{
                    $select = $db->query("UPDATE products SET ProductID = '$ProductID', ProductCategory = '$category', Deeplink = '$Deeplink', ProductName = '$ProductName', ImageUrl =  '$ImageUrl', ImageUrl2 = '$ImageUrl2', ImageUrl3 = '$ImageUrl3', ImageUrl4 = '$ImageUrl4', BrandName = '$BrandName', EAN = '$EAN', Stylekey = '$Stylekey', BestPerformer = '$BestPerformer', Kollektion = '$Kollektion', Merchant = '$Merchant', ProductScore = '$ProductScore' WHERE `ProductID` = '$ProductID'");
                }

                $select = $db->query("SELECT * FROM `brand` WHERE `ProductID` = '$ProductID' ");
                if(!empty($select)){
                    $row = mysqli_fetch_assoc($select);
                }
                
                if(empty($row)){
                    $t = $db->query("INSERT INTO brand(ProductID, BrandName) VALUES('$ProductID', '$BrandName')");
                }else{
                    $select = $db->query("UPDATE brand SET ProductID = '$ProductID', BrandName = '$BrandName' WHERE `ProductID` = '$ProductID'");

                }

                
                
                $query = "INSERT INTO productdescription (productID, meta_key, meta_value) VALUES";
                $values = [];
                 foreach($descriptions as $description => $value){
                    $values[] = "('$ProductID', '$description', '$value')";
                  
                 }
                //  echo "<pre>";
                //  print_r($values);

                 $select = $db->query("SELECT * FROM `productdescription` WHERE `productID` = '$ProductID' AND `meta_key` = '$description'");
                 
               
                if(!empty($select)){
                    $row = mysqli_fetch_assoc($select);
                }
                
                if(empty($row)){
                    $querys = $query.implode(",", $values);
                    $t = $db->query($querys);
                }else{
                    foreach($descriptions as $description => $value){
                        $select = $db->query("UPDATE productdescription SET  meta_value = '$value' WHERE `ProductID` = '$ProductID' AND `meta_key` = '$description'");  
                     }
                   
                }
               
                
             
            }
        }
        echo "Records inserted successfully.";
    } else {
        echo "Upload only CSV or Excel file.";
    }
}

 
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>shop</title>
</head>
<body>
    <style>
        .myform{
            display: flex;
            flex-direction: column;
            background-color: grey;
            width: 100%;
            justify-content: center;
            align-items: center;
        }
    </style>
<form enctype="multipart/form-data" method="post" class="myform">
    <input type="file" name="file">
   
    <input type='submit' name="submit" value="submit">
</form>
</body>
</html