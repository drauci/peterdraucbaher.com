self.addEventListener('push', function(event) {
    let data = { title: 'peterdraucbaher.com', body: 'Novo obvestilo!' };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: '/wp-content/plugins/mobile-notification-handler/icon.png',
        badge: '/wp-content/plugins/mobile-notification-handler/badge.png',
        data: { url: data.url || '/' }
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Ko uporabnik klikne na obvestilo, se odpre tvoja stran/aplikacija
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});