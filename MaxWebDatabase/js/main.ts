let lastScrollTop = 0;

// Hide navbar on scroll down, show on scroll up
window.addEventListener('scroll', () => {
    const nav = document.querySelector<HTMLElement>('.main-nav');
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    
    if (nav) {
        if (currentScroll > lastScrollTop) {
            // Scrolling down
            nav.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            nav.style.transform = 'translateY(0)';
        }
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    }
});

// Smooth scrolling with TypeScript typings
document.querySelectorAll<HTMLAnchorElement>('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e: Event) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href')!);
        target?.scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Mobile menu functionality with type safety
const mobileMenu = (): void => {
    const nav: HTMLElement | null = document.querySelector('.nav-links');
    if (nav) {
        if (window.innerWidth < 768) {
            nav.classList.toggle('active');
        }
    }
}

// Close menu when clicking outside
document.addEventListener('click', (e: Event) => {
    const nav = document.querySelector('.nav-links');
    const menuButton = document.querySelector('.mobile-menu-btn');
    
    if (window.innerWidth < 768 && nav && menuButton) {
        if (!(e.target as Element).closest('.nav-container')) {
            nav.classList.remove('active');
        }
    }
});

// Update menu on resize
window.addEventListener('resize', () => {
    const nav = document.querySelector('.nav-links');
    if (nav && window.innerWidth >= 768) {
        nav.classList.remove('active');
        nav.removeAttribute('style');
    }
});


// Initialize mobile menu button with proper typing
const initMobileMenu = (): void => {
    const menuButton: HTMLDivElement = document.createElement('div');
    menuButton.className = 'mobile-menu-btn';
    menuButton.innerHTML = '☰';
    menuButton.addEventListener('click', mobileMenu);
    
    const navContainer: HTMLElement | null = document.querySelector('.nav-container');
    navContainer?.appendChild(menuButton);
}

// DOMContentLoaded with TypeScript event type
document.addEventListener('DOMContentLoaded', (): void => {
    if (window.innerWidth < 768) {
        initMobileMenu();
    }
});
