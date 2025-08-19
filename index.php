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
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;margin:20px}
    .controls{display:flex;gap:8px;align-items:center;margin-bottom:12px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px;border:1px solid #ddd;text-align:left}
    th{background:#f3f3f3;cursor:pointer}
    tr:nth-child(even){background:#fafafa}
    /* Severity colors applied to cells and whole rows (bare classes: e.g. 'fatal-error') */
    .fatal-error, tr.fatal-error { color:#b71c1c; font-weight:700; background:#fdecea }
    .warning, tr.warning { color:#f57f17; background:#fff4e5 }
    .notice, tr.notice { color:#1565c0; background:#e8f1ff }
    .deprecated, tr.deprecated { color:#6a1b9a; background:#f5eaf8 }
    #loader{display:none}
  </style>
</head>
<body>
  <h1>PHP Error Dashboard</h1>
  <div class="controls">
    <label>Search: <input id="search" placeholder="search message, file, timestamp"></label>
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
      return (item.message||'').toLowerCase().includes(term)
        || (item.file||'').toLowerCase().includes(term)
        || (item.timestamp||'').toLowerCase().includes(term);
    }

    function render() {
      // Apply filtering and sorting; show all matching rows
      const severityOrder = (s) => {
        if (!s) return 0;
        const t = String(s).toLowerCase();
        if (t.includes('fatal')) return 4;       // highest
        if (t.includes('notice')) return 3;
        if (t.includes('warning')) return 2;
        if (t.includes('deprecated')) return 1;  // lowest
        return 0;
      };

      const rows = data.filter(matches).sort((a,b)=>{
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

      tbody.innerHTML = rows.map(r=>`<tr class="${escapeClass(r.type||'')}">
        <td>${escapeHtml(r.timestamp||'')}</td>
        <td class="${escapeClass(r.type||'')}">${escapeHtml(r.type||'')}</td>
        <td><pre style="white-space:pre-wrap;margin:0">${escapeHtml(r.message||'')}</pre></td>
        <td>${escapeHtml(r.file||'')}</td>
        <td>${escapeHtml(r.line||'')}</td>
      </tr>`).join('');

      count.textContent = `showing ${rows.length} of ${data.length} parsed`;
    }

    function escapeHtml(s){
      return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function escapeClass(s){
      return String(s).toLowerCase().replace(/[^a-z0-9 _-]/gi,'').replace(/\s+/g,'-');
    }

    document.querySelectorAll('th[data-key]').forEach(th=>th.addEventListener('click',()=>{
      const k = th.dataset.key;
      if (sortKey === k) sortDir = -sortDir; else { sortKey = k; sortDir = 1; }
      render();
    }));

    q.addEventListener('input', render);
    severity.addEventListener('change', render);
    refresh.addEventListener('click', load);

    // initial load
    load();
   </script>
 </body>
 </html>
