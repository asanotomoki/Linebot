 <?php
// phpQueryの読み込み
require_once("./phpQuery-onefile.php");
$accessToken = 'DdmNT4QarD6NJOjux4Zk2W7oFhaueH//hEqGiCCFLYkze0hmomf66qmBiK+sXAxHFDtBu/a5GalY+Z510PkygPUa/o4dtszXoZsxJth7xHyg02hxh8cWO1bLlv+Y6/OZa5t4psyLWM3cGfd84QT/UwdB04t89/1O/w1cDnyilFU=';
 
//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);
//取得データ
$replyToken = $json_object->{"events"}[0]->{"replyToken"};        //返信用トークン
$message_type = $json_object->{"events"}[0]->{"message"}->{"type"};    //メッセージタイプ*/
$message_txt = $json_object->{"events"}[0]->{"message"}->{"text"};    //メッセージ内容
$message_text = trim($message_txt);
//メッセージタイプが「text」以外のときは何も返さず終了
if($message_type != "text") exit;
//返信メッセージ
//ページ取得 
$html = file_get_contents("https://www.ei-navi.jp/dictionary/content/".$message_text."/");
//要素取得
$sentenceList = phpQuery::newDocument($html)->find(".example");
foreach( $sentenceList as $sentence ) {
    $Example_sentence = pq($sentence);
    $return_message_text = $Example_sentence->text(); 
    //返信実行
    sending_messages($accessToken, $replyToken, $message_type, $return_message_text);
}
 ?>
<?php
//メッセージの送信
function sending_messages($accessToken, $replyToken, $message_type, $return_message_text){
    //レスポンスフォーマット
    $response_format_text = [
        "type" => $message_type,
        "text" => $return_message_text
    ];
 
    //ポストデータ
    $post_data = [
        "replyToken" => $replyToken,
        "messages" => [$response_format_text]
    ];
 
    //curl実行
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
}
?>