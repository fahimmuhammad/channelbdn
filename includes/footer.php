<?php $footer_cats = getCategories(); ?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <!-- About -->
                <div>
                    <div class="footer-logo-text"><?= htmlspecialchars(getSetting('site_name') ?: SITE_NAME) ?></div>
                    <p class="footer-tagline"><?= htmlspecialchars(getSetting('site_tagline') ?: SITE_TAGLINE) ?></p>
                    <p class="footer-desc"><?= htmlspecialchars(getSetting('footer_about') ?: 'বাংলাদেশ ও বিশ্বের সকল খবর, ব্রেকিং নিউজ, লাইভ নিউজ, রাজনীতি, বাণিজ্য, খেলা, বিনোদনসহ সকল সর্বশেষ সংবাদ সবার আগে পড়তে ক্লিক করুন।') ?></p>
                    <div class="footer-social-row">
                        <a href="<?= htmlspecialchars(getSetting('facebook') ?: '#') ?>" class="footer-social-btn fb" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= htmlspecialchars(getSetting('youtube') ?: '#') ?>" class="footer-social-btn yt" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="<?= htmlspecialchars(getSetting('twitter') ?: '#') ?>" class="footer-social-btn tw" target="_blank"><i class="fab fa-x-twitter"></i></a>
                        <a href="<?= htmlspecialchars(getSetting('linkedin') ?: '#') ?>" class="footer-social-btn li" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?= htmlspecialchars(getSetting('instagram') ?: '#') ?>" class="footer-social-btn ig" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div class="footer-newsletter">
                        <p>প্রতিদিন মেইলে আপডেট পেতে সাবস্ক্রাইব করুন</p>
                        <div class="footer-newsletter-form" id="footerNlForm">
                            <input type="email" id="footerNlEmail" placeholder="আপনার ইমেইল লিখুন">
                            <button type="button" id="footerNlBtn"><i class="fas fa-paper-plane"></i></button>
                        </div>
                        <div id="footerNlMsg" style="font-size:.78rem;margin-top:6px;display:none"></div>
                    </div>
                </div>

                <!-- Categories 1 -->
                <div>
                    <h4 class="footer-heading">বিভাগসমূহ</h4>
                    <ul class="footer-links">
                        <?php foreach (array_slice($footer_cats, 0, 7) as $cat): ?>
                        <li><a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>"><i class="fas fa-angle-right"></i> <?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Categories 2 -->
                <div>
                    <h4 class="footer-heading">আরও বিভাগ</h4>
                    <ul class="footer-links">
                        <?php foreach (array_slice($footer_cats, 7) as $cat): ?>
                        <li><a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>"><i class="fas fa-angle-right"></i> <?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="#"><i class="fas fa-angle-right"></i> ভিডিও</a></li>
                        <li><a href="#"><i class="fas fa-angle-right"></i> ফটোগ্যালারি</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="footer-heading">যোগাযোগ</h4>
                    <ul class="footer-contact">
                        <li><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars(getSetting('address') ?: '১২৩, মতিঝিল বাণিজ্যিক এলাকা, ঢাকা-১০০০') ?></li>
                        <li><i class="fas fa-phone"></i> <?= htmlspecialchars(getSetting('site_phone') ?: '+880 1700-000000') ?></li>
                        <li><i class="fas fa-envelope"></i> <?= htmlspecialchars(getSetting('site_email') ?: 'info@channelbdn.com') ?></li>
                        <li><i class="fas fa-envelope"></i> বিজ্ঞাপন: <?= htmlspecialchars(getSetting('ads_email') ?: 'ads@channelbdn.com') ?></li>
                    </ul>
                    <div class="footer-app-row">
                        <a href="#" class="footer-app-btn"><i class="fab fa-google-play"></i> গুগল প্লে</a>
                        <a href="#" class="footer-app-btn"><i class="fab fa-apple"></i> অ্যাপ স্টোর</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-mid">
        <div class="container">
            <div class="footer-nav-links">
                <a href="#"><?= htmlspecialchars(getSetting('site_name') ?: SITE_NAME) ?></a>
                <a href="#">গোপনীয়তা নীতি</a>
                <a href="#">শর্তাবলি</a>
                <a href="#">মন্তব্য প্রকাশের নীতিমালা</a>
                <a href="#">বাংলা কনভার্টার</a>
                <a href="#">বিজ্ঞাপন</a>
                <a href="#">যোগাযোগ</a>
                <a href="#">ছুটির তালিকা</a>
                <a href="#">নিবাস</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>সম্পাদক: <?= htmlspecialchars(getSetting('editor_name') ?: 'সম্পাদক মহোদয়') ?> | প্রকাশক: <?= htmlspecialchars(getSetting('publisher_name') ?: 'প্রকাশক মহোদয়') ?> | <?= htmlspecialchars(getSetting('site_name') ?: SITE_NAME) ?> কর্তৃক প্রকাশিত।</p>
            <p class="footer-bottom-right">&copy; <?= date('Y') ?> <?= htmlspecialchars(getSetting('site_name') ?: SITE_NAME) ?> | ওয়েবসাইটের কোনো লেখা, ছবি, ভিডিও অনুমতি ছাড়া ব্যবহার করা বেআইনি।</p>
        </div>
    </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="উপরে যান"><i class="fas fa-chevron-up"></i></button>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>

<!-- Footer newsletter subscribe -->
<script>
(function(){
    var btn = document.getElementById('footerNlBtn');
    if (!btn) return;
    btn.addEventListener('click', function(){
        var email = document.getElementById('footerNlEmail').value.trim();
        var msg   = document.getElementById('footerNlMsg');
        if (!email) return;
        var fd = new FormData();
        fd.append('email', email);
        fetch('<?= SITE_URL ?>/newsletter_subscribe.php', { method:'POST', body:fd })
            .then(function(r){ return r.json(); })
            .then(function(d){
                msg.style.display = 'block';
                if (d.success) {
                    msg.style.color = '#4ade80';
                    msg.textContent = d.message;
                    document.getElementById('footerNlEmail').value = '';
                } else {
                    msg.style.color = '#f87171';
                    msg.textContent = d.message || 'একটি সমস্যা হয়েছে।';
                }
            });
    });
    document.getElementById('footerNlEmail').addEventListener('keydown', function(e){
        if (e.key === 'Enter') btn.click();
    });
})();
</script>

<!-- Google Translate -->
<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'bn',
        includedLanguages: 'en,bn',
        autoDisplay: false
    }, 'google_translate_element');
}
function setLang(lang) {
    document.getElementById('langBn').classList.toggle('active', lang === 'bn');
    document.getElementById('langEn').classList.toggle('active', lang === 'en');
    if (lang === 'en') {
        var sel = document.querySelector('.goog-te-combo');
        if (sel) { sel.value = 'en'; sel.dispatchEvent(new Event('change')); }
        else { setTimeout(function(){ setLang('en'); }, 500); }
    } else {
        var frame = document.querySelector('.goog-te-banner-frame');
        if (frame) {
            var d = frame.contentDocument || frame.contentWindow.document;
            var btns = d.querySelectorAll('button,a');
            for (var i = 0; i < btns.length; i++) {
                if (/original|বাংলা/i.test(btns[i].textContent)) { btns[i].click(); break; }
            }
        } else {
            var c = document.cookie.match(/googtrans=([^;]+)/);
            if (c) { document.cookie = 'googtrans=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT'; location.reload(); }
        }
    }
}
// Restore button state from cookie
(function(){
    var c = document.cookie.match(/googtrans=\/bn\/en/);
    if (c) {
        document.addEventListener('DOMContentLoaded', function(){
            var b = document.getElementById('langEn');
            var a = document.getElementById('langBn');
            if (b) b.classList.add('active');
            if (a) a.classList.remove('active');
        });
    }
})();
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
