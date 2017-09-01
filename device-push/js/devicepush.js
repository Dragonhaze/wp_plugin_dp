'use strict';
var _dp_globalUrl = '';
var _dp_globalObj;
var evtDR = document.createEvent("Event");
evtDR.initEvent("deviceRegistered",true,true);
var evtND = document.createEvent("Event");
evtND.initEvent("notificationDenied",true,true);
var evtER = document.createEvent("Event");
evtER.initEvent("noSupportPush",true,true);
var evtRT = document.createEvent("Event");
evtRT.initEvent("registerToken",true,true);

var checkRemotePermission = function (permissionData, callback) {
  window.safari.pushNotification.requestPermission('https://apiweb.devicepush.com:8081', _dp_globalObj.websitepushid, {}, function(permissionData){
    if (permissionData.permission === 'denied') {
      console.log('Permission for Notifications was denied');
      //dispatchEvent notification denied
      document.dispatchEvent(evtND);
    }else if (permissionData.permission === 'granted') {
      console.log('granted');
      callback({nav: 'Safari', token: permissionData.deviceToken});
    }
  });
};
function admincookies(action, key, value, expirationDays){

    if(action === 'GET'){

        var valueResGet = "";
        var cookie = document.cookie;
        var cookieSplit = cookie.split(';');
        for(var i = 0; i<cookieSplit.length;i++ ){
            if(cookieSplit[i].split("=")[0].trim() === key ){
                valueResGet = cookieSplit[i].split("=")[1].trim();
            }
        }

        if(valueResGet === ""){
            console.log("Cookie not found");
            return valueResGet;
        }else{
            return valueResGet;
        }

    }else if(action === 'SET'){

        var date = new Date();
        date.setTime(date.getTime() + (expirationDays*24*60*60*1000));
        var expires = "expires="+ date.toUTCString();
        document.cookie = key + "=" + value + ";" + expires + ";path=/";

    }else if(action === 'REMOVE'){

        var valueResRemove = admincookies('GET', key, '', 0);

        if(valueResRemove !== ""){
            var expires = "expires=Thu, 01 Jan 1970 00:00:00 UTC";  //Set the expires parameter to a passed date
            document.cookie = key + "=" + value + ";" + expires + ";path=/";
        }

    }
}

var devicePush = {
    getBrowser: function () {
        console.log(navigator.userAgent);
        if(navigator.userAgent.indexOf("OPR") != -1){ return 'Opera'; }
        else if(navigator.userAgent.indexOf("Chrome") != -1){ return 'Chrome'; }
        else if(navigator.userAgent.indexOf("Firefox") != -1){ return 'Firefox'; }
        else if (navigator.userAgent.indexOf("Safari") != -1){ return 'Safari'; }
        else {return '';}
    },
  getToken: function(callback){
    if(_dp_globalObj.key){
			if ('safari' in window && 'pushNotification' in window.safari) {
				console.log('Safari is supported');
        checkRemotePermission(window.safari.pushNotification.permission(_dp_globalObj.websitepushid), function(data){
					callback(data);
				});
		  }else if ('serviceWorker' in navigator) {
			  console.log('Service Worker is supported');
        navigator.serviceWorker.register(sw.file + '?key=' + _dp_globalObj.key).then(function(reg) {
          navigator.serviceWorker.getRegistration(sw.file + '?key=' + _dp_globalObj.key).then(function(reg) {
				    console.log('Service Worker is ready :^)', reg);
						setTimeout(function(){
							reg.pushManager.subscribe({userVisibleOnly: true}).then(function(sub) {
                console.log('granted');
                callback({nav: devicePush.getBrowser(), token: sub.endpoint});
							}).catch(function(error) {
                if (Notification.permission === 'denied') {
                  console.log('Permission for Notifications was denied');
                  //dispatchEvent notification denied
        					document.dispatchEvent(evtND);
                } else {
                  console.log('Service Worker error :^(', error);
                  //dispatchEvent error
        					document.dispatchEvent(evtER);
                }
      				});
						}, 500);
			    });
				}).catch(function(error) { //CATCH REGISTER
          if (Notification.permission === 'denied') {
            console.log('Permission for Notifications was denied');
            //dispatchEvent notification denied
            document.dispatchEvent(evtND);
          } else {
            console.log('Service Worker error :^(', error);
            //dispatchEvent error
  					document.dispatchEvent(evtER);
          }
				});
			}else {  //IF INIT
		    console.log("Your browser does not support push notifications");
		    //dispatchEvent error
		    document.dispatchEvent(evtER);
			}
		}
	},
  register: function(obj){
    _dp_globalObj = obj;
    if(!localStorage.getItem('_DP_registered') || localStorage.getItem('_DP_registered') == undefined || localStorage.getItem('_DP_registered') == ''){
      devicePush.getToken(function(result){
        obj.device = result.nav;
        obj.token = result.token;
        var xmlhttpReg = new XMLHttpRequest();
        xmlhttpReg.open("POST", "https://apiweb.devicepush.com:8081/1.0/device/register/" + obj.key, true);
        xmlhttpReg.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xmlhttpReg.onreadystatechange = function(){
          if (xmlhttpReg.readyState == 4 && xmlhttpReg.status == 200){
            console.log('Correct register Device Push');
            evtDR.devicePushId = JSON.parse(xmlhttpReg.responseText)._id;
            evtDR.devicePushToken = obj.token;
            evtDR.devicePushBrowser = devicePush.getBrowser();
              //dispatchEvent register
            document.dispatchEvent(evtDR);
            localStorage.setItem('_DP_registered', 'true');
          }else if(xmlhttpReg.readyState == 4 && xmlhttpReg.status != 200){
            console.log("Error service Device Push");
            //dispatchEvent error
            document.dispatchEvent(evtER);
          }
        }
        xmlhttpReg.send(JSON.stringify({device: obj.device, token: obj.token, additionaldata: obj.additionalData}));
      });
    }
	},
	putAdditionalData: function(obj){
    _dp_globalObj = obj;
    devicePush.getToken(function(result){
      obj.device = result.nav;
      obj.token = result.token;
      var xmlhttpReg = new XMLHttpRequest();
      xmlhttpReg.open("POST", "https://apiweb.devicepush.com:8081/1.0/device/additionaldata/update/" + obj.key, true);
  		xmlhttpReg.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  		xmlhttpReg.onreadystatechange = function(){
  			if (xmlhttpReg.readyState == 4 && xmlhttpReg.status == 200){
  				console.log(xmlhttpReg.responseText);
  			}
  		}
  		xmlhttpReg.send(JSON.stringify(obj));
    });
	},
  getUrlData: function(){
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
      var pair = vars[i].split("=");
      if (typeof query_string[pair[0]] === "undefined") {
        query_string[pair[0]] = decodeURIComponent(pair[1]);
      } else if (typeof query_string[pair[0]] === "string") {
        var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
        query_string[pair[0]] = arr;
      } else {
        query_string[pair[0]].push(decodeURIComponent(pair[1]));
      }
    }
    return query_string;
  }(),
  replaceAll: function(str, find, replace){
    return str.replace(new RegExp(find, 'g'), replace);
  },
  closePopup: function(obj){
    var result = {};
    if(obj != undefined && obj.devicePushId){ result.devicePushId = obj.devicePushId; }
    if(obj != undefined && obj.status){ result.status = obj.status; }
    window.opener.postMessage(result, devicePush.getUrlData.websiteurl);
    window.close();
  }
}
