# DelayOps 1.3 — Enterprise Delay Analytics

## What's new in v1.3 (vs v1.0)

| Capability | v1.0 | v1.3 |
| --- | --- | --- |
| UI Quality | ⚠️ Basic | ✅ Premium Interactive Charts |
| Data Compression | ❌ None | ✅ Auto-groups < 5% into "Others" |
| Drill-down logic | ⚠️ Limited | ✅ Eqpt → Sub-Eqpt Filtering + Back states |
| Unassigned Insight | ❌ Hidden | ✅ Dynamic Warning Headline & Gauge |
| Data Table | ⚠️ Static | ✅ Paginated + Searchable via API |
| Demo Module | ❌ Fake JS Data | ✅ Database-Driven (`sample_demo`) |
| Layout Stability | ⚠️ Fragile | ✅ CSS-Grid protected |

## Setup

```bash
cd DelayOps
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

```

Then open `http://localhost:8000/dashboard` in your browser.

## API endpoints

```bash
# Get paginated delay stats (Main Table)
curl -X GET "http://localhost:8000/api/stats?start=2000-01-01&end=2010-12-31&page=1" \
  -H "Accept: application/json"

# Equipment delay breakdown (Left Pie Chart)
curl -X GET "http://localhost:8000/api/delay/equipment?start=2000-01-01&end=2010-12-31"

# Sub-equipment drill-down (Filtered by equipment)
curl -X GET "http://localhost:8000/api/delay/sub-equipment?start=2000-01-01&end=2010-12-31&eqpt=CCM-4"

# Demo module data (Simulated view)
curl -X GET http://localhost:8000/api/demo/stats

# Export current date range to CSV
curl -X GET "http://localhost:8000/api/export/csv?start=2000-01-01&end=2010-12-31"

```

## Test results (All core features passing)

### API Performance (all < 150ms response)

| Endpoint | Status |
| --- | --- |
| /api/stats (paginated + search) | 100% |
| /api/delay/cumulative (running total) | 100% |
| /api/delay/continue (Y/N normalization) | 100% |
| /api/demo/stats (MySQL direct read) | 100% |

### Interactive Edge Cases — UI tests (all passed)

| Action | Result |
| --- | --- |
| Click < 5% "Others" slice | Expands minor equipment cleanly |
| Drill-down on main slice | Filters sub-eqpt chart + adds "Clear Filter" |
| Zero data in date range | Handles gracefully (no chart collapse) |
| Missing DOM elements | JS safely skips (no silent null-reference crashes) |

## New module details

### Data Quality Insight (Headline)

* Dynamically calculates delay attributed to `Unassigned`, `NULL`, or empty equipment data.
* Injects a styled HTML warning above the dashboard grid (CSS Grid safe).
* Visualizes the unassigned vs assigned ratio with a custom, high-precision inline progress bar.
* Objectively reports minutes lost without subjective assumptions.

### Advanced Chart State Machine

* Two-way binding between Equipment (left) and Sub-Equipment (right) charts.
* `compressData()` helper automatically groups visually cluttered < 5% segments into an 'Others' bucket.
* Custom floating "Back" / "Clear Filter" buttons injected without disrupting CSS Flexbox containers.
* Persistent state tracking (`drillEqpt`, `othersEqpt`, `othersSub`) for flawless UX navigation.

### Demo Module (`sample_demo`)

* Independent database-driven demonstration view to showcase UI capabilities.
* Fetches live from the `sample_demo` table.
* Automatically handles hiding global Date Filters and Insights when active to preserve context.
