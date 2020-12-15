<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>tech_mission5</title>
</head>
<body>

  <h2>ドラえもんの道具で１番欲しいものは？</h2>

  <!-- 投稿フォーム -->
  <h4>［投稿］</h4>
  <form method = "POST">
    名前：<input type="text" name="name" placeholder="名前"><br>
    コメント：<input type="text" name="comment" placeholder="コメント"><br>
    パスワード：<input type="password" name="pass">
    <input type="submit" name="submit"><br>
    <br>
  </form>

  <!-- 削除フォーム -->
  <h4>［削除］</h4>
  <form method = "POST">
    削除対象番号：<input type="number" name="deleteNO"><br>
    パスワード：<input type="password" name="deletepass">
    <input type="submit" name="delete" value="削除"><br>
    <br>
  </form>

  <!-- 編集フォーム -->
  <h4>［編集］</h4>
  <form method = "POST">
    編集対象番号：<input type="number" name="editNO"><br>
    名前：<input type="text" name="editname"><br>
    コメント：<input type="text" name="editcom"><br>
    パスワード：<input type="password" name="editpass">
    <input type="submit" name="edit" value="編集"><br>
    <br>
  </form>

    <!-- コードの流れ
    1.新規投稿 
      送信がpostされ、名前、コメント、パスワードあり
      →それぞれの入力内容を変数に代入→DB接続→テーブルにデータを入力
    
    2.削除処理
      削除がPOSTされ、番号あり→
      →削除したい番号と同じid、入力したパスワードとテーブルのパスワードが一致すれば削除
      →更新したもの（削除したidのレコードが空になったもの）を表示

    3.編集処理
      編集がPOSTされ、番号、名前、コメント、パスワードあり
      →編集したい番号と同じid、入力したパスワードとテーブルのパスワードが一致している
      →編集したい内容に更新

    4.表示機能
      上から順に実行され残ったテーブル内の内容を表示させる -->
  
  
    
  <?php
    // ①新規投稿(INSERT文)
    // 名前、コメント、パスワードあり
    if(isset($_POST["submit"])){
      if(isset($_POST["name"], $_POST["comment"],$_POST["pass"]) ){
        
      // それぞれ変数に代入 
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $pass=$_POST["pass"];
        

      // DB接続
        $dsn='mysql:dbname=****;host=localhost';
        $user='****';
        $password='****';
        $pdo= new PDO($dsn,$user,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブルにデータを入力
        $sql= $pdo -> prepare("INSERT INTO mission (name, comment, date, password) VALUES(:name, :comment, :date, :password)");
        $sql->bindParam(':name', $name, PDO::PARAM_STR);
        $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql->bindParam(':date', $date, PDO::PARAM_STR);
        $sql->bindParam(':password', $pass, PDO::PARAM_STR);

        // executeでクエリ（問い合わせ）を実行
        $sql -> execute();
      } 
    }


  //  ③削除処理(DELETE文)
  // 削除対象番号とパスワードあり
    if(isset($_POST["delete"])){
      if(isset($_POST["deleteNO"], $_POST["deletepass"])){
        
      // パスワードが一致しているのか確認したい
      // 1.削除したいとpostされた番号と同じid＆パスワードが同じレコードだけ抽出
      // 2.削除する（mission_4-8)
      
      // DB接続 
      $dsn='mysql:dbname=****;host=localhost';
      $user='****';
      $password='****';
      $pdo= new PDO($dsn,$user,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
      
      // 連番に振っていくようにするやり方（＠でエラー出てくる。。。idをAUTO_INSERTで指定しているからできないのかも）
      // $sql = set @i=0; update tbmission_5 set name, comment, date, password = (@i= $i + 1 );


      // 削除
      $id=$_POST["deleteNO"];
      $deletepass=$_POST["deletepass"];
            
      // (↓参考) https://teratail.com/questions/22704
      //        https://techacademy.jp/magazine/29052 (bindparam と 疑問符プレースホルダー(id=?) について)
      
      $sql='delete from mission WHERE id=? AND password=?';
      $stmt=$pdo->prepare($sql);
      $result = $stmt -> fetch();  //１つだけ抜き出す

          $stmt -> bindParam(1, $id, PDO::PARAM_INT);
          $stmt -> bindParam(2, $deletepass, PDO::PARAM_STR);

          $stmt -> execute();
      } 
    }
    

    // ④編集処理(UPDATE文)
    // 編集したい番号、名前、コメント、パスワードあり
    // UPDATE [テーブル名] SET [更新処理] WHERE [条件式];

    if(isset($_POST["edit"])){
      if(isset($_POST["editNO"], $_POST["editname"], $_POST["editcom"], $_POST["editpass"])){

        // DB接続 
        $dsn='mysql:dbname=****;host=localhost';
        $user='****';
        $password='****';
        $pdo= new PDO($dsn,$user,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        // それぞれの値を変数に代入
        $id=$_POST["editNO"];
        $name=$_POST["editname"];
        $comment=$_POST["editcom"];
        $date=date("Y/m/d H:i:s");
        $editpass=$_POST["editpass"];

        // 更新
        $sql='UPDATE mission SET name=:name, comment=:comment, date=:date WHERE id=:id AND password=:editpass';
        $stmt=$pdo -> prepare($sql);
        // $result = $stmt -> fetch();  //１つだけ抜き出す

          // 値をバインド
          $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
          $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
          $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
          $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
          $stmt -> bindParam(':editpass', $editpass, PDO::PARAM_STR);

          // executeでクエリを実行
          $stmt -> execute();
      }    
    }


    // 表示処理（SELECT文）

    // DB接続
    $dsn='mysql:dbname=****;host=localhost';
    $user='****';
    $password='****';
    $pdo= new PDO($dsn,$user,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    // データを取得し表示
    $sql = 'SELECT id, name, comment, date FROM mission';
    $stmt = $pdo->query($sql);
    $results=$stmt->fetchAll();

    foreach($results as $row){
    //$rowの中にはテーブルのカラム名が入る
      echo $row['id'].','.' ';
      echo $row['name'].','.' ';
      echo $row['comment'].','.' ';
      echo $row['date'].'<br>';
    }
      echo "<hr>";
  ?>
</body>
</html>