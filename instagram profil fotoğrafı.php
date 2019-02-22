<?php

date_default_timezone_set('Europe/Istanbul');
$getUrl = $_SERVER['REQUEST_URI'];
class myApp {
	
	private function getUserId($userName){
		$getHtml = file_get_contents('https://instagram.com/'.$userName);
		$getData = substr($getHtml, strpos($getHtml, 'window._sharedData'), strpos($getHtml, '};'));
		$userId  = strstr($getData, '"id":"'); 
		$userId  = str_replace('"id":"', '', $userId); 
		$userId  = strstr($userId, '"', true); 
		return $userId;
    }
	
	public function getBigProfile($userName){
		$userId  = $this->getUserId($userName); 
		$getJson = file_get_contents('https://i.instagram.com/api/v1/users/' . $userId . '/info/');
		$getData = json_decode($getJson, true);
		$resultData = array('status' => 'ok', 'hd_profile_pic_url' => $getData['user']['hd_profile_pic_url_info']['url']);
		return json_encode($resultData);
	}
	
}

if(isset($_POST['userName'])){
header('Content-Type: application/json');
$myApp = new myApp;
echo $myApp->getBigProfile($_POST['userName']);
}else{
?>
<html>
<body>
<head>
<script src="https://code.jquery.com/jquery-3.2.1.js" type="text/javascript"></script>
<script>
function getProfilePicture(){
userName = $("#userName").val();
$.ajax({
	type: "POST",
	url: "<?=$getUrl?>",
	data: {userName:userName},
	dataType: "json",
	beforeSend: function(){
		$("#pageLoading").html('Yükleniyor...');
	},
	success: function(data){
		$("#pageLoading").html('Başarılı...');
		profilePicUrl = data.hd_profile_pic_url;
		$("#pageResult").html('<img src="'+profilePicUrl+'">');
	},
	error: function(){
		$("#pageLoading").html('Bir sorun oluştu...');
	}
});
}
</script>
</head>
<input type="text" id="userName" placeholder="Kullanıcı adı">
<button type="button" onclick="getProfilePicture()">Gönder</button>
<div id="pageLoading"></div>
<div id="pageResult"></div>
</body>
</html>
<?php } ?>