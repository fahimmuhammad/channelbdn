document.addEventListener('DOMContentLoaded', function () {

    // ===== DARK MODE =====
    var dmBtn  = document.getElementById('darkModeToggle');
    var dmIcon = document.getElementById('darkIcon');
    function syncDarkIcon() {
        if (!dmIcon) return;
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        dmIcon.className = dark ? 'fas fa-sun' : 'fas fa-moon';
        if (dmBtn) dmBtn.title = dark ? 'লাইট মোড' : 'ডার্ক মোড';
    }
    syncDarkIcon();
    if (dmBtn) {
        dmBtn.addEventListener('click', function () {
            var dark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (dark) {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('darkMode', '0');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('darkMode', '1');
            }
            syncDarkIcon();
        });
    }

    // ===== READING PROGRESS BAR =====
    var progressBar = document.getElementById('reading-progress');
    if (progressBar) {
        window.addEventListener('scroll', function () {
            var scrollTop  = window.scrollY || document.documentElement.scrollTop;
            var docHeight  = document.documentElement.scrollHeight - window.innerHeight;
            var pct        = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            progressBar.style.width = Math.min(100, pct) + '%';
        }, { passive: true });
    }

    // ===== HIJRI DATE =====
    var hijriEl = document.getElementById('hijri-date');
    if (hijriEl) {
        try {
            var parts = new Intl.DateTimeFormat('bn-BD', {
                calendar: 'islamic',
                day: 'numeric', month: 'long', year: 'numeric'
            }).formatToParts(new Date());
            var hijri = parts
                .filter(function(p){ return p.type !== 'era'; })
                .map(function(p){ return p.value; })
                .join('')
                .trim();
            hijriEl.textContent = hijri;
        } catch(e) { hijriEl.textContent = ''; }
    }

    // ===== LIVE CLOCK (Bengali digits) =====
    var clockEl = document.getElementById('live-clock');
    if (clockEl) {
        var bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        function toBn(n) { return String(n).split('').map(function(d){ return bnDigits[d] || d; }).join(''); }
        function tickClock() {
            var now = new Date();
            var h   = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            clockEl.textContent = toBn(h) + ':' + toBn(String(m).padStart(2,'0')) + ':' + toBn(String(s).padStart(2,'0')) + ' ' + ampm;
        }
        tickClock();
        setInterval(tickClock, 1000);
    }

    // ===== WEATHER WIDGET =====
    var weatherEl = document.getElementById('weather-display');
    if (weatherEl) {
        fetch('https://wttr.in/Dhaka?format=j1')
            .then(function(r){ return r.json(); })
            .then(function(d){
                var c   = d.current_condition[0];
                var t   = c.temp_C;
                var desc= c.lang_bn ? c.lang_bn[0].value : c.weatherDesc[0].value;
                weatherEl.innerHTML = '<i class="fas fa-cloud-sun"></i> ঢাকা <span class="w-temp">' + t + '°C</span> &middot; ' + desc;
            })
            .catch(function(){
                weatherEl.innerHTML = '<i class="fas fa-cloud-sun"></i> ঢাকা —°C';
            });
    }

    // ===== LIVE SEARCH AUTOCOMPLETE =====
    var searchInput = document.querySelector('.nav-search-box input[type="search"]');
    var searchForm  = document.querySelector('.nav-search-box form');
    if (searchInput && searchForm) {
        var acBox    = null;
        var acTimer  = null;
        var siteBase = searchInput.closest('form').getAttribute('action').replace('/search.php','');
        if (!siteBase) siteBase = '';

        function buildAutocomplete(results) {
            removeAutocomplete();
            if (!results.length) return;
            acBox = document.createElement('div');
            acBox.className = 'search-autocomplete';
            results.forEach(function(item) {
                var el = document.createElement('a');
                el.href = siteBase + '/single.php?slug=' + encodeURIComponent(item.slug);
                el.className = 'search-autocomplete-item';
                el.innerHTML =
                    '<img src="' + (item.image || '') + '" alt="">' +
                    '<div><div class="search-autocomplete-cat">' + item.category_name + '</div>' +
                    '<div class="search-autocomplete-title">' + item.title + '</div></div>';
                acBox.appendChild(el);
            });
            var navSearch = document.getElementById('searchBox');
            if (navSearch) { navSearch.style.position = 'relative'; navSearch.appendChild(acBox); }
        }

        function removeAutocomplete() {
            if (acBox) { acBox.remove(); acBox = null; }
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(acTimer);
            var q = this.value.trim();
            if (q.length < 2) { removeAutocomplete(); return; }
            acTimer = setTimeout(function () {
                fetch(siteBase + '/search_ajax.php?q=' + encodeURIComponent(q))
                    .then(function(r){ return r.json(); })
                    .then(buildAutocomplete)
                    .catch(function(){});
            }, 280);
        });

        document.addEventListener('click', function(e) {
            if (acBox && !acBox.contains(e.target) && e.target !== searchInput) removeAutocomplete();
        });
    }

    // ===== SEARCH TOGGLE =====
    var searchToggle = document.getElementById('searchToggle');
    var searchBox    = document.getElementById('searchBox');
    if (searchToggle && searchBox) {
        searchToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            searchBox.classList.toggle('open');
            if (searchBox.classList.contains('open')) {
                var inp = searchBox.querySelector('input');
                if (inp) inp.focus();
            }
        });
        document.addEventListener('click', function (e) {
            if (!searchBox.contains(e.target) && e.target !== searchToggle) {
                searchBox.classList.remove('open');
            }
        });
    }

    // ===== BACK TO TOP =====
    var backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function () {
            backToTop.classList.toggle('show', window.scrollY > 400);
        }, { passive: true });
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===== BREAKING NEWS TICKER DUPLICATE =====
    var ticker = document.getElementById('ticker');
    if (ticker && ticker.children.length > 0) {
        ticker.innerHTML += ticker.innerHTML;
    }

    // ===== SHARE BUTTONS =====
    document.querySelectorAll('.share-btn[data-share]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var type = this.dataset.share;
            var url  = encodeURIComponent(window.location.href);
            var title = encodeURIComponent(document.title);
            var shareUrl = '';
            if (type === 'fb') shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
            else if (type === 'tw') shareUrl = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + title;
            else if (type === 'wa') shareUrl = 'https://api.whatsapp.com/send?text=' + title + ' ' + url;
            else if (type === 'ln') shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + url;
            if (shareUrl) window.open(shareUrl, '_blank', 'width=600,height=400');
        });
    });

    // ===== BLUR-UP IMAGE LAZY LOAD =====
    if ('IntersectionObserver' in window) {
        var lazyObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) return;
                var img = entry.target;
                img.classList.add('blur-load');
                if (img.dataset.src) img.src = img.dataset.src;
                img.addEventListener('load', function() { img.classList.add('loaded'); }, { once: true });
                if (img.complete) img.classList.add('loaded');
                lazyObs.unobserve(img);
            });
        }, { rootMargin: '200px' });

        document.querySelectorAll('img[loading="lazy"]').forEach(function(img) {
            img.classList.add('blur-load');
            if (img.complete) img.classList.add('loaded');
            else {
                img.addEventListener('load', function(){ img.classList.add('loaded'); }, { once: true });
            }
            lazyObs.observe(img);
        });
    }

    // ===== READING TIME =====
    var content = document.querySelector('.post-content');
    var rtEl    = document.getElementById('readingTime');
    if (content && rtEl) {
        var words = content.innerText.trim().split(/\s+/).length;
        var mins  = Math.max(1, Math.ceil(words / 200));
        rtEl.textContent = mins + ' মিনিট পড়ার সময়';
    }

    // ===== RELATED POSTS CAROUSEL =====
    var track   = document.querySelector('.carousel-track');
    var prevBtn = document.querySelector('.carousel-btn.prev');
    var nextBtn = document.querySelector('.carousel-btn.next');
    if (track && prevBtn && nextBtn) {
        var idx    = 0;
        var cards  = track.querySelectorAll('.news-card');
        var visible = window.innerWidth > 600 ? 2 : 1;
        var maxIdx  = Math.max(0, cards.length - visible);

        function moveCarousel() {
            var cardW = cards[0] ? (cards[0].offsetWidth + 16) : 0;
            track.style.transform = 'translateX(-' + (idx * cardW) + 'px)';
            prevBtn.style.display = idx === 0 ? 'none' : 'flex';
            nextBtn.style.display = idx >= maxIdx ? 'none' : 'flex';
        }
        prevBtn.addEventListener('click', function(){ if(idx>0){ idx--; moveCarousel(); } });
        nextBtn.addEventListener('click', function(){ if(idx<maxIdx){ idx++; moveCarousel(); } });
        window.addEventListener('resize', function(){
            visible = window.innerWidth > 600 ? 2 : 1;
            maxIdx = Math.max(0, cards.length - visible);
            idx = Math.min(idx, maxIdx);
            moveCarousel();
        });
        moveCarousel();
    }

    // ===== LIVE FEED PANEL =====
    var feedBtn     = document.getElementById('liveFeedBtn');
    var feedPanel   = document.getElementById('liveFeedPanel');
    var feedClose   = document.getElementById('liveFeedClose');
    var feedBody    = document.getElementById('liveFeedBody');
    var feedOverlay = document.getElementById('liveFeedOverlay');
    var feedTimer   = null;
    var siteUrl     = document.body.dataset.siteurl || '';

    function renderFeed(items) {
        if (!feedBody) return;
        feedBody.innerHTML = items.map(function(item) {
            return '<a href="' + siteUrl + '/single.php?slug=' + encodeURIComponent(item.slug) + '" class="live-feed-item">' +
                '<img src="' + (item.image || '') + '" alt="" loading="lazy">' +
                '<div><div class="live-feed-item-cat">' + item.category + '</div>' +
                '<div class="live-feed-item-title">' + item.title + '</div>' +
                '<div class="live-feed-item-time"><i class="far fa-clock"></i> ' + item.time + '</div></div></a>';
        }).join('');
    }

    function fetchFeed() {
        fetch(siteUrl + '/latest_feed.php')
            .then(function(r){ return r.json(); })
            .then(renderFeed)
            .catch(function(){});
    }

    function openFeed() {
        if (!feedPanel) return;
        feedPanel.classList.add('open');
        if (feedOverlay) feedOverlay.classList.add('show');
        if (!feedBody.children.length) fetchFeed();
        feedTimer = setInterval(fetchFeed, 60000);
    }

    function closeFeed() {
        if (feedPanel) feedPanel.classList.remove('open');
        if (feedOverlay) feedOverlay.classList.remove('show');
        clearInterval(feedTimer);
    }

    if (feedBtn)     feedBtn.addEventListener('click', openFeed);
    if (feedClose)   feedClose.addEventListener('click', closeFeed);
    if (feedOverlay) feedOverlay.addEventListener('click', closeFeed);

    // ===== CONFIRM DELETE BUTTONS =====
    document.querySelectorAll('[data-confirm]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

});
