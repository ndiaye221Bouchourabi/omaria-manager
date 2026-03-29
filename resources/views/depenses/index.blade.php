@extends('layouts.app')

@section('content')
    @php use App\Helpers\FinanceHelper; @endphp

    <div class="container-fluid px-4 py-4">

        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des Dépenses</h1>
                <p class="page-subtitle">
                    <i class="bi bi-wallet2 me-1" style="color:var(--premium-accent);"></i>
                    Suivez et gérez toutes les dépenses (points et globales)
                </p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-premium-point" data-bs-toggle="modal" data-bs-target="#addDepensePointModal">
                        <i class="bi bi-plus-lg"></i> Dépense Point
                    </button>
                    <button class="btn-premium-global" data-bs-toggle="modal" data-bs-target="#addDepenseGlobaleModal">
                        <i class="bi bi-plus-lg"></i> Dépense Globale
                    </button>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="premium-alert success mb-3">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="row g-3 g-md-4 mb-4 mb-md-5">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Dépenses</div>
                    <div class="stat-value">{{ FinanceHelper::formatMoney(($totalSpecifique ?? 0) + ($totalGlobal ?? 0)) }}
                    </div>
                    <i class="bi bi-cash stat-icon"></i>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Dépenses Points</div>
                    <div class="stat-value" style="color:var(--premium-purple);">
                        {{ FinanceHelper::formatMoney($totalSpecifique ?? 0) }}</div>
                    <i class="bi bi-geo-alt stat-icon" style="color:var(--premium-purple);"></i>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Dépenses Globales</div>
                    <div class="stat-value">{{ FinanceHelper::formatMoney($totalGlobal ?? 0) }}</div>
                    <i class="bi bi-globe stat-icon"></i>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Nombre Total</div>
                    <div class="stat-value">{{ $stats['nombre_depenses'] ?? 0 }}</div>
                    <i class="bi bi-list-check stat-icon"></i>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-card">
            <form action="{{ route('depenses.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="filter-label">Point</div>
                    <select name="point_id" class="premium-select w-100" onchange="this.form.submit()">
                        <option value="">Tous les points</option>
                        @foreach($points as $p)
                            <option value="{{ $p->id }}" {{ request('point_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nom_machine }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-lg-2">
                    <div class="filter-label">Type</div>
                    <select name="type" class="premium-select w-100" onchange="this.form.submit()">
                        <option value="">Tous les types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-lg-2">
                    <div class="filter-label">Portée</div>
                    <select name="portee" class="premium-select w-100" onchange="this.form.submit()">
                        <option value="toutes" {{ request('portee', 'toutes') == 'toutes' ? 'selected' : '' }}>Toutes</option>
                        <option value="point" {{ request('portee') == 'point' ? 'selected' : '' }}>Points uniquement</option>
                        <option value="globale" {{ request('portee') == 'globale' ? 'selected' : '' }}>Globales uniquement
                        </option>
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-lg-2">
                    <div class="filter-label">Année</div>
                    <select name="annee" class="premium-select w-100" onchange="this.form.submit()">
                        <option value="">Toutes</option>
                        @foreach($anneesDisponibles as $annee)
                            <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    @if($isFiltered)
                        <a href="{{ route('depenses.index') }}" class="btn-reset w-100 justify-content-center">
                            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Totaux -->
        @if($isFiltered)
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-12">
                    <div class="total-card-point">
                        <div class="total-label"><i class="bi bi-funnel-fill me-2"></i>Total Filtré</div>
                        <div class="total-value">{{ FinanceHelper::formatMoney($totalFiltré ?? 0) }}</div>
                        <div class="mt-2 text-white-50">
                            <small>{{ $stats['nombre_depenses'] ?? 0 }} dépense(s) • Moy.
                                {{ FinanceHelper::formatMoney($stats['moyenne'] ?? 0) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-12 col-md-6">
                    <div class="total-card-point">
                        <div class="total-label"><i class="bi bi-geo-alt-fill me-2"></i>Total Dépenses Points</div>
                        <div class="total-value">{{ FinanceHelper::formatMoney($totalSpecifique ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="total-card-global">
                        <div class="total-label"><i class="bi bi-globe2 me-2"></i>Total Dépenses Globales</div>
                        <div class="total-value">{{ FinanceHelper::formatMoney($totalGlobal ?? 0) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tableau -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Portée / Point</th>
                            <th>Type</th>
                            <th class="d-none d-md-table-cell">Description</th>
                            <th class="d-none d-sm-table-cell">Date</th>
                            <th>Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses as $d)
                            <tr>
                                <td>
                                    @if($d->portee == 'point')
                                        <div class="badge-point">
                                            <i class="bi bi-geo-alt-fill"></i>
                                            <span class="d-none d-sm-inline">{{ $d->point->nom_machine ?? 'Point inconnu' }}</span>
                                            <span class="d-inline d-sm-none">Point</span>
                                        </div>
                                    @else
                                        <div class="badge-global">
                                            <i class="bi bi-globe2"></i>
                                            <span class="d-none d-sm-inline">Globale</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $d->type_depense }}</span>
                                    {{-- Date visible sous le type sur mobile --}}
                                    <small class="d-block d-sm-none text-muted mt-1">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ Carbon\Carbon::parse($d->date_depense)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="text-muted small">{{ Str::limit($d->description, 30) ?: '—' }}</span>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-calendar3 me-2"></i>
                                        {{ Carbon\Carbon::parse($d->date_depense)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="montant-3dec">{{ FinanceHelper::formatMoney($d->montant) }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
                                            <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $d->id }}" title="Modifier">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @endif
                                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire']))
                                            <form action="{{ route('depenses.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('⚠️ Supprimer cette dépense ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Supprimer">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h6 class="fw-bold">Aucune dépense trouvée</h6>
                                        <p class="small text-muted">Commencez par ajouter une dépense</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ══════════════════════ MODAL DÉPENSE POINT ══════════════════════ -->
    <div class="modal fade premium-modal" id="addDepensePointModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('depenses.store') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="portee" value="point">
                <div class="modal-header-premium-point">
                    <div>
                        <h5 class="modal-title fw-bold mb-2">Nouvelle Dépense Point</h5>
                        <small style="opacity:0.8;">Ajouter une dépense pour un point spécifique</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="premium-input-label">Point concerné *</label>
                        <select name="point_id" class="premium-input" required>
                            <option value="">Sélectionner un point…</option>
                            @foreach($points as $p)
                                <option value="{{ $p->id }}">{{ $p->nom_machine }} — {{ $p->lieu }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="premium-input-label">Type de dépense *</label>
                        <select name="type_depense" class="premium-input type-select" required>
                            <option value="">Sélectionner…</option>
                            <option value="Facture d'eau">Facture d'eau</option>
                            <option value="Facture électricité">Facture électricité</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Fournitures">Fournitures</option>
                            <option value="Autre">Autre (saisir…)</option>
                        </select>
                        <input type="text" class="premium-input mt-2 d-none custom-type-input"
                            placeholder="Précisez le type">
                    </div>
                    <div class="mb-4">
                        <label class="premium-input-label">Description (optionnelle)</label>
                        <textarea name="description" class="premium-input" rows="3" placeholder="Détails…"></textarea>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="premium-input-label">Date *</label>
                            <input type="date" name="date_depense" class="premium-input" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="premium-input-label">Montant (FCFA) *</label>
                            <div class="input-group-premium">
                                <span><i class="bi bi-cash"></i></span>
                                <input type="text" name="montant" class="montant-input" inputmode="decimal"
                                    placeholder="ex: 1200,2367" required pattern="[0-9]+([,\.][0-9]{1,4})?">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium">
                    <button type="button" class="btn btn-light flex-fill py-3 rounded-4"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-premium-point flex-fill py-3 justify-content-center">
                        <i class="bi bi-check-lg"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════ MODAL DÉPENSE GLOBALE ══════════════════════ -->
    <div class="modal fade premium-modal" id="addDepenseGlobaleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('depenses.store') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="portee" value="globale">
                <div class="modal-header-premium-global">
                    <div>
                        <h5 class="modal-title fw-bold mb-2">Nouvelle Dépense Globale</h5>
                        <small style="opacity:0.8;">Ajouter une dépense pour toute la flotte</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-premium">
                    <div class="mb-4">
                        <label class="premium-input-label">Type de dépense *</label>
                        <select name="type_depense" class="premium-input type-select" required>
                            <option value="">Sélectionner…</option>
                            <option value="Loyer">Loyer</option>
                            <option value="Salaire">Salaire</option>
                            <option value="Internet">Internet / Abonnement</option>
                            <option value="Électricité">Électricité générale</option>
                            <option value="Eau">Eau générale</option>
                            <option value="Autre">Autre (saisir…)</option>
                        </select>
                        <input type="text" class="premium-input mt-2 d-none custom-type-input"
                            placeholder="Précisez le type">
                    </div>
                    <div class="mb-4">
                        <label class="premium-input-label">Description (optionnelle)</label>
                        <textarea name="description" class="premium-input" rows="3" placeholder="Détails…"></textarea>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label class="premium-input-label">Date *</label>
                            <input type="date" name="date_depense" class="premium-input" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="premium-input-label">Montant (FCFA) *</label>
                            <div class="input-group-premium">
                                <span><i class="bi bi-cash"></i></span>
                                <input type="text" name="montant" class="montant-input" inputmode="decimal"
                                    placeholder="ex: 2500,750" required pattern="[0-9]+([,\.][0-9]{1,4})?">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-premium">
                    <button type="button" class="btn btn-light flex-fill py-3 rounded-4"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-premium-global flex-fill py-3 justify-content-center">
                        <i class="bi bi-check-lg"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════ MODALS ÉDITION ══════════════════════ -->
    @foreach($depenses as $d)
        <div class="modal fade premium-modal" id="editModal{{ $d->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form action="{{ route('depenses.update', $d->id) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header-premium-{{ $d->portee == 'point' ? 'point' : 'global' }}">
                        <div>
                            <h5 class="modal-title fw-bold mb-2">Modifier la dépense</h5>
                            <small style="opacity:0.8;">
                                @if($d->portee == 'point')
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $d->point->nom_machine ?? 'Point' }}
                                @else
                                    <i class="bi bi-globe2 me-1"></i>Dépense Globale
                                @endif
                            </small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body-premium">
                        @if($d->portee == 'point')
                            <div class="mb-4">
                                <label class="premium-input-label">Point</label>
                                <div class="premium-input" style="background:#f1f5f9; cursor:default;">
                                    {{ $d->point->nom_machine ?? 'N/A' }} — {{ $d->point->lieu ?? '' }}
                                </div>
                            </div>
                        @endif
                        <div class="mb-4">
                            <label class="premium-input-label">Type de dépense *</label>
                            <input type="text" name="type_depense" class="premium-input" value="{{ $d->type_depense }}"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="premium-input-label">Description</label>
                            <textarea name="description" class="premium-input" rows="3">{{ $d->description }}</textarea>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label class="premium-input-label">Date *</label>
                                <input type="date" name="date_depense" class="premium-input"
                                    value="{{ Carbon\Carbon::parse($d->date_depense)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="premium-input-label">Montant (FCFA) *</label>
                                <div class="input-group-premium">
                                    <span><i class="bi bi-cash"></i></span>
                                    <input type="text" name="montant" class="montant-input" inputmode="decimal"
                                        value="{{ number_format($d->montant, 3, ',', '') }}" pattern="[0-9]+([,\.][0-9]{1,4})?"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer-premium">
                        <button type="button" class="btn btn-light flex-fill py-3 rounded-4"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit"
                            class="btn-premium-{{ $d->portee == 'point' ? 'point' : 'global' }} flex-fill py-3 justify-content-center">
                            <i class="bi bi-check-lg"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* ── Sélecteur "Autre" ── */
            document.querySelectorAll('.type-select').forEach(select => {
                select.addEventListener('change', function () {
                    const custom = this.nextElementSibling;
                    if (this.value === 'Autre') {
                        custom.classList.remove('d-none');
                        custom.setAttribute('name', 'type_depense');
                        this.removeAttribute('name');
                        custom.focus();
                    } else {
                        custom.classList.add('d-none');
                        custom.removeAttribute('name');
                        this.setAttribute('name', 'type_depense');
                    }
                });
            });

            /* ── Saisie montant ── */
            document.querySelectorAll('.montant-input').forEach(input => {
                input.addEventListener('focus', function () { this.select(); });
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9,\.]/g, '');
                    const parts = this.value.split(/[,\.]/);
                    if (parts.length > 2) {
                        const sep = this.value.match(/[,\.]/)[0];
                        this.value = parts[0] + sep + parts.slice(1).join('');
                    }
                });
                input.addEventListener('blur', function () {
                    if (this.value) {
                        const val = parseFloat(this.value.replace(',', '.'));
                        if (!isNaN(val) && val >= 0) this.value = val.toFixed(3).replace('.', ',');
                    }
                });
                input.closest('form')?.addEventListener('submit', function () {
                    this.querySelectorAll('.montant-input').forEach(inp => {
                        if (inp.value) inp.value = inp.value.replace(',', '.');
                    });
                });
            });

            /* ── Auto-hide alertes ── */
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