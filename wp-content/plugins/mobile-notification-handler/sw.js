// sw.js

self.addEventListener('push', function(event) {
    // Privzete (fallback) vrednosti, če gre kaj narobe
    let data = {
        title: 'Novo obvestilo',
        body: 'Imate novo sporočilo.',
        url: '/'
    };

    if (event.data) {
        try {
            // Poskusimo prebrati JSON, ki ga je poslal PHP
            const payload = event.data.json();

            // PHP mora poslati objekt v obliki: {"title": "...", "body": "...", "url": "..."}
            data.title = payload.title || data.title;
            data.body = payload.body || data.body;
            data.url = payload.url || data.url;

        } catch (e) {
            // Če PHP ni poslal JSON-a, ampak navaden tekst
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: '/wp-content/plugins/mobile-notification-handler/icon.png',
        badge: '/wp-content/plugins/mobile-notification-handler/badge.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url
        },
        // Preprečimo podvajanje obvestil, če jih pride več hkrati
        tag: 'mnh-notification-tag',
        renotify: true
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Upravljanje klika na obvestilo
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Odpremo URL, ki smo ga prejeli iz PHP-ja
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Če je zavihek že odprt, ga samo fokusiramo
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if (client.url === event.notification.data.url && 'focus' in client) {
                    return client.focus();
                }
            }
            // Če ni odprt, odpremo novo okno
            if (clients.openWindow) {
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});