

function testpush(idUser,idApplication,title,content,thumbnail,url){

	var resultSendPushNotification = document.getElementById('resultSendPushNotification');
	resultSendPushNotification.innerHTML = '';
    var icon = '';
    var data = '';
	if(idUser == '' || idApplication == ''){

		resultSendPushNotification.innerHTML = 'First, you need configure your settings.';
	}else if (title == '' || content == ' ') {

		resultSendPushNotification.innerHTML = 'Add some contents.';
	}else if(thumbnail.indexOf("https") == -1){
        icon = thumbnail.replace("http", "https");
    }else{
        icon = thumbnail;
    }

        url = '[{"action": "open", "url": "'+url+'"}]';
		var xmlhttpReg = new XMLHttpRequest();
			xmlhttpReg.open("POST", "https://apiweb.devicepush.com:8081/1.0/send/", true);
			xmlhttpReg.setRequestHeader("token", idUser);
			xmlhttpReg.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
			xmlhttpReg.onreadystatechange = function(){
		    if (xmlhttpReg.readyState == 4 && xmlhttpReg.status == 200){
	        console.log(xmlhttpReg.responseText);

	        resultSendPushNotification.innerHTML = 'Push notification sent. :)';
		    }
			}
			xmlhttpReg.send(JSON.stringify({
				idApplication: idApplication,
				title: title,
				content: content,
                icon: icon,
                data: url
			}));
}
		




var numberMaxCaracters = 200;
var iframes = '';
function showTab(tab){
	document.getElementById('tab_configure').classList.remove('active');
	document.getElementById('tab_advanced').classList.remove('active');
	document.getElementById('tab_send').classList.remove('active');
	document.getElementById('tab_' + tab).classList.add('active');
	document.getElementById('content_configure').classList.remove('active');
	document.getElementById('content_advanced').classList.remove('active');
	document.getElementById('content_send').classList.remove('active');
	document.getElementById('content_' + tab).classList.add('active');

	if(tab == 'configure'){
		iframes = document.getElementsByTagName("iframe");
		console.log(document.getElementsByTagName("iframe"));
		for (var i = 0; i < iframes.length; i++){
			document.getElementsByTagName("iframe")[i].style.height = (document.getElementsByTagName("iframe")[i].offsetWidth * 0.56) + "px";
		}
	}
}

function openDevicePushWeb(){
	window.open('https://www.devicepush.com/es/wordpress/', '_blank');
}

function countCaracteres(e){
	if( e.value.length > numberMaxCaracters && !document.getElementById('numberCaracteres').classList.contains('color-red')){
		document.getElementById('numberCaracteres').classList.add('color-red');
	}else if(e.value.length <= numberMaxCaracters && document.getElementById('numberCaracteres').classList.contains('color-red')){
		document.getElementById('numberCaracteres').classList.remove('color-red');
	}
	document.getElementById('numberCaracteres').innerHTML = e.value.length + '/' + numberMaxCaracters;
}

function writeNotification(input){
	document.getElementById(input + 'Preview').innerHTML = document.getElementById(input + 'Notification').value;
}

document.addEventListener('DOMContentLoaded', function(event) {
	if(document.getElementById('numberCaracteres')){
		document.getElementById('numberCaracteres').innerHTML = '0/200';
	}
	if(document.getElementById('numberCaracteresPopup')){
		document.getElementById('numberCaracteresPopup').innerHTML = '0/200';
	}
});

window.onresize = function(){
  for (var i = 0; i < iframes.length; i++){
    document.getElementsByTagName("iframe")[i].style.height = (document.getElementsByTagName("iframe")[i].offsetWidth * 0.56) + "px";
  }
}

/*advanced*/
function writePopup(input){
	if(input == 'text' || input == "textBlock" || input == "textActive"){
		document.getElementById(input + 'PreviewPopup').innerHTML = document.getElementById(input + 'Popup').value;
		if( document.getElementById(input + 'Popup').value.length == 0){
			if(input == "text"){
				document.getElementById(input + 'PreviewPopup').innerHTML = "Activate push notifications to improve your experience on our website.";	}
			if(input == "textBlock"){
				document.getElementById(input + 'PreviewPopup').innerHTML = "Later";
			}
			if(input == "textActive"){
				document.getElementById(input + 'PreviewPopup').innerHTML = "Activate";
			}
		}
	}
	if(input == 'backgroundCircle'){
		document.getElementById(input + 'PreviewPopup').style.backgroundColor = document.getElementById(input + 'Popup').value;
	}
	if(input == "backgroundActive"){
		document.getElementById('textActivePreviewPopup').style.backgroundColor = document.getElementById(input + 'Popup').value;
	}
	if(input == "backgroundBlock"){
		document.getElementById('textBlockPreviewPopup').style.backgroundColor = document.getElementById(input + 'Popup').value;
	}
}
function countCaracteresPopup(e){
	if( e.value.length > numberMaxCaracters && !document.getElementById('numberCaracteresPopup').classList.contains('color-red')){
		document.getElementById('numberCaracteresPopup').classList.add('color-red');
	}else if(e.value.length <= numberMaxCaracters && document.getElementById('numberCaracteresPopup').classList.contains('color-red')){
		document.getElementById('numberCaracteresPopup').classList.remove('color-red');
	}
	document.getElementById('numberCaracteresPopup').innerHTML = e.value.length + '/' + numberMaxCaracters;
}

function setConfigurePopup(){
	var resultSetConfigurePopup = document.getElementById('resultSetConfigurePopup');
	resultSetConfigurePopup.classList.remove('red');
	resultSetConfigurePopup.classList.add('green');
	resultSetConfigurePopup.innerHTML = 'Modification saved! The window will refresh automatically.';
	setTimeout(function(){
		document.getElementById('saveFrmPopup').click();
	}, 1000);
}

/*advanced*/

function checkAccount(){
	var resultCheckDataUser = document.getElementById('resultCheckDataUser');
	resultCheckDataUser.innerHTML = '';
	if(document.getElementById('dp_option_iduser').value == '' || document.getElementById('dp_option_idaplication').value == ''){
		resultCheckDataUser.classList.remove('green');
		resultCheckDataUser.classList.add('red');
		resultCheckDataUser.innerHTML = 'Please, fill out the form.';
	}else{
		var xmlhttpReg = new XMLHttpRequest();
		xmlhttpReg.open("GET", "https://apiweb.devicepush.com:8081/1.0/" + document.getElementById('dp_option_idaplication').value + "/", true);
		xmlhttpReg.setRequestHeader("token", document.getElementById('dp_option_iduser').value);
		xmlhttpReg.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		xmlhttpReg.onreadystatechange = function(){
	    if (xmlhttpReg.readyState == 4 && xmlhttpReg.status == 200){
        if(JSON.parse(xmlhttpReg.responseText) == null){
        	resultCheckDataUser.classList.remove('green');
        	resultCheckDataUser.classList.add('red');
	        resultCheckDataUser.innerHTML = 'Your User ID or your App/Web ID are incorrect.';
        }else if(JSON.parse(xmlhttpReg.responseText).fcmsenderid){
        	resultCheckDataUser.classList.remove('red');
        	resultCheckDataUser.classList.add('green');
        	resultCheckDataUser.innerHTML = 'Your credentials are correct! The window will refresh automatically.';
        	setTimeout(function(){
        		document.getElementById('checkDataUser').click();
        	}, 1000);
	        return "ok";
        }else{
        	resultCheckDataUser.classList.remove('green');
        	resultCheckDataUser.classList.add('red');
        	resultCheckDataUser.innerHTML = 'Your credentials are not correct. Please check your User ID and App or Web ID in you admin panel in panel.devicepush.com';
	        return "no_sender_id";
        }
	    }
		}
		xmlhttpReg.send(JSON.stringify({
			idApplication: document.getElementById('dp_option_idaplication').value
		}));
	}
}

function checkActive(){
	var checkDynamic = document.getElementsByClassName('checkDynamic');
	var checkedDynamic = false;
	for (var i = 0; i < checkDynamic.length; i++){
		if(checkDynamic[i].checked == true){
			checkedDynamic = true;
		}
	}
	var resultCheckActive = document.getElementById('resultCheckActive');
	if(document.getElementById('welcomeNotification').checked == true || checkedDynamic == true){
		if(document.getElementById('welcomeNotification').checked == true && (document.getElementById('titleWelcomeNotification').value == '' || document.getElementById('textWelcomeNotification').value == '')){
			resultCheckActive.classList.remove('green');
			resultCheckActive.classList.add('red');
			resultCheckActive.innerHTML = 'You must include a title and text in you automatic welcome push notification.';
		}else{
			resultCheckActive.classList.remove('red');
			resultCheckActive.classList.add('green');
			resultCheckActive.innerHTML = 'Your push notifications are activated! The window will refresh automatically.';
			setTimeout(function(){
				document.getElementById('saveFrmCheck').click();
			}, 1000);
		}
	}else{
		resultCheckActive.classList.remove('green');
		resultCheckActive.classList.add('red');
		resultCheckActive.innerHTML = 'You must activate at least one type of notification.';
	}
}

function saveImage(){
	document.getElementById('checkDataUser').click();
}

function sendPushNotification(){
	var resultSendPushNotification = document.getElementById('resultSendPushNotification');
	resultSendPushNotification.innerHTML = '';
	if(document.getElementById('dp_option_iduser').value == '' || document.getElementById('dp_option_idaplication').value == ''){
		resultSendPushNotification.classList.remove('green');
		resultSendPushNotification.classList.add('red');
		resultSendPushNotification.innerHTML = 'First, you need configure.';
	}else if(document.getElementById('titleNotification').value == '' || document.getElementById('textNotification').value == ''){
		resultSendPushNotification.classList.remove('green');
		resultSendPushNotification.classList.add('red');
		resultSendPushNotification.innerHTML = 'Please, fill out the form.';
	}else{
		var icon = '';
		if(document.getElementById('iconNotification').value.indexOf("https") == -1){
			icon = document.getElementById('iconNotification').value.replace("http", "https");
		}else{
			icon = document.getElementById('iconNotification').value;
		}
		var segmentation = '';
		if(document.getElementById('userNotification').value != ''){
			segmentation = '{"cms_user_id":"' + document.getElementById('userNotification').value + '"}';
		}
		var data = '';
		if(document.getElementById('urlNotification').value != ''){
			data = '[{"action": "open", "url": "' + document.getElementById('urlNotification').value + '"}]';
		}
		var xmlhttpReg = new XMLHttpRequest();
		xmlhttpReg.open("POST", "https://apiweb.devicepush.com:8081/1.0/send/", true);
		xmlhttpReg.setRequestHeader("token", document.getElementById('dp_option_iduser').value);
		xmlhttpReg.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		xmlhttpReg.onreadystatechange = function(){
	    if (xmlhttpReg.readyState == 4 && xmlhttpReg.status == 200){
        console.log(xmlhttpReg.responseText);
        resultSendPushNotification.classList.remove('red');
        resultSendPushNotification.classList.add('green');
        resultSendPushNotification.innerHTML = 'Push notification sent. :)';
	    }
		}
		xmlhttpReg.send(JSON.stringify({
			idApplication: document.getElementById('dp_option_idaplication').value,
			title: document.getElementById('titleNotification').value,
			content: document.getElementById('textNotification').value,
			icon: icon,
			segmentation: segmentation,
			data: data
		}));
	}
}

function sendWelcomPushNotification(data){
	var icon = '';
	if(document.getElementById('iconNotification').value.indexOf("https") == -1){
		icon = document.getElementById('iconNotification').value.replace("http", "https");
	}else{
		icon = document.getElementById('iconNotification').value;
	}
	var segmentation = '';
	if(document.getElementById('userNotification').value != ''){
		segmentation = '{"cms_user_id":"' + document.getElementById('userNotification').value + '"}';
	}
	var xmlhttpReg = new XMLHttpRequest();
	xmlhttpReg.open("POST", "https://apiweb.devicepush.com:8081/1.0/send/", true);
	xmlhttpReg.setRequestHeader("token", document.getElementById('dp_option_iduser').value);
	xmlhttpReg.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
	xmlhttpReg.onreadystatechange = function(){
    if (xmlhttpReg.readyState == 4 && xmlhttpReg.status == 200){
      console.log(xmlhttpReg.responseText);
    }
	}
	xmlhttpReg.send(JSON.stringify({
		key: data.key,
		idDevice: data.idDevice,
		title: data.title,
		content: data.content,
		icon: data.icon
	}));
}

function activeInputsPopup(e){
	console.log('activeInputsPopup -> ' + e.checked);
	if(e.checked == true){
		document.getElementById('backgroundCirclePopup').disabled = '';
		document.getElementById('textPopup').disabled = '';
		document.getElementById('textBlockPopup').disabled = '';
		document.getElementById('backgroundBlockPopup').disabled = '';
		document.getElementById('textActivePopup').disabled = '';
		document.getElementById('backgroundActivePopup').disabled = '';
		document.getElementById('legendActivePopup').disabled = '';
	}else{
		document.getElementById('backgroundCirclePopup').disabled = 'disabled';
		document.getElementById('textPopup').disabled = 'disabled';
		document.getElementById('textBlockPopup').disabled = 'disabled';
		document.getElementById('backgroundBlockPopup').disabled = 'disabled';
		document.getElementById('textActivePopup').disabled = 'disabled';
		document.getElementById('backgroundActivePopup').disabled = 'disabled';
		document.getElementById('legendActivePopup').disabled = 'disabled';
	}
}

function actionCheckLegend(e){
	console.log(e.checked);
	if(e.checked == false){
		document.getElementById('legend').classList.add('hide');
	}else{
		document.getElementById('legend').classList.remove('hide');
	}
}
