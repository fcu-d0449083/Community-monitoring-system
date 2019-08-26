<?php  //拍照功能的數據處理//
date_default_timezone_set("Asia/Taipei");
require_once(dirname(__FILE__) . '/connectionClass.php');
class webcamClass extends connectionClass
{
    private $imageFolder = "webcamImage/";
    private $fileName = "";

    //This function will create a new name for every image captured using the current data and time.
    private function getNameWithPath() //讀取檔名 如果沒檔名預設時間.jpg
    {
        if ($this->fileName == '') {
            $this->fileName = date('YmdHis');
        }
        $name = $this->imageFolder . $this->fileName . ".jpg";
        return $name;
    }
    public function setFileName($val)
    {
        $this->fileName = $val;
    }

    //function will get the image data and save it to the provided path with the name and save it to the database
    public function showImage()  //在網頁下方顯示剛拍下來的照片//
    {
        $file = file_put_contents($this->getNameWithPath(), file_get_contents('php://input'));
        //        var_dump(file_get_contents('php://input'));
        if (!$file) {
            $this->saveImageToDatabase($this->getNameWithPath());
            return "ERROR: Failed to write data to " . $this->getNameWithPath() . ", check permissions\n";
        } else {
            $this->saveImageToDatabase($this->getNameWithPath(), $this->fileName);         // this line is for saveing image to database
            return $this->getNameWithPath();
        }
    }

    //function for changing the image to base64  //把照片轉為base64//
    public function changeImagetoBase64($image)
    {
        $path = $image;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function saveImageToDatabase($imageurl, $fileName)//存資料到資料庫//
    {
        $image = $imageurl;
        $inputfilename = $fileName;
        $str = preg_replace("[\d]", '', $fileName);
        $image64 =  $this->changeImagetoBase64($image);
        if ($image and $image64) {
            $query = "Insert into snapshot (Image) values('" . $image . "')";//新增一筆含路徑的檔名到snapshot//
            $result = $this->query($query);
            $query = "Insert into namee (name) values('" . $inputfilename . "')";//新增一筆檔名到namee//
            $result = $this->query($query);
            $query = "Insert into nameonly (name) values('" . $str . "')";//新增一筆去掉數字只有人名到nameonly//
            $result = $this->query($query);
            $query = "Insert into photoo (photo) values('" . $image64 . "')";//新增一筆照片image64到photoo//
            $result = $this->query($query);
            $query = "UPDATE `switch` SET `switch` = '1' WHERE `switch` = 0;";//更新switch表格//
            $result = $this->query($query);
            if ($result) {
                return "Image saved to database";
            } else {
                return "Image not saved to database";
            }
        }
    }
}
