/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
const firebaseConfig = {
    apiKey: "AIzaSyCzOE2U3w2MPn3zIydix8lyvu5n3TYuQ5A",
    authDomain: "wsn-app-fdd74.firebaseapp.com",
    projectId: "wsn-app-fdd74",
    storageBucket: "wsn-app-fdd74.appspot.com",
    messagingSenderId: "554214638474",
    appId: "1:554214638474:web:e32b6111e05c952c7d7e73",
    measurementId: "G-SWLP28R751"
}

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
firebase.initializeApp(firebaseConfig)
const messaging = firebase.messaging();
messaging.onBackgroundMessage(function(payload) {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
      body: payload.notification.body,
      icon: payload.notification.image,
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

