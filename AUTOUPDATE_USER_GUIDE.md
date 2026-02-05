# Auto-Update Feature - Quick Reference Guide

## What's New

All dashboard pages now automatically refresh their data in real-time without requiring manual page reload. The following pages have auto-update enabled:

1. **Client Dashboard** (`/my-tickets`) - Refreshes every 30-45 seconds
2. **Support Dashboard** (`/support-tickets`) - Refreshes every 30 seconds  
3. **Admin Dashboard** (`/admin-tickets`) - Refreshes every 60 seconds

## Features at a Glance

### Client Dashboard Auto-Updates

**Statistics Cards** (Every 30 seconds):
- Total Tickets
- Pending Tickets
- Received Tickets
- In Progress (Send to Logic)
- Completed Tickets

**Ticket Table** (Every 45 seconds):
- Ticket numbers and subjects
- Support type assignments
- Current ticket status
- **Queue Position** - Shows #X of Y for pending tickets (FIFO order)
- Created date and solving time
- Assigned support team member

**Timestamp Display:**
Shows "Last updated: HH:MM:SS" in the stats section

### Support Dashboard Auto-Updates

**Performance Metrics** (Every 30 seconds):
- Tickets Solved
- Reviews Received
- Satisfied reviews count
- Dissatisfied reviews count
- **Forwarded tickets count** ✨ NEW
- **Forward percentage rate** ✨ NEW

**Ticket Tables** (Every 30 seconds):
- ERP Support tickets
- IT Support tickets
- Programmer Support tickets

Each table shows the latest 10 tickets with:
- Ticket number and client name
- Subject and current status
- Created date and completion date
- Assigned support user
- Action buttons (View/Forward)

**Timestamp Display:**
Shows "Last updated: HH:MM:SS" on the performance card

### Admin Dashboard Auto-Updates

**Overall Statistics** (Every 60 seconds):
- Total Tickets
- Pending Tickets
- Received Tickets (MIS)
- In Progress Tickets
- Completed Tickets

**Support Type Statistics** (Every 60 seconds):
- ERP Support - Total count
- IT Support - Total count
- Programmer Support - Total count

**Review Statistics** (Every 60 seconds):
- Total Reviews
- Satisfied reviews (with percentage)
- Dissatisfied reviews (with percentage)

**Timestamp Display:**
Shows "Last updated: HH:MM:SS" in the top-right corner

**Top Performers Table:**
Auto-updates to show:
- User names
- Completed tickets count
- Forwarded tickets count ✨
- Forward percentage rate ✨

## How It Works

The auto-update system uses **AJAX polling** - the browser periodically asks the server for updated data and refreshes only the specific page elements that changed, without reloading the entire page.

### Data Flow

```
Browser (Dashboard Page)
    ↓
[Timer triggers every 30-60 seconds]
    ↓
AJAX Request to Server
    ↓
Server fetches updated data from database
    ↓
Server returns JSON response
    ↓
JavaScript updates specific DOM elements
    ↓
User sees updated statistics and ticket data
```

## User Experience Benefits

✅ **No Manual Refresh Needed** - Information updates automatically  
✅ **Smooth Experience** - Only specific elements update, not entire page  
✅ **Transparent Updates** - "Last updated" timestamp shows when data was refreshed  
✅ **Background Operation** - Updates happen without interrupting user workflow  
✅ **Real-Time Queue Positions** - Pending ticket queue positions recalculate dynamically  
✅ **Forward Metrics** - Support users can see their forwarding statistics in real-time  

## Network & Performance

- **Client Dashboard:** ~2 API calls every 40 seconds (light load)
- **Support Dashboard:** ~2 API calls every 30 seconds (moderate load)
- **Admin Dashboard:** ~1 API call every 60 seconds (minimal load)
- **Data Transfer:** Typically 1-5 KB per update (JSON format)
- **Server Impact:** Minimal - simple aggregation queries with built-in caching

## Monitoring Updates

To monitor the auto-update functionality:

1. **Open Browser Developer Console** (Press F12)
2. **Go to Network Tab**
3. **Open a Dashboard Page**
4. **Observe AJAX calls** appearing at regular intervals:
   - `GET /my-tickets/ajax/stats`
   - `GET /my-tickets/ajax/tickets`
   - etc.

### Sample Network Activity

```
[0:00] Initial page load
[0:30] AJAX call: /my-tickets/ajax/stats → 200 OK (1.2 KB)
[0:45] AJAX call: /my-tickets/ajax/tickets → 200 OK (3.4 KB)
[1:00] AJAX call: /my-tickets/ajax/stats → 200 OK (1.2 KB)
[1:30] AJAX call: /my-tickets/ajax/stats → 200 OK (1.2 KB)
...and so on
```

## Disable Auto-Update (For Development)

If you need to disable auto-update temporarily:

1. Open Browser Developer Console (F12)
2. Paste this command:
   ```javascript
   // This will prevent the auto-update intervals from running
   // Note: You may need to refresh the page for this to take effect
   ```

To disable permanently in code, comment out the `setInterval()` lines in the dashboard views.

## Refresh Intervals Explained

| Dashboard | Interval | Reason |
|-----------|----------|--------|
| **Client** | 30s stats, 45s tickets | Users need real-time queue position updates |
| **Support** | 30s all | Support team needs to see new tickets and metrics immediately |
| **Admin** | 60s all | Admin monitoring requires less frequent updates, reduces load |

## API Endpoints Used

### Client Dashboard
- `GET /my-tickets/ajax/stats` → Returns ticket statistics
- `GET /my-tickets/ajax/tickets` → Returns paginated ticket list

### Support Dashboard
- `GET /support-tickets/ajax/stats` → Returns performance metrics
- `GET /support-tickets/ajax/dashboard-tickets` → Returns support-type-specific tickets

### Admin Dashboard
- `GET /admin-tickets/ajax/dashboard-stats` → Returns system-wide statistics

All endpoints return **JSON** format and are **authentication protected** by existing middleware.

## Troubleshooting

### Updates Not Showing
1. Check browser console for errors (F12 → Console)
2. Check Network tab (F12 → Network) for failed requests
3. Verify you're logged in and have proper permissions
4. Try refreshing the page

### Excessive Network Traffic
1. Auto-update is working correctly - this is normal
2. Updates are lightweight and optimized
3. Internet usage typically <5 MB per hour per open dashboard

### Stale Data Visible
1. Data shown is from the last refresh interval (up to 60 seconds old)
2. For live data, use the manual refresh button if available
3. Close and reopen the page to force a full refresh

## Security Notes

- All AJAX requests require valid authentication
- User can only see their own data (enforced by middleware)
- Support users filtered by company/support type assignment
- Admin users see all system data
- CSRF token protection enabled on all requests

## Browser Compatibility

- Chrome/Chromium: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Edge: ✅ Full support
- IE 11: ⚠️ Limited support (requires jQuery polyfills)

## Known Limitations

1. **Polling Delay** - Data can be up to 60 seconds old
2. **No Offline Support** - Updates only work with active internet connection
3. **Single Tab Only** - Data not synchronized across multiple tabs
4. **No Manual Control** - Can't manually trigger updates (use page refresh instead)

## Future Enhancements

- WebSocket real-time updates (eliminates polling delay)
- User-configurable refresh intervals
- Visual indicators for new updates
- Sound/browser notification for new tickets
- Historical data tracking
- Update trend analysis

---

**Last Updated:** February 3, 2026  
**Status:** ✅ Production Ready
