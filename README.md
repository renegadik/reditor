<h1 align="left">📝 Reditor</h1> <p align="left">Simple Redis UI built with Laravel</p>

🚀 Overview
Reditor is a lightweight and fast web-based Redis editor.
It provides a clean interface for managing Redis keys of types:
String, Array, and JSON — with inline editing support.

<h3 align="left">⚙️ Installation</h3>

<pre><code>git clone https://github.com/renegadik/reditor.git
cd reditor</code></pre>

<pre><code>composer update
cp .env.example .env
php artisan key:generate</code></pre>

<pre><code>php artisan serve</code></pre>
Then open http://127.0.0.1:8000 in your browser.

<h3 align="left">🌐 Supported Languages</h3>
<ul>
  <li>English</li>
  <li>Russian</li>
</ul>

<h3 align="left">🔐 Configuration</h3>
Edit .env for Redis connection settings
<pre><code>REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null</code></pre>

## 📄 License
This project is licensed under the MIT License.
