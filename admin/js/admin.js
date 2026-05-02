// Admin JS
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // Image URL preview
    const imgInput = document.getElementById('imageUrl');
    const imgPreview = document.getElementById('imagePreview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('input', function () {
            const url = this.value.trim();
            imgPreview.style.display = url ? 'block' : 'none';
            imgPreview.src = url;
        });
        if (imgInput.value) {
            imgPreview.style.display = 'block';
            imgPreview.src = imgInput.value;
        }
    }

    // Auto-generate slug from title
    const titleInput = document.getElementById('postTitle');
    const slugInput = document.getElementById('postSlug');
    if (titleInput && slugInput && !slugInput.dataset.locked) {
        titleInput.addEventListener('input', function () {
            const slug = this.value
                .toLowerCase()
                .replace(/[ঀ-৿]/g, function(c) { return c; })
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9ঀ-৿-]/g, '')
                .replace(/-+/g, '-')
                .substring(0, 200);
            slugInput.value = slug || Date.now();
        });
    }
    if (slugInput) {
        slugInput.addEventListener('input', function () {
            slugInput.dataset.locked = '1';
        });
    }

    // Confirm deletes
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    // Alert auto-dismiss
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (a) {
            a.style.transition = 'opacity 0.5s';
            a.style.opacity = '0';
            setTimeout(function () { a.remove(); }, 500);
        });
    }, 4000);
});
