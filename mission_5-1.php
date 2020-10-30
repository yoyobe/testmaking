<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>

<?php 
   // DB接続設定
	$dsn = 'mysql:dbname=tb220751db;host=localhost';
	$user = 'tb-220751';
	$password = '2J6TJkTP7p';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	//TABLE作成
	$sql = "CREATE TABLE IF NOT EXISTS userinfo"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"//加算されていく
	. "name char(32),"
	. "comment TEXT,"
	. "day TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
	
	
	
	
	//投稿
	if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && 
	empty($_POST["delete"]) && empty($_POST["edit"]) && empty($_POST["delpass"]) && empty($_POST["editnum"])) {
	
	$sql = $pdo -> prepare("INSERT INTO userinfo (name, comment, day, pass) 
	VALUES (:name, :comment, :day, :pass)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql->bindParam(':day', $day, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	$name = $_POST["name"];
	$comment = $_POST["comment"]; 
	$day = date("Y年m月d日 H:i:s");
	$pass = $_POST["pass"];
	$sql -> execute();
	
	}
   
   //削除
   if(!empty($_POST["delete"]) && !empty($_POST["delpass"]) && 
   empty($name) && empty($comment) && empty($pass) && empty($_POST["edit"])) {
        $id = $_POST["delete"];
        $pass = $_POST["delpass"];
	    $sql = 'delete from userinfo where id=:id AND pass=:pass';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
	    $stmt->execute();

	    
	    $sql = 'SELECT * FROM userinfo';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach($results as $delrow) {
	        if($delrow['id'] == $_POST['delete'] && $delrow['pass'] != $_POST['delpass']) {
	            echo "パスワードが違います";
	        }
	    }
	    
   }
   
   //編集番号選択
   if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
    && empty($_POST["delete"]) && empty($_POST["delpass"]) 
    && empty($_POST["name"]) && empty($_POST["comment"]) && empty($_POST["pass"])) {
        
        $id = $_POST["edit"];
        $pass = $_POST["editpass"];
        $sql = 'SELECT * FROM userinfo WHERE id=:id AND pass=:pass';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR); 
        $stmt->execute();   // ←SQLを実行する。
        
        $sql = 'SELECT * FROM userinfo';
	    $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(); 
        foreach($results as $rerow) {
            if($rerow['id'] == $_POST['edit'] && $rerow['pass'] == $_POST['editpass']) {
                $rename = $rerow['name'];
                $recomment = $rerow['comment'];
                $repass = $rerow['pass'];
                $reid = $rerow['id'];
            }elseif($rerow['id'] == $_POST['edit'] && $rerow['pass'] != $_POST['editpass']) {
                echo "パスワードが違います";
            }
        }
        
    }
    
    //内容編集
    if(!empty($_POST["editnum"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) 
    && empty($_POST["delete"]) && empty($_POST["edit"]) && empty($_POST["delpass"])) {
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $id = $_POST["editnum"];
        $pass = $_POST["pass"];
        
        $sql = 'UPDATE userinfo SET name=:name,comment=:comment, pass=:pass WHERE id=:id';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR); 
	    $stmt->execute();
        
        
	    $results = $stmt->fetchAll();
	    
    }
   
	?>
	
 <form action ="" method="post">
        
        <input type="name" name="name"  placeholder="名前" value="<?php echo $rename ?>"><br>
        <input type="text" name="comment"  placeholder="コメント" value="<?php echo $recomment ?>"><br>
        <input type="text" name="pass"  placeholder="パスワード" value="<?php echo $repass ?>">
        <input type="submit"><br>
        
         <!--編集番号がポストされた場合以下の削除フォームと編集フォームは不要になるのでその場合type属性をhiddenにして隠すための処理-->
        <?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
        && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo "投稿番号（変更しないでください):";}
         ?>
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
         && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo text;}
        else{
            echo hidden;    
            }
         ?>" name="editnum" value="<?php echo $reid ?>"><br><br>
        
        <!--削除フォーム-->
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
         && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo hidden;}
        else{
            echo number;    
            }
         ?>" name ="delete" placeholder ="削除対象番号"><br>
        
        
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
        && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo hidden;}
        else{
            echo text;    
            }
         ?>" name="delpass" placeholder="パスワード">
        
        
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"]) 
        && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo hidden;}
        else{
            echo submit;    
            }
         ?>" value="削除"><br><br>
        
        
        <!--編集フォーム-->
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
         && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo hidden;}
        else{
            echo number;    
            }
         ?>" name="edit" placeholder="編集対象番号"><br>
            
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"]) 
        && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo hidden;}
        else{
            echo text;    
            }
         ?>" name="editpass" placeholder="パスワード">
            
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["editpass"])
         && $reid == $_POST['edit'] && $repass == $_POST['editpass']) {
            echo hidden;}
        else{
            echo submit;    
            }
         ?>" value="編集"><br><br>
        
        
    </form>
    <?php 
    echo "__________________掲示板______________________<br><br>";

	//書き込み処理
	$sql = 'SELECT * FROM userinfo';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
	//$rowの中にはテーブルのカラム名が入る
	    echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['day'].'<br>';
	    echo "<hr>";
	    }
    ?>

        
</body>
</html>