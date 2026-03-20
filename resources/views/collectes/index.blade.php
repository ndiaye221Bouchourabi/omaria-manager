@extends('layouts.app')

@section('content')
    @php use App\Helpers\FinanceHelper; @endphp

    <div class="container-fluid px-4 py-4">

        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Collectes</h1>
                <p class="page-subtitle">
                    <i class="bi bi-cash-stack me-1" style="color: var(--premium-accent);"></i>
                    Suivez et gérez les flux financiers de vos points de distribution
                </p>
            </div>
            <button class="btn-primary-premium" data-bs-toggle="modal" data-bs-target="#addCollecteModal">
                <i class="bi bi-plus-lg"></i> Nouvelle Collecte
            </button>
        </div>

        @if(session('success'))
            <div class="premium-alert success mb-3">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Collectes</div>
                    <div class="stat-value">{{ FinanceHelper::formatMoney($stats['total_global'] ?? 0) }}</div>
                    <i class="bi bi-piggy-bank stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Moyenne / Collecte</div>
                    <div class="stat-value">{{ FinanceHelper::formatMoney($stats['moyenne_globale'] ?? 0) }}</div>
                    <i class="bi bi-bar-chart stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Points Actifs</div>
                    <div class="stat-value">{{ $stats['points_actifs'] ?? 0 }}/{{ $stats['total_points'] ?? 0 }}</div>
                    <i class="bi bi-geo-alt stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Collectes affichées</div>
                    <div class="stat-value">{{ $nombreCollectes ?? 0 }}</div>
                    <i class="bi bi-list-check stat-icon"></i>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-card">
            <form action="{{ route('collectes.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <div class="filter-label">Point de distribution</div>
                    <select name="point_id" class="premium-select" onchange="this.form.submit()">
                        <option value="">Tous les points</option>
                        @foreach($points as $p)
                            <option value="{{ $p->id }}" {{ request('point_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nom_machine }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="filter-label">Année</div>
                    <select name="annee" class="premium-select" onchange="this.form.submit()">
                        <option value="">Toutes</option>
                        @foreach($anneesDisponibles as $annee)
                            <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="filter-label">Mois</div>
                    <select name="mois" class="premium-select" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="filter-label">Semaine</div>
                    <select name="semaine" class="premium-select" onchange="this.form.submit()">
                        <option value="">Toutes</option>
                        @foreach($semainesDisponibles as $sem)
                            <option value="{{ $sem }}" {{ request('semaine') == $sem ? 'selected' : '' }}>{{ $sem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    @if(request()->anyFilled(['point_id', 'semaine', 'mois', 'annee']))
                        <a href="{{ route('collectes.index') }}" class="btn-reset">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Total filtré -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="total-card">
                    <div class="total-label">
                        {{ request()->anyFilled(['point_id', 'semaine', 'mois', 'annee']) ? 'Total Filtré' : 'Total Global' }}
                    </div>
                    <div class="total-value">
                        {{ FinanceHelper::formatMoney($totalFiltre ?? 0) }}
                        <span class="total-currency">FCFA</span>
                    </div>
                    @if(($moyenneFiltre ?? 0) > 0)
                        <div class="mt-2 text-white-50">
                            <small>Moyenne : {{ FinanceHelper::formatMoney($moyenneFiltre) }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Point de Distribution</th>
                            <th class="text-center">Semaine</th>
                            <th>Date de Saisie</th>
                            <th>Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collectes as $c)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="machine-avatar me-3"><i class="bi bi-shop"></i></div>
                                        <div>
                                            <span class="fw-bold d-block">{{ $c->point->nom_machine }}</span>
                                            <small class="text-muted">{{ $c->point->lieu }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="week-badge">
                                        <i class="bi bi-calendar-week"></i> {{ $c->semaine }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-calendar3 me-2"></i>
                                        {{ Carbon\Carbon::parse($c->date_collecte)->format('d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="amount-badge">{{ FinanceHelper::formatMoney($c->montant) }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="action-buttons">

                                        {{-- Modifier — tous les rôles --}}
                                        <button type="button" class="btn-edit" data-bs-toggle="modal"
                                            data-bs-target="#editCollecte{{ $c->id }}" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Supprimer — admin + proprietaire uniquement --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire']))
                                            <form action="{{ route('collectes.destroy', $c->id) }}" method="POST"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette collecte ?')"
                                                style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-delete" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
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
                                        <i class="bi bi-inbox"></i>
                                        <h6>Aucune collecte trouvée</h6>
                                        <p class="small text-muted">Aucune donnée ne correspond à vos critères</p>
                                        <a href="{{ route('collectes.index') }}" class="btn btn-light mt-3 rounded-pill">
                                            <i class="bi bi-arrow-counterclockwise me-2"></i>Réinitialiser
                                        </a>
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
    <div class="modal fade premium-modal" id="addCollecteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('collectes.store') }}" method="POST" class="modal-content">
                @csrf
                {{-- Champ semaine caché — généré automatiquement par JS --}}
                <input type="hidden" name="semaine" id="add_semaine">

                <div class="modal-header-premium">
                    <div>
                        <h5 class="modal-title fw-bold mb-2">Nouvelle Collecte</h5>
                        <small style="opacity:0.8;">Enregistrer une collecte de fonds</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="premium-input-label">Point de distribution *</label>
                        <select name="point_id" class="premium-input" required>
                            <option value="">Sélectionner un point...</option>
                            @foreach($points as $p)
                                <option value="{{ $p->id }}">{{ $p->nom_machine }} - {{ $p->lieu }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="premium-input-label">Date de collecte *</label>
                        <input type="date" name="date_collecte" id="add_date_collecte" class="premium-input"
                            value="{{ date('Y-m-d') }}" required oninput="updateSemaine('add')">
                    </div>

                    {{-- Semaine calculée automatiquement — lecture seule --}}
                    <div class="mb-4">
                        <label class="premium-input-label">Semaine (calculée automatiquement)</label>
                        <div class="premium-input d-flex align-items-center gap-2"
                            style="background:#f8fafc; color:#64748b; cursor:default;" id="add_semaine_display">
                            <i class="bi bi-calendar-week" style="color:#3b82f6;"></i>
                            <span id="add_semaine_text">—</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="premium-input-label">Montant collecté (FCFA) *</label>
                        <div class="input-group-premium">
                            <span><i class="bi bi-cash"></i></span>
                            <input type="number" name="montant" placeholder="0" step="0.001" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-premium">
                    <button type="button" class="btn btn-light w-100 py-3 rounded-4"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-primary-premium w-100 py-3 justify-content-center">
                        <i class="bi bi-check-lg"></i> Enregistrer la collecte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODALS ÉDITION -->
    @foreach($collectes as $c)
        <div class="modal fade premium-modal" id="editCollecte{{ $c->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('collectes.update', $c->id) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')
                    {{-- Champ semaine caché — mis à jour par JS --}}
                    <input type="hidden" name="semaine" id="edit_semaine_{{ $c->id }}" value="{{ $c->semaine }}">

                    <div class="modal-header-premium" style="background:linear-gradient(145deg,#1a2639,#2d3748);">
                        <div>
                            <h5 class="modal-title fw-bold mb-2">Modifier la collecte</h5>
                            <small style="opacity:0.8;">Mettre à jour les informations</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body-premium">
                        <div class="mb-4">
                            <label class="premium-input-label">Point de distribution *</label>
                            <select name="point_id" class="premium-input" required>
                                @foreach($points as $p)
                                    <option value="{{ $p->id }}" {{ $c->point_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nom_machine }} - {{ $p->lieu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="premium-input-label">Date *</label>
                            <input type="date" name="date_collecte" id="edit_date_{{ $c->id }}" class="premium-input"
                                value="{{ $c->date_collecte }}" required oninput="updateSemaineEdit({{ $c->id }})">
                        </div>

                        {{-- Semaine calculée automatiquement — lecture seule --}}
                        <div class="mb-4">
                            <label class="premium-input-label">Semaine (calculée automatiquement)</label>
                            <div class="premium-input d-flex align-items-center gap-2"
                                style="background:#f8fafc; color:#64748b; cursor:default;">
                                <i class="bi bi-calendar-week" style="color:#3b82f6;"></i>
                                <span id="edit_semaine_text_{{ $c->id }}">{{ $c->semaine }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="premium-input-label">Montant (FCFA) *</label>
                            <div class="input-group-premium">
                                <span><i class="bi bi-cash"></i></span>
                                <input type="number" name="montant" value="{{ $c->montant }}" step="0.001" required>
                            </div>
                        </div>
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

    <script>
        // Calcule "S{semaine}-{année}" depuis une date
        function getSemaineLabel(dateStr) {
            if (!dateStr) return '—';
            const date = new Date(dateStr);
            // Calcul ISO week number
            const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            const dayNum = d.getUTCDay() || 7;
            d.setUTCDate(d.getUTCDate() + 4 - dayNum);
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return 'S' + weekNo + '-' + d.getUTCFullYear();
        }

        // Pour le formulaire AJOUT
        function updateSemaine(prefix) {
            const dateVal = document.getElementById('add_date_collecte').value;
            const label = getSemaineLabel(dateVal);
            document.getElementById('add_semaine').value = label;
            document.getElementById('add_semaine_text').textContent = label;
        }

        // Pour les formulaires ÉDITION
        function updateSemaineEdit(id) {
            const dateVal = document.getElementById('edit_date_' + id).value;
            const label = getSemaineLabel(dateVal);
            document.getElementById('edit_semaine_' + id).value = label;
            document.getElementById('edit_semaine_text_' + id).textContent = label;
        }

        // Initialiser la semaine au chargement pour le formulaire AJOUT
        document.addEventListener('DOMContentLoaded', function () {
            updateSemaine('add');

            // Auto-hide alertes
            setTimeout(() => {
                document.querySelectorAll('.premium-alert').forEach(el => {
                    el.style.transition = 'all 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                });
            }, 5000);
        });
    </script>
@endsection