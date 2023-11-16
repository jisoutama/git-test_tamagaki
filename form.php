<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コメントフォーム</title>
    <link rel="stylesheet" type="text/css" href="newstyle.css">
    <script>
        var confirmationWindow;

        function showConfirmation() {
            confirmationWindow = window.open("", "ConfirmationWindow", "width=400,height=200");
            confirmationWindow.document.write("<p>コメントを送信しました</p>");
        }

        function showError() {
            confirmationWindow = window.open("", "ConfirmationWindow", "width=400,height=200");
            confirmationWindow.document.write("<p>コメントの送信に失敗しました</p>");
        }

        function handleSubmit() {
            // フォームデータの送信処理をここに追加する

            // 仮の条件: サーバーサイドでの処理が成功と仮定
            var isSubmissionSuccessful = true;

            if (isSubmissionSuccessful) {
                showConfirmation(); // 送信成功メッセージを表示
                return true; // フォームの送信を続行
            } else {
                showError(); // 送信エラーメッセージを表示
                return false; // フォームの送信を中止
            }
        }
    </script>
    <style>
        /* Your existing styles */
    </style>
</head>
<body>

    <!-- 自己紹介文 -->
    <h1>私の自己紹介</h1>
    <h2>基本情報</h2>
    <p>こんにちは！私は 玉垣 です。大阪に住んでいます。</p>
    <img src="flag.png" alt="Your Photo Description">

    <!-- コメントフォーム -->
    <h1>コメントフォーム</h1>
    <form action="" method="post" onsubmit="return handleSubmit();">
        <!-- フォームの内容をここに再表示する -->

        <!-- ニックネーム入力欄 -->

        <!-- 名前入力欄 -->
        <label for="name">名前:</label>
        <input type="text" name="name" id="name" required><br>

        <!-- メールアドレス入力欄 -->
        <label for="email">メールアドレス:</label>
        <input type="email" name="email" id="email" required><br>

        <!-- コメント入力欄 -->
        <label for="comment">コメント:</label>
        <textarea name="comment" id="comment" rows="4" required><?php echo isset($_SESSION['comment']) ? $_SESSION['comment'] : ''; ?></textarea><br>

        <!-- 日付入力欄 -->
        <label for="date">日付:</label>
        <input type="date" name="date" id="date" required><br>

        <input type="submit" value="送信">
    </form>

    <?php
    session_start(); // セッションを開始

    // データベース接続情報
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "git-test";

    // データベースへの接続
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 接続を確認
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ユーザー入力を安全に処理するための関数
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // フォームの送信が行われた場合
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // フォームの入力データを処理
        $name = isset($_POST["name"]) ? test_input($_POST["name"]) : "";
        $email = isset($_POST["email"]) ? test_input($_POST["email"]) : "";
        $comment = isset($_POST["comment"]) ? test_input($_POST["comment"]) : "";
        $date = isset($_POST["date"]) ? test_input($_POST["date"]) : "";

        // データベースに名前、メールアドレス、コメント、日付を挿入するプリペアドステートメント
        $sql = "INSERT INTO comments (name, email, comment, date) VALUES (?, ?, ?, ?)";

        // プリペアドステートメントを準備して実行
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $comment, $date);
        $stmt->execute();

        // コメントの挿入が成功した場合
        $_SESSION['comment'] = ''; // コメントのセッション変数をクリア
        echo "コメントが正常に送信されました";

        // ステートメントを閉じる
        $stmt->close();
    }

    // 既存のコメントを取得して表示するSQL文（今日の日付のコメントだけを取得するように変更）
    $today = date("Y-m-d"); // 今日の日付を取得
    $sql_select = "SELECT name, email, comment, date FROM comments WHERE date = '$today'";
    $result = $conn->query($sql_select);

    // 既存のコメントがある場合は表示
    if ($result->num_rows > 0) {
        echo "<h2>今日のコメント</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "名前: " . $row["name"] . " - メールアドレス: " . $row["email"] . " - コメント: " . $row["comment"] . " - 日付: " . $row["date"] . "<br>";
        }
    } else {
        echo "<p>今日のコメントはありません。</p>";
    }

    // データベース接続を閉じる
    $conn->close();
    ?>
</body>
</html>
