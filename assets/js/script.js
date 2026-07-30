// Initialize Swiper for Product Sliders
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.swiper-products')) {
        const swiper = new Swiper('.swiper-products', {
            slidesPerView: 2,
            grid: {
                rows: 2,
                fill: 'row',
            },
            spaceBetween: 10,
            loop: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 3,
                    grid: { rows: 2 },
                    spaceBetween: 15,
                },
                1024: {
                    slidesPerView: 4,
                    grid: { rows: 2 },
                    spaceBetween: 20,
                },
            },
        });
    }

    // Back to Top Visibility
    const backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});

// Navigation Toggle
function toggleNav() {
    const nav = document.getElementById('main-nav');
    const btn = document.getElementById('hamburger-btn');
    const overlay = document.getElementById('nav-overlay');
    if (nav) nav.classList.toggle('open');
    if (btn) btn.classList.toggle('active');
    if (overlay) overlay.classList.toggle('open');
}

// Filter Modal Toggle
function toggleFilterModal() {
    const modal = document.getElementById('filter-modal');
    if (modal) {
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }
}

// Mobile Menu Toggle
function toggleMobileMenu() {
    const drawer = document.getElementById('mobile-drawer');
    const overlay = document.getElementById('mobile-overlay');
    const detailSidebar = document.getElementById('mobile-nav-sidebar');

    if (drawer && overlay) {
        // Case for header.php
        drawer.classList.toggle('translate-x-full');
        overlay.classList.toggle('hidden');
        
        if (!drawer.classList.contains('translate-x-full')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    } else if (detailSidebar) {
        // Case for header_detail.php
        detailSidebar.classList.toggle('translate-x-full');
        
        if (!detailSidebar.classList.contains('translate-x-full')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

// Mobile Submenu Toggle
function toggleMobileSubmenu(id) {
    const submenu = document.getElementById(id);
    const icon = document.getElementById('icon-' + id.split('-')[1]);
    if (submenu) {
        submenu.classList.toggle('hidden');
        if (icon) icon.classList.toggle('rotate-180');
    }
}

// Copy Link to Clipboard
function copyLink(btn) {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ Berhasil Salin';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}
