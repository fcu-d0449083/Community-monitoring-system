<?
define('MYSQL_SERVER', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', 'a92015a92015');
define('MYSQL_DB', 'timedb');

date_default_timezone_set("Asia/Taipei");
error_reporting(E_ALL);
$conn = mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
mysqli_query($conn, "SET NAMES 'UTF8'");
mysqli_query($conn, "SET CHARACTER 'UTF8'");
mysqli_query($conn, "SET CHARACTER_SET_RESULTS = 'UTF8'");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>社區監控</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="jpeg_camera/jpeg_camera_with_dependencies.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <style>
        #camera {
            width: 600px;
            height: 480px;
        }
    </style>
    <script type="text/javascript">
        function Ajax() { //////在監控系統中讀文字檔來顯示現在監控到的人名//////
            var
                $http,
                $self = arguments.callee;

            if (window.XMLHttpRequest) {
                $http = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                try {
                    $http = new ActiveXObject('Msxml2.XMLHTTP');
                } catch (e) {
                    $http = new ActiveXObject('Microsoft.XMLHTTP');
                }
            }

            if ($http) {
                $http.onreadystatechange = function() {
                    if (/4|^complete$/.test($http.readyState)) {
                        document.getElementById('ReloadThis').innerHTML = $http.responseText;
                        setTimeout(function() {
                            $self();
                        }, 1000);
                    }
                };
                $http.open('GET', 'loadtxt.php' + '?' + new Date().getTime(), true);
                $http.send(null);
            }

        }
    </script>
</head>

<body>
    <div class="container">
        <h3>
            <font color="blue" size=8 face="DFKai-sb">社區監控系統 </font>
        </h3>
        <div>
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#home" data-toggle="tab">
                        <font size=4>拍照</font>
                    </a>
                </li>
                <li>
                    <a href="#about" data-toggle="tab">
                        <font size=4>監控</font>
                    </a>
                </li>
                <li>
                    <a href="#contact" data-toggle="tab">
                        <font size=4>關於我們</font>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="home">
                    <h3>
                        <font color=black size=6 face="DFKai-sb">拍照</font>
                    </h3>
                    <p>
                        <div class="container">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#area1">拍照放進資料庫</a>
                                        </h4>
                                    </div>
                                    <div id="area1" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            <div class="container">
                                                <div class="col-md-6">
                                                    <div class="text-center">
                                                        <div id="camera_info"></div>
                                                        <div id="camera"></div><br>
                                                        檔案名稱: <input type="text" name="file_name" value="" />
                                                        <button id="take_snapshots" class="btn btn-success btn-sm">Take Snapshots</button><br>
                                                        <button onclick="csvv()">snapshot路徑表格csv</button>
                                                        <button onclick="csvvv()">nameonly表格csv</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Image</th>
                                                                <th>Image Name</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="imagelist">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 區塊 id要改必須echarts_js.php一起改 -->
                            <div id="container_echarts" style="height: 800px"></div>
                            <select id="yType">
                                <option value="last30Day">最近30天</option>
                                <option value="last7Day">最近7天</option>
                                <option value="today">今天</option>
                            </select>
                            <select id="xType">
                            </select>
                            <? require_once("echarts_js.php"); //這邊路徑要對
                            ?>
                            <!-- 區塊 -->
                            <br><br><br><br>
                            <table>
                                <thead>
                                    <tr>
                                        <th>名字</th>
                                        <th>開始時間</th>
                                        <th>結束時間</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?
                                    $sql = "SELECT * FROM time ";
                                    
                                    $query =  mysqli_query($conn, $sql);
                                    while ($data = mysqli_fetch_array($query, MYSQLI_ASSOC)) { ?>
                                        <tr>
                                            <td style="padding: 0 10px;"><?= $data['user_name'] ?></td>
                                            <td style="padding: 0 10px;"><?= $data['year'] ."/". $data['month'] ."/". $data['day']."  ".$data['hour'] .":". $data['start_minute'] .":". $data['start_second']?></td>
                                            <td style="padding: 0 10px;"><?= $data['year'] ."/". $data['month'] ."/". $data['day']."  ".$data['hour'] .":". $data['end_minute'] .":". $data['end_second'] ?></td>
                                        </tr>
                                    <? } ?>
                                </tbody>
                            </table>
                            
                        </div>
                    </p>
                </div>
                <div class="tab-pane" id="about">
                    <h3>
                        <font color="black" size=6 face="DFKai-sb">監控 </font><br>
                        <img id="reloadImage" src="show/showimage.jpg">
                        <script type="text/javascript">
                            setTimeout(function() {
                                Ajax();
                            }, 1000);
                        </script>
                        <div id="ReloadThis">Default text</div>
                    </h3>
                    <p>
                        <font color="black" size="5">
                        </font>
                    </p>
                </div>
                <div class="tab-pane" id="contact">
                    <h3>
                        <font color="black" size=6 face="DFKai-sb">關於我們 </font><br><br><br>
                        <font face="DFKai-sb">
                            組員:<br>
                            資訊四丁徐任亨<br>
                            資訊四丁黃子軒<br>
                            資訊四丁陳俊維<br>
                        </font>
                    </h3>
                    <p></p>
                </div>
            </div>
        </div>
</body>
<script>
    function csvv() { ///////使用outputcsv.php來輸出資料庫為csv檔////////
        location.href = "outputcsv.php?my_var=charlie";
    }

    function csvvv() {
        location.href = "outputcsvv.php?my_var=charlie";
    }

    function done() {
        $('#snapshots').html("uploaded");
    }

    function reloadImagge() { //////監控系統(讀取不斷覆蓋的showimage.jpg)/////
        $('#reloadImage').attr('src', 'show/showimage.jpg?' + Math.random());
    }
    $(document).ready(function() {
        var options = {
            shutter_ogg_url: "jpeg_camera/shutter.ogg",
            shutter_mp3_url: "jpeg_camera/shutter.mp3",
            swf_url: "jpeg_camera/jpeg_camera.swf",
        };
        var camera = new JpegCamera("#camera", options);
        $('#take_snapshots').click(function() { ///拍照function///
            var snapshot = camera.capture();
            snapshot.show();
            snapshot.upload({
                api_url: "action.php?file_name=" + $('[name="file_name"]').val()
            }).done(function(response) {
                $('#imagelist').prepend("<tr><td><img src='" + response + "' width='100px' height='100px'></td><td>" + response + "</td></tr>");
                camera = new JpegCamera("#camera", options);
            }).fail(function(response) {
                alert("Upload failed with status " + response);
            });
        })
        window.setInterval(reloadImagge, 1000); //////刷新監控功能的照片/////
    });
</script>

</html>