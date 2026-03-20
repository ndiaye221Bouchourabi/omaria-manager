@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">

        <!-- Header -->
        <div class="premium-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="premium-title">Points de Distribution</h1>
                <div class="premium-subtitle">
                    <i class="bi bi-geo-alt-fill me-1" style="color:var(--premium-accent);"></i>
                    Gérez l'emplacement et la disponibilité de vos machines
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="premium-badge">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    {{ $stats['total'] }} unités
                </div>
                {{-- Bouton ajout — admin + proprietaire + gestionnaire --}}
                @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
                    <button class="btn-primary-premium" data-bs-toggle="modal" data-bs-target="#addPointModal">
                        <i class="bi bi-plus-lg"></i> Nouveau Point
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="premium-alert success">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="premium-alert error">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Points</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <i class="bi bi-building stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">En Service</div>
                    <div class="stat-value" style="color:var(--premium-success);">{{ $stats['actifs'] }}</div>
                    <i class="bi bi-check-circle-fill stat-icon" style="color:var(--premium-success);"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">En Maintenance</div>
                    <div class="stat-value" style="color:var(--premium-warning);">{{ $stats['inactifs'] }}</div>
                    <i class="bi bi-tools stat-icon" style="color:var(--premium-warning);"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Taux d'activité</div>
                    <div class="stat-value">{{ $stats['taux_activite'] }}%</div>
                    <i class="bi bi-graph-up stat-icon"></i>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="main-card">
            <div class="card-header-premium">
                <div>
                    <h6 class="fw-bold mb-1" style="color:var(--text-primary);">Liste des unités opérationnelles</h6>
                    <small class="text-muted">{{ $stats['actifs'] }} points actifs sur {{ $stats['total'] }}</small>
                </div>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher..."
                        style="border-radius:30px;width:200px;" id="searchInput">
                </div>
            </div>

            <div class="table-responsive">
                <table class="premium-table" id="pointsTable">
                    <thead>
                        <tr>
                            <th>Machine</th>
                            <th>Localisation</th>
                            <th>Statut</th>
                            <th>Activité</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($points as $p)
                            <tr onclick="window.location='{{ route('points.show', $p->id) }}'" style="cursor:pointer;">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="machine-avatar"><i class="bi bi-cpu"></i></div>
                                        <div>
                                            <span class="fw-bold d-block">{{ $p->nom_machine }}</span>
                                            <small class="text-muted">ID: #{{ $p->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-2" style="color:var(--premium-danger);"></i>
                                        {{ $p->lieu }}
                                    </div>
                                </td>
                                <td>
                                    @if($p->status == 'Actif')
                                        <span class="status-badge active"><i class="bi bi-check-circle-fill"></i>
                                            Opérationnel</span>
                                    @else
                                        <span class="status-badge inactive"><i class="bi bi-pause-circle-fill"></i> En arrêt</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <i class="bi bi-arrow-up-short text-success"></i>
                                        {{ $p->collectes_count ?? 0 }} collectes
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2" onclick="event.stopPropagation();">

                                        {{-- Modifier — admin + proprietaire + gestionnaire --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
                                            <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editPoint{{ $p->id }}" title="Modifier">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </button>
                                        @endif

                                        {{-- Supprimer — admin + proprietaire uniquement --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire']))
                                            <form action="{{ route('points.destroy', $p->id) }}" method="POST" class="d-inline"
                                                onsubmit="event.stopPropagation(); return confirm('⚠️ Supprimer ce point ? Action irréversible.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Supprimer">
                                                    <i class="bi bi-trash3 fs-6"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-map"></i>
                                        <h6 class="fw-bold">Aucun point de distribution</h6>
                                        <p class="small text-muted">Commencez par ajouter votre premier point</p>
                                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
                                            <button class="btn-primary-premium mt-3" data-bs-toggle="modal"
                                                data-bs-target="#addPointModal">
                                                <i class="bi bi-plus-lg"></i> Ajouter un point
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT -->
    @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
        <div class="modal fade premium-modal" id="addPointModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('points.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header-premium">
                        <div>
                            <h5 class="modal-title fw-bold mb-2">Créer une nouvelle unité</h5>
                            <small style="opacity:0.8;">Ajoutez un point de distribution à votre réseau</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body-premium">
                        <div class="mb-4">
                            <label class="premium-input-label">Nom d'identification</label>
                            <input type="text" name="nom_machine" class="premium-input w-100"
                                placeholder="ex: MACHINE-CENTRE-01" value="{{ old('nom_machine') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="premium-input-label">Emplacement</label>
                            <input type="text" name="lieu" class="premium-input w-100"
                                placeholder="ex: Quartier Central, Avenue Principale" value="{{ old('lieu') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="premium-input-label">Statut initial</label>
                            <select name="status" class="premium-input w-100">
                                <option value="Actif" selected>🟢 Actif - Prêt à l'emploi</option>
                                <option value="Inactif">🔴 Inactif - En maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer-premium">
                        <button type="button" class="btn btn-light w-100 py-3 rounded-4"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn-primary-premium w-100 py-3 justify-content-center">
                            <i class="bi bi-check-lg"></i> Créer le point
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODALS ÉDITION -->
    @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
        @foreach($points as $p)
            <div class="modal fade premium-modal" id="editPoint{{ $p->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('points.update', $p->id) }}" method="POST" class="modal-content">
                        @csrf @method('PUT')
                        <div class="modal-header-premium" style="background:linear-gradient(145deg,#1a2639,#2d3748);">
                            <div>
                                <h5 class="modal-title fw-bold mb-2">Configuration de l'unité</h5>
                                <small style="opacity:0.8;">Modifier les paramètres de {{ $p->nom_machine }}</small>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body-premium">
                            <div class="mb-4">
                                <label class="premium-input-label">Nom d'identification</label>
                                <input type="text" name="nom_machine" class="premium-input w-100" value="{{ $p->nom_machine }}"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="premium-input-label">Emplacement</label>
                                <input type="text" name="lieu" class="premium-input w-100" value="{{ $p->lieu }}" required>
                            </div>
                            <div class="mb-4">
                                <label class="premium-input-label">Statut</label>
                                <select name="status" class="premium-input w-100">
                                    <option value="Actif" {{ $p->status == 'Actif' ? 'selected' : '' }}>🟢 Actif - En service</option>
                                    <option value="Inactif" {{ $p->status == 'Inactif' ? 'selected' : '' }}>🔴 Inactif - Maintenance
                                    </option>
                                </select>
                            </div>
                            @if($p->collectes_count > 0 || $p->depenses_count > 0)
                                <div class="alert alert-light small p-3 rounded-4">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Ce point a {{ $p->collectes_count }} collecte(s) et {{ $p->depenses_count }} dépense(s)
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer-premium">
                            <button type="button" class="btn btn-light w-100 py-3 rounded-4"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn-primary-premium w-100 py-3 justify-content-center">
                                <i class="bi bi-check-lg"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    <script>
        document.getElementById('searchInput')?.addEventListener('keyup', function () {
            let searchValue = this.value.toLowerCase();
            document.querySelectorAll('#pointsTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
            });
        });
        setTimeout(() => {
            document.querySelectorAll('.premium-alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s'; alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
@endsection