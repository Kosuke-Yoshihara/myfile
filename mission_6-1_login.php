<?php
session_start();  
if(isset($_POST["sousin"])){
   setcookie("username",$_POST["name"]);
}
 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>mission6-1_login</title>
 

<?php
	
$keikoku="";   
//データベース接続
$dsn = 'データベース名' ;
$user = 'ユーザー名' ;
$psw = 'パスワード名' ;
$pdo = new PDO($dsn,$user,$psw); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*ログインテーブル作成 カラム名:name,password
$sql = "CREATE TABLE IF NOT EXISTS tblogin"
."("
."name char(32),"
."password char(32),"
."PRIMARY KEY(name)"
.");";
$stmt = $pdo->query($sql); */ 
if(isset($_POST["sousin"])){
//ユーザー名をDB内で照合
    $sql = $pdo->prepare("SELECT EXISTS(SELECT * FROM tblogin WHERE name=:name)");
	$name = $_POST["name"];
	$sql ->bindValue(':name',$name,PDO::PARAM_STR);
	$sql -> execute();
	$hantei=$sql->fetch();

	//送信されたユーザー名が存在していない→無効
    if(!intval($hantei[0])){
	   		$keikoku = "そのユーザー名は登録されていません";
	}
	//ユーザー名が存在している→パスワードを照合
    else{
	    $sql = $pdo->prepare("SELECT * FROM tblogin WHERE name=:name");
	    $name = $_POST["name"];
	    $sql ->bindValue(':name',$name,PDO::PARAM_STR);
	    $sql ->execute();
	    $hantei=$sql->fetch();
//	    var_dump($hantei);

		//パスワードが不一致→無効
		if($hantei['password']!==$_POST["password"]){
             $keikoku = "パスワードが違います";
		}
		//一致→メインページへ
		else{
            $_SESSION["login"]=1; //ユーザー名を別ページに反映できるように
        	echo  "<meta http-equiv='refresh' content='2;URL=mission_6-1_main.php'>" ;
		}
	}
}
?>

</head>

<body>

<p>
<?php echo $keikoku; ?>
</p>

 
<!--入力フォーム-->
<form method = "post">

    ユーザー名:<input type = "text" name = "name"  maxlength="15" required ><br>
    パスワード(半角英数字):<input type = "password" name = "password" maxlength="15" required >
    <input type = "submit" name = "sousin" value = "送信"> <br><br>
    
    <a href="mission_6-1_touroku.php"> 新規登録はこちら </a>
    
</form>
</body>
</html>
