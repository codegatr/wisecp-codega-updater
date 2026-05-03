<style>
.cu-wrap{font-family:Inter,-apple-system,Segoe UI,sans-serif;color:#dde1eb;}
.cu-head{background:linear-gradient(135deg,#1e293b,#0f172a);border:1px solid #334155;border-radius:14px;padding:20px 24px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
.cu-head h2{margin:0;font-size:18px;color:#fff;display:flex;align-items:center;gap:10px;}
.cu-head .v{display:inline-block;background:#10b981;color:#0f172a;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:800;}
.cu-meta{font-size:12px;color:#94a3b8;}
.cu-meta b{color:#fff;}

.cu-tabs{display:flex;gap:0;border-bottom:1px solid #334155;margin-bottom:18px;overflow-x:auto;background:#0f172a;border-radius:12px 12px 0 0;padding:0 4px;}
.cu-tab{padding:12px 18px;font-size:13px;font-weight:600;border:none;background:none;color:#94a3b8;border-bottom:2px solid transparent;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:7px;font-family:inherit;}
.cu-tab:hover{color:#dde1eb;background:rgba(79,142,247,0.06);}
.cu-tab.on{color:#4f8ef7;border-bottom-color:#4f8ef7;background:rgba(79,142,247,0.08);}

.cu-body{display:none;}
.cu-body.on{display:block;}

.cu-card{background:#1e293b;border:1px solid #334155;border-radius:14px;padding:20px 22px;margin-bottom:14px;}
.cu-card h3{margin:0 0 14px;font-size:14px;color:#fff;display:flex;align-items:center;gap:9px;}

.cu-stat{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-bottom:12px;}
.cu-stat-box{background:#0f172a;border:1px solid #334155;border-radius:10px;padding:14px;text-align:center;}
.cu-stat-box .lbl{font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.6px;font-weight:600;margin-bottom:6px;}
.cu-stat-box .val{font-size:24px;font-weight:800;color:#fff;}
.cu-stat-box.ok .val{color:#10b981;}
.cu-stat-box.diff .val{color:#f59e0b;}
.cu-stat-box.miss .val{color:#ef4444;}

.cu-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
.cu-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:8px;border:0;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;text-decoration:none;}
.cu-btn-primary{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;}
.cu-btn-warn{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;}
.cu-btn-danger{background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;}
.cu-btn-success{background:linear-gradient(135deg,#10b981,#059669);color:#fff;}
.cu-btn-secondary{background:#334155;color:#fff;}
.cu-btn:hover{opacity:0.92;}
.cu-btn:disabled{opacity:0.5;cursor:not-allowed;}

.cu-alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:12px;line-height:1.6;}
.cu-alert-info{background:rgba(59,130,246,0.1);border:1px solid #3b82f6;color:#60a5fa;}
.cu-alert-success{background:rgba(16,185,129,0.1);border:1px solid #10b981;color:#34d399;}
.cu-alert-danger{background:rgba(239,68,68,0.1);border:1px solid #dc2626;color:#f87171;}
.cu-alert-warn{background:rgba(245,158,11,0.1);border:1px solid #f59e0b;color:#fbbf24;}

.cu-log{background:#0f172a;border:1px solid #334155;border-radius:8px;padding:14px;font-family:ui-monospace,monospace;font-size:12px;color:#10b981;margin-top:12px;max-height:340px;overflow-y:auto;line-height:1.7;white-space:pre-wrap;}
.cu-log.empty{color:#64748b;font-style:italic;text-align:center;padding:20px;}

.cu-spin{display:inline-block;width:13px;height:13px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:cuspin 1s linear infinite;}
@keyframes cuspin{to{transform:rotate(360deg)}}

.cu-table{width:100%;border-collapse:collapse;font-size:12.5px;}
.cu-table th{background:#0f172a;color:#94a3b8;text-align:left;padding:10px 12px;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #334155;}
.cu-table td{padding:9px 12px;border-bottom:1px solid #1e293b;}
.cu-table tr:hover td{background:rgba(79,142,247,0.04);}
.cu-table .badge{display:inline-block;padding:3px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;}
.cu-table .badge-ok{background:rgba(16,185,129,0.15);color:#10b981;}
.cu-table .badge-diff{background:rgba(245,158,11,0.15);color:#f59e0b;}
.cu-table .badge-miss{background:rgba(239,68,68,0.15);color:#ef4444;}
.cu-table .filename{font-family:ui-monospace,monospace;font-size:12px;color:#cbd5e1;}

.cu-input{width:100%;padding:11px 14px;border-radius:8px;border:1px solid #475569;background:#0f172a;color:#fff;font-size:13px;font-family:inherit;box-sizing:border-box;margin-bottom:10px;}
.cu-input:focus{outline:none;border-color:#3b82f6;}

.cu-help{font-size:12px;color:#94a3b8;line-height:1.6;margin-top:8px;}
.cu-help b{color:#cbd5e1;}

.cu-row{display:grid;grid-template-columns:160px 1fr;gap:12px;padding:10px 0;border-bottom:1px solid #334155;font-size:13px;}
.cu-row:last-child{border-bottom:0;}
.cu-row b{color:#94a3b8;font-weight:600;}
.cu-row span{color:#fff;font-weight:600;font-family:ui-monospace,monospace;font-size:12.5px;}

.cu-filter{margin-bottom:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
.cu-filter-btn{padding:6px 12px;border-radius:6px;background:#334155;color:#cbd5e1;border:0;font-size:12px;cursor:pointer;font-family:inherit;}
.cu-filter-btn.on{background:#3b82f6;color:#fff;}
</style>

<div class="cu-wrap">
    <!-- HEAD -->
    <div class="cu-head">
        <div>
            <h2>🛡️ Codega Smart Update Manager <span class="v">v<?php echo htmlspecialchars($version); ?></span></h2>
            <div class="cu-meta">
                Tema: <b>v<?php echo htmlspecialchars($local_ver); ?></b>
                · Repo: <b><?php echo htmlspecialchars($gh_repo); ?></b>
                · Branch: <b><?php echo htmlspecialchars($gh_branch); ?></b>
            </div>
        </div>
        <?php if(!$has_token): ?>
        <div style="background:rgba(245,158,11,0.15);color:#fbbf24;padding:8px 14px;border-radius:8px;font-size:12.5px;border:1px solid #f59e0b;">
            ⚠️ GitHub token gerekli — Ayarlar sekmesi
        </div>
        <?php endif; ?>
    </div>

    <!-- TABS -->
    <div class="cu-tabs">
        <button class="cu-tab on" onclick="cuTab('overview',this)">📊 Genel Durum</button>
        <button class="cu-tab" onclick="cuTab('files',this)">📁 Dosyalar</button>
        <button class="cu-tab" onclick="cuTab('commits',this)">📝 Commits</button>
        <button class="cu-tab" onclick="cuTab('backups',this)">💾 Yedekler</button>
        <button class="cu-tab" onclick="cuTab('manual',this)">📦 Manuel ZIP</button>
        <button class="cu-tab" onclick="cuTab('settings',this)">⚙️ Ayarlar</button>
    </div>

    <!-- ============== TAB: Genel Durum ============== -->
    <div id="cu-overview" class="cu-body on">
        <div class="cu-card">
            <h3>📡 Repository Durumu</h3>
            <div id="cu-status-content">
                <div class="cu-alert cu-alert-info"><span class="cu-spin"></span>&nbsp; Durum kontrol ediliyor...</div>
            </div>
        </div>

        <div class="cu-card" id="cu-action-card" style="display:none;">
            <h3>🚀 Aksiyonlar</h3>
            <div class="cu-actions" id="cu-action-buttons"></div>
            <div class="cu-log" id="cu-sync-log" style="display:none;"></div>
        </div>
    </div>

    <!-- ============== TAB: Dosyalar ============== -->
    <div id="cu-files" class="cu-body">
        <div class="cu-card">
            <h3>📁 Dosya Listesi (Git Blob SHA Karşılaştırması)</h3>
            <div class="cu-filter">
                <span style="font-size:12px;color:#94a3b8;">Filtre:</span>
                <button class="cu-filter-btn on" onclick="cuFilter('all',this)">Tümü</button>
                <button class="cu-filter-btn" onclick="cuFilter('diff',this)">Farklı</button>
                <button class="cu-filter-btn" onclick="cuFilter('missing',this)">Eksik</button>
                <button class="cu-filter-btn" onclick="cuFilter('ok',this)">Aynı</button>
                <input type="text" id="cu-file-search" placeholder="Ara..." style="margin-left:auto;padding:6px 10px;background:#0f172a;border:1px solid #334155;color:#fff;border-radius:6px;font-size:12px;width:180px;font-family:inherit;" oninput="cuRenderFiles()">
            </div>
            <div id="cu-files-list">
                <div class="cu-alert cu-alert-info">Genel Durum sekmesini açtığınızda dosyalar yüklenir.</div>
            </div>
        </div>
    </div>

    <!-- ============== TAB: Commits ============== -->
    <div id="cu-commits" class="cu-body">
        <div class="cu-card">
            <h3>📝 Son 20 Commit</h3>
            <div class="cu-actions" style="margin-bottom:12px;">
                <button class="cu-btn cu-btn-secondary" onclick="cuLoadCommits()">🔄 Yenile</button>
            </div>
            <div id="cu-commits-list">
                <div class="cu-alert cu-alert-info">Yüklemek için Yenile butonuna basın.</div>
            </div>
        </div>
    </div>

    <!-- ============== TAB: Yedekler ============== -->
    <div id="cu-backups" class="cu-body">
        <div class="cu-card">
            <h3>💾 Yedekler (Max <?php echo CodegaUpdater::MAX_BK; ?>)</h3>
            <div class="cu-actions" style="margin-bottom:12px;">
                <button class="cu-btn cu-btn-secondary" onclick="cuLoadBackups()">🔄 Yenile</button>
            </div>
            <div id="cu-backups-list">
                <div class="cu-alert cu-alert-info">Yüklemek için Yenile butonuna basın.</div>
            </div>
        </div>
    </div>

    <!-- ============== TAB: Manuel ZIP ============== -->
    <div id="cu-manual" class="cu-body">
        <div class="cu-card">
            <h3>📦 Manuel ZIP Yükleme</h3>
            <div class="cu-alert cu-alert-info">
                <b>Ne zaman kullanılır?</b><br>
                GitHub'a erişim yoksa veya tek seferlik özel bir paket yüklenecekse. <code>codega-theme-vX.Y.Z.zip</code> dosyasını seçin, sistem otomatik açıp kuracak.
            </div>
            <input type="file" id="cu-manual-zip" accept=".zip" class="cu-input">
            <div class="cu-actions">
                <button class="cu-btn cu-btn-primary" onclick="cuManualUpload()">📤 Yükle ve Kur</button>
            </div>
            <div class="cu-log" id="cu-manual-log" style="display:none;"></div>
        </div>
    </div>

    <!-- ============== TAB: Ayarlar ============== -->
    <div id="cu-settings" class="cu-body">
        <div class="cu-card">
            <h3>🔑 GitHub Token</h3>
            <div class="cu-alert cu-alert-info">
                <b>Token nasıl alınır?</b><br>
                1) GitHub.com'a gir → Profil → Settings → Developer settings → Personal access tokens (classic)<br>
                2) "Generate new token" → <code>repo</code> kapsamını seç → süre belirle → oluştur<br>
                3) Aşağıya yapıştır. Token <code><?php echo htmlspecialchars(basename($dir_path) . '/.gh_token'); ?></code> dosyasında saklanır (chmod 0600).
            </div>
            <input type="password" id="cu-token" placeholder="ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" class="cu-input">
            <div class="cu-actions">
                <button class="cu-btn cu-btn-success" onclick="cuSaveToken()">💾 Kaydet</button>
                <button class="cu-btn cu-btn-secondary" onclick="cuTestToken()">🧪 Test Et</button>
            </div>
            <div id="cu-token-status" style="margin-top:12px;"></div>
        </div>

        <div class="cu-card">
            <h3>📋 Yapılandırma</h3>
            <div class="cu-row"><b>Eklenti Sürümü:</b> <span>v<?php echo htmlspecialchars($version); ?></span></div>
            <div class="cu-row"><b>Tema Sürümü:</b> <span>v<?php echo htmlspecialchars($local_ver); ?></span></div>
            <div class="cu-row"><b>GitHub Repo:</b> <span><?php echo htmlspecialchars($gh_repo); ?></span></div>
            <div class="cu-row"><b>Branch:</b> <span><?php echo htmlspecialchars($gh_branch); ?></span></div>
            <div class="cu-row"><b>Tema Yolu:</b> <span><?php echo htmlspecialchars($theme_path); ?></span></div>
            <div class="cu-row"><b>Tema Root:</b> <span><?php echo htmlspecialchars($theme_root); ?></span></div>
            <div class="cu-help">Yapılandırma değişiklikleri için: <b>WiseCP → Eklentiler → Codega Updater → Ayarlar (sağ üst)</b></div>
        </div>
    </div>
</div>

<script>
(function(){
    'use strict';

    var BASE = '<?php echo $link; ?>';
    var $ = function(id){ return document.getElementById(id); };
    var STATUS_DATA = null; // global, dosyalar sekmesi için

    function api(action, opts){
        opts = opts || {};
        var url = BASE + '?cdg_ajax=' + encodeURIComponent(action);
        var fopt = { method: opts.method || 'GET' };
        if(opts.body) fopt.body = opts.body;
        return fetch(url, fopt)
            .then(function(r){ return r.text(); })
            .then(function(txt){
                try { return JSON.parse(txt); }
                catch(e){
                    return { ok:false, error:'JSON parse hatası: ' + txt.substring(0,200) };
                }
            })
            .catch(function(e){ return { ok:false, error:'Ağ hatası: ' + e.message }; });
    }

    window.cuTab = function(name, btn){
        document.querySelectorAll('.cu-tab').forEach(function(t){ t.classList.remove('on'); });
        document.querySelectorAll('.cu-body').forEach(function(b){ b.classList.remove('on'); });
        if(btn) btn.classList.add('on');
        var body = $('cu-' + name);
        if(body) body.classList.add('on');
    };

    // === STATUS ===
    function loadStatus(){
        $('cu-status-content').innerHTML = '<div class="cu-alert cu-alert-info"><span class="cu-spin"></span>&nbsp; GitHub\'dan durum çekiliyor...</div>';
        api('status').then(function(r){
            STATUS_DATA = r;
            renderStatus(r);
        });
    }

    function renderStatus(r){
        var box = $('cu-status-content');
        if(!r.ok){
            box.innerHTML = '<div class="cu-alert cu-alert-danger">❌ ' + (r.error || 'Bilinmeyen hata') + '</div>';
            $('cu-action-card').style.display = 'none';
            return;
        }

        var html = '';
        if(r.needs_update){
            html += '<div class="cu-alert cu-alert-warn">🆕 Yeni güncelleme var: <b>v' + r.local_ver + '</b> → <b>v' + r.remote_ver + '</b> (' + r.stats.diff + ' değişen, ' + r.stats.missing + ' eksik dosya)</div>';
        } else {
            html += '<div class="cu-alert cu-alert-success">✅ Tema güncel — tüm ' + r.total + ' dosya senkronize</div>';
        }

        html += '<div class="cu-stat">';
        html += '<div class="cu-stat-box"><div class="lbl">Toplam Dosya</div><div class="val">' + r.total + '</div></div>';
        html += '<div class="cu-stat-box ok"><div class="lbl">✅ Aynı</div><div class="val">' + r.stats.ok + '</div></div>';
        html += '<div class="cu-stat-box diff"><div class="lbl">⚠️ Farklı</div><div class="val">' + r.stats.diff + '</div></div>';
        html += '<div class="cu-stat-box miss"><div class="lbl">❌ Eksik</div><div class="val">' + r.stats.missing + '</div></div>';
        html += '</div>';

        html += '<div style="margin-top:14px;">';
        html += '<div class="cu-row"><b>Yerel Sürüm:</b> <span>v' + r.local_ver + '</span></div>';
        html += '<div class="cu-row"><b>GitHub Sürüm:</b> <span>v' + r.remote_ver + '</span></div>';
        html += '</div>';

        box.innerHTML = html;

        // Aksiyon butonları
        $('cu-action-card').style.display = 'block';
        var btns = '';
        if(r.needs_update){
            btns += '<button class="cu-btn cu-btn-primary" onclick="cuSync(false)">⚡ Smart Sync (sadece değişenleri)</button>';
        }
        btns += '<button class="cu-btn cu-btn-warn" onclick="cuSync(true)">🔥 Force Sync (tüm dosyaları yeniden indir)</button>';
        btns += '<button class="cu-btn cu-btn-secondary" onclick="loadStatus()">🔄 Tekrar Kontrol</button>';
        $('cu-action-buttons').innerHTML = btns;

        // Dosyalar sekmesi içeriği
        renderFiles();
    }

    window.cuSync = function(force){
        var msg = force
            ? 'TÜM dosyalar yeniden indirilecek. Devam edilsin mi?'
            : 'Sadece değişen dosyalar güncellenecek. Devam edilsin mi?';
        if(!confirm(msg)) return;

        var log = $('cu-sync-log');
        log.style.display = 'block';
        log.textContent = '⏳ Başlıyor...';

        api(force ? 'force_sync' : 'sync').then(function(r){
            var lines = (r.log || []).join('\n');
            if(r.errors && r.errors.length){
                lines += '\n\n' + r.errors.join('\n');
            }
            log.textContent = lines;

            if(r.ok){
                setTimeout(loadStatus, 800);
            }
        });
    };

    // === FILES ===
    var FILTER = 'all';
    window.cuFilter = function(f, btn){
        FILTER = f;
        document.querySelectorAll('.cu-filter-btn').forEach(function(b){ b.classList.remove('on'); });
        if(btn) btn.classList.add('on');
        renderFiles();
    };

    window.cuRenderFiles = function(){ renderFiles(); };

    function renderFiles(){
        var list = $('cu-files-list');
        if(!STATUS_DATA || !STATUS_DATA.ok){
            list.innerHTML = '<div class="cu-alert cu-alert-info">Önce Genel Durum sekmesinde durum kontrol edilmeli.</div>';
            return;
        }

        var search = ($('cu-file-search').value || '').toLowerCase();
        var rows = '';
        var paths = Object.keys(STATUS_DATA.files).sort();
        var shown = 0;

        paths.forEach(function(path){
            var f = STATUS_DATA.files[path];
            if(FILTER !== 'all' && f.status !== FILTER) return;
            if(search && path.toLowerCase().indexOf(search) === -1) return;

            var bcls = 'badge-ok', btxt = 'Aynı';
            if(f.status === 'diff'){ bcls = 'badge-diff'; btxt = 'Farklı'; }
            if(f.status === 'missing'){ bcls = 'badge-miss'; btxt = 'Eksik'; }

            rows += '<tr>';
            rows += '<td class="filename">' + path + '</td>';
            rows += '<td><span class="badge ' + bcls + '">' + btxt + '</span></td>';
            rows += '<td style="text-align:right;font-size:11px;color:#94a3b8;">' + humanSize(f.size) + '</td>';
            rows += '<td style="text-align:right;">';
            if(f.status !== 'ok'){
                rows += '<button class="cu-btn cu-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="cuUpdateFile(\'' + path.replace(/'/g, "\\'") + '\')">📥 İndir</button>';
            }
            rows += '</td>';
            rows += '</tr>';
            shown++;
        });

        if(shown === 0){
            list.innerHTML = '<div class="cu-alert cu-alert-info">📭 Filtreye uygun dosya yok.</div>';
            return;
        }

        var html = '<table class="cu-table"><thead><tr>';
        html += '<th>Dosya</th><th>Durum</th><th style="text-align:right;">Boyut</th><th></th>';
        html += '</tr></thead><tbody>' + rows + '</tbody></table>';
        html += '<div style="margin-top:12px;font-size:12px;color:#94a3b8;">' + shown + '/' + paths.length + ' dosya gösteriliyor</div>';
        list.innerHTML = html;
    }

    window.cuUpdateFile = function(path){
        if(!confirm('Bu dosya GitHub\'tan indirilip üzerine yazılacak:\n\n' + path + '\n\nDevam?')) return;
        var fd = new FormData();
        fd.append('file', path);
        api('update_file', { method:'POST', body: fd }).then(function(r){
            if(r.ok){
                alert('✅ ' + r.msg);
                loadStatus();
            } else {
                alert('❌ ' + r.error);
            }
        });
    };

    // === COMMITS ===
    window.cuLoadCommits = function(){
        var box = $('cu-commits-list');
        box.innerHTML = '<div class="cu-alert cu-alert-info"><span class="cu-spin"></span>&nbsp; Yükleniyor...</div>';
        api('commits').then(function(r){
            if(!r.ok){
                box.innerHTML = '<div class="cu-alert cu-alert-danger">❌ ' + (r.error || 'Hata') + '</div>';
                return;
            }
            if(!r.commits || !r.commits.length){
                box.innerHTML = '<div class="cu-alert cu-alert-info">📭 Commit yok</div>';
                return;
            }
            var html = '<table class="cu-table"><thead><tr><th>SHA</th><th>Mesaj</th><th>Yazar</th><th>Tarih</th></tr></thead><tbody>';
            r.commits.forEach(function(c){
                var msg = c.message.split('\n')[0];
                if(msg.length > 80) msg = msg.substring(0, 80) + '...';
                html += '<tr>';
                html += '<td><code style="background:#0f172a;padding:2px 6px;border-radius:4px;font-size:11px;color:#fbbf24;">' + c.sha + '</code></td>';
                html += '<td style="color:#cbd5e1;">' + escapeHtml(msg) + '</td>';
                html += '<td style="color:#94a3b8;font-size:12px;">' + escapeHtml(c.author) + '</td>';
                html += '<td style="color:#94a3b8;font-size:11px;">' + (c.date || '').substring(0,10) + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            box.innerHTML = html;
        });
    };

    // === BACKUPS ===
    window.cuLoadBackups = function(){
        var box = $('cu-backups-list');
        box.innerHTML = '<div class="cu-alert cu-alert-info"><span class="cu-spin"></span>&nbsp; Yükleniyor...</div>';
        api('backups').then(function(r){
            if(!r.ok || !r.backups || !r.backups.length){
                box.innerHTML = '<div class="cu-alert cu-alert-info">📭 Yedek yok. Smart Sync veya Force Sync sırasında otomatik yedek alınır.</div>';
                return;
            }
            var html = '<table class="cu-table"><thead><tr><th>Yedek</th><th>Sürüm</th><th>Boyut</th><th>Tarih</th><th></th></tr></thead><tbody>';
            r.backups.forEach(function(b){
                html += '<tr>';
                html += '<td class="filename">' + b.name + '</td>';
                html += '<td><span class="badge badge-ok">v' + b.ver + '</span></td>';
                html += '<td style="color:#94a3b8;">' + humanSize(b.size) + '</td>';
                html += '<td style="color:#94a3b8;font-size:11px;">' + b.date + '</td>';
                html += '<td style="text-align:right;">';
                html += '<button class="cu-btn cu-btn-warn" style="padding:5px 11px;font-size:11px;" onclick="cuRestore(\'' + b.name + '\')">⏮ Geri Al</button> ';
                html += '<button class="cu-btn cu-btn-danger" style="padding:5px 11px;font-size:11px;" onclick="cuDeleteBackup(\'' + b.name + '\')">🗑️</button>';
                html += '</td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            box.innerHTML = html;
        });
    };

    window.cuRestore = function(name){
        if(!confirm('Bu yedeğe geri dönülecek:\n\n' + name + '\n\nMevcut tema dosyaları üzerine yazılacak. Önce güvenlik yedeği alınacak. Devam?')) return;
        var fd = new FormData();
        fd.append('backup', name);
        api('restore', { method:'POST', body: fd }).then(function(r){
            if(r.ok){
                alert('✅ ' + r.restored + ' dosya geri yüklendi');
                cuLoadBackups();
                loadStatus();
            } else {
                alert('❌ ' + r.error);
            }
        });
    };

    window.cuDeleteBackup = function(name){
        if(!confirm('Bu yedek silinecek:\n\n' + name + '\n\nDevam?')) return;
        var fd = new FormData();
        fd.append('backup', name);
        api('delete_backup', { method:'POST', body: fd }).then(function(r){
            if(r.ok) cuLoadBackups();
        });
    };

    // === MANUAL ZIP ===
    window.cuManualUpload = function(){
        var input = $('cu-manual-zip');
        if(!input.files || !input.files[0]){
            alert('Önce ZIP dosyası seçin');
            return;
        }
        var fd = new FormData();
        fd.append('zip', input.files[0]);

        var log = $('cu-manual-log');
        log.style.display = 'block';
        log.textContent = '⏳ ZIP yükleniyor ve açılıyor...';

        api('manual_zip', { method:'POST', body: fd }).then(function(r){
            if(!r.ok){
                log.textContent = '❌ ' + r.error;
                return;
            }
            log.textContent = (r.log || []).join('\n');
            setTimeout(loadStatus, 800);
        });
    };

    // === TOKEN ===
    window.cuSaveToken = function(){
        var token = $('cu-token').value.trim();
        if(token.length < 20){
            $('cu-token-status').innerHTML = '<div class="cu-alert cu-alert-danger">❌ Token çok kısa</div>';
            return;
        }
        var fd = new FormData();
        fd.append('token', token);
        api('save_token', { method:'POST', body: fd }).then(function(r){
            if(r.ok){
                $('cu-token-status').innerHTML = '<div class="cu-alert cu-alert-success">✅ Token kaydedildi. Sayfa yenileniyor...</div>';
                setTimeout(function(){ location.reload(); }, 1200);
            } else {
                $('cu-token-status').innerHTML = '<div class="cu-alert cu-alert-danger">❌ ' + r.error + '</div>';
            }
        });
    };

    window.cuTestToken = function(){
        $('cu-token-status').innerHTML = '<div class="cu-alert cu-alert-info"><span class="cu-spin"></span>&nbsp; Test ediliyor...</div>';
        api('test_token').then(function(r){
            if(r.ok){
                $('cu-token-status').innerHTML = '<div class="cu-alert cu-alert-success">✅ Token geçerli — Repo: <b>' + r.repo + '</b>' + (r.private ? ' (private)' : '') + '</div>';
            } else {
                $('cu-token-status').innerHTML = '<div class="cu-alert cu-alert-danger">❌ ' + r.error + '</div>';
            }
        });
    };

    // === Helpers ===
    function humanSize(b){
        if(b < 1024) return b + ' B';
        if(b < 1024*1024) return (b/1024).toFixed(1) + ' KB';
        return (b/1024/1024).toFixed(1) + ' MB';
    }
    function escapeHtml(s){
        return String(s||'').replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    // İlk yükleme
    <?php if($has_token): ?>
    setTimeout(loadStatus, 100);
    <?php else: ?>
    $('cu-status-content').innerHTML = '<div class="cu-alert cu-alert-warn">⚠️ GitHub token ayarlı değil. Önce <b>Ayarlar</b> sekmesinden token girin.</div>';
    $('cu-action-card').style.display = 'none';
    <?php endif; ?>
})();
</script>
