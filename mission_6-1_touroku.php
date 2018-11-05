<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>mission6-1_touroku</title>
  </head>
<body>

<p>新規登録</p>

<?php	
     
//データベース接続
$dsn = 'データベース名' ;
$user = 'ユーザー名' ;
$psw = 'パスワード名' ;
$pdo = new PDO($dsn,$user,$psw); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//ログインテーブル作成 カラム名:name,password
$sql = "CREATE TABLE IF NOT EXISTS tblogin"
."("
."name char(32),"
."password char(32),"
."PRIMARY KEY(name)"
.");";
$stmt = $pdo->query($sql); 

if(isset($_POST["touroku"])){
	//パスワードが半角英数字でなかったら、再入力
	if(!ctype_alnum($_POST["password"])){
	    echo "半角英数字にしてください";
	}
	//再確認用のパスワードが一致していなかったら再入力
	elseif($_POST["password"]!=$_POST["password2"]){
	    echo "再確認のパスワードが一致していません";
	}
	else{ 
	//同じユーザー名がすでにDB内に存在するか照合
	$sql = $pdo->prepare("SELECT EXISTS(SELECT * FROM tblogin WHERE name=:name)");
	$name = $_POST["name"];
	$sql ->bindValue(':name',$name,PDO::PARAM_STR);
	$sql -> execute();
	$hantei=$sql->fetch();
	
		//ユーザー名がすでにDB内部にあったら、無効
	    if(intval($hantei[0])){
	   		echo "同じユーザー名が既に使われています。別の名前にしてください";
		}
		//送信されたユーザー名、パスワードをDB内に格納
		else{
	         $sql = $pdo->prepare("INSERT INTO tblogin(name,password) VALUES (:name,:password)");
	         $name = $_POST["name"];
	         $password = $_POST["password"];
	         $sql -> bindValue(':name',$name,PDO::PARAM_STR);
	         $sql -> bindValue(':password',$password,PDO::PARAM_STR);
	         $sql -> execute();
	         echo "登録が完了しました";
		}
	}
}
?>


<!--入力フォーム-->
<form method = "post">

    ユーザー名:<input type = "text" name = "name" required ><br>
    パスワード(半角英数字):<input type = "text" name = "password" maxlength = "15"  required > <br>
    パスワード( 再確認用  ):<input type = "text" name = "password2" maxlength = "15" required > 
    <input type = "submit" name = "touroku" value = "登録"> <br>
     <a href="mission_6-1_login.php"> ログイン画面に戻る </a>
<form>


</body>
</html>
