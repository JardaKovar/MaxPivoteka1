// Initialize delivery animation when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Use a small delay to ensure all elements are properly loaded
    setTimeout(initializeDeliveryAnimation, 100);
});

// Also initialize when page is fully loaded (fallback)
window.addEventListener('load', function() {
    setTimeout(initializeDeliveryAnimation, 200);
});

function initializeDeliveryAnimation() {
    const deliveryVan = document.querySelector('.delivery-item img');
    
    if (!deliveryVan) {
        console.log('Delivery van element not found, retrying...');
        // Retry after a short delay if element not found
        setTimeout(initializeDeliveryAnimation, 500);
        return;
    }
    
    console.log('Delivery animation initialized');
    
    // Remove any existing animation class to allow re-triggering
    deliveryVan.classList.remove('car-animation');
    
    // Create the Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add the animation class when the van becomes visible
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via intersection observer');
            } else {
                // Remove animation when not visible to allow re-triggering
                deliveryVan.classList.remove('car-animation');
            }
        });
    }, {
        threshold: 0.3, // Trigger when 30% of the element is visible (more sensitive)
        rootMargin: '0px 0px -20px 0px' // Trigger slightly before fully visible
    });

    // Start observing the delivery van
    observer.observe(deliveryVan);
    
    // Handle direct navigation to delivery section
    if (window.location.hash === '#Rozvoz') {
        setTimeout(() => {
            deliveryVan.classList.add('car-animation');
            console.log('Delivery animation triggered via direct navigation');
        }, 300);
    }
    
    // Listen for hash changes to handle navigation
    window.addEventListener('hashchange', function() {
        if (window.location.hash === '#Rozvoz') {
            setTimeout(() => {
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via hash change');
            }, 200);
        }
    });
    
    // Listen for clicks on navigation links (improved selector)
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target && target.getAttribute('href') === '#Rozvoz') {
            setTimeout(() => {
                deliveryVan.classList.add('car-animation');
                console.log('Delivery animation triggered via navigation click');
            }, 400);
        }
    });
    
    // Force animation on scroll to delivery section
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
