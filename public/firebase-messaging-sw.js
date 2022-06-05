importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
firebase.initializeApp({
    apiKey: "AIzaSyCZj9ALIgeu6JtQHvOzh0y4t9MKhQ",
    projectId: "chce-application",
    messagingSenderId: "653227667756",
    appId: "1:653227667756:web:a9c4bf40ac17011e693c89"
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload,
);
    
const notificationTitle = "Background Message Title";
const notificationOptions = {
    body: "Background Message body.",
    icon: "/itwonders-web-logo.png",
};

return self.registration.showNotification(
    notificationTitle,
    notificationOptions,
);

});

