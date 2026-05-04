// public/firebase-messaging-sw.js
importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "<?= env('VITE_FIREBASE_API_KEY') ?>",
  projectId: "<?= env('VITE_FIREBASE_PROJECT_ID') ?>",
  messagingSenderId: "<?= env('VITE_FIREBASE_SENDER_ID') ?>",
  appId: "<?= env('VITE_FIREBASE_APP_ID') ?>",
});

const messaging = firebase.messaging();

// Background messages
messaging.onBackgroundMessage((payload) => {
  const title = payload.notification?.title || payload.data?.title || 'New Notification';
  const options = {
    body: payload.notification?.body || payload.data?.body || '',
    icon: '/icon-192x192.png',
    badge: '/badge-72x72.png',
    data: { url: payload.data?.link || '/' },
  };
  self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const url = event.notification.data?.url || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      for (let client of windowClients) {
        if (client.url === url && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});

