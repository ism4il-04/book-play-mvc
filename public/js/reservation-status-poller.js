(function(){
  function mapStatusToView(statusRaw){
    const s = (statusRaw || '').toLowerCase().trim();
    if (s === 'accepté' || s === 'acceptée' || s === 'accepted' || s === 'accepte') {
      return { label: 'Acceptée', cls: 'status-accepted' };
    }
    if (s === 'refusé' || s === 'refusée' || s === 'refused' || s === 'refuse') {
      return { label: 'Refusée', cls: 'status-refused' };
    }
    if (s === 'annulé' || s === 'annule' || s === 'annulée') {
      return { label: 'Annulée', cls: 'status-refused' };
    }
    return { label: 'En attente', cls: 'status-pending' };
  }

  async function fetchStatuses(baseUrl, ids){
    if (!ids.length) return {};
    const url = baseUrl + 'reservation/statuses?ids=' + encodeURIComponent(ids.join(','));
    const res = await fetch(url, { credentials: 'same-origin' });
    if (!res.ok) return {};
    const data = await res.json().catch(() => ({}));
    if (data && data.success && data.statuses) return data.statuses;
    return {};
  }

  function collectReservations(){
    const cards = document.querySelectorAll('.reservation-card[data-reservation-id]');
    const list = [];
    cards.forEach(card => {
      const id = parseInt(card.getAttribute('data-reservation-id'), 10);
      if (id > 0) {
        const badge = card.querySelector('.js-status-badge');
        if (badge) list.push({ id, card, badge });
      }
    });
    return list;
  }

  function updateBadge(badge, target){
    // Only change when necessary
    const currentText = (badge.textContent || '').trim();
    const classes = ['status-pending','status-accepted','status-refused'];
    if (currentText !== target.label || !badge.classList.contains(target.cls)){
      classes.forEach(c => badge.classList.remove(c));
      badge.classList.add(target.cls);
      badge.textContent = target.label;
    }
  }

  function startPolling(baseUrl, intervalMs){
    let timer = null;
    const tick = async () => {
      try {
        const items = collectReservations();
        const ids = items.map(it => it.id);
        if (!ids.length) return;
        const statuses = await fetchStatuses(baseUrl, ids);
        if (!statuses) return;
        items.forEach(it => {
          const raw = statuses[String(it.id)];
          if (typeof raw === 'undefined') return;
          const view = mapStatusToView(raw);
          updateBadge(it.badge, view);
        });
      } catch (e) {
        // Silent fail to avoid console noise
      }
    };

    // First run quickly, then interval
    tick();
    timer = setInterval(tick, intervalMs || 1000);

    // Pause when tab hidden, resume when visible
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        if (timer) { clearInterval(timer); timer = null; }
      } else {
        if (!timer) {
          tick();
          timer = setInterval(tick, intervalMs || 1000);
        }
      }
    });
  }

  if (typeof window !== 'undefined'){
    window.BookPlayReservationStatusPoller = { start: startPolling };
  }
})();
