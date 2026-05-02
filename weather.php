<?php
$page_title = 'আবহাওয়া পূর্বাভাস';
$meta_desc  = 'বাংলাদেশের সকল বিভাগীয় শহরের আবহাওয়ার সর্বশেষ তথ্য, ঘণ্টাওয়ারি ও ৩ দিনের পূর্বাভাস।';
require_once 'includes/header.php';
?>
<style>
/* ===== WEATHER PAGE ===== */
.wp { padding: 24px 0 56px; }

.wp-header { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:18px; }
.wp-title  { font-size:1.45rem; font-weight:700; color:var(--text-color,#1a1a1a); display:flex; align-items:center; gap:10px; margin:0; }
.wp-title i { color:#e8001c; }
.wp-meta   { margin-left:auto; display:flex; align-items:center; gap:10px; font-size:.82rem; color:#888; }
.wp-refresh{ border:none; background:#f5f5f5; border-radius:8px; padding:6px 12px; cursor:pointer;
             font-size:.82rem; color:#555; display:flex; align-items:center; gap:5px; transition:all .2s; }
.wp-refresh:hover{ background:#e8001c; color:#fff; }
[data-theme="dark"] .wp-refresh{ background:#2a2a2a; color:#aaa; }
[data-theme="dark"] .wp-refresh:hover{ background:#e8001c; color:#fff; }

/* ---- City tabs ---- */
.city-tabs { display:flex; flex-wrap:wrap; gap:7px; margin-bottom:18px; }
.city-tab  { padding:7px 18px; border-radius:24px; border:2px solid #e0e0e0; background:none;
             cursor:pointer; font-family:inherit; font-size:.9rem; font-weight:600;
             color:var(--text-color,#333); transition:all .2s; }
.city-tab:hover  { border-color:#e8001c; color:#e8001c; }
.city-tab.active { background:#e8001c; border-color:#e8001c; color:#fff; }
[data-theme="dark"] .city-tab { border-color:#444; color:#ccc; }
[data-theme="dark"] .city-tab.active { color:#fff; }

/* ---- View tabs ---- */
.view-tabs { display:flex; flex-wrap:wrap; gap:0; margin-bottom:22px;
             background:var(--card-bg,#fff); border-radius:14px; padding:6px;
             box-shadow:0 2px 10px rgba(0,0,0,.07); width:fit-content; }
[data-theme="dark"] .view-tabs { background:#1e1e1e; box-shadow:0 2px 10px rgba(0,0,0,.3); }
.view-tab  { padding:9px 20px; border-radius:10px; border:none; background:none;
             cursor:pointer; font-family:inherit; font-size:.88rem; font-weight:600;
             color:var(--text-color,#555); transition:all .2s; white-space:nowrap; }
.view-tab:hover  { color:#e8001c; }
.view-tab.active { background:#e8001c; color:#fff; box-shadow:0 2px 8px rgba(232,0,28,.3); }
.view-tab .vtdate{ display:block; font-size:.72rem; font-weight:400; opacity:.75; margin-top:1px; }

/* ---- Hero card ---- */
.w-hero {
    border-radius:18px; padding:32px 36px; color:#fff; margin-bottom:22px;
    display:grid; grid-template-columns:1fr auto; gap:24px; align-items:center;
    background:linear-gradient(135deg,#1565c0,#0d47a1); transition:background .4s;
    position:relative; overflow:hidden;
}
.w-hero::after { content:''; position:absolute; right:-40px; top:-40px;
                 width:220px; height:220px; border-radius:50%;
                 background:rgba(255,255,255,.06); pointer-events:none; }
.w-hero.wc-clear   { background:linear-gradient(135deg,#29b6f6,#0277bd); }
.w-hero.wc-cloud   { background:linear-gradient(135deg,#78909c,#455a64); }
.w-hero.wc-rain    { background:linear-gradient(135deg,#1e88e5,#1565c0); }
.w-hero.wc-thunder { background:linear-gradient(135deg,#4527a0,#1a237e); }
.w-hero.wc-fog     { background:linear-gradient(135deg,#90a4ae,#607d8b); }
.w-hero.wc-snow    { background:linear-gradient(135deg,#81d4fa,#29b6f6); }

.hero-city   { font-size:1rem; opacity:.85; margin-bottom:4px; }
.hero-temp   { font-size:4.5rem; font-weight:800; line-height:1; margin-bottom:6px; }
.hero-desc   { font-size:1.1rem; margin-bottom:18px; }
.hero-pills  { display:flex; flex-wrap:wrap; gap:10px; }
.hero-pill   { background:rgba(255,255,255,.18); border-radius:10px; padding:8px 14px;
               font-size:.84rem; display:flex; align-items:center; gap:7px; }
.hero-pill strong { display:block; font-size:.7rem; opacity:.72; margin-bottom:1px; }
.hero-icon-wrap { text-align:center; }
.hero-icon   { font-size:6rem; opacity:.9; line-height:1; }
.hero-feels  { font-size:.85rem; opacity:.78; margin-top:8px; }

/* ---- Day summary ---- */
.day-summary {
    border-radius:18px; padding:24px 28px; margin-bottom:22px;
    background:var(--card-bg,#fff); box-shadow:0 2px 14px rgba(0,0,0,.07);
    border:1px solid var(--border-color,#f0f0f0);
}
[data-theme="dark"] .day-summary { background:#1e1e1e; border-color:#333; }
.day-summary-top { display:flex; flex-wrap:wrap; align-items:center; gap:16px; margin-bottom:18px; }
.day-icon  { font-size:3.5rem; color:#e8001c; }
.day-date  { font-size:1.1rem; font-weight:700; color:var(--text-color,#1a1a1a); }
.day-sdesc { font-size:.9rem; color:#777; margin-top:3px; }
[data-theme="dark"] .day-sdesc { color:#aaa; }
.day-hilow { margin-left:auto; text-align:center; }
.day-hilow .hi { font-size:2.2rem; font-weight:800; color:#e8001c; line-height:1; }
.day-hilow .lo { font-size:1.1rem; color:#888; }
.day-meta-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:10px; }
.day-meta-item { background:var(--bg-alt,#f8f8f8); border-radius:10px; padding:10px 14px; }
[data-theme="dark"] .day-meta-item { background:#2a2a2a; }
.day-meta-label { font-size:.72rem; color:#888; margin-bottom:3px; }
[data-theme="dark"] .day-meta-label { color:#aaa; }
.day-meta-val   { font-size:.95rem; font-weight:700; color:var(--text-color,#222); }

/* ---- Section title ---- */
.w-section-title { font-size:1rem; font-weight:700; margin:0 0 14px;
                   color:var(--text-color,#1a1a1a); border-left:4px solid #e8001c; padding-left:10px; }

/* ---- Hourly table ---- */
.hourly-wrap { overflow-x:auto; margin-bottom:28px; border-radius:14px;
               box-shadow:0 2px 12px rgba(0,0,0,.06); }
.hourly-table { width:100%; border-collapse:collapse; background:var(--card-bg,#fff);
                border-radius:14px; overflow:hidden; min-width:700px; }
[data-theme="dark"] .hourly-table { background:#1e1e1e; }
.hourly-table thead tr { background:#e8001c; color:#fff; }
.hourly-table thead th { padding:12px 14px; font-size:.8rem; font-weight:600;
                          text-align:center; white-space:nowrap; }
.hourly-table tbody tr { border-bottom:1px solid var(--border-color,#f0f0f0); transition:background .15s; }
[data-theme="dark"] .hourly-table tbody tr { border-color:#2a2a2a; }
.hourly-table tbody tr:last-child { border-bottom:none; }
.hourly-table tbody tr:hover { background:rgba(232,0,28,.04); }
[data-theme="dark"] .hourly-table tbody tr:hover { background:rgba(232,0,28,.08); }
.hourly-table td { padding:12px 14px; text-align:center; font-size:.85rem;
                   color:var(--text-color,#333); white-space:nowrap; }
.hourly-table .h-time   { font-weight:700; color:#e8001c; }
.hourly-table .h-icon   { font-size:1.4rem; color:#e8001c; }
.hourly-table .h-temp   { font-size:1.05rem; font-weight:800; }
.hourly-table .h-feels  { color:#888; }
[data-theme="dark"] .hourly-table .h-feels { color:#aaa; }
.rain-bar { display:inline-block; width:36px; height:5px; background:#e0e0e0;
            border-radius:3px; vertical-align:middle; margin-left:4px; overflow:hidden; }
.rain-fill { height:100%; background:#1565c0; border-radius:3px; }
[data-theme="dark"] .rain-bar { background:#333; }
.h-rain-pct { color:#1565c0; font-weight:700; }
[data-theme="dark"] .h-rain-pct { color:#90caf9; }
.h-wind-dir { font-size:.7rem; color:#888; }
[data-theme="dark"] .h-wind-dir { color:#aaa; }
.h-uv-low   { color:#4caf50; }
.h-uv-mid   { color:#ff9800; }
.h-uv-high  { color:#f44336; }

/* Night highlight */
.hourly-table tbody tr.night-row { background:rgba(100,100,180,.04); }
[data-theme="dark"] .hourly-table tbody tr.night-row { background:rgba(100,100,180,.08); }

/* ---- 8-city grid ---- */
.cities-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:10px; }
.city-card { background:var(--card-bg,#fff); border-radius:14px; padding:16px;
             cursor:pointer; border:2px solid var(--border-color,#f0f0f0);
             transition:all .2s; display:flex; align-items:center; gap:12px;
             box-shadow:0 2px 8px rgba(0,0,0,.05); }
.city-card:hover  { border-color:#e8001c; box-shadow:0 4px 16px rgba(232,0,28,.12); }
.city-card.active { border-color:#e8001c; background:#fff5f5; }
[data-theme="dark"] .city-card { background:#1e1e1e; border-color:#333; }
[data-theme="dark"] .city-card.active { background:#2a1515; }
.cc-icon { font-size:1.8rem; color:#e8001c; width:38px; text-align:center; }
.cc-name { font-weight:700; font-size:.9rem; color:var(--text-color,#222); }
.cc-temp { font-size:1.35rem; font-weight:800; color:#e8001c; }
.cc-desc { font-size:.74rem; color:#888; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:90px; }
[data-theme="dark"] .cc-desc { color:#aaa; }
.cc-shimmer { height:18px; border-radius:4px; background:linear-gradient(90deg,#eee 25%,#f5f5f5 50%,#eee 75%);
              background-size:200% 100%; animation:shimmer 1.4s infinite; }
[data-theme="dark"] .cc-shimmer { background:linear-gradient(90deg,#2a2a2a 25%,#333 50%,#2a2a2a 75%);
              background-size:200% 100%; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Skeleton hero */
.hero-skeleton { border-radius:18px; min-height:200px; padding:32px;
                 background:linear-gradient(135deg,#e0e0e0,#bdbdbd); margin-bottom:22px;
                 display:flex; align-items:center; justify-content:center; gap:10px;
                 color:#888; font-size:1rem; }
[data-theme="dark"] .hero-skeleton { background:linear-gradient(135deg,#2a2a2a,#1e1e1e); color:#666; }

/* Panel show/hide */
.w-panel { display:none; }
.w-panel.active { display:block; }

@media(max-width:900px){ .cities-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:768px){
    .w-hero{ grid-template-columns:1fr; gap:16px; padding:22px; }
    .hero-icon{font-size:4rem;}
    .hero-temp{font-size:3.5rem;}
    .hero-icon-wrap{display:flex;align-items:center;gap:14px;}
    .cities-grid{grid-template-columns:1fr 1fr;}
    .view-tabs{gap:0;overflow-x:auto;}
    .view-tab{padding:8px 14px;font-size:.82rem;}
}
@media(max-width:480px){
    .cities-grid{grid-template-columns:1fr 1fr;}
    .city-tabs{gap:5px;}
    .city-tab{padding:6px 12px;font-size:.82rem;}
}

/* ── dark mode text fixes ── */
[data-theme="dark"] .wp-title,
[data-theme="dark"] .w-section-title,
[data-theme="dark"] .day-date,
[data-theme="dark"] .day-meta-val,
[data-theme="dark"] .cc-name,
[data-theme="dark"] .cc-temp,
[data-theme="dark"] .view-tab,
[data-theme="dark"] .city-tab,
[data-theme="dark"] .forecast-day,
[data-theme="dark"] .hourly-table td,
[data-theme="dark"] .hourly-table .h-temp { color: #e4e6ef; }
[data-theme="dark"] .hourly-table .h-feels,
[data-theme="dark"] .day-sdesc,
[data-theme="dark"] .day-meta-label,
[data-theme="dark"] .hourly-time,
[data-theme="dark"] .h-wind-dir,
[data-theme="dark"] .cc-desc { color: #9aa0b8; }
</style>

<div class="container wp">

    <!-- Header -->
    <div class="wp-header">
        <h1 class="wp-title"><i class="fas fa-cloud-sun"></i> আবহাওয়া পূর্বাভাস</h1>
        <div class="wp-meta">
            <span id="wpUpdated"></span>
            <button class="wp-refresh" id="wpRefresh"><i class="fas fa-sync-alt"></i> রিফ্রেশ</button>
        </div>
    </div>

    <!-- City selector -->
    <div class="city-tabs" id="cityTabs">
        <?php
        $city_map = ['Dhaka'=>'ঢাকা','Chittagong'=>'চট্টগ্রাম','Sylhet'=>'সিলেট',
                     'Rajshahi'=>'রাজশাহী','Khulna'=>'খুলনা','Barisal'=>'বরিশাল',
                     'Rangpur'=>'রংপুর','Mymensingh'=>'ময়মনসিংহ'];
        foreach ($city_map as $en => $bn):
        ?>
        <button class="city-tab <?= $en==='Dhaka'?'active':'' ?>" data-city="<?= $en ?>"><?= $bn ?></button>
        <?php endforeach; ?>
    </div>

    <!-- View tabs -->
    <div class="view-tabs" id="viewTabs">
        <button class="view-tab active" data-view="now">
            <i class="fas fa-satellite-dish"></i> এখন
        </button>
        <button class="view-tab" data-view="day0" id="vtDay0">
            আজ<span class="vtdate" id="vtDate0"></span>
        </button>
        <button class="view-tab" data-view="day1" id="vtDay1">
            আগামীকাল<span class="vtdate" id="vtDate1"></span>
        </button>
        <button class="view-tab" data-view="day2" id="vtDay2">
            পরশু<span class="vtdate" id="vtDate2"></span>
        </button>
    </div>

    <!-- Panel: Now -->
    <div class="w-panel active" id="panelNow">
        <div id="heroWrap">
            <div class="hero-skeleton"><i class="fas fa-spinner fa-spin"></i> আবহাওয়া লোড হচ্ছে…</div>
        </div>
        <p class="w-section-title"><i class="fas fa-map-marked-alt"></i> বিভাগীয় শহরের আবহাওয়া</p>
        <div class="cities-grid" id="citiesGrid">
            <?php foreach ($city_map as $en => $bn): ?>
            <div class="city-card <?= $en==='Dhaka'?'active':'' ?>" id="cc_<?= $en ?>" onclick="switchCity('<?= $en ?>')">
                <div class="cc-icon"><i class="fas fa-cloud-sun" id="ccIcon_<?= $en ?>"></i></div>
                <div style="flex:1;min-width:0">
                    <div class="cc-name"><?= $bn ?></div>
                    <div class="cc-temp" id="ccTemp_<?= $en ?>"><div class="cc-shimmer" style="width:46px"></div></div>
                    <div class="cc-desc" id="ccDesc_<?= $en ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Panel: Day 0 -->
    <div class="w-panel" id="panelDay0">
        <div id="sumDay0"></div>
        <p class="w-section-title"><i class="fas fa-clock"></i> ঘণ্টাওয়ারি পূর্বাভাস</p>
        <div class="hourly-wrap" id="hourlyDay0"></div>
    </div>

    <!-- Panel: Day 1 -->
    <div class="w-panel" id="panelDay1">
        <div id="sumDay1"></div>
        <p class="w-section-title"><i class="fas fa-clock"></i> ঘণ্টাওয়ারি পূর্বাভাস</p>
        <div class="hourly-wrap" id="hourlyDay1"></div>
    </div>

    <!-- Panel: Day 2 -->
    <div class="w-panel" id="panelDay2">
        <div id="sumDay2"></div>
        <p class="w-section-title"><i class="fas fa-clock"></i> ঘণ্টাওয়ারি পূর্বাভাস</p>
        <div class="hourly-wrap" id="hourlyDay2"></div>
    </div>

</div><!-- .wp -->

<script>
(function(){
/* ── Bengali numerals ── */
var BN = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
function bn(n){ return String(Math.round(n)).replace(/-/g,'−').split('').map(function(d){return BN[d]||d;}).join(''); }
function bnf(n){ var s=parseFloat(n).toFixed(1); return s.split('').map(function(d){return BN[d]||d;}).join(''); }

var BN_DAYS   = ['রোববার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'];
var BN_MONTHS = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];

/* ── WMO weather codes (Open-Meteo standard) ── */
var WMO_DESC = {
    0:'পরিষ্কার আকাশ', 1:'প্রায় পরিষ্কার', 2:'আংশিক মেঘলা', 3:'সম্পূর্ণ মেঘলা',
    45:'কুয়াশা', 48:'ঘন কুয়াশা',
    51:'হালকা গুঁড়িগুঁড়ি', 53:'গুঁড়িগুঁড়ি বৃষ্টি', 55:'ভারী গুঁড়িগুঁড়ি',
    56:'হিমশীতল গুঁড়িগুঁড়ি', 57:'ভারী হিমশীতল গুঁড়িগুঁড়ি',
    61:'হালকা বৃষ্টি', 63:'মাঝারি বৃষ্টি', 65:'ভারী বৃষ্টি',
    66:'হিমশীতল বৃষ্টি', 67:'ভারী হিমশীতল বৃষ্টি',
    71:'হালকা তুষারপাত', 73:'মাঝারি তুষারপাত', 75:'ভারী তুষারপাত', 77:'শিলাকণা',
    80:'হালকা বৃষ্টি', 81:'মাঝারি বৃষ্টি', 82:'তীব্র বৃষ্টি',
    85:'হালকা তুষার', 86:'ভারী তুষার',
    95:'বজ্রঝড়', 96:'শিলাসহ বজ্রঝড়', 99:'তীব্র বজ্রঝড়'
};

function wmoDesc(c){ return WMO_DESC[c] || 'আংশিক মেঘলা'; }

function wmoIcon(c){
    if(c<=1) return 'fas fa-sun';
    if(c===2) return 'fas fa-cloud-sun';
    if(c===3) return 'fas fa-cloud';
    if(c===45||c===48) return 'fas fa-smog';
    if(c>=51&&c<=57) return 'fas fa-cloud-rain';
    if(c>=61&&c<=67) return 'fas fa-cloud-showers-heavy';
    if(c>=71&&c<=77) return 'fas fa-snowflake';
    if(c>=80&&c<=82) return 'fas fa-cloud-showers-heavy';
    if(c>=85&&c<=86) return 'fas fa-snowflake';
    if(c>=95) return 'fas fa-bolt';
    return 'fas fa-cloud-sun';
}

function wmoBg(c){
    if(c<=1) return 'wc-clear';
    if(c<=3) return 'wc-cloud';
    if(c<=48) return 'wc-fog';
    if(c>=95) return 'wc-thunder';
    if(c>=71&&c<=77) return 'wc-snow';
    if(c>=51) return 'wc-rain';
    return 'wc-cloud';
}

/* ── wind direction (degrees → 16-point) ── */
var DIRS16 = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW'];
var WIND_BN = {N:'উত্তর',NNE:'উ-উপূ',NE:'উত্তরপূর্ব',ENE:'পূ-উপূ',
    E:'পূর্ব',ESE:'পূ-দপূ',SE:'দক্ষিণপূর্ব',SSE:'দ-দপূ',
    S:'দক্ষিণ',SSW:'দ-দপশ',SW:'দক্ষিণপশ্চিম',WSW:'প-দপশ',
    W:'পশ্চিম',WNW:'প-উপশ',NW:'উত্তরপশ্চিম',NNW:'উ-উপশ'};
function windDir(deg){ var k=DIRS16[Math.round(deg/22.5)%16]; return WIND_BN[k]||k; }

/* ── date helpers ── */
function bnDate(ds){
    var d=new Date(ds+'T00:00:00');
    return BN_DAYS[d.getDay()]+', '+bn(d.getDate())+' '+BN_MONTHS[d.getMonth()]+' '+bn(d.getFullYear());
}
function bnDateShort(ds){
    var d=new Date(ds+'T00:00:00');
    return bn(d.getDate())+' '+BN_MONTHS[d.getMonth()];
}
function astroTime(iso){
    var p=iso.split('T')[1]; var h=parseInt(p),m=p.split(':')[1];
    var lb=h<4?'রাত':h<6?'ভোর':h<12?'সকাল':h===12?'দুপুর':h<17?'বিকাল':h<20?'সন্ধ্যা':'রাত';
    return lb+' '+bn(h%12||12)+':'+String(m).split('').map(function(d){return BN[d]||d;}).join('');
}
function hourLabel(iso){
    var h=parseInt(iso.split('T')[1]);
    var lb=h===0?'রাত':h<6?'রাত':h===6?'ভোর':h<12?'সকাল':h===12?'দুপুর':h<17?'বিকাল':h<20?'সন্ধ্যা':'রাত';
    return lb+' '+bn(h%12||12)+':০০';
}
function isNightHour(iso){ var h=parseInt(iso.split('T')[1]); return h<6||h>=20; }

function uvClass(u){ u=Math.round(u); return u<=2?'h-uv-low':u<=7?'h-uv-mid':'h-uv-high'; }
function uvLabel(u){ u=Math.round(u); return u<=2?'কম':u<=5?'মাঝারি':u<=7?'উচ্চ':u<=10?'অতি উচ্চ':'বিপজ্জনক'; }

/* ── city data — precise coordinates for each division ── */
var CITY_COORDS = {
    Dhaka:      [23.7104, 90.4074],
    Chittagong: [22.3569, 91.7832],
    Sylhet:     [24.8949, 91.8687],
    Rajshahi:   [24.3745, 88.6042],
    Khulna:     [22.8456, 89.5403],
    Barisal:    [22.7010, 90.3535],
    Rangpur:    [25.7439, 89.2752],
    Mymensingh: [24.7471, 90.4203]
};
var CITIES = {Dhaka:'ঢাকা',Chittagong:'চট্টগ্রাম',Sylhet:'সিলেট',Rajshahi:'রাজশাহী',
    Khulna:'খুলনা',Barisal:'বরিশাল',Rangpur:'রংপুর',Mymensingh:'ময়মনসিংহ'};

var cache={}, selCity='Dhaka', selView='now';

/* ── render hero ── */
function renderHero(cityEn, data){
    var c=data.current;
    var code=c.weather_code;
    var vis=Math.round((c.visibility||0)/1000);
    var uv=Math.round(c.uv_index||0);
    document.getElementById('heroWrap').innerHTML=
        '<div class="w-hero '+wmoBg(code)+'">'+
        '<div>'+
          '<div class="hero-city"><i class="fas fa-map-marker-alt"></i> '+CITIES[cityEn]+'</div>'+
          '<div class="hero-temp">'+bn(c.temperature_2m)+'°C</div>'+
          '<div class="hero-desc">'+wmoDesc(code)+'</div>'+
          '<div class="hero-pills">'+
            '<div class="hero-pill"><i class="fas fa-tint"></i><div><strong>আর্দ্রতা</strong>'+bn(c.relative_humidity_2m)+'%</div></div>'+
            '<div class="hero-pill"><i class="fas fa-wind"></i><div><strong>বায়ু</strong>'+bn(c.wind_speed_10m)+' কিমি/ঘ ('+windDir(c.wind_direction_10m)+')</div></div>'+
            '<div class="hero-pill"><i class="fas fa-eye"></i><div><strong>দৃশ্যমানতা</strong>'+bn(vis)+' কিমি</div></div>'+
            '<div class="hero-pill"><i class="fas fa-sun"></i><div><strong>UV সূচক</strong>'+bn(uv)+' ('+uvLabel(uv)+')</div></div>'+
            '<div class="hero-pill"><i class="fas fa-tachometer-alt"></i><div><strong>বায়ুচাপ</strong>'+bn(c.surface_pressure)+' mb</div></div>'+
            '<div class="hero-pill"><i class="fas fa-cloud"></i><div><strong>মেঘাচ্ছাদন</strong>'+bn(c.cloud_cover)+'%</div></div>'+
          '</div>'+
        '</div>'+
        '<div class="hero-icon-wrap">'+
          '<div class="hero-icon"><i class="'+wmoIcon(code)+'"></i></div>'+
          '<div class="hero-feels">অনুভূতি '+bn(c.apparent_temperature)+'°C</div>'+
        '</div>'+
        '</div>';
}

/* ── render city card ── */
function renderCard(cityEn, data){
    var c=data.current;
    var ti=document.getElementById('ccTemp_'+cityEn);
    var di=document.getElementById('ccDesc_'+cityEn);
    var ii=document.getElementById('ccIcon_'+cityEn);
    if(ti) ti.textContent=bn(c.temperature_2m)+'°C';
    if(di) di.textContent=wmoDesc(c.weather_code);
    if(ii) ii.className=wmoIcon(c.weather_code);
}

/* ── render day summary ── */
function renderDaySummary(data, idx){
    var d=data.daily, h=data.hourly;
    var off=idx*24;
    var midCode=h.weather_code[off+12];
    var humSum=0, maxWind=0;
    for(var i=0;i<24;i++){
        humSum+=h.relative_humidity_2m[off+i];
        if(h.wind_speed_10m[off+i]>maxWind) maxWind=h.wind_speed_10m[off+i];
    }
    var avgHum=Math.round(humSum/24);
    var html='<div class="day-summary">'+
        '<div class="day-summary-top">'+
            '<div class="day-icon"><i class="'+wmoIcon(midCode)+'"></i></div>'+
            '<div>'+
                '<div class="day-date">'+bnDate(d.time[idx])+'</div>'+
                '<div class="day-sdesc">'+wmoDesc(d.weather_code[idx])+'</div>'+
            '</div>'+
            '<div class="day-hilow">'+
                '<div class="hi">'+bn(d.temperature_2m_max[idx])+'°</div>'+
                '<div class="lo">'+bn(d.temperature_2m_min[idx])+'° সর্বনিম্ন</div>'+
            '</div>'+
        '</div>'+
        '<div class="day-meta-grid">'+
            item('fas fa-thermometer-full','সর্বোচ্চ তাপমাত্রা',bn(d.temperature_2m_max[idx])+'°C')+
            item('fas fa-thermometer-quarter','সর্বনিম্ন তাপমাত্রা',bn(d.temperature_2m_min[idx])+'°C')+
            item('fas fa-cloud-rain','মোট বৃষ্টিপাত',bnf(d.precipitation_sum[idx])+' মিমি')+
            item('fas fa-water','গড় আর্দ্রতা',bn(avgHum)+'%')+
            item('fas fa-sunrise','সূর্যোদয়',astroTime(d.sunrise[idx]))+
            item('fas fa-sunset','সূর্যাস্ত',astroTime(d.sunset[idx]))+
            item('fas fa-wind','সর্বোচ্চ বায়ু',bn(maxWind)+' কিমি/ঘ')+
            item('fas fa-sun','UV সূচক (সর্বোচ্চ)',bn(d.uv_index_max[idx])+' ('+uvLabel(d.uv_index_max[idx])+')')+
        '</div>'+
    '</div>';
    var el=document.getElementById('sumDay'+idx);
    if(el) el.innerHTML=html;
}

function item(ic,lbl,val){
    return '<div class="day-meta-item"><div class="day-meta-label"><i class="'+ic+'"></i> '+lbl+'</div><div class="day-meta-val">'+val+'</div></div>';
}

/* ── render hourly table ── */
function renderHourlyTable(data, idx){
    var h=data.hourly, off=idx*24, rows='';
    [0,3,6,9,12,15,18,21].forEach(function(hr){
        var i=off+hr;
        var code=h.weather_code[i];
        var rain=h.precipitation_probability[i]||0;
        var uv=Math.round(h.uv_index[i]||0);
        var night=isNightHour(h.time[i]);
        rows+='<tr class="'+(night?'night-row':'')+'">'+
            '<td class="h-time">'+hourLabel(h.time[i])+'</td>'+
            '<td class="h-icon"><i class="'+wmoIcon(code)+'"></i></td>'+
            '<td><span style="font-size:.75rem;color:#888">'+(night?'<i class="fas fa-moon" style="margin-right:3px"></i>':'<i class="fas fa-sun" style="margin-right:3px"></i>')+'</span>'+wmoDesc(code)+'</td>'+
            '<td class="h-temp">'+bn(h.temperature_2m[i])+'°C</td>'+
            '<td class="h-feels">'+bn(h.apparent_temperature[i])+'°C</td>'+
            '<td>'+
                '<span class="h-rain-pct">'+bn(rain)+'%</span>'+
                '<span class="rain-bar"><span class="rain-fill" style="width:'+rain+'%"></span></span>'+
                '<br><small style="color:#888">'+bnf(h.precipitation[i]||0)+' মিমি</small>'+
            '</td>'+
            '<td>'+bn(h.wind_speed_10m[i])+' কিমি/ঘ<br><span class="h-wind-dir">'+windDir(h.wind_direction_10m[i])+'</span></td>'+
            '<td>'+bn(h.relative_humidity_2m[i])+'%</td>'+
            '<td>'+bn(h.cloud_cover[i])+'%</td>'+
            '<td class="'+uvClass(uv)+'">'+bn(uv)+'<br><small>'+uvLabel(uv)+'</small></td>'+
        '</tr>';
    });
    var el=document.getElementById('hourlyDay'+idx);
    if(el) el.innerHTML='<table class="hourly-table"><thead><tr>'+
        '<th>সময়</th><th>আইকন</th><th>অবস্থা</th><th>তাপমাত্রা</th><th>অনুভূতি</th>'+
        '<th>বৃষ্টি</th><th>বায়ু</th><th>আর্দ্রতা</th><th>মেঘ</th><th>UV</th>'+
        '</tr></thead><tbody>'+rows+'</tbody></table>';
}

function updateTabDates(data){
    for(var i=0;i<3;i++){
        var el=document.getElementById('vtDate'+i);
        if(el&&data.daily.time[i]) el.textContent=bnDateShort(data.daily.time[i]);
    }
}

function renderAll(cityEn, data){
    renderHero(cityEn, data);
    renderCard(cityEn, data);
    for(var i=0;i<3;i++){ renderDaySummary(data,i); renderHourlyTable(data,i); }
    updateTabDates(data);
    var upd=document.getElementById('wpUpdated');
    if(upd){
        var now=new Date(), h=now.getHours(), m=now.getMinutes();
        upd.textContent='আপডেট: '+bn(h%12||12)+':'+String(m).padStart(2,'0').split('').map(function(d){return BN[d]||d;}).join('')+(h<12?' AM':' PM');
    }
}

/* ── switch city / view ── */
window.switchCity=function(cityEn){
    selCity=cityEn;
    document.querySelectorAll('.city-tab').forEach(function(t){ t.classList.toggle('active',t.dataset.city===cityEn); });
    document.querySelectorAll('.city-card').forEach(function(c){ c.classList.toggle('active',c.id==='cc_'+cityEn); });
    if(cache[cityEn]){ renderAll(cityEn,cache[cityEn]); }
    else {
        document.getElementById('heroWrap').innerHTML='<div class="hero-skeleton"><i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে…</div>';
        fetchCity(cityEn);
    }
};
document.getElementById('viewTabs').addEventListener('click',function(e){
    var t=e.target.closest('.view-tab'); if(!t) return;
    selView=t.dataset.view;
    document.querySelectorAll('.view-tab').forEach(function(b){ b.classList.toggle('active',b===t); });
    document.querySelectorAll('.w-panel').forEach(function(p){ p.classList.remove('active'); });
    var map={now:'panelNow',day0:'panelDay0',day1:'panelDay1',day2:'panelDay2'};
    var el=document.getElementById(map[t.dataset.view]); if(el) el.classList.add('active');
});
document.getElementById('cityTabs').addEventListener('click',function(e){
    var t=e.target.closest('.city-tab'); if(t) switchCity(t.dataset.city);
});

/* ── fetch from Open-Meteo (ECMWF model — same data source as major weather sites) ── */
var pendingCount=0;
function fetchCity(cityEn){
    var coords=CITY_COORDS[cityEn]; if(!coords) return;
    pendingCount++;
    var url='https://api.open-meteo.com/v1/forecast'
        +'?latitude='+coords[0]+'&longitude='+coords[1]
        +'&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,'
        +'wind_speed_10m,wind_direction_10m,surface_pressure,cloud_cover,visibility,uv_index'
        +'&hourly=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation_probability,'
        +'precipitation,weather_code,wind_speed_10m,wind_direction_10m,cloud_cover,uv_index'
        +'&daily=weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,precipitation_sum,uv_index_max'
        +'&timezone=Asia%2FDhaka&forecast_days=3&wind_speed_unit=kmh&models=best_match';
    fetch(url)
        .then(function(r){ return r.json(); })
        .then(function(data){
            cache[cityEn]=data;
            renderCard(cityEn,data);
            if(cityEn===selCity) renderAll(cityEn,data);
        })
        .catch(function(){
            var ti=document.getElementById('ccTemp_'+cityEn);
            if(ti) ti.textContent='—';
            if(cityEn===selCity)
                document.getElementById('heroWrap').innerHTML=
                    '<div class="hero-skeleton" style="color:#e8001c"><i class="fas fa-exclamation-circle"></i> তথ্য লোড হয়নি। রিফ্রেশ করুন।</div>';
        })
        .finally(function(){
            pendingCount--;
            if(pendingCount<=0){ pendingCount=0; setRefreshIdle(); }
        });
}

var refreshBtn=document.getElementById('wpRefresh');
function setRefreshBusy(){ refreshBtn.disabled=true; refreshBtn.innerHTML='<i class="fas fa-sync-alt fa-spin"></i> রিফ্রেশ হচ্ছে…'; }
function setRefreshIdle(){ refreshBtn.disabled=false; refreshBtn.innerHTML='<i class="fas fa-sync-alt"></i> রিফ্রেশ'; }
refreshBtn.addEventListener('click',function(){
    cache={}; setRefreshBusy();
    document.getElementById('heroWrap').innerHTML='<div class="hero-skeleton"><i class="fas fa-spinner fa-spin"></i> আবহাওয়া আপডেট হচ্ছে…</div>';
    Object.keys(CITIES).forEach(fetchCity);
});

/* ── boot ── */
fetchCity('Dhaka');
['Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh'].forEach(fetchCity);
setInterval(function(){ Object.keys(CITIES).forEach(fetchCity); }, 600000);

})();
</script>

<?php require_once 'includes/footer.php'; ?>
