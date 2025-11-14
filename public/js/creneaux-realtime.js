(function(){
  function renderCreneaux(container, data){
    if (!container) return;
    const modalBody = container.closest('.modal-body') || document;
    const heureDebutInput = modalBody.querySelector('#heure_debut') || document.getElementById('heure_debut');
    const heureFinInput = modalBody.querySelector('#heure_fin') || document.getElementById('heure_fin');

    const norm = (t) => (t || '').toString().trim().substring(0,5);
    const currentStart = norm(container.dataset.currentStart || (heureDebutInput && heureDebutInput.value));
    const currentEnd = norm(container.dataset.currentEnd || (heureFinInput && heureFinInput.value));

    container.innerHTML = '';

    if (data && data.success && Array.isArray(data.creneaux) && data.creneaux.length){
      data.creneaux.forEach(creneau => {
        const isCurrent = currentStart && currentEnd && currentStart === creneau.heure_ouverture && currentEnd === creneau.heure_fermeture;
        const div = document.createElement('div');
        div.className = 'creneau-item';
        if (creneau.disponible == 0 && !isCurrent){
          div.classList.add('disabled');
        }
        if (isCurrent){
          div.classList.add('selected');
        }
        div.dataset.start = creneau.heure_ouverture;
        div.dataset.end = creneau.heure_fermeture;
        div.innerHTML = `
          <div class="d-flex justify-content-between align-items-center">
            <span style="font-size: 1.05rem;"><strong>${creneau.heure_ouverture} - ${creneau.heure_fermeture}</strong></span>
            <span class="badge ${(creneau.disponible == 1 || isCurrent) ? 'bg-success' : 'bg-danger'}">${(creneau.disponible == 1) ? 'Disponible' : (isCurrent ? 'Votre créneau' : 'Réservé')}</span>
          </div>
        `;
        container.appendChild(div);
      });

      // Re-attach click delegation
      container.onclick = (e) => {
        const item = e.target.closest('.creneau-item');
        if (!item || item.classList.contains('disabled')) return;
        const data = { heure_ouverture: item.dataset.start, heure_fermeture: item.dataset.end };
        if (typeof window.selectCreneau === 'function'){
          window.selectCreneau(item, data);
        } else {
          // Fallback selection
          container.querySelectorAll('.creneau-item').forEach(el => el.classList.remove('selected'));
          item.classList.add('selected');
          if (heureDebutInput) heureDebutInput.value = data.heure_ouverture;
          if (heureFinInput) heureFinInput.value = data.heure_fermeture;
        }
        // Memorize current selection on container
        container.dataset.currentStart = data.heure_ouverture;
        container.dataset.currentEnd = data.heure_fermeture;
      };

      // If previously selected slot became unavailable, clear selection to block submit
      const selected = container.querySelector('.creneau-item.selected');
      if (selected && selected.classList.contains('disabled')){
        selected.classList.remove('selected');
        if (heureDebutInput) heureDebutInput.value = '';
        if (heureFinInput) heureFinInput.value = '';
      }
    } else {
      container.innerHTML = '<div class="alert alert-warning">Aucun créneau disponible pour cette date.</div>';
    }
  }

  function getContext(root){
    const scope = root || document;
    const list = scope.querySelector('#creneauxList');
    const terrainIdEl = scope.querySelector('#modal_terrain_id') || scope.querySelector('#terrain_id') || document.getElementById('modal_terrain_id') || document.getElementById('terrain_id');
    const dateEl = scope.querySelector('#date_reservation') || document.getElementById('date_reservation');
    const terrainId = terrainIdEl ? parseInt(terrainIdEl.value, 10) : 0;
    const date = dateEl ? dateEl.value : '';
    return { list, terrainId, date };
  }

  function startPolling(baseUrl, root, intervalMs){
    let timer = null;
    let detachFieldListeners = null;
    let detachDelegatedListeners = null;

    const tick = async () => {
      try{
        const ctx = getContext(root);
        if (!ctx.list) return;
        // Optional loader: show only when container is empty
        if (!ctx.list.hasChildNodes()){
          ctx.list.innerHTML = '<div class="d-flex align-items-center gap-2"><div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div><span>Chargement des créneaux...</span></div>';
        }
        if (!ctx.terrainId || !ctx.date) return;
        const url = baseUrl + 'terrain/creneaux?id=' + encodeURIComponent(ctx.terrainId) + '&date=' + encodeURIComponent(ctx.date);
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) return;
        const data = await res.json().catch(()=>null);
        renderCreneaux(ctx.list, data);
      } catch(e){
        // silent
      }
    };

    // Attach field listeners to trigger immediate refresh on user changes
    const scope = root || document;
    const dateEl = scope.querySelector('#date_reservation') || document.getElementById('date_reservation');
    const terrainEl = scope.querySelector('#modal_terrain_id') || scope.querySelector('#terrain_id') || document.getElementById('modal_terrain_id') || document.getElementById('terrain_id');
    const onFieldChange = () => { tick(); };
    if (dateEl) {
      dateEl.addEventListener('change', onFieldChange);
      dateEl.addEventListener('input', onFieldChange);
    }
    if (terrainEl) {
      terrainEl.addEventListener('change', onFieldChange);
      terrainEl.addEventListener('input', onFieldChange);
    }
    // Delegated listeners to handle dynamically injected forms
    const rootEl = scope instanceof Element ? scope : document;
    const delegatedHandler = (e) => {
      const t = e.target;
      if (!t) return;
      const id = (t.id || '').toLowerCase();
      if (id === 'date_reservation' || id === 'terrain_id' || id === 'modal_terrain_id') {
        tick();
      }
    };
    rootEl.addEventListener('change', delegatedHandler, true);
    rootEl.addEventListener('input', delegatedHandler, true);
    detachDelegatedListeners = () => {
      rootEl.removeEventListener('change', delegatedHandler, true);
      rootEl.removeEventListener('input', delegatedHandler, true);
    };
    detachFieldListeners = () => {
      if (dateEl) {
        dateEl.removeEventListener('change', onFieldChange);
        dateEl.removeEventListener('input', onFieldChange);
      }
      if (terrainEl) {
        terrainEl.removeEventListener('change', onFieldChange);
        terrainEl.removeEventListener('input', onFieldChange);
      }
    };

    tick();
    timer = setInterval(tick, intervalMs || 3000);

    const visHandler = () => {
      if (document.hidden){
        if (timer){ clearInterval(timer); timer = null; }
      } else if (!timer){
        tick();
        timer = setInterval(tick, intervalMs || 3000);
      }
    };
    document.addEventListener('visibilitychange', visHandler);

    return {
      stop(){ if (timer){ clearInterval(timer); timer = null; } if (detachFieldListeners) { detachFieldListeners(); detachFieldListeners = null; } if (detachDelegatedListeners){ detachDelegatedListeners(); detachDelegatedListeners = null; } document.removeEventListener('visibilitychange', visHandler); },
      tick
    };
  }

  if (typeof window !== 'undefined'){
    window.BookPlayCreneauxRealtime = {
      attachToModal: function(baseUrl, modalEl, intervalMs){
        let instance = null;
        const start = () => { instance = startPolling(baseUrl, modalEl, intervalMs); };
        const stop = () => { if (instance){ instance.stop(); instance = null; } };
        modalEl.addEventListener('shown.bs.modal', start);
        modalEl.addEventListener('hidden.bs.modal', stop);
        if (modalEl.classList.contains('show')) start();
        return { stop };
      },
      start: function(baseUrl, rootSelector, intervalMs){
        const root = typeof rootSelector === 'string' ? document.querySelector(rootSelector) : (rootSelector || document);
        return startPolling(baseUrl, root, intervalMs);
      }
    };
  }
})();
