
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeDeliveryAnimation, 100);
});
window.addEventListener('load', function() {
    setTimeout(initializeDeliveryAnimation, 200);
});

function initializeDeliveryAnimation() {
    const deliveryVan = document.querySelector('.delivery-item img');
    
    if (!deliveryVan) {
        console.log('Delivery van element not found, retrying...');
        setTimeout(initializeDeliveryAnimation, 500);
        return;
    }
    
    console.log('Delivery animation initialized');
    deliveryVan.classList.remove('car-animation');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via intersection observer');
            } else {
                deliveryVan.classList.remove('car-animation');
            }
        });
    }, {
        threshold: 0.3, // Trigger when 30% of the element is visible (more sensitive)
        rootMargin: '0px 0px -20px 0px' // Trigger slightly before fully visible
    });
    observer.observe(deliveryVan);
    if (window.location.hash === '#Rozvoz') {
        setTimeout(() => {
            deliveryVan.classList.add('car-animation');
            console.log('Delivery animation triggered via direct navigation');
        }, 300);
    }
    window.addEventListener('hashchange', function() {
        if (window.location.hash === '#Rozvoz') {
            setTimeout(() => {
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via hash change');
            }, 200);
        }
    });
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target && target.getAttribute('href') === '#Rozvoz') {
            setTimeout(() => {
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via navigation click');
            }, 400);
        }
    });
    window.addEventListener('scroll', function() {
        const deliverySection = document.getElementById('Rozvoz');
        if (deliverySection) {
            const rect = deliverySection.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
            
            if (isVisible && !deliveryVan.classList.contains('car-animation')) {
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via scroll detection');
            }
        }
    });
}
