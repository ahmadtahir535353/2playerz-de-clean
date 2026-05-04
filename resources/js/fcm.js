import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage, isSupported } from "firebase/messaging";
isSupported().then(console.log);

const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
  messagingSenderId: import.meta.env.VITE_FIREBASE_SENDER_ID,
  appId: import.meta.env.VITE_FIREBASE_APP_ID,
};

export async function bootFcm(currentUserId = null) {
  // console.log("✅ bootFcm called with currentUserId:", currentUserId);
  try {
    if (!('Notification' in window)) return;

    const supported = await isSupported();
    if (!supported) return;

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // register SW
    const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

    // ask permission
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') return;

    const vapidKey = import.meta.env.VITE_FIREBASE_VAPID_PUBLIC_KEY;

    const token = await getToken(messaging, { vapidKey, serviceWorkerRegistration: swReg });
    // console.log("Generated FCM Token:", token);
    if (!token) return;

   // save token to backend
    await fetch('/save-fcm-token', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
    body: JSON.stringify({ 
      token,
      device: navigator.userAgent,
      user_id: currentUserId 
    }),
    });


    // Foreground message handler
    onMessage(messaging, (payload) => {
      console.log("📩 FCM Foreground Message Received:", payload);
      const title = payload.notification?.title || payload.data?.title || 'New Notification';
      const body  = payload.notification?.body  || payload.data?.body  || '';
      new Notification(title, { body, icon: '/icon-192x192.png' });
      
    });
  } catch (e) {
    console.error('FCM boot error:', e);
  }
}
