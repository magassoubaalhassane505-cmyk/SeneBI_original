<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Analyses BI - SeneBI</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        .chart-box{position:relative;height:300px}
        .chart-box.tall{height:360px}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:18px}
        .top-table{width:100%;border-collapse:collapse}
        .top-table th{text-align:left;padding:10px 8px;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);font-weight:700;border-bottom:2px solid var(--border)}
        .top-table td{padding:12px 8px;font-size:13px;border-bottom:1px solid var(--border);vertical-align:middle}
        .top-table tbody tr:hover{background:rgba(248,250,252,.6)}
        .risk-chip{display:inline-flex;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;margin:2px;background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2)}
        .rec-item{display:flex;align-items:flex-start;gap:12px;padding:14px;border-radius:14px;background:#f8fafc;border:1px solid rgba(15,23,42,.06);transition:transform .2s ease,box-shadow .2s ease}
        .rec-item:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(15,23,42,.06)}
        .rec-icon{width:38px;height:38px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:15px}
        .btn-pill{border-radius:999px;padding:10px 20px;background:#0b1220;color:#fff;font-weight:700;font-size:13px;border:none;cursor:pointer;transition:all .2s ease;font-family:var(--font-main)}
        .btn-pill:hover{background:#111827;transform:translateY(-1px)}
    </style>
</head>
<body data-page="analyses-bi">
<div class="app">
    @include('header-manager')

    <main class="container">
        <div class="page-title">
            <div>
                <h1>Analyses BI</h1>
                <p>Centre de Business Intelligence agricole — Données en temps réel MySQL</p>
            </div>
            <div class="head-actions">
                <button class="btn-pill" id="exportPdfBtn" type="button">
                    <i class="fas fa-file-pdf" style="color:#ef4444;margin-right:6px"></i>Export PDF
                </button>
                <button class="btn-pill" id="exportExcelBtn" type="button">
                    <i class="fas fa-file-excel" style="color:#10b981;margin-right:6px"></i>Export Excel
                </button>
            </div>
        </div>

<section class="grid kpis">
             <article class="card">
                 <div class="card-header">
                     <p class="card-title">Production nationale</p>
                     <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M7 6c2 2 2 4 0 6"/><path d="M17 6c-2 2-2 4 0 6"/><path d="M7 12c2 2 2 4 0 6"/><path d="M17 12c-2 2-2 4 0 6"/></svg></div>
                 </div>
                 <div class="kpi-value counter" data-target="{{ $stats['production_totale'] }}">{{ number_format($stats['production_totale'], 0, ',', ' ') }}</div>
                 <div class="kpi-sub"><span>kg</span><span class="muted">Toutes cultures confondues</span></div>
             </article>
             <article class="card">
                 <div class="card-header">
                     <p class="card-title">Revenus agricoles</p>
                     <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M12 7v10"/><path d="M9.5 9.5c.6-1 4.4-1 5 0"/><path d="M9.5 14.5c.6 1 4.4-1 5 0"/></svg></div>
                 </div>
                 <div class="kpi-value counter" data-target="{{ $stats['revenus_totaux'] }}">{{ number_format($stats['revenus_totaux'], 0, ',', ' ') }}</div>
                 <div class="kpi-sub"><span>FCFA</span><span class="muted">Chiffre d'affaires total</span></div>
             </article>
             <article class="card">
                 <div class="card-header">
                     <p class="card-title">Rendement moyen</p>
                     <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 7-7"/><path d="M14 8h6v6"/></svg></div>
                 </div>
                 <div class="kpi-value counter" data-target="{{ $stats['rendement_moyen'] }}">{{ number_format($stats['rendement_moyen'], 2, ',', ' ') }}</div>
                 <div class="kpi-sub"><span>kg/ha</span><span class="muted">Moyenne nationale</span></div>
             </article>
             <article class="card">
                 <div class="card-header">
                     <p class="card-title">Agriculteurs actifs</p>
                     <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                 </div>
                 <div class="kpi-value counter" data-target="{{ $stats['agriculteurs_actifs'] }}">{{ $stats['agriculteurs_actifs'] }}</div>
                 <div class="kpi-sub"><span>approuvés</span><span class="muted">Clients actifs</span></div>
             </article>
             <article class="card">
                 <div class="card-header">
                     <p class="card-title">Parcelles actives</p>
                     <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-4.5 7-11a7 7 0 0 0-14 0c0 6.5 7 11 7 11z"/><path d="M12 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></div>
                 </div>
                 <div class="kpi-value counter" data-target="{{ $stats['parcelles_actives'] }}">{{ $stats['parcelles_actives'] }}</div>
                 <div class="kpi-sub"><span>enregistrées</span><span class="muted">Toutes régions</span></div>
             </article>
             <article class="card">
                 <div class="card-header">
                     <p class="card-title">Alertes critiques</p>
                     <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></div>
                 </div>
                 <div class="kpi-value {{ $stats['alertes_critiques'] > 0 ? 'alert' : 'success' }}" data-target="{{ $stats['alertes_critiques'] }}">{{ $stats['alertes_critiques'] }}</div>
                 <div class="kpi-sub"><span>stocks sous seuil</span><span class="muted">Nécessitent intervention</span></div>
             </article>
         </section>

        <section class="grid-kpis-2">
            <article class="card">
                <div class="card-header">
                    <div><h3>Évolution de la production agricole</h3><div class="small muted">Récoltes cumulées dans le temps</div></div>
                    <span class="tag muted">Tonnes</span>
                </div>
                <div class="chart-box tall"><canvas id="productionChart"></canvas></div>
            </article>
            <article class="card">
                <div class="card-header">
                    <div><h3>Revenus vs Coûts globaux</h3><div class="small muted">Comparaison sur toutes les exploitations</div></div>
                    <span class="tag good">FCFA</span>
                </div>
                <div class="chart-box tall"><canvas id="revenueCostChart"></canvas></div>
            </article>
        </section>

        <section class="grid-kpis-2">
            <article class="card">
                <div class="card-header">
                    <div><h3>Performance par culture</h3><div class="small muted">Maïs / Riz / Coton</div></div>
                </div>
                <div class="chart-box"><canvas id="performanceChart"></canvas></div>
            </article>
            <article class="card">
                <div class="card-header">
                    <div><h3>Répartition des agriculteurs par région</h3><div class="small muted">Distribution géographique</div></div>
                </div>
                <div class="chart-box"><canvas id="regionChart"></canvas></div>
            </article>
        </section>

        <section class="grid-kpis-2">
            <article class="card">
                <div class="card-header">
                    <div><h3>Tendances des rendements</h3><div class="small muted">Évolution du rendement moyen national</div></div>
                    <span class="tag muted">kg/ha</span>
                </div>
                <div class="chart-box"><canvas id="yieldChart"></canvas></div>
            </article>
            <article class="card">
                <div class="card-header">
                    <div><h3>Conseils intelligents SeneBI</h3><div class="small muted">Recommandations générées automatiquement</div></div>
                    <span class="tag good"><i class="fas fa-lightbulb"></i>BI</span>
                </div>
                <div class="rec-list">
                    @foreach($recommendations as $rec)
                        <div class="rec-item">
                            <div class="rec-icon {{ $rec['type'] ?? '' }}"><i class="fas fa-lightbulb"></i></div>
                            <div>
                                <div class="rec-title">{{ $rec['message'] }}</div>
                            </div>
                        </div>
                    @endforeach
                    @if(empty($recommendations))
                        <div class="rec-item">
                            <div class="rec-icon success"><i class="fas fa-check"></i></div>
                            <div>
                                <div class="rec-title">Tous les indicateurs sont bons.</div>
                            </div>
                        </div>
                    @endif
                </div>
            </article>
        </section>

        <section class="bi-grid-3">
            <article class="card">
                <div class="card-header">
                    <div><h3 style="margin:0;font-size:16px">Top 5 — Plus forte rentabilité</h3><div class="small muted">Classement par bénéfice net</div></div>
                    <span class="tag good">Profit</span>
                </div>
                <div style="overflow-x:auto"><table class="top-table">
                    <thead><tr><th>#</th><th>Agriculteur</th><th>Bénéfice net</th></tr></thead>
                    <tbody>
                    @forelse($topFarmers->sortByDesc('benefice')->take(5) as $idx => $farmer)
                        <tr>
                            <td style="font-weight:900;color:#059669;font-size:16px">{{ $idx + 1 }}</td>
                            <td style="font-weight:700;color:#0f172a">{{ $farmer['name'] }}</td>
                            <td style="font-weight:700;color:{{ $farmer['benefice'] >= 0 ? '#16a34a' : '#dc2626' }}">{{ number_format($farmer['benefice'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#64748b;padding:20px">Aucune donnée</td></tr>
                    @endforelse
                    </tbody>
                </table></div>
            </article>
            <article class="card">
                <div class="card-header">
                    <div><h3 style="margin:0;font-size:16px">Top 5 — Meilleur rendement</h3><div class="small muted">Classement par kg/ha moyen</div></div>
                    <span class="tag muted">Yield</span>
                </div>
                <div style="overflow-x:auto"><table class="top-table">
                    <thead><tr><th>#</th><th>Agriculteur</th><th>Rendement</th></tr></thead>
                    <tbody>
                    @forelse($topFarmers->sortByDesc('rendement')->take(5) as $idx => $farmer)
                        <tr>
                            <td style="font-weight:900;color:#059669;font-size:16px">{{ $idx + 1 }}</td>
                            <td style="font-weight:700;color:#0f172a">{{ $farmer['name'] }}</td>
                            <td style="font-weight:700;color:#0f172a">{{ number_format($farmer['rendement'], 2, ',', ' ') }} kg/ha</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#64748b;padding:20px">Aucune donnée</td></tr>
                    @endforelse
                    </tbody>
                </table></div>
            </article>
            <article class="card">
                <div class="card-header">
                    <div><h3 style="margin:0;font-size:16px">Top 5 — Plus grande surface</h3><div class="small muted">Classement par hectares cultivés</div></div>
                    <span class="tag muted">Surface</span>
                </div>
                <div style="overflow-x:auto"><table class="top-table">
                    <thead><tr><th>#</th><th>Agriculteur</th><th>Surface</th></tr></thead>
                    <tbody>
                    @forelse($topFarmers->sortByDesc('surface')->take(5) as $idx => $farmer)
                        <tr>
                            <td style="font-weight:900;color:#059669;font-size:16px">{{ $idx + 1 }}</td>
                            <td style="font-weight:700;color:#0f172a">{{ $farmer['name'] }}</td>
                            <td style="font-weight:700;color:#0f172a">{{ number_format($farmer['surface'], 1, ',', ' ') }} ha</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#64748b;padding:20px">Aucune donnée</td></tr>
                    @endforelse
                    </tbody>
                </table></div>
            </article>
        </section>

        <section style="margin-top:18px">
            <article class="card">
                <div class="card-header">
                    <div><h3 style="margin:0;font-size:16px">Agriculteurs à risque</h3><div class="small muted">Détection automatique : stocks, rentabilité, activité et rendements</div></div>
                    <a class="btn" href="{{ route('manager.visites') }}" style="background:#059669;color:#fff;text-decoration:none;font-size:13px;padding:8px 16px;border-radius:8px"><i class="fas fa-calendar-plus" style="margin-right:6px"></i>Planifier une visite</a>
                </div>
                <div style="overflow-x:auto"><table class="farmers-table">
                    <thead><tr><th class="th-name">Agriculteur</th><th class="th-location">Localisation</th><th>Risques détectés</th><th style="text-align:center">Action</th></tr></thead>
                    <tbody>
                    @forelse($atRiskFarmers as $farmer)
                        <tr>
                            <td style="padding:12px;font-weight:700;color:#0f172a">{{ $farmer['name'] }}</td>
                            <td style="padding:12px;color:#64748b">{{ $farmer['location'] }}</td>
                            <td style="padding:12px">@foreach($farmer['risks'] as $risk)<span class="risk-chip"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $risk }}</span>@endforeach</td>
                            <td style="padding:12px;text-align:center"><a class="btn" href="{{ route('manager.visites') }}" style="background:#059669;color:#fff;text-decoration:none;font-size:12px;padding:6px 14px;border-radius:6px"><i class="fas fa-calendar-plus" style="margin-right:4px"></i>Visite</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:32px;color:#16a34a"><i class="fas fa-check-circle" style="font-size:24px;margin-bottom:8px;display:block"></i>Aucun agriculteur à risque détecté.</td></tr>
                    @endforelse
                    </tbody>
                </table></div>
            </article>
        </section>

        <div class="footer-note">Source : Données MySQL — Dernière mise à jour : {{ now()->format('d/m/Y H:i') }}</div>
    </main>

    @include('partials.footer-manager')
</div>

<script src="{{ asset('assets/js/layout.js') }}"></script>
<script src="{{ asset('assets/js/core.js') }}"></script>
<script>
(function(){
    const font="'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif";
    const C={primary:'#059669',grid:'rgba(226,232,240,.6)',muted:'#64748b',accent:'#0f172a'};
    function fmt(v,u){v=Number(v||0);if(u==='FCFA')return v.toLocaleString('fr-FR')+' FCFA';if(u==='t')return v.toLocaleString('fr-FR')+' t';if(u==='kg')return v.toLocaleString('fr-FR')+' kg';if(u==='ha')return v.toLocaleString('fr-FR')+' ha';return v.toLocaleString('fr-FR')}
    function destroy(n){if(window[n]){window[n].destroy();window[n]=null}}

    document.addEventListener('DOMContentLoaded',function(){
        initProductionChart();initRevenueCostChart();initPerformanceChart();initRegionChart();initYieldChart();initPdfExport();initExcelExport();initActiveNav();
    });

    function initActiveNav(){document.querySelectorAll('.manager-nav a').forEach(function(l){try{if(location.pathname===new URL(l.href,location.origin).pathname)l.classList.add('active')}catch(e){}})}

    function initProductionChart(){
        const c=document.getElementById('productionChart');if(!c)return;const ctx=c.getContext('2d');destroy('_p');
        const labels = @json($productionByMonth->pluck('mois'));
        const data = @json($productionByMonth->pluck('total')->map(fn($v)=>$v/1000));
        window._p=new Chart(ctx,{type:'line',data:{labels:labels,datasets:[{label:'Production (tonnes)',data:data,borderColor:C.primary,backgroundColor:'rgba(16,185,129,.12)',fill:true,tension:.35,pointRadius:4,pointBackgroundColor:'#fff',pointBorderColor:C.primary,pointBorderWidth:2,borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font},bodyFont:{family:font},callbacks:{label:function(c){return fmt(c.parsed.y,'t')}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}}}});
    }

    function initRevenueCostChart(){
        const c=document.getElementById('revenueCostChart');if(!c)return;const ctx=c.getContext('2d');destroy('_rc');
        window._rc=new Chart(ctx,{type:'bar',data:{labels:['Revenus','Coûts'],datasets:[{data:[{!! json_encode(($stats['revenus_totaux'] ?? 0)/1000000) !!}, {!! json_encode(($stats['revenus_totaux'] - $stats['production_totale']) ?? 0/1000000) !!}],backgroundColor:['#10b981','#ef4444'],borderColor:['#059669','#dc2626'],borderWidth:1,borderRadius:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font},bodyFont:{family:font},callbacks:{label:function(c){return c.parsed.y.toLocaleString('fr-FR',{minimumFractionDigits:2,maximumFractionDigits:2})+' M FCFA'}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v+' M'}}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}}}});
    }

    function initPerformanceChart(){
        const c=document.getElementById('performanceChart');if(!c)return;const ctx=c.getContext('2d');destroy('_perf');
        const labels = @json($performanceByCulture->pluck('culture'));
        const data = @json($performanceByCulture->pluck('total_qte')->map(fn($v)=>$v/1000));
        const pal=['#10b981','#0ea5e9','#f97316','#8b5cf6','#ef4444'];
        window._perf=new Chart(ctx,{type:'bar',data:{labels:labels,datasets:[{label:'Production (tonnes)',data:data,backgroundColor:pal.slice(0,labels.length),borderColor:pal.slice(0,labels.length),borderWidth:1,borderRadius:6}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font},bodyFont:{family:font},callbacks:{label:function(c){return fmt(c.parsed.x,'t')}}}},scales:{x:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v+' t'}}},y:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}}}});
    }

    function initRegionChart(){
        const c=document.getElementById('regionChart');if(!c)return;const ctx=c.getContext('2d');destroy('_reg');
        const labels = @json($farmersByRegion->pluck('location'));
        const data = @json($farmersByRegion->pluck('total'));
        const pal=['#059669','#10b981','#0ea5e9','#f97316','#8b5cf6','#ef4444','#f59e0b'];
        window._reg=new Chart(ctx,{type:'doughnut',data:{labels:labels,datasets:[{data:data,backgroundColor:pal.slice(0,labels.length),borderColor:'#fff',borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,cutout:'55%',plugins:{legend:{position:'bottom',labels:{font:{family:font},usePointStyle:true,padding:16}},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font},bodyFont:{family:font},callbacks:{label:function(c){return c.label+' : '+c.parsed+' agriculteurs'}}}}}});
    }

    function initYieldChart(){
        const c=document.getElementById('yieldChart');if(!c)return;const ctx=c.getContext('2d');destroy('_y');
        const labels = @json($rendementByMonth->pluck('mois'));
        const data = @json($rendementByMonth->pluck('avg_rendement'));
        window._y=new Chart(ctx,{type:'line',data:{labels:labels,datasets:[{label:'Rendement (kg/ha)',data:data,borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.12)',fill:true,tension:.35,pointRadius:4,pointBackgroundColor:'#fff',pointBorderColor:'#f59e0b',pointBorderWidth:2,borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font},bodyFont:{family:font},callbacks:{label:function(c){return fmt(c.parsed.y,'kg')}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v+' kg/ha'}}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}}}});
    }

    function initPdfExport(){
        document.getElementById('exportPdfBtn').addEventListener('click',function(){
            const{jsPDF}=window.jspdf||{};if(!jsPDF){alert('PDF indisponible');return}
            const doc=new jsPDF({unit:'pt',format:'a4'});const W=doc.internal.pageSize.getWidth(),H=doc.internal.pageSize.getHeight(),m=48,acc=[5,150,105],slate=[15,23,42];
            let y=m;doc.setFillColor(...acc);doc.rect(0,0,W,6,'F');
            doc.setFont('helvetica','bold');doc.setFontSize(22);doc.setTextColor(...slate);doc.text('Analyses BI — SeneBI',m,y+28);
            doc.setFont('helvetica','normal');doc.setFontSize(10);doc.setTextColor(100,116,139);doc.text('Généré le '+new Date().toLocaleDateString('fr-FR',{dateStyle:'long',timeStyle:'short'}),m,y+48);
            y+=72;
            const kpis=[
                {l:'Production',v:fmt(@json($stats['production_totale']),'t')},
                {l:'Revenus',v:fmt(@json($stats['revenus_totaux']),'FCFA')},
                {l:'Rendement',v:fmt(@json($stats['rendement_moyen']),'kg/ha')},
                {l:'Agriculteurs',v:@json($stats['agriculteurs_actifs'])+' actifs'},
                {l:'Parcelles',v:@json($stats['parcelles_actives'])+' actives'},
                {l:'Alertes',v:@json($stats['alertes_critiques'])+' critiques'}
            ];
            const gap=10,bw=(W-2*m-5*gap)/6;let x=m;
            kpis.forEach(function(k){
                doc.setFillColor(248,250,252);doc.setDrawColor(226,232,240);doc.roundedRect(x,y,bw,64,4,4,'FD');
                doc.setFillColor(...acc);doc.rect(x,y,5,64,'F');
                doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(100,116,139);doc.text(k.l,x+14,y+20);
                doc.setFont('helvetica','bold');doc.setFontSize(11);doc.setTextColor(...slate);
                const lines=doc.splitTextToSize(k.v,bw-22);doc.text(lines,x+14,y+40);
                x+=bw+gap;
            });
            y+=84;
            [
                ['productionChart','Évolution de la production agricole'],
                ['revenueCostChart','Revenus vs Coûts globaux'],
                ['performanceChart','Performance par culture'],
                ['regionChart','Répartition des agriculteurs par région'],
                ['yieldChart','Tendances des rendements']
            ].forEach(function(pair){
                const el=document.getElementById(pair[0]);if(!el)return;
                if(y+220>H-m-26){doc.addPage();y=m;doc.setFillColor(...acc);doc.rect(0,0,W,6,'F')}
                doc.setFont('helvetica','bold');doc.setFontSize(12);doc.setTextColor(...slate);doc.text(pair[1],m,y);y+=18;
                try{const img=el.toDataURL('image/png',1.0);doc.setFillColor(248,250,252);doc.setDrawColor(226,232,240);doc.roundedRect(m,y,W-2*m,180,4,4,'FD');doc.addImage(img,'PNG',m+10,y+10,W-2*m-20,160)}catch(e){doc.setFont('helvetica','italic');doc.setFontSize(9);doc.setTextColor(100,116,139);doc.text('Graphique non disponible.',m+10,y+24)}
                y+=190;
            });
            const total=doc.internal.getNumberOfPages();
            for(let i=1;i<=total;i++){doc.setPage(i);doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(148,163,184);doc.text('SeneBI · Page '+i+' / '+total,W/2,H-24,{align:'center'});doc.text('Confidentiel — usage interne',m,H-24)}
            doc.save('SeneBI_Analyses_BI.pdf');
        });
    }

    function initExcelExport(){
        document.getElementById('exportExcelBtn').addEventListener('click',function(){
            if(typeof XLSX==='undefined'){alert('Excel indisponible');return}
            const wb=XLSX.utils.book_new();
            const kpis=[
                ['Indicateur','Valeur'],
                ['Production nationale (kg)',@json($stats['production_totale'])],
                ['Revenus agricoles (FCFA)',@json($stats['revenus_totaux'])],
                ['Rendement moyen (kg/ha)',@json($stats['rendement_moyen'])],
                ['Agriculteurs actifs',@json($stats['agriculteurs_actifs'])],
                ['Parcelles actives',@json($stats['parcelles_actives'])],
                ['Alertes critiques',@json($stats['alertes_critiques'])]
            ];
            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(kpis),'KPIs');
            const prod=[['Mois','Production (kg)']];
            @foreach($productionByMonth as $row)prod.push(['{{ $row->mois }}',{{ $row->total }}]);@endforeach
            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(prod),'Production');
            const perf=[['Culture','Production (kg)']];
            @foreach($performanceByCulture as $r)perf.push(['{{ $r->culture }}',{{ $r->total_qte }}]);@endforeach
            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(perf),'Performance');
            const region=[['Région','Agriculteurs']];
            @foreach($farmersByRegion as $r)region.push(['{{ $r->location }}',{{ $r->total }}]);@endforeach
            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(region),'Region');
            XLSX.writeFile(wb,'SeneBI_Analyses_BI.xlsx');
        });
    }
})();
</script>
</body>
</html>
