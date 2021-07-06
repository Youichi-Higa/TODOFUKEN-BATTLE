<?php
include("functions.php");

$pdo = connect_to_db();

$sql = 'SELECT * FROM prefectures_table';

$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// var_dump($result);
// exit();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>都道府県別データ（一覧画面）</title>
</head>

<body>
    <p>都道府県別データ</p>
    <table border="1">
        <thead>
            <tr>
                <th>都道府県名</th>
                <th>HP</th>
                <th>MP</th>
                <th>攻撃力</th>
                <th>防御力</th>
                <!-- <th>職員数</th>
                    <th>有給休暇取得数</th>
                    <th>時間外勤務数</th>
                    <th>平均給与月額</th>
                    <th>男性の育児休業取得者数</th>
                    <th>殺処分数（犬・猫）</th> -->
            </tr>
        </thead>
        <!-- ここに<tr><td>deadline</td><td>todo</td><tr>の形でデータが入る -->
        <tbody id="output">
        </tbody>
    </table>

    <!-- jpuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
        const result = <?= json_encode($result) ?>;
        console.log(result)

        // randomNumber = Math.random() * 21 / 100;

        // 20%の範囲でランダムにパラメーターを生成
        // console.log(Math.round(parseInt(result[0]["num_staff"]) / 10 * (1 + Math.random() * 21 / 100)))
        // <td>${Math.round(parseInt(x.num_staff) / 10 * (1 + Math.random() * 21 / 100))}</td>

        // console.log(parseInt(result[0]["num_staff"]) / 10)

        // -99から+99の乱数
        // let num = Math.floor(Math.random() * 99) + 1; // this will get a number between 1 and 99;
        // num *= Math.floor(Math.random() * 2) == 1 ? 1 : -1; // this will add minus sign in 50% of cases

        // Math.round(x.num_staff / 1000 * Math.random() * 21 / 100);


        const output_data = [];
        result.forEach((x) => {
            output_data.push(`
            <tr>
                <td> <a href = "battle.php?myId=${x.id}">${x.prefectures_name}</a></td>
                <td>${Math.floor(parseInt(x.num_staff) / 10)}</td>
                <td>${Math.floor(parseInt(x.male_childcare_leave))}</td>
                <td>${Math.floor(parseInt(x.ave_monthly_salary) /1000)}</td>
                <td>${Math.floor(parseInt(x.paid_holidays)  / 1000)}</td>
            </tr>
            `)
        });


        $("#output").html(output_data)
    </script>
</body>

</html>