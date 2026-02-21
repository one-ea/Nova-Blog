/**
 * Flavor Theme - Service Worker
 * 静态资源 cache-first, 页面 network-first, 离线 fallback
 */
const CACHE_NAME = 'flavor-v2.4.0';
const OFFLINE_URL = '/offline/';

// 预缓存离线页面
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.add(OFFLINE_URL))
  );
  self.skipWaiting();
});

// 清理旧缓存
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

// 请求拦截
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // 仅处理 GET 请求
  if (request.method !== 'GET') return;

  const url = new URL(request.url);

  // 跳过 wp-admin / wp-login / REST API
  if (
    url.pathname.startsWith('/wp-admin') ||
    url.pathname.startsWith('/wp-login') ||
    url.pathname.includes('/wp-json/')
  ) {
    return;
  }

  // 静态资源: cache-first
  if (isStaticAsset(url.pathname)) {
    event.respondWith(cacheFirst(request));
    return;
  }

  // HTML 页面: network-first + 离线 fallback
  if (request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(networkFirstWithFallback(request));
    return;
  }
});

function isStaticAsset(pathname) {
  return /\.(css|js|woff2?|ttf|eot|png|jpe?g|gif|svg|webp|ico)$/i.test(pathname);
}

async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    return new Response('', { status: 408 });
  }
}

async function networkFirstWithFallback(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    if (cached) return cached;
    return caches.match(OFFLINE_URL);
  }
}
