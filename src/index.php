<?php
// programmer : AmirHossein Naei
// website : Amirhn.ir

$mainChannel = "@"; // آی دی کانال اصلی به همراه @ 
$interfaceChannel = "@"; // آی دی کانال واسط به همراه @ - کانالی که بنر ها در اون ارسال میشن تا شمارنده بگیرن
$adminId = 0000000; // آی دی عددی مدیر
$views = 200; // تعداد ویو مورد نیاز برای هر پست

// اطلاعات مورد نیاز برای اتصال به دیتابیس
$server_name = ""; 
$username = "";
$password = "";
$my_db = "";


function sendReq($url, $param){
	$token = ""; // توکن ربات
	$url = "https://api.telegram.org/bot$token/$url";
	$handler = curl_init($url);             
	curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($handler, CURLOPT_POSTFIELDS, $param);                       
	curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
	$response2 = curl_exec($handler);
	curl_close($handler);
	$response2 = json_decode($response2);
	return $response2;
}
function getRandNum($my_connection){
	$rand = rand(1000, 10000);
	$q = mysqli_query($my_connection, "SELECT `user_id` FROM `banners` WHERE `banner_id`=$rand ");
	if (mysqli_num_rows($q) > 0)
		return 0;
		return $rand;
}
function read_file($name){
	$file = fopen("users/".$name.".txt", "r+");
	$text = fgets($file);
	fclose($file);
	return $text;
}
function write_file($name , $text){
	if($text == ""){
		unlink("users/".$name.".txt");
		return;
	}
	$file = fopen("users/".$name.".txt" , "w+");
	fwrite($file , $text);
	fclose($file);
}

$my_connection = mysqli_connect($server_name, $username, $password, $my_db);
		
$update = json_decode(file_get_contents('php://input'), true);
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$userId = $update["message"]["from"]["id"];

$param = array
(
	"chat_id"=>$mainChannel,
	"user_id"=>$userId
);
$res = sendReq("getChatMember", $param);
if($res->result->status == "creator" || $res->result->status == "administrator" || $res->result->status == "member"){
		if($message == "/start" || $message == "راهنما"){
			$param = array(
				'chat_id'=>$mainChannel
			);
			$numberofusers = sendReq("getChatMembersCount", $param)->result;
			$param = array(
				"chat_id"=>$chatId,
				"text"=>"سلام 😄✋️ خوش اومدی !!!
من ربات بنردهی 🍆بادمجون🍆 هستم .
با من میتونی به راحتی برای پستات بازدید بگیری یا یه تبلیغات پربازده انجام بدی ؛ خیلی راحت و البته کاملا ✅ رایگان ✅

⁉️حتما میپرسی چطوری ؟! 🧐
الان بهت میگم 😉
👈 اول باید روی دکمه «دریافت بنر» در پایین صفحه کلیک کنی ؛ بعد من یه بنر بهت میدم که تو باید اون بنرو به گروهات ، دوستات و... فوروارد کنی ؛ وقتی تعداد ویو های اون بنر به $views تا رسید ، میتونی یه پست برام ارسال کنی تا اونو تو کانال $numberofusers عضویمون به آی دی $mainChannel 👈بصورت دائمی🤗 بذارم تا ویو بخوره . 
به همین راحتی !!!☺️

👌 یکبار امتحان کنی ، مشتری میشی 😅
✳️⬅️ اگه موافقی ، بزن بریم !",
				"reply_markup"=>json_encode([
						'keyboard'=>[
						[
							['text'=>"✅ دریافت بنر"],
							["text"=>"👤بنرهای من👤"],
							['text'=>"❇️ تحویل بنر"]
						],
						[
							['text'=>"تماس با برنامه نویس"]
							,['text'=>"راهنما"]
						]
						],
						"resize_keyboard"=>true
					])
			);
			sendReq("sendMessage",$param);
		}elseif ($message == "لغو"){
			write_file($userId , "");
			$param = array(
				'chat_id'=>$chatId,
				'text'=>"درخواست شما لغو شد",
				"reply_markup"=>json_encode([
					'keyboard'=>[
					[
						['text'=>"✅ دریافت بنر"],
						["text"=>"👤بنرهای من👤"],
						['text'=>"❇️ تحویل بنر"]
					],[
							['text'=>"تماس با برنامه نویس"]
							,['text'=>"راهنما"]
						]
					],
					"resize_keyboard"=>true
				])
			);
			sendReq("sendMessage", $param);
		} elseif($message == "send" && $userId == $adminId){
			
				$param = array(
					'chat_id'=>$mainChannel,
					'from_chat_id'=>$chatId,
					'message_id'=>$update["message"]["reply_to_message"]["message_id"]
				);
				sendReq("forwardMessage", $param);
				
				$param = array(
					'chat_id'=>$chatId,
					'message_id'=>$update["message"]["reply_to_message"]["message_id"]
				);
				sendReq("deleteMessage", $param);
				
			
		} elseif($message == "تماس با برنامه نویس"){
			$param = array(
					'chat_id'=>$chatId,
					'text'=>"برنامه نویس ربات : امیرحسین نائی \n وبسایت : https://amirhn.ir/ \n برای دانلود رایگان سورس این ربات به وبسایت برنامه نویس مراجعه کنید."
				);
				sendReq("sendMessage", $param);
		} elseif($message == "👤بنرهای من👤"){
			
			$q = mysqli_query($my_connection, "SELECT `message_id` FROM `banners` WHERE `user_id`=$userId ");
			if(mysqli_num_rows($q) > 0){
				$c = false;
				while($d = mysqli_fetch_assoc($q)){
					if($d["message_id"] != -1){	
						$param = array(
							'chat_id'=>$chatId,
							'from_chat_id'=>$interfaceChannel,
							'message_id'=>$d["message_id"]
						);
						sendReq("forwardMessage", $param);
						$c = true;
					}
				}
				if($c==false){
					
					$q = mysqli_query($my_connection, "SELECT `banner_id` FROM `banners` WHERE `user_id`=$userId ");
					$txt = "";
					while($d = mysqli_fetch_assoc($q)){
						$txt = $txt.$d["banner_id"]."\n";
					}
					$param = array (
						'chat_id'=>$chatId , 
						'text'=>"کد بنر های شما : \n $txt میتونی تو کانال زیر دنبال بنرت بگردی ، کافیه کدشو سرچ کنی \n $interfaceChannel "
					);
					sendReq("sendMessage", $param);
					
				}
			} else {
				$param = array (
					'chat_id'=>$chatId , 
					'text'=>"⏺ درحال حاضر هیچ بنر نداری که تحویل نداده باشیش \n میتونی با انتخاب گزینه «دریافت بنر» یکی بگیری !"
				);
				sendReq("sendMessage", $param);
			}
			
		} elseif($message == "✅ دریافت بنر"){
			
			$q = mysqli_query($my_connection, "SELECT `message_id` FROM `banners` WHERE `user_id` = $userId ");
			if(mysqli_num_rows($q) >= 2){
				
				while($d = mysqli_fetch_assoc($q)){
					if($d["message_id"] != -1){	
						$param = array(
							'chat_id'=>$chatId,
							'from_chat_id'=>$interfaceChannel,
							'message_id'=>$d["message_id"]
						);
						sendReq("forwardMessage", $param);
					}
				}
				$param = array (
					'chat_id'=>$chatId , 
					'text'=>"❌ تا حالا دو تا بنر بالا رو بهت تحویل دادم که هنوز تحویلشون ندادی ! \n تا وقتی اینارو تحویل ندی نمیتونی بنر جدیدی بگیری ."
				);
				sendReq("sendMessage", $param);
				
			} else {
			
				$rand = getRandNum($my_connection);
				if($rand == 0){
					$param = array(
						"chat_id"=>$chatId,
						"text"=>"❌ خطایی رخ داده است . لطفا دوباره تلاش کنید."
					);
					sendReq("sendMessage", $param);
				} else {
					
					if(rand(0,1) == 0){
						$param = array(
							"chat_id"=>$interfaceChannel,
							"text"=>"👈 ربات بنردهی 🍆بادمجون🍆
	با این ربات میتونی به راحتی برای پستات بازدید بگیری یا یه تبلیغات پربازده انجام بدی ؛ خیلی راحت و البته کاملا ✅ رایگان ✅
	$mainChannel

	⁉️حتما میپرسی چطوری ؟! 🧐
	👈 اول باید با استفاده از دکمه «دریافت بنر» یه بنر تبلیغاتی بگیری و بعد باید اون بنرو به گروهات ، دوستات و... فوروارد کنی ؛ وقتی تعداد ویو های اون بنر به تعداد مشخص (بیا تو ربات تا بگم چقدر) رسید ، میتونی یه پست براش ارسال کنی تا اونو تو کانالش به آی دی $mainChannel بصورت دائمی🤗 بذاره تا ویو بخوره . 
	به همین راحتی !!!☺️

	👌 یکبار امتحان کنی ، مشتری میشی 😅
	✳️⬅️ اگه موافقی ، بزن بریم !
	$mainChannel
	"."=!".$rand."#"
						);
						$res = sendReq("sendMessage", $param);
					} else {
						$param = array(
							"chat_id"=>$interfaceChannel,
							"photo"=>"",// لینک تصویر بنر مورد نظر
							"caption"=>"✅ با استفاده از ربات بنردهی بادمجون خیلی راحت و سریع و کاملا رایگان😳 تبلیغات کنید و ویو بگیرید 👁‍🗨
$mainChannel
"."=!".$rand."#"
						);
						$res = sendReq("sendPhoto", $param);
					}
					
					
					$param = array(
						'chat_id'=>$chatId,
						'from_chat_id'=>$interfaceChannel,
						'message_id'=>$res->result->message_id
					);
					$res1 = sendReq("forwardMessage", $param);
					$param = array(
						'chat_id'=>$chatId,
						'reply_to_message_id'=>$res1->result->message_id,
						'text'=>"✅ بفرما ، اینم بنرت ! \n برو بینم چیکار میکنی ! منتظرم تا بیای و بنرو تحویل بدی ..."
					);
					sendReq("sendMessage", $param);
					mysqli_query($my_connection, "INSERT INTO `banners` (`user_id`,`banner_id`,`message_id`) VALUES ('$userId','$rand','" . $res->result->message_id . "') ");
				}
			
			}
			
			
			
		} elseif($message == "❇️ تحویل بنر"){
			
			$param = array(
				'chat_id'=>$chatId,
				'text'=>"✅ اگر میزان ویو های پست رو به $views ویو رسوندی ، برام فورواردش کن .
				اگر بازدید بنر به تعداد مشخص شده نرسیده باشه، امتیازت از بین میره و باید دوباره بنره جدیدی بگیری.",
				"reply_markup"=>json_encode([
						'keyboard'=>[
						[
							['text'=>"لغو"]
						]
						],
						"resize_keyboard"=>true
					])
			);
			sendReq("sendMessage", $param);
			write_file($userId, "tahvil");
			
		} else {
			if (read_file($userId) == "tahvil"){
				
				if($update["message"]["forward_from_chat"]["username"] == substr($interfaceChannel,1) ){
					
					if ($update["message"]["caption"]){
						$message = $update["message"]["caption"];
					}
					
					$pos1 = strpos($message, "=!");
					$pos2 = strpos($message, "#");
					if($pos1 !== false && $pos2 !== false){
						
						$selectedMessage = substr($message, $pos1+2, $pos2);
						
						$q = mysqli_query($my_connection, "SELECT `user_id` FROM `banners` WHERE `banner_id`=$selectedMessage ");
						$d = mysqli_fetch_assoc($q);
						if ($d["user_id"] == $userId){
							
							$param = array(
								'chat_id'=>$chatId,
								'text'=>"✅ حالا پستی که میخوای بذارمش داخل کانال رو بفرست ... 
 ⬅️ این پست بعد از بررسی ، در کانال قرار میگیره و پست هایی که در چهاچوب قوانین جمهوری اسلامی ایران نباشند ، تایید نمیشن ... اگرم تایید نشن دیگه نمیتونی پست دیگه ای رو بفرستی و باید دوباره بنر بگیری و ویو جمع کنی ! 
 🔴 پس دقت کن !!!!! 
 همچنین اگر تو این مرحله لغو رو بزنی ، امتیازت از بین میره و دوباره باید بنر بگیری و ویو جمع کنی"
							);
							sendReq("sendMessage", $param);
							write_file($userId, "tahvil2-".$update["message"]["message_id"] );
							
							$param = array(
								'chat_id'=>$chatId,
								'text'=>"متن تمام مطالب کانال قبل از ارسال ، بررسی میشوند اما مسئولیت لینک های ارسال شده و محتوای آن ها بر عهده کاربر ارسال کننده است."
							);
							sendReq("sendMessage", $param);
							
							mysqli_query($my_connection, "DELETE FROM `banners` WHERE `banner_id` = $selectedMessage ");
							
						} else {
							$param = array(
								'chat_id'=>$chatId,
								'text'=>"❌ بنری که ارسال کردی برای شما اعتبار نداره . \n احتمالا قبلا یکبار تحویلش دادی ! یا اینکه بنر مال فرد دیگه ایه ..."
							);
							sendReq("sendMessage", $param);
						}
						
					} else {
						$param = array(
							'chat_id'=>$chatId,
							'text'=>"❌ بنر ارسالی اشتباه است"
						);
						sendReq("sendMessage", $param);
					}
					
				} else {
					$param = array(
						'chat_id'=>$chatId,
						'text'=>"❌ دقیقا باید همون بنری که برات فرستادم رو فوروارد کنی."
					);
					sendReq("sendMessage", $param);
				}
				
			} elseif(substr(read_file($userId),0,7) == "tahvil2"){
				
				$param = array(
					'chat_id'=>$adminId,
					'from_chat_id'=>$chatId,
					'message_id'=>substr(read_file($userId),8)
				);
				sendReq("forwardMessage", $param);
				
				$param = array(
					'chat_id'=>$adminId,
					'from_chat_id'=>$chatId,
					'message_id'=>$update["message"]["message_id"]
				);
				sendReq("forwardMessage", $param);
				
				
				
				$param = array(
					'chat_id'=>$chatId,
					'text'=>"✅ پستت دریافت شد ! \n بعد از بررسی مدیر ، در کانال قرار میگیره . ممکنه این کار تا چند ساعت طول بکشه .... لطفا شکیبا باشید :)",
					"reply_markup"=>json_encode([
						'keyboard'=>[
						[
							['text'=>"✅ دریافت بنر"],
							["text"=>"👤بنرهای من👤"],
							['text'=>"❇️ تحویل بنر"]
						],[
							['text'=>"تماس با برنامه نویس"]
							,['text'=>"راهنما"]
						]
						],
						"resize_keyboard"=>true
					])
				);
				sendReq("sendMessage", $param);
				write_file($userId, "");
				
			}else {
				$param = array(
					'chat_id'=>$chatId,
					'text'=>"⏹ متوجه نشدم چی گفتی . برای شروع روی /start کلیک کن"
				);
				sendReq("sendMessage", $param);
			}
		}
} else {
	
	if($message == "/start"){
		$param = array(
				'chat_id'=>$mainChannel
			);
			$numberofusers = sendReq("getChatMembersCount", $param)->result;
			$param = array(
				"chat_id"=>$chatId,
				"text"=>"سلام 😄✋️ خوش اومدی !!!
من ربات بنردهی 🍆بادمجون🍆 هستم .
با من میتونی به راحتی برای پستات بازدید بگیری یا یه تبلیغات پربازده انجام بدی ؛ خیلی راحت و البته کاملا ✅ رایگان ✅

⁉️حتما میپرسی چطوری ؟! 🧐
الان بهت میگم 😉
👈 اول باید روی دکمه «دریافت بنر» در پایین صفحه کلیک کنی ؛ بعد من یه بنر بهت میدم که تو باید اون بنرو به گروهات ، دوستات و... فوروارد کنی ؛ وقتی تعداد ویو های اون بنر به $views تا رسید ، میتونی یه پست برام ارسال کنی تا اونو تو کانال $numberofusers عضویمون به آی دی $mainChannel 👈بصورت دائمی🤗 بذارم تا ویو بخوره . 
به همین راحتی !!!☺️

👌 یکبار امتحان کنی ، مشتری میشی 😅
✳️⬅️ اگه موافقی ، بزن بریم !",
				"reply_markup"=>json_encode([
						'keyboard'=>[
						[
							['text'=>"✅ دریافت بنر"],
							["text"=>"👤بنرهای من👤"],
							['text'=>"❇️ تحویل بنر"]
						],
						[
							['text'=>"تماس با برنامه نویس"]
							,['text'=>"راهنما"]
						]
						],
						"resize_keyboard"=>true
					])
			);
			sendReq("sendMessage",$param);
	} else {
		$param = array(
			"chat_id"=>$chatId,
			"text"=>"⏹ اول باید تو کانالمون $mainChannel عضو شی . \n بعد برگرد و روی /start کلیک کن.",
			"reply_markup"=>json_encode(['remove_keyboard'=>true])
		);
		sendReq("sendMessage",$param);
	}
}
mysqli_close($my_connection);

?>
