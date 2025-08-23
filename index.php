<?php
// Simple dashboard that loads parsed errors from parse-errors.php
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>PHP Error Dashboard</title>
    <style>
    :root {
        --bg-color: #ffffff;
        --text-color: #333333;
        --border-color: #ddd;
        --header-bg: #f3f3f3;
        --row-alt-bg: #fafafa;
        --fatal-error-color: #b71c1c;
        --fatal-error-bg: #fdecea;
        --warning-color: #f57f17;
        --warning-bg: #fff4e5;
        --notice-color: #1565c0;
        --notice-bg: #e8f1ff;
        --deprecated-color: #6a1b9a;
        --deprecated-bg: #f5eaf8;
        --control-bg: #f5f5f5;
        --control-border: #ccc;
        --button-bg: #4a76a8;
        --button-color: white;
        --button-hover: #3a5b88;
        --shadow-color: rgba(0, 0, 0, 0.1);
        --transition-speed: 0.3s;
    }

    body.dark-mode {
        --bg-color: #232323;
        --text-color: #e0e0e0;
        --border-color: #444;
        --header-bg: #333;
        --row-alt-bg: #2a2a2a;
        --fatal-error-color: #ff6b6b;
        --fatal-error-bg: #661919;
        --warning-color: #ffd166;
        --warning-bg: #664d1a;
        --notice-color: #5ba9ff;
        --notice-bg: #1a3b66;
        --deprecated-color: #d3a4f7;
        --deprecated-bg: #46276a;
        --control-bg: #333333;
        --control-border: #555;
        --button-bg: #4a76a8;
        --button-color: white;
        --button-hover: #5b8ac1;
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    body {
        font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
        margin: 20px;
        background-color: var(--bg-color);
        color: var(--text-color);
        transition: background-color var(--transition-speed), color var(--transition-speed);
    }

    .app-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .controls {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 18px;
        padding: 15px;
        background: var(--control-bg);
        border-radius: 8px;
        box-shadow: 0 2px 5px var(--shadow-color);
    }

    .search-box {
        position: relative;
        flex-grow: 1;
    }

    .search-box input {
        width: 100%;
        padding: 10px 15px 10px 35px;
        border: 1px solid var(--control-border);
        border-radius: 6px;
        background-color: var(--bg-color);
        color: var(--text-color);
        font-size: 15px;
        transition: all 0.2s;
    }

    .search-box:before {
        content: "🔍";
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.6;
    }

    select,
    button {
        padding: 8px 12px;
        border: 1px solid var(--control-border);
        border-radius: 6px;
        background-color: var(--bg-color);
        color: var(--text-color);
        font-size: 14px;
        transition: all 0.2s;
    }

    button {
        background-color: var(--button-bg);
        color: var(--button-color);
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        font-weight: 500;
        min-width: 80px;
        justify-content: center;
    }

    button:hover {
        background-color: var(--button-hover);
    }

    .theme-toggle {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: var(--control-bg);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px var(--shadow-color);
    }

    th,
    td {
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        text-align: left;
        transition: background-color var(--transition-speed);
    }

    th {
        background: var(--header-bg);
        cursor: pointer;
        position: relative;
    }

    th:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    tr:nth-child(even) {
        background: var(--row-alt-bg);
    }

    /* Severity colors applied to cells and whole rows */
    .fatal-error,
    tr.fatal-error {
        color: var(--fatal-error-color);
        font-weight: 700;
        background: var(--fatal-error-bg);
    }

    .warning,
    tr.warning {
        color: var(--warning-color);
        background: var(--warning-bg);
    }

    .notice,
    tr.notice {
        color: var(--notice-color);
        background: var(--notice-bg);
    }

    .deprecated,
    tr.deprecated {
        color: var(--deprecated-color);
        background: var(--deprecated-bg);
    }

    #loader {
        display: none;
    }
    </style>
</head>

<body>
    <div class="app-header">
        <h1>PHP Error Dashboard</h1>
        <button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode">🌙</button>
    </div>
    <div class="controls">
        <div class="search-box">
            <input id="search" placeholder="Search messages, files, caches.....">
        </div>
        <label>Severity:
            <select id="severity">
                <option value="">(all)</option>
                <option>Fatal error</option>
                <option>Warning</option>
                <option>Notice</option>
                <option>Deprecated</option>
            </select>
        </label>
        <button id="refresh">Refresh</button>
        <span id="loader">Loading…</span>
    </div>
    <div id="count"></div>
    <table id="errors">
        <thead>
            <tr>
                <th data-key="timestamp">Time</th>
                <th data-key="type">Severity</th>
                <th>Message</th>
                <th>File</th>
                <th data-key="line">Line</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
    const q = document.getElementById('search');
    const severity = document.getElementById('severity');
    const refresh = document.getElementById('refresh');
    const loader = document.getElementById('loader');
    const tbody = document.querySelector('#errors tbody');
    const count = document.getElementById('count');
    let data = [];
    let sortKey = 'timestamp';
    let sortDir = -1; // desc

    async function load() {
        loader.style.display = '';
        try {
            const res = await fetch('parse-errors.php');
            // parse-errors.php should return a JSON array of errors
            data = await res.json();
            render();
        } catch (err) {
            console.error(err);
            alert('Failed to load parse-errors.php — check console');
        } finally {
            loader.style.display = 'none';
        }
    }

    function matches(item) {
        const term = q.value.trim().toLowerCase();
        if (severity.value && item.type !== severity.value) return false;
        if (!term) return true;
        return (item.message || '').toLowerCase().includes(term) ||
            (item.file || '').toLowerCase().includes(term) ||
            (item.timestamp || '').toLowerCase().includes(term);
    }

    function render() {
        // Apply filtering and sorting; show all matching rows
        const severityOrder = (s) => {
            if (!s) return 0;
            const t = String(s).toLowerCase();
            if (t.includes('fatal')) return 4; // highest
            if (t.includes('notice')) return 3;
            if (t.includes('warning')) return 2;
            if (t.includes('deprecated')) return 1; // lowest
            return 0;
        };

        const rows = data.filter(matches).sort((a, b) => {
            if (!a[sortKey]) return 1;
            if (!b[sortKey]) return -1;
            if (a[sortKey] === b[sortKey]) return 0;
            if (sortKey === 'type') {
                const ra = severityOrder(a.type);
                const rb = severityOrder(b.type);
                if (ra !== rb) return (ra - rb) * sortDir;
                return a.type > b.type ? sortDir : -sortDir;
            }
            return a[sortKey] > b[sortKey] ? sortDir : -sortDir;
        });

        tbody.innerHTML = rows.map(r => `<tr class="${escapeClass(r.type||'')}">
        <td>${escapeHtml(r.timestamp||'')}</td>
        <td class="${escapeClass(r.type||'')}">${escapeHtml(r.type||'')}</td>
        <td><pre style="white-space:pre-wrap;margin:0">${escapeHtml(r.message||'')}</pre></td>
        <td>${escapeHtml(r.file||'')}</td>
        <td>${escapeHtml(r.line||'')}</td>
      </tr>`).join('');

        count.textContent = `showing ${rows.length} of ${data.length} parsed`;
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function escapeClass(s) {
        return String(s).toLowerCase().replace(/[^a-z0-9 _-]/gi, '').replace(/\s+/g, '-');
    }

    document.querySelectorAll('th[data-key]').forEach(th => th.addEventListener('click', () => {
        const k = th.dataset.key;
        if (sortKey === k) sortDir = -sortDir;
        else {
            sortKey = k;
            sortDir = 1;
        }
        render();
    }));

    // Dark mode toggle functionality
    const themeToggle = document.getElementById('theme-toggle');

    // Check for saved theme preference or respect OS preference
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.body.classList.add('dark-mode');
        themeToggle.textContent = '☀️';
    }

    // Toggle theme
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');

        // Update button icon
        if (document.body.classList.contains('dark-mode')) {
            themeToggle.textContent = '☀️';
            localStorage.setItem('theme', 'dark');
        } else {
            themeToggle.textContent = '🌙';
            localStorage.setItem('theme', 'light');
        }
    });

    q.addEventListener('input', render);
    severity.addEventListener('change', render);
    refresh.addEventListener('click', load);

    // initial load
    load();
    </script>
</body>

</html>