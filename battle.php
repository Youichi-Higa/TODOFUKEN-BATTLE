<?php
include("functions.php");
$pdo = connect_to_db();

$myId = $_GET["myId"];
$cpuId = mt_rand(2, 48);


// 自分が選んだ都道府県の情報をDBから取得
$sql = 'SELECT * FROM prefectures_table WHERE id = :myId';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':myId', $myId, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $myResult = $stmt->fetch(PDO::FETCH_ASSOC);
}

// cpuの都道府県の情報をDBから取得
$sql = 'SELECT * FROM prefectures_table WHERE id = :cpuId';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':cpuId', $cpuId, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $cpuResult = $stmt->fetch(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Document</title>
</head>

<body>

    <div class="my_cpu_wrapper">
        <div class="my_cpu_border">
            <p id="myPrefecture"></p>
            <p id="myStatus"></p>
        </div>
        <div>vs</div>
        <div class="my_cpu_border">
            <p id="cpuPrefecture"></p>
            <p id="cpuStatus"></p>
        </div>
    </div>

    <div class="border" id="opponent"></div>

    <div class="border_hide" id="messageWindow"></div>
    <div class="border_hide" id="damageWindow"></div>

    <div class="border_hide" id="actionWrapper">
        <div id="turnWindow"></div>
        <div id="triangle">▼</div>
        <div id="playerAction">
            <p>アクションを選択</p>
            <div id="attack">・攻撃</div>
            <div id="magic">・特産品</div>
        </div>
    </div>

    <!-- jpuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
        // 都道府県名、各パラメータ
        const myResult = <?= json_encode($myResult) ?>;
        const cpuResult = <?= json_encode($cpuResult) ?>;

        const myPref = myResult["prefectures_name"]
        const cpuPref = cpuResult["prefectures_name"]

        let myHP = Math.floor(myResult["num_staff"] / 10);
        let cpuHP = Math.floor(cpuResult["num_staff"] / 10);

        let myMP = Math.floor(myResult["male_childcare_leave"]);
        let cpuMP = Math.floor(cpuResult["male_childcare_leave"]);

        const myAttack = Math.floor(myResult["ave_monthly_salary"] / 1000);
        const cpuAttack = Math.floor(cpuResult["ave_monthly_salary"] / 1000);

        const myDefense = Math.floor(myResult["paid_holidays"] / 1000);
        const cpuDefense = Math.floor(cpuResult["paid_holidays"] / 1000);


        // ブラウザに表示
        $("#myPrefecture").html(myPref)
        $("#myStatus").html(`HP：${myHP}　MP：${myMP}`)

        $("#cpuPrefecture").html(cpuPref)
        $("#cpuStatus").html(`HP：${cpuHP}　MP：${cpuMP}`)

        $("#opponent").html(`${cpuPref}が相手だ！`)


        $("#opponent").on("click", () => {
            const randomNum = Math.floor(Math.random() * 2);
            $("#opponent").hide();
            if (randomNum == 0) {
                $("#actionWrapper").show()
                $("#turnWindow").html(`${myPref}（あなた）のターン`)
                $("#playerAction").show()
            } else if (randomNum == 1) {
                $("#actionWrapper").show()
                $("#turnWindow").html(`${cpuPref}（相手）のターン`)
                $("#triangle").show()
            }
        });



        // 勝敗決定後のメッセージ表示を関数化
        function winFunc() {
            if (myHP > 0 && cpuHP <= 0) {
                $("#messageWindow").show();
                $("#messageWindow").html(`${myPref}（あなた）の勝ち！`);
                $("#damageWindow").hide();
                // $("#actionWrapper").hide()
            } else if (myHP <= 0 && cpuHP > 0) {
                $("#messageWindow").show();
                $("#messageWindow").html(`${cpuPref}（相手）に負けてしまった。。。`);
                $("#damageWindow").hide();
                // $("#actionWrapper").hide()

            }
        }

        // 「攻撃」をクリック後の処理
        $("#attack").on("click", () => {
            // 攻撃力と防御力の調整用数値の変数(攻撃力が高すぎるため、バランスを取るための調整)
            let attackAdjustment = (1 - Math.random() * 31 / 100) //乱数生成（70 〜 100%）
            let defenseAdjustment = (1 + Math.random() * 81 / 100) //乱数生成（100 〜 180%）

            let cpuDamage = Math.floor(myAttack * attackAdjustment - cpuDefense * defenseAdjustment)
            cpuHP -= cpuDamage

            $("#turnWindow").html(`${cpuPref}（相手）のターン`)
            $("#triangle").show();
            $("#damageWindow").show()
            $("#damageWindow").html(`${myPref}（あなた）の攻撃！<br>${cpuPref}（相手）に${cpuDamage}のダメージ`)
            $("#playerAction").hide();


            if (cpuHP > 0) {
                $("#cpuStatus").html(`HP：${cpuHP}　MP：${cpuMP}`)
            } else {
                $("#cpuStatus").html(`HP：0　MP：${cpuMP}`)
                $("#actionWrapper").hide()
            }

            setTimeout(() => {
                winFunc();
            }, 2000);
        });

        // 自分が「攻撃」をクリック後に表示される「▼」をクリックした時の処理
        $("#triangle").on("click", () => {
            // 攻撃力と防御力の調整用数値の変数(攻撃力が高すぎるため、バランスを取るための調整)
            let attackAdjustment = (1 - Math.random() * 31 / 100) //乱数生成（0 〜 70%）
            let defenseAdjustment = (1 + Math.random() * 81 / 100) //乱数生成（100 〜 180%）

            let myDamage = Math.floor(cpuAttack * attackAdjustment - myDefense * defenseAdjustment)
            myHP -= myDamage

            $("#turnWindow").html(`${myPref}（あなた）のターン`)
            $("#triangle").hide()
            $("#damageWindow").show()
            $("#damageWindow").html(`${cpuPref}（相手）の攻撃！<br>${myPref}（あなた）に${myDamage}のダメージ`)
            $("#playerAction").show()

            if (myHP > 0) {
                $("#myStatus").html(`HP：${myHP}　MP：${myMP}`)
            } else {
                $("#myStatus").html(`HP：0　MP：${myMP}`)
                $("#actionWrapper").hide()
            }

            setTimeout(() => {
                winFunc();
            }, 2000);
        });
    </script>

</body>

</html>