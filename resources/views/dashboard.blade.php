<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VSP Delay Analytics · Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --bg-base:#0c0e14;--bg-surface:#12151e;--bg-card:#191d2a;--bg-hover:#1e2334;
  --border:rgba(255,255,255,0.07);--border-active:rgba(255,255,255,0.18);
  --text-primary:#e8eaf0;--text-secondary:#8b90a4;--text-muted:#555a6e;
  --amber:#f59e0b;--amber-dim:rgba(245,158,11,0.12);
  --teal:#14b8a6;--teal-dim:rgba(20,184,166,0.12);
  --blue:#6366f1;--blue-dim:rgba(99,102,241,0.12);
  --coral:#f97316;--coral-dim:rgba(249,115,22,0.12);
  --green:#22c55e;--green-dim:rgba(34,197,94,0.1);
  --red:#ef4444;--red-dim:rgba(239,68,68,0.12);
  --sw:220px;
  --mono:'IBM Plex Mono',monospace;--sans:'IBM Plex Sans',sans-serif;
}

body{font-family:var(--sans);background:var(--bg-base);color:var(--text-primary);display:flex;min-height:100vh;font-size:14px;line-height:1.5}

/* ── SIDEBAR ── */
#sidebar{width:var(--sw);background:var(--bg-surface);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100}
.sb-logo{padding:22px 20px 18px;border-bottom:1px solid var(--border)}
.sb-brand{font-family:var(--mono);font-size:13px;font-weight:500;color:var(--amber);letter-spacing:.08em;text-transform:uppercase}
.sb-ver{font-family:var(--mono);font-size:10px;color:var(--text-muted);margin-top:3px}
.sb-sec{padding:16px 20px 6px;font-size:10px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted)}
.sb-nav{flex:1;overflow-y:auto}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 20px;cursor:pointer;color:var(--text-secondary);font-size:13px;transition:all .15s;border-left:2px solid transparent;user-select:none}
.nav-item:hover{background:var(--bg-hover);color:var(--text-primary)}
.nav-item.active{color:var(--amber);background:var(--amber-dim);border-left-color:var(--amber);font-weight:500}
.nav-icon{width:16px;height:16px;opacity:.7;flex-shrink:0}
.nav-item.active .nav-icon{opacity:1}
.sb-foot{padding:14px 20px;border-top:1px solid var(--border);font-size:11px;color:var(--text-muted);font-family:var(--mono)}
.live-dot{display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--green);margin-right:6px;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

/* ── MAIN ── */
#main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh}

/* ── TOPBAR ── */
.topbar{background:var(--bg-surface);border-bottom:1px solid var(--border);padding:0 28px;height:60px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:50}
.topbar-title{font-size:15px;font-weight:500;flex:1}
.topbar-sub{font-family:var(--mono);font-size:11px;color:var(--text-muted);margin-left:8px;font-weight:400}

.filter-pill{display:flex;align-items:center;gap:8px;background:var(--bg-card);border:1px solid var(--border);border-radius:6px;padding:5px 10px}
.filter-pill label{font-size:10px;color:var(--text-muted);font-family:var(--mono);letter-spacing:.06em;text-transform:uppercase}
.filter-pill input[type=date]{background:transparent;border:none;color:var(--text-primary);font-family:var(--mono);font-size:12px;outline:none;cursor:pointer;width:116px}
.filter-pill input[type=date]::-webkit-calendar-picker-indicator{filter:invert(.5);cursor:pointer}

.btn{display:flex;align-items:center;gap:6px;padding:6px 13px;border-radius:6px;font-size:12px;font-weight:500;font-family:var(--sans);cursor:pointer;border:1px solid;transition:all .15s;white-space:nowrap}
.btn-ghost{background:transparent;color:var(--text-secondary);border-color:var(--border-active)}
.btn-ghost:hover{background:var(--bg-hover);color:var(--text-primary)}
.btn-ghost:disabled{opacity:.35;cursor:not-allowed}
.btn-amber{background:var(--amber);color:#0c0e14;border-color:var(--amber);font-weight:600}
.btn-amber:hover{background:#d97706;border-color:#d97706}
.btn-red{background:var(--red-dim);color:var(--red);border-color:rgba(239,68,68,.3)}
.btn-red:hover{background:rgba(239,68,68,.2)}

.role-badge{font-family:var(--mono);font-size:10px;padding:4px 10px;border-radius:4px;border:1px solid;font-weight:500;text-transform:uppercase;letter-spacing:.06em;cursor:pointer}
.role-admin{color:var(--amber);border-color:rgba(245,158,11,.3);background:var(--amber-dim)}
.role-user{color:var(--teal);border-color:rgba(20,184,166,.3);background:var(--teal-dim)}

.refresh-toggle{display:flex;align-items:center;gap:6px;font-size:11px;font-family:var(--mono);color:var(--text-muted);cursor:pointer;padding:5px 8px;border-radius:5px;border:1px solid var(--border);transition:all .15s}
.refresh-toggle:hover{border-color:var(--border-active);color:var(--text-secondary)}
.refresh-toggle.on{color:var(--green);border-color:rgba(34,197,94,.3);background:var(--green-dim)}
.refresh-dot{width:6px;height:6px;border-radius:50%;background:currentColor}
.refresh-toggle.on .refresh-dot{animation:pulse 1s infinite}

/* ── CONTENT ── */
.content{padding:24px 28px;flex:1}
.dash-page{display:none}
.dash-page.active{display:block}

/* ── LOADING OVERLAY ── */
.chart-card{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s;position:relative}
.chart-card:hover{border-color:var(--border-active)}
.chart-card.wide{grid-column:1/-1}

.loading-overlay{position:absolute;inset:0;background:rgba(12,14,20,.75);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;z-index:10;opacity:0;pointer-events:none;transition:opacity .2s;border-radius:10px}
.loading-overlay.show{opacity:1;pointer-events:auto}
.spinner{width:22px;height:22px;border:2px solid rgba(245,158,11,.2);border-top-color:var(--amber);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.loading-label{font-family:var(--mono);font-size:11px;color:var(--text-muted)}

.error-overlay{position:absolute;inset:0;background:rgba(12,14,20,.85);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;z-index:10;opacity:0;pointer-events:none;transition:opacity .2s;border-radius:10px}
.error-overlay.show{opacity:1;pointer-events:auto}
.error-icon{font-size:22px}
.error-msg{font-family:var(--mono);font-size:11px;color:var(--red);text-align:center}
.retry-btn{margin-top:4px;font-size:11px;font-family:var(--mono);color:var(--amber);background:var(--amber-dim);border:1px solid rgba(245,158,11,.3);border-radius:4px;padding:4px 10px;cursor:pointer}
.retry-btn:hover{background:rgba(245,158,11,.2)}

/* ── KPI CARDS ── */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.kpi-card{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:16px 18px;position:relative;overflow:hidden;transition:border-color .2s}
.kpi-card:hover{border-color:var(--border-active)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px}
.kpi-card.am::before{background:var(--amber)}
.kpi-card.te::before{background:var(--teal)}
.kpi-card.bl::before{background:var(--blue)}
.kpi-card.co::before{background:var(--coral)}
.kpi-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;font-weight:500;margin-bottom:8px}
.kpi-val{font-family:var(--mono);font-size:26px;font-weight:500;line-height:1;margin-bottom:6px}
.kpi-card.am .kpi-val{color:var(--amber)}
.kpi-card.te .kpi-val{color:var(--teal)}
.kpi-card.bl .kpi-val{color:var(--blue)}
.kpi-card.co .kpi-val{color:var(--coral)}
.kpi-sub{font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:4px}
.trend{font-family:var(--mono)}
.trend.up{color:var(--green)}.trend.dn{color:var(--red)}

/* ── SECTION HEADER ── */
.sec-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.sec-title{font-size:12px;font-weight:500;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em}

/* ── CHART GRID ── */
.chart-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.chart-header{padding:14px 18px 10px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between}
.chart-title{font-size:13px;font-weight:500}
.chart-sub{font-size:11px;color:var(--text-muted);font-family:var(--mono);margin-top:2px}
.chart-badge{font-family:var(--mono);font-size:10px;padding:3px 7px;border-radius:4px;font-weight:500;flex-shrink:0}
.b-pie{background:var(--amber-dim);color:var(--amber)}
.b-bar{background:var(--teal-dim);color:var(--teal)}
.b-line{background:var(--blue-dim);color:var(--blue)}
.b-dnt{background:var(--coral-dim);color:var(--coral)}
.chart-legend{display:flex;flex-wrap:wrap;gap:10px;padding:0 18px 10px;font-size:11px;color:var(--text-secondary)}
.leg-item{display:flex;align-items:center;gap:5px}
.leg-dot{width:8px;height:8px;border-radius:2px;flex-shrink:0}
.chart-body{padding:14px 18px 18px}

/* ── DRILL-DOWN BACK ── */
.drill-back{display:none;align-items:center;gap:6px;font-size:11px;font-family:var(--mono);color:var(--amber);cursor:pointer;padding:0 18px 10px}
.drill-back.show{display:flex}
.drill-back:hover{text-decoration:underline}

/* ── API TABLE ── */
.api-card{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:18px}
.api-hdr{padding:13px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px}
.api-method{font-family:var(--mono);font-size:10px;background:var(--green-dim);color:var(--green);padding:2px 6px;border-radius:3px;font-weight:500}
.api-ep{font-family:var(--mono);font-size:12px;color:var(--teal);background:var(--teal-dim);padding:2px 8px;border-radius:4px}
.api-count{font-family:var(--mono);font-size:11px;color:var(--text-muted);margin-left:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{font-family:var(--mono);font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);padding:9px 18px;text-align:left;font-weight:500;border-bottom:1px solid var(--border)}
.data-table td{font-family:var(--mono);font-size:12px;padding:8px 18px;color:var(--text-secondary);border-bottom:1px solid rgba(255,255,255,.03)}
.data-table tr:hover td{background:var(--bg-hover);color:var(--text-primary)}
.data-table tr:last-child td{border-bottom:none}
.c-am{color:var(--amber)!important;font-weight:500}
.c-gn{color:var(--green)!important}
.c-rd{color:var(--red)!important}

/* ── PAGINATION ── */
.pagination{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-top:1px solid var(--border)}
.page-info{font-family:var(--mono);font-size:11px;color:var(--text-muted)}
.page-btns{display:flex;gap:6px}
.page-btn{font-family:var(--mono);font-size:11px;padding:4px 10px;border-radius:4px;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text-secondary);transition:all .15s}
.page-btn:hover:not(:disabled){background:var(--bg-hover);color:var(--text-primary);border-color:var(--border-active)}
.page-btn:disabled{opacity:.3;cursor:not-allowed}
.page-btn.active{background:var(--amber-dim);color:var(--amber);border-color:rgba(245,158,11,.3)}

/* ── SEARCH ── */
.search-bar{position:relative;flex:1;max-width:240px}
.search-bar input{width:100%;background:var(--bg-card);border:1px solid var(--border);border-radius:6px;padding:6px 10px 6px 30px;font-family:var(--mono);font-size:12px;color:var(--text-primary);outline:none;transition:border-color .2s}
.search-bar input:focus{border-color:var(--border-active)}
.search-bar input::placeholder{color:var(--text-muted)}
.search-icon{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-muted)}

/* ── TOAST ── */
.toast{position:fixed;bottom:22px;right:22px;background:var(--bg-card);border:1px solid;border-radius:8px;padding:11px 16px;font-size:12px;font-family:var(--mono);z-index:9999;opacity:0;transform:translateY(6px);transition:all .2s;pointer-events:none;max-width:320px}
.toast.show{opacity:1;transform:translateY(0)}
.toast.info{border-color:var(--teal);color:var(--teal)}
.toast.error{border-color:var(--red);color:var(--red)}
.toast.ok{border-color:var(--green);color:var(--green)}

/* ── REFRESH PROGRESS ── */
.refresh-bar{position:fixed;top:0;left:var(--sw);right:0;height:2px;background:transparent;z-index:200;pointer-events:none}
.refresh-bar-fill{height:100%;background:var(--amber);width:0;transition:width linear}

::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border-active);border-radius:2px}
</style>
</head>
<body>

<!-- ── SIDEBAR ── -->
<nav id="sidebar">
  <div class="sb-logo">
    <div class="sb-brand">VSP Analytics</div>
    <div class="sb-ver" style="display:flex; flex-direction:column; gap:6px; margin-top:4px;">
      <span>v1.3 · production-ready</span>
      <span style="color:var(--amber); font-family:var(--mono); font-style:italic; opacity:0.8; font-size:9.5px; letter-spacing:0.05em;">
        // created by Sarat
      </span>
    </div>
  </div>
  <div class="sb-nav">
    <div class="sb-sec">Dashboards</div>
    <div class="nav-item active" onclick="switchPage('delay',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="2" y="4" width="12" height="10" rx="1.5"/><path d="M5 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1"/><path d="M6 8h4M6 11h2"/>
      </svg>Delay Module
    </div>
    <div class="nav-item" onclick="switchPage('demo',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M2 12L5 8l3 2 3-5 3 2"/><circle cx="14" cy="7" r="1" fill="currentColor"/>
      </svg>Demo Module
    </div>
    <div class="sb-sec">Data</div>
    <div class="nav-item" onclick="switchPage('api',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M2 4h12M2 8h8M2 12h10"/>
      </svg>API Explorer
    </div>
  </div>
  <div class="sb-foot"><span class="live-dot"></span>samplevsp · live</div>
</nav>

<!-- ── REFRESH BAR ── -->
<div class="refresh-bar"><div class="refresh-bar-fill" id="refresh-fill"></div></div>

<!-- ── MAIN ── -->
<div id="main">
  <div class="topbar">
    <div class="topbar-title" id="page-title">DELAY-OPS v1.3 <span class="topbar-sub" id="page-sub">samplevsp · 6 charts</span></div>
    <div class="filter-pill" id="date-filter">
      <label>FROM</label>
      <input type="date" id="dt-start" value="2005-03-01">
      <label style="margin-left:4px;">TO</label>
      <input type="date" id="dt-end" value="2005-04-30">
    </div>
    <div class="refresh-toggle" id="refresh-toggle" onclick="toggleRefresh()" title="Auto-refresh every 10s">
      <div class="refresh-dot"></div><span id="refresh-label">Auto</span>
    </div>
    <div class="role-badge role-admin" id="role-badge" onclick="toggleRole()" title="Toggle role">Admin</div>
    <button class="btn btn-ghost" onclick="triggerExport()">
      <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M8 2v9M5 8l3 3 3-3"/><path d="M3 14h10"/>
      </svg>Export CSV
    </button>
  </div>
  
  <div id="insight-headline" style="grid-column: 1 / -1; width: 100%; margin-bottom: 20px;"></div>

  <div class="content">

    <!-- ═══ DELAY PAGE ═══ -->
    <div class="dash-page active" id="page-delay">
      <div class="kpi-grid">
        <div class="kpi-card am"><div class="kpi-label">Total Delay (min)</div><div class="kpi-val" id="k-total">—</div><div class="kpi-sub"><span class="trend up">↑ 12%</span><span>vs prior period</span></div></div>
        <div class="kpi-card te"><div class="kpi-label">Total Records</div><div class="kpi-val" id="k-count">—</div><div class="kpi-sub">across selected range</div></div>
        <div class="kpi-card bl"><div class="kpi-label">Avg Delay / Job</div><div class="kpi-val" id="k-avg">—</div><div class="kpi-sub"><span class="trend dn">↓ 4%</span><span>improving</span></div></div>
        <div class="kpi-card co"><div class="kpi-label">Continue Rate</div><div class="kpi-val" id="k-cont">—</div><div class="kpi-sub">jobs marked continue</div></div>
      </div>

      <div class="sec-hdr">
        <div class="sec-title">Breakdown Charts</div>
        <div style="font-family:var(--mono);font-size:11px;color:var(--text-muted)" id="rec-range">loading…</div>
      </div>

      <div class="chart-grid">

        <!-- 1. Equipment Pie (drill-down enabled) -->
        <div class="chart-card" id="card-eqpt">
          <div class="chart-header">
            <div><div class="chart-title">Equipment-wise Delay</div><div class="chart-sub" id="eqpt-sub">click a slice to drill down</div></div>
            <span class="chart-badge b-pie">PIE</span>
          </div>
          <div class="drill-back" id="drill-back" onclick="exitDrill()">← back to equipment</div>
          <div class="chart-legend" id="leg-eqpt"></div>
          <div class="chart-body"><div style="position:relative;height:220px"><canvas id="eqptChart" role="img" aria-label="Equipment delay pie chart">Equipment delay breakdown.</canvas></div></div>
          <div class="loading-overlay" id="lo-eqpt"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-eqpt"><div class="error-icon">⚠</div><div class="error-msg">Failed to load data</div><div class="retry-btn" onclick="retryFetch('delay')">Retry</div></div>
        </div>

        <!-- 2. Sub-Eqpt Pie -->
        <div class="chart-card" id="card-sub">
          <div class="chart-header">
            <div><div class="chart-title">Sub-Equipment Delay</div><div class="chart-sub">delay split by sub-component</div></div>
            <span class="chart-badge b-pie">PIE</span>
          </div>
          <div class="chart-legend" id="leg-sub"></div>
          <div class="chart-body"><div style="position:relative;height:220px"><canvas id="subChart" role="img" aria-label="Sub-equipment delay pie">Sub-equipment breakdown.</canvas></div></div>
          <div class="loading-overlay" id="lo-sub"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-sub"></div>
        </div>

        <!-- 3. Shop Bar -->
        <div class="chart-card">
          <div class="chart-header">
            <div><div class="chart-title">Delay by Shop Code</div><div class="chart-sub">sorted highest to lowest</div></div>
            <span class="chart-badge b-bar">BAR</span>
          </div>
          <div class="chart-body"><div style="position:relative;height:220px"><canvas id="shopChart" role="img" aria-label="Shop code delay bar chart">Shop delay totals.</canvas></div></div>
          <div class="loading-overlay" id="lo-shop"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-shop"></div>
        </div>

        <!-- 4. Monthly Bar -->
        <div class="chart-card">
          <div class="chart-header">
            <div><div class="chart-title">Monthly Delay Trend</div><div class="chart-sub">total delay grouped by month</div></div>
            <span class="chart-badge b-bar">BAR</span>
          </div>
          <div class="chart-body"><div style="position:relative;height:220px"><canvas id="monthChart" role="img" aria-label="Monthly delay bar chart">Monthly delay totals.</canvas></div></div>
          <div class="loading-overlay" id="lo-month"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-month"></div>
        </div>

        <!-- 5. Cumulative Line (wide) -->
        <div class="chart-card wide">
          <div class="chart-header">
            <div><div class="chart-title">Cumulative Delay Trend</div><div class="chart-sub">running total across delivery dates</div></div>
            <span class="chart-badge b-line">LINE</span>
          </div>
          <div class="chart-body"><div style="position:relative;height:200px"><canvas id="cumChart" role="img" aria-label="Cumulative delay line chart">Cumulative delay trend.</canvas></div></div>
          <div class="loading-overlay" id="lo-cum"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-cum"></div>
        </div>

        <!-- 6. Continue Donut -->
        <div class="chart-card">
          <div class="chart-header">
            <div><div class="chart-title">Continue vs Not Continue</div><div class="chart-sub">job continuation status</div></div>
            <span class="chart-badge b-dnt">DONUT</span>
          </div>
          <div class="chart-legend" id="leg-cont"></div>
          <div class="chart-body"><div style="position:relative;height:200px"><canvas id="contChart" role="img" aria-label="Continue status donut chart">Continue vs not continue.</canvas></div></div>
          <div class="loading-overlay" id="lo-cont"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-cont"></div>
        </div>

        <!-- placeholder -->
        <div class="chart-card" style="display:flex;align-items:center;justify-content:center;min-height:260px;cursor:pointer;border-style:dashed" onclick="showToast('Feature slot — wire up your next chart here','info')">
          <div style="text-align:center;color:var(--text-muted)">
            <div style="font-size:28px;margin-bottom:6px;font-family:var(--mono)">+</div>
            <div style="font-size:12px;font-family:var(--mono)">Add chart</div>
          </div>
        </div>

      </div>
    </div>

    <!-- ═══ DEMO PAGE ═══ -->
    <div class="dash-page" id="page-demo">
      <div class="kpi-grid">
        <div class="kpi-card am"><div class="kpi-label">Total Value</div><div class="kpi-val" id="d-total">—</div><div class="kpi-sub">sum across labels</div></div>
        <div class="kpi-card te"><div class="kpi-label">Labels</div><div class="kpi-val" id="d-count">—</div><div class="kpi-sub">distinct categories</div></div>
        <div class="kpi-card bl"><div class="kpi-label">Avg Value</div><div class="kpi-val" id="d-avg">—</div><div class="kpi-sub">mean per label</div></div>
        <div class="kpi-card co"><div class="kpi-label">Peak Value</div><div class="kpi-val" id="d-max">—</div><div class="kpi-sub">highest label</div></div>
      </div>
      <div class="chart-grid">
        <div class="chart-card">
          <div class="chart-header"><div><div class="chart-title">Value Distribution</div><div class="chart-sub">proportion per label</div></div><span class="chart-badge b-pie">PIE</span></div>
          <div class="chart-legend" id="leg-demo-pie"></div>
          <div class="chart-body"><div style="position:relative;height:220px"><canvas id="dPie" role="img" aria-label="Demo pie">Demo distribution.</canvas></div></div>
          <div class="loading-overlay" id="lo-dpie"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-dpie"></div>
        </div>
        <div class="chart-card">
          <div class="chart-header"><div><div class="chart-title">Label vs Value</div><div class="chart-sub">comparison across categories</div></div><span class="chart-badge b-bar">BAR</span></div>
          <div class="chart-body"><div style="position:relative;height:220px"><canvas id="dBar" role="img" aria-label="Demo bar">Demo bar chart.</canvas></div></div>
          <div class="loading-overlay" id="lo-dbar"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-dbar"></div>
        </div>
        <div class="chart-card wide">
          <div class="chart-header"><div><div class="chart-title">Value Trend</div><div class="chart-sub">sequential value across labels</div></div><span class="chart-badge b-line">LINE</span></div>
          <div class="chart-body"><div style="position:relative;height:200px"><canvas id="dLine" role="img" aria-label="Demo line">Demo trend.</canvas></div></div>
          <div class="loading-overlay" id="lo-dline"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
          <div class="error-overlay" id="err-dline"></div>
        </div>
      </div>
    </div>

    <!-- ═══ API PAGE ═══ -->
    <div class="dash-page" id="page-api">
      <div class="sec-hdr" style="margin-bottom:16px">
        <div class="sec-title">API Explorer</div>
        <div style="display:flex;gap:10px;align-items:center">
          <div class="search-bar">
            <svg class="search-icon" width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="7" r="4"/><path d="M10.5 10.5l3 3"/></svg>
            <input type="text" id="tbl-search" placeholder="search records…" oninput="filterTable()">
          </div>
          <button class="btn btn-ghost" onclick="triggerExport()">Export CSV</button>
        </div>
      </div>

      <div class="api-card">
        <div class="api-hdr">
          <span class="api-method">GET</span>
          <span class="api-ep">/api/stats</span>
          <span class="api-count" id="tbl-count">— records</span>
        </div>
        <div style="overflow-x:auto;position:relative">
          <table class="data-table">
            <thead><tr>
              <th>ID</th><th>EQPT</th><th>SUB_EQPT</th><th>SHOP</th>
              <th>DELAY (min)</th><th>CUM DELAY</th><th>DELIVERY DATE</th><th>CONTINUE</th>
            </tr></thead>
            <tbody id="tbl-body"></tbody>
          </table>
          <div class="loading-overlay" id="lo-tbl" style="border-radius:0"><div class="spinner"></div><div class="loading-label">fetching…</div></div>
        </div>
        <div class="pagination">
          <div class="page-info" id="page-info">—</div>
          <div class="page-btns" id="page-btns"></div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="toast" id="toast"></div>

<script>
// ── CONSTANTS & PALETTE ──────────────────────────────────
const PALETTE=['#f59e0b','#14b8a6','#6366f1','#f97316','#22c55e','#e879f9','#38bdf8','#fb7185','#a3e635','#94a3b8'];

// ── REAL LARAVEL API LAYER ───────────────────────────────
async function apiFetch(endpoint, params = {}) {
  const url = new URL('/api/' + endpoint, window.location.origin);
  Object.entries(params || {}).forEach(([k, v]) => { if (v) url.searchParams.set(k, v); });
  const response = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }});
  if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
  return response.json();
}

// ── HELPERS & DATA COMPRESSION ───────────────────────────
function setLoading(id, on) { const el=document.getElementById('lo-'+id); if(el) el.classList.toggle('show',on); }
function setError(id, msg='') {
  const el=document.getElementById('err-'+id); if(!el) return;
  if(msg){ el.innerHTML=`<div class="error-icon">⚠</div><div class="error-msg">${msg}</div>`; el.classList.add('show'); } 
  else el.classList.remove('show');
}

function compressData(rawObj, threshold = 0.05) {
  let total = Object.values(rawObj).reduce((sum, val) => sum + Number(val), 0);
  let labels = [], vals = [], others = {}, othersTotal = 0;
  for (const [key, val] of Object.entries(rawObj)) {
    if (total > 0 && Number(val) / total < threshold) {
      others[key] = Number(val); othersTotal += Number(val);
    } else {
      labels.push(key); vals.push(Number(val));
    }
  }
  if (othersTotal > 0) { labels.push('Others'); vals.push(othersTotal); }
  return { labels, vals, others };
}

// ── NON-DESTRUCTIVE UI INJECTORS ─────────────────────────
function manageChartBtn(chartId, show, actionName, text="← Back") {
  const canvas = document.getElementById(chartId); if(!canvas) return;
  const card = canvas.closest('.card, .bg-card, .dashboard-card, .bg-slate-800') || canvas.parentElement.parentElement;
  if(!card) return;
  
  let btn = card.querySelector('.chart-action-btn');
  if (show) {
    if (!btn) {
      btn = document.createElement('button'); btn.className = 'chart-action-btn';
      // Absolutely positioned to the card, completely ignores chart canvas layout
      btn.style.cssText = "position:absolute; top:16px; right:16px; background:rgba(245,158,11,0.1); color:#f59e0b; border:1px solid rgba(245,158,11,0.25); padding:4px 10px; border-radius:6px; font-size:11px; cursor:pointer; z-index:50; font-weight:600; backdrop-filter:blur(4px); transition:all 0.2s;";
      btn.onmouseover = () => btn.style.background = 'rgba(245,158,11,0.2)';
      btn.onmouseout = () => btn.style.background = 'rgba(245,158,11,0.1)';
      if(getComputedStyle(card).position === 'static') card.style.position = 'relative';
      card.appendChild(btn);
    }
    btn.innerHTML = text; btn.setAttribute('onclick', actionName); btn.style.display = 'block';
  } else { if (btn) btn.style.display = 'none'; }
}

function updateSubtitle(chartId, htmlText) {
  const canvas = document.getElementById(chartId); if(!canvas) return;
  const card = canvas.closest('.card, .bg-card, .dashboard-card, .bg-slate-800') || canvas.parentElement.parentElement;
  if(!card) return;

  let subEl = card.querySelector('.forced-subtitle');
  if(!subEl) {
    subEl = document.createElement('div'); subEl.className = 'forced-subtitle';
    subEl.style.cssText = 'color:#8b90a4; font-size:12px; margin-bottom:16px; font-family:"IBM Plex Mono", monospace; font-style:italic; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:8px;';
    const title = card.querySelector('h2, h3, .card-title, .section-title');
    if(title) title.parentNode.insertBefore(subEl, title.nextSibling);
    else card.insertBefore(subEl, card.firstChild);
  }
  subEl.innerHTML = htmlText;
}

// ── PREMIUM CHART DEFAULTS ───────────────────────────────
Chart.defaults.color = '#8b90a4'; Chart.defaults.font.family = "'IBM Plex Mono', monospace";
const CH={}; function destroyChart(id){if(CH[id]){CH[id].destroy();delete CH[id]}}
function baseOpts(extra={}){
  return Object.assign({ responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:{ backgroundColor:'rgba(15, 23, 42, 0.95)', borderColor:'rgba(255,255,255,0.1)', borderWidth:1, titleColor:'#f8fafc', bodyColor:'#cbd5e1', padding:12, boxPadding:6, usePointStyle:true } } },extra);
}
function darkScales(){ return { x:{grid:{color:'rgba(255,255,255,0.03)'},ticks:{maxRotation:45}}, y:{grid:{color:'rgba(255,255,255,0.03)'}} }; }
function buildLegend(cid, labels, colors, values){
  const el=document.getElementById(cid); if(!el) return;
  const total = values.reduce((a,b) => Number(a) + Number(b), 0);
  el.innerHTML=labels.map((l,i)=>`<span class="leg-item"><span class="leg-dot" style="background:${colors[i%colors.length]}"></span>${l}<span style="color:var(--text-muted);margin-left:4px">${total > 0 ? Math.round(values[i]/total*100) : 0}%</span></span>`).join('');
}

// ── PERFECTED STATE MACHINE ──────────────────────────────
let drillEqpt = null; let othersEqpt = null; let othersSub = null;
window.exitOthersEqpt = function() { othersEqpt = null; renderEqptChart(); }
window.exitOthersSub = function() { othersSub = null; renderSubChart(); }
window.clearDrillDown = function() { drillEqpt = null; othersSub = null; renderSubChart(); }

const NOTE_TEXT = "Click a slice to drill down, or 'Others' to expand";

async function renderEqptChart(){
  const {start,end}=getDates(); setLoading('eqpt',true); setError('eqpt','');
  try {
    let raw, displayLabels, displayVals, hiddenDict = {};
    if (othersEqpt) {
      raw = othersEqpt; displayLabels = Object.keys(raw); displayVals = Object.values(raw);
      manageChartBtn('eqptChart', true, 'exitOthersEqpt()', '← Back to Main');
      updateSubtitle('eqptChart', 'Viewing expanded minor equipment (< 5%)');
    } else {
      raw = await apiFetch('delay/equipment', {start,end});
      const comp = compressData(raw, 0.05);
      displayLabels = comp.labels; displayVals = comp.vals; hiddenDict = comp.others;
      manageChartBtn('eqptChart', false);
      updateSubtitle('eqptChart', NOTE_TEXT);
    }
    buildLegend('leg-eqpt',displayLabels,PALETTE,displayVals);
    destroyChart('eqptChart');
    CH['eqptChart']=new Chart(document.getElementById('eqptChart'),{
      type:'pie', data:{labels:displayLabels,datasets:[{data:displayVals,backgroundColor:PALETTE.slice(0,displayLabels.length), borderWidth:0, spacing:3, hoverOffset:6}]}, 
      options:baseOpts({ onClick:(_,elements)=>{
          if(!elements.length) return;
          const clicked = displayLabels[elements[0].index];
          if (clicked === 'Others') { othersEqpt = hiddenDict; renderEqptChart(); } 
          else { drillEqpt = clicked; othersSub = null; renderSubChart(); }
      }})
    });
  } catch(e) {} setLoading('eqpt',false);
}

async function renderSubChart(){
  const {start,end}=getDates(); setLoading('sub',true); setError('sub','');
  try {
    let raw, displayLabels, displayVals, hiddenDict = {};
    if (othersSub) {
      raw = othersSub; displayLabels = Object.keys(raw); displayVals = Object.values(raw);
      manageChartBtn('subChart', true, 'exitOthersSub()', '← Back to Main');
      updateSubtitle('subChart', 'Viewing expanded minor sub-equipment');
    } else {
      raw = await apiFetch('delay/sub-equipment', drillEqpt ? {start,end,eqpt:drillEqpt} : {start,end});
      const comp = compressData(raw, 0.05);
      displayLabels = comp.labels; displayVals = comp.vals; hiddenDict = comp.others;
      if (drillEqpt) {
        manageChartBtn('subChart', true, 'clearDrillDown()', `✖ Clear Filter: ${drillEqpt}`);
        updateSubtitle('subChart', `Filtered by: <b style="color:#f59e0b">${drillEqpt}</b>`);
      } else {
        manageChartBtn('subChart', false);
        updateSubtitle('subChart', NOTE_TEXT);
      }
    }
    buildLegend('leg-sub',displayLabels,PALETTE,displayVals);
    destroyChart('subChart');
    CH['subChart']=new Chart(document.getElementById('subChart'),{
      type:'pie', data:{labels:displayLabels,datasets:[{data:displayVals,backgroundColor:PALETTE.slice(0,displayLabels.length), borderWidth:0, spacing:3, hoverOffset:6}]}, 
      options:baseOpts({ onClick:(_,elements)=>{
          if(!elements.length || othersSub) return;
          if(displayLabels[elements[0].index] === 'Others') { othersSub = hiddenDict; renderSubChart(); }
      }})
    });
  } catch(e) {} setLoading('sub',false);
}

// ── MAIN RENDER & PREMIUM HEADLINE ───────────────────────
async function renderDelay(){
  const {start,end}=getDates();
  document.getElementById('rec-range').textContent = `fetching data...`;

  await Promise.allSettled([
    renderEqptChart(), renderSubChart(), renderShopChart(start,end),
    renderMonthChart(start,end), renderCumChart(start,end), renderContChart(start,end),
  ]);

  try {
    const stats = await apiFetch('stats', {start, end, per_page: 1});
    const cont = await apiFetch('delay/continue', {start, end});
    const eqpt = await apiFetch('delay/equipment', {start, end});
    
    let totalDelay = 0; let unassignedDelay = 0;
    Object.entries(eqpt).forEach(([k,v]) => {
      let num = Number(v); totalDelay += num;
      if(!k.trim() || k.toLowerCase().includes('unassigned') || k.toLowerCase() === 'null' || k.toLowerCase() === 'none') {
        unassignedDelay += num;
      }
    });

    const totalRecords = stats.total || 0;
    const avgDelay = totalRecords > 0 ? Math.round(totalDelay / totalRecords) : 0;
    const continueRate = totalRecords > 0 ? Math.round((cont.Y / totalRecords) * 100) : 0;
    
    // THE MISSING MATH: Both percentages defined here!
    const unassignedPct = totalDelay > 0 ? ((unassignedDelay / totalDelay) * 100).toFixed(2) : "0.00";
    const assignedPct = (100 - Number(unassignedPct)).toFixed(2);

    const hl = document.getElementById('insight-headline');
    if(hl && totalRecords > 0 && unassignedDelay > 0) {
      hl.innerHTML = `
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; background:rgba(30, 41, 59, 0.4); border:1px solid rgba(255,255,255,0.05); padding:16px 20px; border-radius:12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
          
          <div style="display:flex; align-items:center; gap:14px;">
            <div style="background:rgba(239, 68, 68, 0.15); padding:8px; border-radius:8px; border:1px solid rgba(239, 68, 68, 0.3);">
              <svg width="20" height="20" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            <div>
              <h4 style="margin:0; color:#f8fafc; font-size:15px; font-weight:600; letter-spacing:0.3px; font-family:'IBM Plex Sans', sans-serif;">Data Quality Insight</h4>
              <p style="margin:5px 0 0 0; color:#94a3b8; font-size:13px; font-family:'IBM Plex Sans', sans-serif;">
                The cumulative delay where no equipment has been identified accounts for <strong style="color:#ef4444; font-size:15px;">${unassignedPct}%</strong> of the total delay. That equals <strong style="color:#e8eaf0;">${unassignedDelay.toLocaleString()} min</strong>.
              </p>
            </div>
          </div>
          
          <div style="display:flex; align-items:center; gap:20px; background:rgba(15, 23, 42, 0.6); padding:10px 20px; border-radius:8px; border:1px solid rgba(255,255,255,0.03);">
            <div style="text-align:right;">
              <div style="color:#ef4444; font-size:18px; font-weight:700; font-family:'IBM Plex Mono', monospace;">${unassignedDelay.toLocaleString()}</div>
              <div style="color:#64748b; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; margin-top:2px; font-family:'IBM Plex Sans', sans-serif;">Minutes Lost</div>
            </div>
            
            <div style="width: 140px; display:flex; flex-direction:column; gap:8px;">
              <div style="display:flex; justify-content:space-between; font-size:11px; font-weight:600; font-family:'IBM Plex Mono', monospace;">
                <span style="color:#ef4444;">${unassignedPct}%</span>
                <span style="color:#38bdf8;">${assignedPct}%</span>
              </div>
              <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden; display: flex;">
                <div style="width: ${unassignedPct}%; background: #ef4444; height: 100%; box-shadow: 0 0 10px rgba(239,68,68,0.5);"></div>
                <div style="width: ${assignedPct}%; background: #38bdf8; height: 100%;"></div>
              </div>
            </div>
          </div>

        </div>
      `;
      hl.style.display = 'block';
    } else if (hl) {
      hl.style.display = 'none';
    }

    document.getElementById('k-total').textContent = totalDelay.toLocaleString();
    document.getElementById('k-count').textContent = totalRecords.toLocaleString();
    document.getElementById('k-avg').textContent = avgDelay + ' min';
    document.getElementById('k-cont').textContent = continueRate + '%';
    document.getElementById('rec-range').textContent = `${totalRecords} records · ${start} → ${end}`;
  } catch(e) { console.error(e) }
}

async function renderShopChart(start,end){
  setLoading('shop',true); setError('shop','');
  try{ const raw=await apiFetch('delay/shop',{start,end}); destroyChart('shopChart'); CH['shopChart']=new Chart(document.getElementById('shopChart'),{ type:'bar', data:{labels:Object.keys(raw),datasets:[{label:'Delay (min)',data:Object.values(raw), backgroundColor:'#f59e0b', borderRadius:4}]}, options:baseOpts({scales:darkScales()})}); }catch(e){} setLoading('shop',false);
}
async function renderMonthChart(start,end){
  setLoading('month',true); setError('month','');
  try{ const raw=await apiFetch('delay/monthly',{start,end}); destroyChart('monthChart'); CH['monthChart']=new Chart(document.getElementById('monthChart'),{ type:'bar', data:{labels:Object.keys(raw),datasets:[{label:'Monthly Delay',data:Object.values(raw),backgroundColor:'rgba(20,184,166,0.8)', borderRadius:4}]}, options:baseOpts({scales:darkScales()})}); }catch(e){} setLoading('month',false);
}
async function renderCumChart(start,end){
  setLoading('cum',true); setError('cum','');
  try{ const rows=await apiFetch('delay/cumulative',{start,end}); destroyChart('cumChart'); CH['cumChart']=new Chart(document.getElementById('cumChart'),{ type:'line', data:{labels:rows.map(r=>r.date),datasets:[{ label:'Cumulative Delay', data:rows.map(r=>r.cum), borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,0.1)', fill:true, tension:0.4, borderWidth:3, pointRadius:4, pointBackgroundColor:'#1e293b', pointBorderColor:'#6366f1', pointBorderWidth:2, pointHoverRadius:6 }]}, options:baseOpts({scales:darkScales()}) }); }catch(e){} setLoading('cum',false);
}
async function renderContChart(start,end){
  setLoading('cont',true); setError('cont','');
  try{ const {Y,N}=await apiFetch('delay/continue',{start,end}); buildLegend('leg-cont',['Continue','Not Continue'],['#10b981','#ef4444'],[Y,N]); destroyChart('contChart'); CH['contChart']=new Chart(document.getElementById('contChart'),{ type:'doughnut', data:{labels:['Continue','Not Continue'],datasets:[{data:[Y,N],backgroundColor:['#10b981','#ef4444'], borderWidth:0, spacing:3, hoverOffset:6, cutout:'75%'}]}, options:baseOpts() }); }catch(e){} setLoading('cont',false);
}

// ── REAL DEMO MODULE RENDERER ────────────────────────────
async function renderDemo(){
  setLoading('dpie',true); setLoading('dbar',true); setLoading('dline',true);

  try {
    // 1. Fetch data from your real backend!
    const data = await apiFetch('demo/stats');

    // 2. Parse the SQL columns ('label' and 'value')
    const categories = data.map(row => row.label);
    const values = data.map(row => Number(row.value));
    const total = values.reduce((sum, val) => sum + val, 0);

    // 3. Populate Demo KPIs
    document.getElementById('d-total').textContent = total.toLocaleString();
    document.getElementById('d-count').textContent = categories.length;
    document.getElementById('d-avg').textContent = categories.length > 0 ? Math.round(total / categories.length).toLocaleString() : 0;
    document.getElementById('d-max').textContent = values.length > 0 ? Math.max(...values).toLocaleString() : 0;

    // 4. Render Demo Pie Chart
    buildLegend('leg-demo-pie', categories, PALETTE, values);
    destroyChart('dPie'); 
    CH['dPie'] = new Chart(document.getElementById('dPie'),{ 
      type:'pie', 
      data:{labels:categories, datasets:[{data:values, backgroundColor:PALETTE.slice(0, categories.length), borderWidth:0, spacing:3, hoverOffset:6}]}, 
      options:baseOpts()
    });

    // 5. Render Demo Bar Chart
    destroyChart('dBar'); 
    CH['dBar'] = new Chart(document.getElementById('dBar'),{ 
      type:'bar', 
      data:{labels:categories, datasets:[{label:'Volume', data:values, backgroundColor:'#14b8a6', borderRadius:4}]}, 
      options:baseOpts({scales:darkScales()})
    });

    // 6. Render Demo Line Trend
    destroyChart('dLine'); 
    CH['dLine'] = new Chart(document.getElementById('dLine'),{ 
      type:'line', 
      data:{labels:categories, datasets:[{ label:'Active Trend', data:values, borderColor:'#f97316', backgroundColor:'rgba(249,115,22,0.1)', fill:true, tension:0.4, borderWidth:3, pointRadius:4, pointBackgroundColor:'#1e293b', pointBorderColor:'#f97316', pointBorderWidth:2, pointHoverRadius:6 }]}, 
      options:baseOpts({scales:darkScales()}) 
    });

  } catch(e) { console.error("Demo Fetch Error:", e); }

  setLoading('dpie',false); setLoading('dbar',false); setLoading('dline',false);
}

let tblPage=1; const PER_PAGE=15; let tblSearch='';
async function renderTable(page=1){
  tblPage=page; setLoading('tbl',true);
  try{ const {start,end}=getDates(); const res=await apiFetch('stats',{start,end,page,per_page:PER_PAGE,search:tblSearch}); document.getElementById('tbl-count').textContent=res.total+' records'; document.getElementById('tbl-body').innerHTML=res.data.map(r=>`<tr><td>${r.id}</td><td>${r.eqpt}</td><td>${r.sub_eqpt}</td><td>${r.shop_code}</td><td class="c-am">${r.delay_duration}</td><td>${Number(r.cumulative_delay).toLocaleString()}</td><td>${r.delivery_date}</td><td class="${['Y','YES','1'].includes((r.continue_y_n||'').toString().toUpperCase())?'c-gn':'c-rd'}">${r.continue_y_n}</td></tr>`).join(''); buildPagination(page, res.pages, res.total); }catch(e){} setLoading('tbl',false);
}
function buildPagination(cur, total, recTotal){
  const from=(cur-1)*PER_PAGE+1, to=Math.min(cur*PER_PAGE,recTotal); document.getElementById('page-info').textContent=`${from}–${to} of ${recTotal}`; const btns=document.getElementById('page-btns'); const pages=[]; pages.push(1); if(cur>3) pages.push('…'); for(let p=Math.max(2,cur-1);p<=Math.min(total-1,cur+1);p++) pages.push(p); if(cur<total-2) pages.push('…'); if(total>1 && !pages.includes(total)) pages.push(total);
  btns.innerHTML=`<button class="page-btn" ${cur<=1?'disabled':''} onclick="renderTable(${cur-1})">←</button>${pages.map(p=>p==='…'?`<span class="page-btn" style="cursor:default">…</span>`:`<button class="page-btn ${p===cur?'active':''}" onclick="renderTable(${p})">${p}</button>`).join('')}<button class="page-btn" ${cur>=total?'disabled':''} onclick="renderTable(${cur+1})">→</button>`;
}
function filterTable(){ tblSearch=document.getElementById('tbl-search').value; renderTable(1); }

// Added the "demo" metadata so the topbar updates correctly!
const PAGE_META={ 
  delay:{title:'Delay Module',sub:'samplevsp · 6 charts'}, 
  demo:{title:'Demo Module',sub:'database data · 3 charts'},
  api:{title:'API Explorer',sub:'GET /api/stats · paginated'} 
};

function switchPage(name,el){ 
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active')); if(el) el.classList.add('active'); 
  document.querySelectorAll('.dash-page').forEach(p=>p.classList.remove('active')); document.getElementById('page-'+name).classList.add('active'); 
  
  document.getElementById('page-title').innerHTML=`${PAGE_META[name].title} <span class="topbar-sub">${PAGE_META[name].sub}</span>`; 
  
  // Hide the Date Filters and the Insight Headline if we aren't on the main Delay page
  const dateFilter = document.getElementById('date-filter');
  if (dateFilter) dateFilter.style.display = (name==='delay') ? 'flex' : 'none'; 
  
  const hl = document.getElementById('insight-headline');
  if(hl && name !== 'delay') hl.style.display = 'none';

  // Trigger the correct render function for the page
  if(name==='api') renderTable(1); 
  if(name==='demo') renderDemo();
  if(name==='delay') renderDelay();
}

function getDates(){ return {start:document.getElementById('dt-start').value, end:document.getElementById('dt-end').value}; }
document.getElementById('dt-start').addEventListener('change',()=>renderDelay()); document.getElementById('dt-end').addEventListener('change',()=>renderDelay());
function triggerExport(){ const {start,end}=getDates(); window.location.href = `/api/export/csv?start=${start}&end=${end}`; }

renderDelay();
</script>
</body>
</html>
