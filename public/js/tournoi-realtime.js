class TournoiRealtimeMonitor {
    constructor(config) {
        this.baseUrl = config.baseUrl || '';
        this.listEndpoint = config.listEndpoint || 'home/tournois?format=json';
        this.containerSelector = config.containerSelector || '#tournoisTableBody';
        this.renderFunction = config.renderFunction || null;
        this.pollingInterval = config.pollingInterval || 1000;
        this.onError = config.onError || null;
        this.timer = null;
        this.container = null;
        this.knownIds = new Set();
    }

    init() {
        this.container = document.querySelector(this.containerSelector);
        if (!this.container) {
            console.warn('[TournoiRealtimeMonitor] Container not found:', this.containerSelector);
            return;
        }

        this.container.querySelectorAll('tr[data-tournoi-id]').forEach(row => {
            const id = Number(row.getAttribute('data-tournoi-id'));
            if (id) this.knownIds.add(id);
        });

        this.fetchLatest();
        this.startPolling();
    }

    startPolling() {
        if (this.timer) clearInterval(this.timer);
        this.timer = setInterval(() => this.fetchLatest(), this.pollingInterval);
    }

    stopPolling() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    buildUrl() {
        const separator = this.listEndpoint.includes('?') ? '&' : '?';
        return `${this.baseUrl}${this.listEndpoint}${separator}_=${Date.now()}`;
    }

    async fetchLatest() {
        if (!this.container) return;
        try {
            const response = await fetch(this.buildUrl(), {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const data = await response.json();
            if (data && data.success && Array.isArray(data.tournois)) {
                this.renderList(data.tournois);
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des tournois:', error);
            if (typeof this.onError === 'function') {
                this.onError(error);
            }
        }
    }

    renderList(tournois) {
        if (!tournois.length) {
            this.knownIds.clear();
            this.container.innerHTML = `
                <tr class="no-data-row">
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-trophy me-2"></i>Aucun tournoi disponible pour le moment
                    </td>
                </tr>`;
            return;
        }

        const fragment = document.createDocumentFragment();
        const currentIds = new Set();
        const newIds = [];

        tournois.forEach(tournoi => {
            const row = this.renderFunction
                ? this.renderFunction(tournoi)
                : this.defaultRender(tournoi);
            fragment.appendChild(row);

            const id = Number(tournoi.id_tournoi);
            currentIds.add(id);
            if (!this.knownIds.has(id)) {
                newIds.push(id);
                row.classList.add('row-highlight');
                setTimeout(() => row.classList.remove('row-highlight'), 3000);
            }
        });

        this.container.innerHTML = '';
        this.container.appendChild(fragment);
        this.knownIds = currentIds;
    }

    defaultRender(tournoi) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-tournoi-id', tournoi.id_tournoi);
        tr.innerHTML = `
            <td>${tournoi.nom_tournoi || ''}</td>
            <td>${tournoi.date_debut || ''}</td>
            <td>${tournoi.nom_terrain || '—'}</td>
            <td>${tournoi.equipes_inscrites || 0} / ${tournoi.nb_equipes || 0}</td>
            <td>${tournoi.statut_inscription || ''}</td>
            <td></td>
        `;
        return tr;
    }
}

if (typeof window !== 'undefined') {
    window.TournoiRealtimeMonitor = TournoiRealtimeMonitor;
}

