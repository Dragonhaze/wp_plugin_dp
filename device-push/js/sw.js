'use strict';

self.addEventListener('install', function(event) {
  self.skipWaiting();
  console.log('Installed', event);
});

self.addEventListener('activate', function(event) {
  console.log('Activated', event);
});

self.addEventListener('push', function(event) {
  event.waitUntil(
  	fetch('https://apiweb.devicepush.com:8081/1.0/lastnotification/' + location.search.split('key=')[1]).then(function(response) {
		if (response.status !== 200) {
			console.log('Looks like there was a problem. Status Code: ' + response.status);
			throw new Error();
		}
		return response.json().then(function(data) {
			var actions;
			if(data[0].info.actions && data[0].info.actions != ''){
				actions = JSON.parse([data[0].info.actions]);
			}
			return self.registration.showNotification(data[0].info.title, {
				body: data[0].info.content,
				icon: data[0].info.icon,
				data: data[0].info.data,
        image: data[0].info.image,
				actions: actions,
        requireInteraction: true,
        isClickable: true
			});
		});
    })
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  var data;
  if(event.notification.data && event.notification.data != ''){
	data = JSON.parse([event.notification.data]);
  }
  var url;
  if(data && data[0].action && data[0].url && data[0].action == 'open'){
	url = data[0].url;
  }
  if(url != undefined){
 	event.waitUntil(clients.matchAll({
      type: 'window'
    }).then(function(activeClients) {
      if (activeClients.length > 0) {
        activeClients[0].navigate(url);
        activeClients[0].focus();
      }else{
      	clients.openWindow(url);
      }
    })
  );
  }else if(event.action){
	  console.log(event.action);
  }
});
