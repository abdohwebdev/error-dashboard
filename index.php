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

    .header-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .about-link {
        cursor: pointer;
        font-weight: 500;
        color: var(--button-bg);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .about-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .about-content {
        background-color: var(--bg-color);
        border-radius: 8px;
        padding: 20px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .about-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px;
    }

    .close-modal {
        cursor: pointer;
        font-size: 22px;
        background: none;
        border: none;
        color: var(--text-color);
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

    .severity-wrapper {
        display: flex;
        align-items: center;
        position: relative;
        min-width: 140px;
    }

    .custom-select {
        position: relative;
        min-width: 180px;
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

    .custom-select select {
        appearance: none;
        -webkit-appearance: none;
        width: 100%;
        padding-right: 30px;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 1em;
    }

    .custom-select select option[disabled] {
        color: var(--text-color);
        font-weight: 500;
    }

    .custom-select select option:first-of-type {
        font-style: italic;
        color: var(--text-color);
        opacity: 0.7;
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
        background: var(--button-bg);
        border: none;
        cursor: pointer;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        color: white;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 5px var(--shadow-color);
        transition: all 0.3s ease;
    }

    .theme-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px var(--shadow-color);
    }

    .theme-toggle svg {
        width: 22px;
        height: 22px;
        transition: transform 0.5s ease;
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

    /* Enhanced severity styling */
    .severity-text {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 500;
        white-space: nowrap;
    }

    tr.fatal-error td.fatal-error .severity-text {
        background: rgba(183, 28, 28, 0.2);
        border-left: 3px solid var(--fatal-error-color);
        padding-left: 8px;
    }

    tr.warning td.warning .severity-text {
        background: rgba(245, 127, 23, 0.2);
        border-left: 3px solid var(--warning-color);
        padding-left: 8px;
    }

    tr.notice td.notice .severity-text {
        background: rgba(21, 101, 192, 0.2);
        border-left: 3px solid var(--notice-color);
        padding-left: 8px;
    }

    tr.deprecated td.deprecated .severity-text {
        background: rgba(106, 27, 154, 0.2);
        border-left: 3px solid var(--deprecated-color);
        padding-left: 8px;
    }

    /* Severity badges animation on hover */
    .severity-text:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }

    /* Error message link styling */
    .error-message-link {
        cursor: pointer;
        color: inherit;
        text-decoration: none;
    }

    .error-message-link:hover {
        text-decoration: underline;
    }

    #loader {
        display: none;
    }
    </style>
</head>

<body>
    <div class="app-header">
        <h1>PHP Error Dashboard</h1>
        <div class="header-right">
            <div class="about-link" id="about-btn">About</div>
            <button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="light-icon">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dark-icon"
                    style="display:none">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- About Modal -->
    <div class="about-modal" id="about-modal">
        <div class="about-content">
            <div class="about-header">
                <h2>About PHP Error Dashboard</h2>
                <button class="close-modal" id="close-modal">&times;</button>
            </div>
            <p>PHP Error Dashboard is a lightweight tool for parsing and analyzing PHP error logs.</p>
            <p><strong>Version:</strong> 1.0.0</p>
            <p><strong>Features:</strong></p>
            <ul>
                <li>Parse PHP error logs</li>
                <li>Filter by error type and text</li>
                <li>Sort by various properties</li>
                <li>Visual categorization of error types</li>
            </ul>
            <p><strong>Repository:</strong> <a href="https://github.com/M9nx/error-dashboard" target="_blank">GitHub -
                    M9nx/error-dashboard</a></p>
            <p><strong>License:</strong> MIT</p>
        </div>
    </div>
    <div class="controls">
        <div class="search-box">
            <input id="search" placeholder="Search messages, files, caches.....">
        </div>
        <div class="severity-wrapper">
            <div class="custom-select">
                <select id="severity">
                    <option value="" disabled selected>Severity</option>
                    <option value="">(all)</option>
                    <option value="Fatal error">Fatal error</option>
                    <option value="Warning">Warning</option>
                    <option value="Notice">Notice</option>
                    <option value="Deprecated">Deprecated</option>
                </select>
            </div>
        </div>
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
        const selectedSeverity = severity.value;
        // Skip the disabled placeholder option
        if (selectedSeverity && selectedSeverity !== "disabled" && item.type !== selectedSeverity) return false;
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

        tbody.innerHTML = rows.map(r => {
            const severityClass = escapeClass(r.type || '');
            const errorMessage = escapeHtml(r.message || '');
            const searchQuery = encodeURIComponent(`php ${r.type} ${r.message} how to solve`);

            return `<tr class="${severityClass}">
                <td>${escapeHtml(r.timestamp||'')}</td>
                <td class="${severityClass}">
                    <span class="severity-text">${escapeHtml(r.type||'')}</span>
                </td>
                <td>
                    <a href="https://www.google.com/search?q=${searchQuery}" target="_blank" class="error-message-link" title="Search for solutions to this error">
                        <pre style="white-space:pre-wrap;margin:0">${errorMessage}</pre>
                    </a>
                </td>
                <td>${escapeHtml(r.file||'')}</td>
                <td>${escapeHtml(r.line||'')}</td>
            </tr>`;
        }).join('');

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
    const lightIcon = themeToggle.querySelector('.light-icon');
    const darkIcon = themeToggle.querySelector('.dark-icon');

    // Check for saved theme preference or respect OS preference
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');

    function updateThemeIcons(isDark) {
        if (isDark) {
            lightIcon.style.display = 'none';
            darkIcon.style.display = 'block';
        } else {
            lightIcon.style.display = 'block';
            darkIcon.style.display = 'none';
        }
    }

    // Apply initial theme
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.body.classList.add('dark-mode');
        updateThemeIcons(true);
    } else {
        updateThemeIcons(false);
    }

    // Toggle theme
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');

        // Update icons
        updateThemeIcons(isDark);

        // Save preference
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }); // About modal functionality
    const aboutBtn = document.getElementById('about-btn');
    const aboutModal = document.getElementById('about-modal');
    const closeModal = document.getElementById('close-modal');

    aboutBtn.addEventListener('click', () => {
        aboutModal.style.display = 'flex';
    });

    closeModal.addEventListener('click', () => {
        aboutModal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === aboutModal) {
            aboutModal.style.display = 'none';
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