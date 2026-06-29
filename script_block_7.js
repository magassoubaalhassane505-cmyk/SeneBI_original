
(function(){
    const font="'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif";
    const C={primary:'#059669',grid:'rgba(226,232,240,.6)',muted:'#64748b',accent:'#0f172a'};
    const MONTHS=['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    function normalizeKey(s){s=String(s||'').toLowerCase();try{s=s.normalize('NFD').replace(/[\u0300-\u036f]/g,'')}catch(e){}return s}
    function fmt(v,u){v=Number(v||0);if(u==='FCFA')return v.toLocaleString('fr-FR')+' FCFA';if(u==='t')return v.toLocaleString('fr-FR')+' t';if(u==='kg')return v.toLocaleString('fr-FR')+' kg';if(u==='ha')return v.toLocaleString('fr-FR')+' ha';return v.toLocaleString('fr-FR')}
    function destroy(n){if(window[n]){window[n].destroy();window[n]=null}}
    function safeChart(id,builder){try{const c=document.getElementById(id);if(!c){console.warn('Chart #'+id+' not found in DOM');return}const ctx=c.getContext('2d');destroy('_'+id);builder(c,ctx);console.log('Chart rendered:',id)}catch(e){console.error('Chart error ['+id+']:',e);const box=c?.closest('.chart-box')||c?.parentElement;if(box&&!box.querySelector('.chart-error')){box.insertAdjacentHTML('beforeend','<div class="chart-error" style="color:#dc2626;font-size:12px;padding:10px">Erreur de chargement du graphique</div>')}}}

    document.addEventListener('DOMContentLoaded',function(){
        console.log('DOM loaded');
        if(typeof Chart==='undefined'){console.error('Chart.js not loaded');return}
        console.log('BI: Chart.js loaded');
        console.log('productionChart element:', document.getElementById('productionChart'));
        console.log('cumulativeChart element:', document.getElementById('cumulativeChart'));
        console.log('revenueCostChart element:', document.getElementById('revenueCostChart'));
        console.log('performanceChart element:', document.getElementById('performanceChart'));
        console.log('regionChart element:', document.getElementById('regionChart'));
        console.log('yieldChart element:', document.getElementById('yieldChart'));
        safeChart('productionChart',initProductionChart);
        safeChart('cumulativeChart',initCumulativeChart);
        safeChart('revenueCostChart',initRevenueCostChart);
        safeChart('performanceChart',initPerformanceChart);
        safeChart('regionChart',initRegionChart);
        safeChart('yieldChart',initYieldChart);
        initPdfExport();initExcelExport();
    });

    function initProductionChart(){
        try{
        const c=document.getElementById('productionChart');if(!c)return;const ctx=c.getContext('2d');destroy('_p');
        const rawMonths=[1,2,3,4,5,6,7,8,9,10,11,12];
        const labels=rawMonths.map(function(m){return MONTHS[m-1] || 'Mois '+m});
        const rawData=[467020,423723,425466,380968,386153,1157216.5,415567,347141,313598,323933,284269,398688];
        const data=rawData.map(function(v){return v/1000});
        console.log('[DEBUG productionChart] labels:',labels,'data:',data,'canvas:',c,'size:',c.width,'x',c.height);
        if(!data.length){c.closest('.chart-box').innerHTML='<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#64748b;font-size:14px">Aucune donnée disponible pour le moment</div>';return}
        const variation=data.length>=2?(((data[data.length-1]-data[0])/Math.max(data[0]||1,1))*100).toFixed(1):0;
        const trend=variation>5?'hausse':variation<-5?'baisse':'stable';
        const trendColor=trend==='hausse'?'#16a34a':trend==='baisse'?'#dc2626':'#64748b';
        const trendIcon=trend==='hausse'?'fa-arrow-up':trend==='baisse'?'fa-arrow-down':'fa-minus';
        window._p=new Chart(ctx,{type:'line',data:{labels:labels,datasets:[{label:'Production (tonnes)',data:data,borderColor:C.primary,backgroundColor:'rgba(16,185,129,.12)',fill:true,tension:.4,pointRadius:4,pointBackgroundColor:'#fff',pointBorderColor:C.primary,pointBorderWidth:2,borderWidth:2.5}]},options:{responsive:true,maintainAspectRatio:false,animation:{duration:1600,easing:'easeInOutQuart'},plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font,weight:'600'},bodyFont:{family:font},padding:14,cornerRadius:12,displayColors:false,callbacks:{title:function(items){return items[0]?.label||''},label:function(c){return 'Production : '+fmt(c.parsed.y,'t')},afterBody:function(items){const idx=items[0]?.dataIndex;if(idx===undefined)return[];const mQte=(data[idx]||0)*1000;const cumul=(data.slice(0,idx+1).reduce((a,b)=>a+b,0)||0)*1000;return['Cumul à ce mois : '+fmt(cumul,'kg'),'Quantité ce mois : '+fmt(mQte,'kg')]}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v.toFixed(0)+' t'}}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}}}});
        c.closest('.card').insertAdjacentHTML('beforeend','<div style="display:flex;align-items:center;gap:6px;margin-top:10px;padding:8px 14px;background:rgba(5,150,105,.06);border-radius:10px;font-size:12px;font-weight:700;color:var(--muted)"><i class="fas '+trendIcon+'" style="color:'+trendColor+';font-size:11px"></i> Variation : <span style="color:'+trendColor+';margin-left:3px">'+variation+'%</span> <span style="font-weight:600;color:#0f172a;margin-left:4px">('+trend.charAt(0).toUpperCase()+trend.slice(1)+')</span></div>');
        }catch(e){console.error('initProductionChart error:',e);const c=document.getElementById('productionChart');if(c){c.closest('.chart-box').insertAdjacentHTML('beforeend','<div style="color:#dc2626;font-size:11px;margin-top:6px">ERREUR JS: '+e.message+'</div>')}}
    }

    function initRevenueCostChart(){
        try{
        const c=document.getElementById('revenueCostChart');if(!c)return;const ctx=c.getContext('2d');destroy('_rc');
        const revenu=parseFloat(3563.035252);
        const cout=parseFloat(893.797985);
        const benefice=parseFloat(2669.237267);
        const margeGlobale=parseFloat(2669.237267);
        const vals=[revenu,cout,benefice,margeGlobale];
        const hasData=vals.some(function(v){return v!==0&&!isNaN(v)});
        if(!hasData){c.closest('.chart-box').innerHTML='<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#64748b;font-size:14px">Aucune donnée disponible pour le moment</div>';return}
        window._rc=new Chart(ctx,{type:'bar',data:{labels:['Revenus totaux','Coûts totaux','Bénéfice net','Marge globale'],datasets:[{data:vals,backgroundColor:['rgba(16,185,129,.88)','rgba(239,68,68,.82)','rgba(5,150,105,.92)','rgba(14,165,233,.85)'],borderColor:['#10b981','#ef4444','#059669','#0ea5e9'],borderWidth:1.5,borderRadius:8,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,animation:{duration:1600,easing:'easeInOutQuart'},plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font,weight:'600'},bodyFont:{family:font},padding:14,cornerRadius:12,displayColors:true,boxPadding:4,callbacks:{label:function(ci){return' '+ci.parsed.y.toLocaleString('fr-FR',{minimumFractionDigits:2,maximumFractionDigits:2})+' M FCFA'}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v+' M FCFA'}}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'700'},color:C.accent}}}}}});
        }catch(e){console.error('initRevenueCostChart error:',e)}
    }

    function initPerformanceChart(){
        const c=document.getElementById('performanceChart');if(!c)return;const ctx=c.getContext('2d');destroy('_perf');
        const labels=["Ma\u00efs","Riz","Coton"];
        const rawQte=[2034898.6,1882774.9,1406069];
        const surfacesJson={"riz":2509,"mais":187.4,"coton":145.3};
        const rendements=rawQte.map(function(q,i){const s=surfacesJson[normalizeKey(labels[i])]||0;return s>0?parseFloat((q/s).toFixed(2)):0});
        const pal=['#10b981','#0ea5e9','#f97316','#8b5cf6','#ef4444','#f59e0b','#3b82f6','#ec4899'];
        const bgColors=rendements.map(function(r){const maxR=Math.max.apply(null,rendements)||1;const ratio=r/maxR;return ratio>=0.8?'#10b981':ratio>=0.5?'#0ea5e9':'#f59e0b'});
        const data={labels:labels,datasets:[{label:'Rendement (kg/ha)',data:rendements,backgroundColor:bgColors,borderColor:bgColors,borderWidth:1.5,borderRadius:6}]};
        const options={indexAxis:'y',responsive:true,maintainAspectRatio:false,animation:{duration:1600,easing:'easeInOutQuart'},plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font,weight:'600'},bodyFont:{family:font},padding:14,cornerRadius:12,displayColors:false,callbacks:{label:function(ci){return' Rendement : '+ci.parsed.x.toLocaleString('fr-FR')+' kg/ha'}}}},scales:{x:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v.toLocaleString('fr-FR')+' kg/ha'}}},y:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'700'},color:C.accent}}}};
        window._perf=new Chart(ctx,{type:'bar',data:data,options:options});
                    const bestHtml='<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px"><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#dcfce7;color:#14532d;border:1px solid rgba(22,163,74,.25)"><i class="fas fa-trophy" style="font-size:10px;color:#059669"></i> Meilleur : '+"Ma\u00efs"+' (10858.58 kg/ha)</span><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#fee2e2;color:#991b1b;border:1px solid rgba(239,68,68,.25)"><i class="fas fa-arrow-down" style="font-size:10px"></i> Moins performant : '+"Riz"+' (750.41 kg/ha)</span></div>';
            c.closest('.card').insertAdjacentHTML('beforeend',bestHtml);
            }

    function initRegionChart(){
        const c=document.getElementById('regionChart');if(!c)return;const ctx=c.getContext('2d');destroy('_reg');
        const labels=["Kayes","Bamako","KOULIKORO","KATI","Sikasso","S\u00e9gou","Mopti"];
        const data=[3,3,2,1,1,1,1];
        const total=12;
        const dominant="Kayes";
        const pal=['#059669','#10b981','#0ea5e9','#f97316','#8b5cf6','#ef4444','#f59e0b','#3b82f6','#ec4899','#14b8a6'];
        window._reg=new Chart(ctx,{type:'doughnut',data:{labels:labels,datasets:[{data:data,backgroundColor:pal.slice(0,labels.length),borderColor:'#ffffff',borderWidth:3,hoverBorderWidth:5,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,cutout:'62%',animation:{duration:1600,easing:'easeInOutQuart'},plugins:{legend:{position:'bottom',labels:{font:{family:font,weight:'600'},usePointStyle:true,pointStyle:'circle',padding:14,color:C.muted}},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font,weight:'600'},bodyFont:{family:font},padding:14,cornerRadius:12,boxPadding:4,callbacks:{label:function(ci){const pct=total>0?((ci.parsed/total)*100).toFixed(1):0;return' '+ci.label+' : '+ci.parsed+' agriculteurs ('+pct+'%)'}}}}}});
        const pctDominant=total>0?((data[0]||0)/total*100).toFixed(1):0;
        const regionHtml='<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px"><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#f0fdf4;color:#14532d;border:1px solid rgba(22,163,74,.25)"><i class="fas fa-map-marker-alt" style="font-size:10px;color:#059669"></i> 7 r\u00e9gions</span><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;background:#eff6ff;color:#1e40af;border:1px solid rgba(59,130,246,.25)"><i class="fas fa-crown" style="font-size:10px;color:#f59e0b"></i> Dominante : '+"Kayes"+' ('+pctDominant+'%)</span></div>';
        c.closest('.card').insertAdjacentHTML('beforeend',regionHtml);
    }

    function initYieldChart(){
        const c=document.getElementById('yieldChart');if(!c)return;const ctx=c.getContext('2d');destroy('_y');
        const rawMonths=[1,2,3,4,5,6,7,8,9,10,11,12];
        const labels=rawMonths.map(function(m){return MONTHS[m-1] || 'Mois '+m});
        const data=[1095.23,82.87,136.01,1213.38,897.43,153.92,894.2,129.23,116.2,65.39,1038.73,1296.19];
        const avg=parseFloat(593.2316666666667);
        const trend="hausse";
        const trendLabel=trend==='hausse'?'Hausse de tendance':trend==='baisse'?'Baisse de tendance':'Stable';
        const trendColor=trend==='hausse'?'#16a34a':trend==='baisse'?'#dc2626':'#64748b';
        if(!data.length){c.closest('.chart-box').innerHTML='<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#64748b;font-size:14px">Aucune donnée disponible pour le moment</div>';return}
        const datasets=[
            {label:'Rendement (kg/ha)',data:data,borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.12)',fill:true,tension:.4,pointRadius:5,pointBackgroundColor:'#fff',pointBorderColor:'#f59e0b',pointBorderWidth:2.5,borderWidth:2.5},
            {label:'Moyenne nationale',data:Array(data.length).fill(avg),borderColor:'#0f172a',backgroundColor:'rgba(15,23,42,.03)',borderDash:[8,4],tension:0,pointRadius:0,pointHoverRadius:4,borderWidth:1.5}
        ];
        const options={responsive:true,maintainAspectRatio:false,animation:{duration:1600,easing:'easeInOutQuart'},plugins:{legend:{display:true,position:'top',labels:{font:{family:font,weight:'600'},usePointStyle:true,pointStyle:'circle',padding:18,color:C.muted}},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font,weight:'600'},bodyFont:{family:font},padding:14,cornerRadius:12,callbacks:{label:function(ci){return' '+ci.dataset.label+' : '+fmt(ci.parsed.y,'kg/ha')}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v.toLocaleString('fr-FR')+' kg/ha'}}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}};
        window._y=new Chart(ctx,{type:'line',data:{labels:labels,datasets:datasets},options:options});
        const bestMonthIdx=12;
        const worstMonthIdx=10;
        const bestMonthLabel=MONTHS[(bestMonthIdx||1)-1]||'N/A';
        const worstMonthLabel=MONTHS[(worstMonthIdx||1)-1]||'N/A';
        const yieldHtml='<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px"><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;color:'+trendColor+';background:'+(trend==='hausse'?'#dcfce7':trend==='baisse'?'#fee2e2':'#f1f5f9')+';border:1px solid '+(trend==='hausse'?'rgba(22,163,74,.25)':trend==='baisse'?'rgba(239,68,68,.25)':'rgba(15,23,42,.08)')+'"><i class="fas fa-chart-line" style="font-size:10px"></i> '+trendLabel+'</span><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;color:#1e40af;background:#eff6ff;border:1px solid rgba(59,130,246,.25)"><i class="fas fa-bullseye" style="font-size:10px;color:#0ea5e9"></i> Meilleur : '+bestMonthLabel+'</span><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;color:#92400e;background:#fffbeb;border:1px solid rgba(245,158,11,.25)"><i class="fas fa-arrow-down" style="font-size:10px;color:#f59e0b"></i> Plus faible : '+worstMonthLabel+'</span><span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;color:#334155;background:#f1f5f9;border:1px solid rgba(15,23,42,.08)"><i class="fas fa-balance-scale" style="font-size:10px"></i> Moyenne : '+fmt(avg,'kg/ha')+'</span></div>';
        c.closest('.card').insertAdjacentHTML('beforeend',yieldHtml);
    }

    function initCumulativeChart(){
        const c=document.getElementById('cumulativeChart');if(!c)return;const ctx=c.getContext('2d');destroy('_cum');
        const rawMonths=[1,2,3,4,5,6,7,8,9,10,11,12];
        const labels=rawMonths.map(function(m){return MONTHS[m-1] || 'Mois '+m});
        const rawQte=[467020,423723,425466,380968,386153,1157216.5,415567,347141,313598,323933,284269,398688];
        const rawData=rawQte.map(function(v){return v/1000});
        if(!rawData.length){c.closest('.chart-box').innerHTML='<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#64748b;font-size:14px">Aucune donnée disponible pour le moment</div>';return}
        let cumulative=[];let sum=0;rawData.forEach(function(v){sum+=v;cumulative.push(sum)});
        const totalActuel=cumulative.length>0?cumulative[cumulative.length-1]:0;
        const gradient=ctx.createLinearGradient(0,0,0,c.height||300);
        gradient.addColorStop(0,'rgba(16,185,129,.35)');gradient.addColorStop(1,'rgba(16,185,129,.02)');
        const data={labels:labels,datasets:[{label:'Cumul (tonnes)',data:cumulative,borderColor:C.primary,backgroundColor:gradient,fill:true,tension:.4,pointRadius:5,pointBackgroundColor:'#fff',pointBorderColor:C.primary,pointBorderWidth:2.5,borderWidth:2.5}]};
        const options={responsive:true,maintainAspectRatio:false,animation:{duration:1800,easing:'easeInOutQuart'},plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(15,23,42,.92)',titleFont:{family:font,weight:'600'},bodyFont:{family:font},padding:14,cornerRadius:12,displayColors:false,callbacks:{title:function(items){return 'Total cumulé au '+items[0]?.label},label:function(c){return 'Cumul : '+fmt(c.parsed.y,'t')},afterLabel:function(c){const idx=c.dataIndex;const mQte=rawData[idx]||0;return 'Ce mois-ci : '+fmt(mQte,'t')}}}},scales:{y:{beginAtZero:true,grid:{color:C.grid,drawBorder:false},ticks:{font:{family:font},color:C.muted,callback:function(v){return v.toFixed(0)+' t'}}},x:{grid:{display:false,drawBorder:false},ticks:{font:{family:font,weight:'600'},color:C.accent}}}};
        window._cum=new Chart(ctx,{type:'line',data:data,options:options});
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
                {l:'Production',v:fmt("5323742.50",'t')},
                {l:'Revenus',v:fmt("3563035252.00",'FCFA')},
                {l:'Rendement',v:fmt(1873.43579547454,'kg/ha')},
                {l:'Agriculteurs',v:16+' actifs'},
                {l:'Parcelles',v:64+' actives'},
                {l:'Alertes',v:6+' critiques'}
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
                ['Production nationale (kg)',"5323742.50"],
                ['Revenus agricoles (FCFA)',"3563035252.00"],
                ['Rendement moyen (kg/ha)',1873.43579547454],
                ['Agriculteurs actifs',16],
                ['Parcelles actives',64],
                ['Alertes critiques',6]
            ];
            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(kpis),'KPIs');
            const prod=[['Mois','Production (kg)']];
            prod.push(['1',467020]);prod.push(['2',423723]);prod.push(['3',425466]);prod.push(['4',380968]);prod.push(['5',386153]);prod.push(['6',1157216.5]);prod.push(['7',415567]);prod.push(['8',347141]);prod.push(['9',313598]);prod.push(['10',323933]);prod.push(['11',284269]);prod.push(['12',398688]);            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(prod),'Production');
            const perf=[['Culture','Production (kg)']];
            perf.push(['Maïs',2034898.6]);perf.push(['Riz',1882774.9]);perf.push(['Coton',1406069]);            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(perf),'Performance');
            const region=[['Région','Agriculteurs']];
            region.push(['Kayes',3]);region.push(['Bamako',3]);region.push(['KOULIKORO',2]);region.push(['KATI',1]);region.push(['Sikasso',1]);region.push(['Ségou',1]);region.push(['Mopti',1]);            XLSX.utils.book_append_sheet(wb,XLSX.utils.aoa_to_sheet(region),'Region');
            XLSX.writeFile(wb,'SeneBI_Analyses_BI.xlsx');
        });
    }

    window._biDebug = {
        chartJsLoaded: typeof Chart !== 'undefined',
        pdfLoaded: typeof window.jspdf !== 'undefined',
        excelLoaded: typeof XLSX !== 'undefined',
        errors: [],
        rendered: [],
        expected: ['productionChart','cumulativeChart','revenueCostChart','performanceChart','regionChart','yieldChart']
    };

    const origSafeChart = window.safeChart || function(){};
    window.safeChart = function(id, builder) {
        try {
            const c = document.getElementById(id);
            if (!c) { window._biDebug.errors.push(id + ': element not found'); return; }
            const ctx = c.getContext('2d');
            destroy('_' + id);
            builder(c, ctx);
            window._biDebug.rendered.push(id);
            console.log('[BI DEBUG] Chart rendered:', id);
        } catch (e) {
            window._biDebug.errors.push(id + ': ' + e.message);
            console.error('[BI DEBUG] Chart error [' + id + ']:', e);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('[BI DEBUG] Chart.js NOT loaded');
            const dbg = document.getElementById('bi-debug');
            if (dbg) {
                dbg.style.borderColor = '#dc2626';
                dbg.innerHTML += '<span style="color:#dc2626;margin-left:12px"><i class="fas fa-exclamation-triangle"></i> Chart.js CDN non chargé — vérifiez votre connexion internet.</span>';
            }
        } else {
            console.log('[BI DEBUG] Chart.js loaded version', Chart.version);
        }

        setTimeout(function() {
            const missing = window._biDebug.expected.filter(function(id) {
                return window._biDebug.rendered.indexOf(id) === -1;
            });
            if (missing.length > 0) {
                const dbg = document.getElementById('bi-debug');
                if (dbg) {
                    dbg.style.borderColor = '#f59e0b';
                    dbg.innerHTML += '<span style="color:#92400e;margin-left:12px"><i class="fas fa-exclamation-circle"></i> Graphiques non rendus : ' + missing.join(', ') + ' — ouvrez la console (F12) pour détails.</span>';
                }
                console.warn('[BI DEBUG] Missing charts:', missing);
                console.warn('[BI DEBUG] Errors:', window._biDebug.errors);
            }
        }, 2000);
    });
})();
