// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: "AIzaSyCJNj8-kgb00Oc_rWPSgNixqQFmqCoTN6c",
    authDomain: "srvinfotech-31f88.firebaseapp.com",
    projectId: "srvinfotech-31f88",
    storageBucket: "srvinfotech-31f88.appspot.com",
    messagingSenderId: "324346137022",
    appId: "1:324346137022:web:45bb2791d44dc22a0111ef",
    measurementId: "G-TNRFBZ5XSN"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png",
    };
    return self.registration.showNotification(
        title,
        options,
    );
});