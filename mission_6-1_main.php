<?php  session_start(); ?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>mission6-1_main</title>
    <link rel="stylesheet" href="mission_6-1main.css">  
<?php
if(!isset($_SESSION["login"]) | !isset($_COOKIE["username"])){
    echo  "<meta http-equiv='refresh' content='2;URL=mission_6-1_login.php'>" ;
    exit('ログイン画面に移動します');
} ?>

</head>

<body>

<!--見出し-->
<div class='title'>
<span class='title'>画像・動画投稿掲示板</span>
</div>




<div class='migi'>

<!--動画、画像アップデートフォーム-->
<form method="post" enctype="multipart/form-data">
<p>画像・動画をアップロード</p>
動画は[mp4,wmv,webm,mpg,mov] <br>画像は[png,gif,jpeg(jpg)]<br>のみ対応しています
  <input class='fileform' type="file" name="upfile" required accept = "video/*,image/*"> 
  <input class='submit' type="submit" name="submit" value="アップロード">
</form>

</div>


<?php
//データベース接続
$dsn = 'データベース名' ;
$user = 'ユーザー名' ;
$psw = 'パスワード名' ;
$pdo = new PDO($dsn,$user,$psw); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*テーブル作成 tbfile(id,username,filename,date)
$sql = "CREATE TABLE IF NOT EXISTS tbfile"
."("
."id INT AUTO_INCREMENT,"
."username char(32),"
."filename TEXT,"
."date TEXT,"
."PRIMARY KEY(id)"
.");";
$stmt = $pdo->query($sql);*/ 


//テーブル作成 tbcomment(id,comusername,filename,comment)
$sql = "CREATE TABLE IF NOT EXISTS tbcomment"
."("
."id INT AUTO_INCREMENT,"
."comusername char(32),"
."filename TEXT,"
."comment TEXT,"
."PRIMARY KEY(id)"
.");";
$stmt = $pdo->query($sql); 




//コメント送信ボタンが押されたら
if(isset($_POST["comsub"])){
	//tbcommentにファイル名、コメント投稿者名,コメント内容を格納
     $sql = $pdo->prepare("INSERT INTO tbcomment(id,comusername,filename,comment) VALUES (0,:comusername,:filename,:comment)");
	 $comusername = $_COOKIE["username"];
	 $sql -> bindValue(':comusername',$comusername,PDO::PARAM_STR);
	 $sql -> bindValue(':filename',$_POST['filename'],PDO::PARAM_STR);
	 $sql -> bindValue(':comment',$_POST['comment'],PDO::PARAM_STR); 
	 $sql -> execute();
	echo "コメントを送信しました";
}


//アップロードボタンが押されたら
if(isset($_POST["submit"])){
//uploadディレクトリに動画、画像を格納
	$updir = "6-1_upload/";
	$filename = $updir.uniqid().$_FILES['upfile']['name'] ;
    //失敗
	if(move_uploaded_file($_FILES['upfile']['tmp_name'], $filename)==FALSE){
	    echo "アップロードに失敗しました<br>";
	}
    //成功
	else{
        //拡張子を調査
		$filepath = pathinfo($filename,PATHINFO_EXTENSION);
		//動画ならmp4に変更
		if($filepath=='mp4'|| $filepath=='mov' || $filepath=='mpg' || $filepath=='webm' || $filepath=='wmv'){
			rename($filename,uniqid().".mp4") ;
			echo "動画をアップロードしました<br>";

			//ファイル名をデータベースに格納
	        $sql = $pdo->prepare("INSERT INTO tbfile(id,username,filename,date) VALUES (0,:username,:filename,:date)");
	        $username = $_COOKIE["username"];
	        $sql -> bindValue(':username',$username,PDO::PARAM_STR);
	        $sql -> bindValue(':filename',$filename,PDO::PARAM_STR);
	        $sql -> bindValue(':date',date("Y/m/d  H:i:s"),PDO::PARAM_STR);
	        $sql -> execute();
		}
		elseif($filepath=='png'||$filepath=='jpeg'||$filepath=='jpg'||$filepath=='gif'){
			echo "画像をアップロードしました<br>";

	        //ファイル名をデータベースに格納
	        $sql = $pdo->prepare("INSERT INTO tbfile(id,username,filename,date) VALUES (0,:username,:filename,:date)");
	        $username = $_COOKIE["username"];
	        $sql -> bindValue(':username',$username,PDO::PARAM_STR);
	        $sql -> bindValue(':filename',$filename,PDO::PARAM_STR);
	        $sql -> bindValue(':date',date("Y/m/d  H:i:s"),PDO::PARAM_STR);
	        $sql -> execute();
		}
        else{
			echo "その拡張子はサポートされていません";
		}
	}	
}

//ファイル名,ユーザー名、時刻をDBから取り出す
$sql ="SELECT * FROM tbfile ;";
$allresult = $pdo->query($sql);


//ファイルを埋め込み
foreach ($allresult as $row){
    $rowfilename=$row['filename'];
	
    echo  "<div class=namedate>"."【".htmlspecialchars($row['username'])."】"."   ".$row['date']."</div>" ;
    echo "<div class='filecomment'>";
    //ファイル表示
    echo  "<div class='file'>";
    //動画
    if(pathinfo($rowfilename,PATHINFO_EXTENSION)=='mp4'){ 
        echo  "<video  src=$rowfilename  alt='動画' controls> </video>";
    }
	//画像
	else{
		echo  "<img  src=$rowfilename alt='画像' >";
	}
    //コメント送信フォーム	
    echo  "<form method='post'>"
          ."<input class='comment' type=text name='comment' placeholder='コメント' required >"
          ."<input type=hidden name='filename' value=$rowfilename >"
          ." <input class='submit' type=submit name='comsub' value='送信'>"
          ."</form> </div>";
    
    //DBから、該当ファイルに対するコメントのみ取り出す 
    $sql ="SELECT * FROM tbcomment WHERE filename='$rowfilename' ;";
    $comresult = $pdo->query($sql);   
    //コメント表示欄 
    echo "<div class='comment'>";
    echo "<p>"."コメント一覧"."</p>" ;
	foreach ($comresult as $comrow){
    	echo  "<br><br>"."【".$comrow['comusername']."】"."<br>".$comrow['comment'] ;
	}



    echo "</div>";
		

    echo "</div>";


}   


?>


</body>
</html>
