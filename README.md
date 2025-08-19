# PHP Error Dashboard

![PHP Error Dashboard screenshot](1.png)

Lightweight tool to parse PHP error logs and inspect results in a simple web UI.

Clone

```bash
git clone https://github.com/abdohwebdev/error-dashboard.git
cd error-dashboard
```

Quick usage

- Run the parser and print JSON:

```bash
php parse-errors.php
```

- Serve the dashboard (example using PHP built-in server):

```bash
php -S 127.0.0.1:8000
# then open http://127.0.0.1:8000/index.php
```

Notes

- `parse-errors.php` reads `error.log` and emits JSON objects with: `timestamp`, `type`, `message`, `file`, `line`.
- For production use with large logs consider adding server-side pagination, indexing (SQLite), or caching.

Feedback & contributions

Issues, suggestions or pull requests are welcome — please open them on GitHub. I appreciate feedback and contributions to improve parsing, performance, and UX.
