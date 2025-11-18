// Global variables for tracking data changes and polling
let lastTapListData = null;
let lastRentalListData = null;
let lastEventsData = null;
let pollingIntervals = {};

document.addEventListener('DOMContentLoaded', function() {
    // Only load data if we're on the main page (index.php), not dashboard
    if (window.location.pathname.includes('index.php') || window.location.pathname === '/') {
        loadTapList();
        loadRentalList();
        loadEvents();
        loadPriceListImages();
        loadGalleryImages();
        startRealTimePolling();
    }
});

// Start independent polling for each section
function startRealTimePolling() {
    // Poll tap list every 10 seconds
    pollingIntervals.tapList = setInterval(async () => {
        await pollTapList();
    }, 10000);
    
    // Poll rental list every 10 seconds
    pollingIntervals.rentalList = setInterval(async () => {
        await pollRentalList();
    }, 10000);
    
    // Poll events every 10 seconds
    pollingIntervals.events = setInterval(async () => {
        await pollEvents();
    }, 10000);
}

// Stop all polling (useful for cleanup)
function stopRealTimePolling() {
    Object.values(pollingIntervals).forEach(interval => {
        if (interval) clearInterval(interval);
    });
    pollingIntervals = {};
}

// Show notification to user
function showUpdateNotification(sectionName) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'update-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">🔄</span>
            <span class="notification-text">${sectionName} byl aktualizován</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        border: 2px solid #fff;
        z-index: 10000;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Add animation styles to document if not already added
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .notification-icon {
                font-size: 16px;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Compare data to detect changes
function hasDataChanged(oldData, newData) {
    return JSON.stringify(oldData) !== JSON.stringify(newData);
}

// Poll tap list for changes
async function pollTapList() {
    try {
        const response = await fetch('get_taplist.php?t=' + Date.now());
        if (response.ok) {
            const tapList = await response.json();

            if (lastTapListData && hasDataChanged(lastTapListData, tapList)) {
                updateTapListTable(tapList);
                showUpdateNotification('Právě na čepu');
            }
            lastTapListData = tapList;
        }
    } catch (error) {
        console.log('Error polling tap list:', error);
    }
}

// Poll rental list for changes
async function pollRentalList() {
    try {
        const response = await fetch('get_rentallist.php?t=' + Date.now());
        if (response.ok) {
            const rentalList = await response.json();

            if (lastRentalListData && hasDataChanged(lastRentalListData, rentalList)) {
                updateRentalListTable(rentalList);
                showUpdateNotification('Půjčovna');
            }
            lastRentalListData = rentalList;
        }
    } catch (error) {
        console.log('Error polling rental list:', error);
    }
}

// Poll events for changes
async function pollEvents() {
    try {
        const response = await fetch('get_events.php?t=' + Date.now());
        if (response.ok) {
            const events = await response.json();

            if (lastEventsData && hasDataChanged(lastEventsData, events)) {
                updateEvents(events);
                showUpdateNotification('Akce');
            }
            lastEventsData = events;
        }
    } catch (error) {
        console.log('Error polling events:', error);
    }
}

// Initial load functions
async function loadTapList() {
    console.log('loadTapList called');
    try {
        const response = await fetch('get_taplist.php?t=' + Date.now());

        if (response.ok) {
            const tapList = await response.json();
            console.log('updateTapListTable called with data:', tapList);
            updateTapListTable(tapList);
            lastTapListData = tapList;
        }
    } catch (error) {
        console.log('Could not load tap list:', error);
    }
}

async function loadRentalList() {
    try {
        const response = await fetch('get_rentallist.php?t=' + Date.now());

        if (response.ok) {
            const rentalList = await response.json();
            updateRentalListTable(rentalList);
            lastRentalListData = rentalList;
        }
    } catch (error) {
        console.log('Could not load rental list:', error);
    }
}

async function loadEvents() {
    try {
        const response = await fetch('get_events.php?t=' + Date.now());

        if (response.ok) {
            const events = await response.json();
            updateEvents(events);
            lastEventsData = events;
        }
    } catch (error) {
        console.log('Could not load events:', error);
    }
}

// Load gallery images dynamically
async function loadGalleryImages() {
    try {
        const response = await fetch('get_gallery_images.php');
        if (response.ok) {
            const galleryData = await response.json();
            if (galleryData.images && galleryData.images.length > 0) {
                updateGallery(galleryData.images);
                return;
            }
        }
        
        // If no images found, keep the default static gallery
        console.log('No gallery images found, keeping static gallery');
        
    } catch (error) {
        console.log('Could not load gallery images:', error);
        // On error, keep the default static gallery
    }
}

// Load price list images dynamically
async function loadPriceListImages() {
    try {
        const pricingContainer = document.getElementById('pricing-image-container');
        
        // Try to get all images from the cenik folder
        const cenikResponse = await fetch('get_cenik_images.php');
        if (cenikResponse.ok) {
            const cenikData = await cenikResponse.json();
            if (cenikData.images && cenikData.images.length > 0) {
                updatePriceListImages(cenikData.images);
                return;
            }
        }
        
        // If no images found at all, show nothing (empty container)
        pricingContainer.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Žádný ceník není k dispozici</p>';
        
    } catch (error) {
        console.log('Could not load price list images:', error);
        // On error, show nothing
        const pricingContainer = document.getElementById('pricing-image-container');
        if (pricingContainer) {
            pricingContainer.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Ceník se nepodařilo načíst</p>';
        }
    }
}

// Render multiple price list images in the pricing container
function updatePriceListImages(images) {
    const pricingContainer = document.getElementById('pricing-image-container');
    if (!pricingContainer || !images || images.length === 0) return;

    // Clear existing content
    pricingContainer.innerHTML = '';

    // Create image elements for each price list image
    images.forEach(img => {
        const imgElement = document.createElement('img');
        imgElement.src = `images/cenik/${img}`;
        imgElement.alt = `Ceník - ${img}`;
        imgElement.className = 'pricing-list-image';
        pricingContainer.appendChild(imgElement);
    });
}

function updateTapListTable(tapData) {
    const tbody = document.querySelector('#Prave-na-cepu .tap-list tbody');

    if (!tbody || !tapData || tapData.length === 0) return;

    // Clear existing rows
    tbody.innerHTML = '';

    // Add new rows from dashboard data
    tapData.forEach((tap, index) => {
        if (tap.brewery || tap.beer) { // Only show rows with data
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${tap.number || index + 1}</td>
                <td>${tap.brewery || ''}</td>
                <td>${tap.beer || ''}</td>
                <td>${tap.alc || ''}</td>
                <td>${tap.epm || ''}</td>
                <td>${tap.ibu || ''}</td>
                <td>${tap.ebc || ''}</td>
                <td>${tap.price_05l || ''}</td>
                <td>${tap.price_03l || ''}</td>
            `;
            tbody.appendChild(row);
        }
    });
}

function updateRentalListTable(rentalData) {
    const tbody = document.getElementById('rental-list-tbody');

    if (!tbody || !rentalData || rentalData.length === 0) return;

    // Clear existing rows
    tbody.innerHTML = '';

    // Check if we're on dashboard.php (has image column) or index.php (has image column too now)
    const isDashboard = window.location.pathname.includes('dashboard.php');

    // Add new rows from dashboard data
    rentalData.forEach((rental, index) => {
        if (rental.desc1) { // Only show rows with data
            const row = document.createElement('tr');
            
            if (isDashboard) {
                // Dashboard version with image column for editing
                const imageSrc = rental.image ? `images/rental/${rental.image}` : getDefaultRentalImage(index);
                row.innerHTML = `
                    <td>${rental.number || index + 1}</td>
                    <td>${rental.desc1 || ''}</td>
                    <td><img src="${imageSrc}" alt="${rental.desc1 || 'Rental item'}" style="max-height: 80px;"></td>
                    <td>${rental.desc2 || ''}</td>
                    <td>${rental.deposit || ''}</td>
                    <td>${rental.day || ''}</td>
                    <td>${rental.weekend || ''}</td>
                    <td>${rental.week || ''}</td>
                    <td>${rental.month || ''}</td>
                `;
            } else {
                // Index.php version with image column
                const imageSrc = rental.image ? `images/rental/${rental.image}` : getDefaultRentalImage(index);
                row.innerHTML = `
                    <td>${rental.number || index + 1}</td>
                    <td>${rental.desc1 || ''}</td>
                    <td><img src="${imageSrc}" alt="${rental.desc1 || 'Rental item'}" style="max-height: 80px;"></td>
                    <td>${rental.desc2 || ''}</td>
                    <td>${rental.deposit || ''}</td>
                    <td>${rental.day || ''}</td>
                    <td>${rental.weekend || ''}</td>
                    <td>${rental.week || ''}</td>
                    <td>${rental.month || ''}</td>
                `;
            }
            tbody.appendChild(row);
        }
    });
}

function updateGallery(galleryImages) {
    const galleryContainer = document.querySelector('#Galerie .gallery-container');
    if (!galleryContainer || !galleryImages || galleryImages.length === 0) return;

    // Clear existing gallery items
    galleryContainer.innerHTML = '';

    // Add new gallery items
    galleryImages.forEach((img, index) => {
        const galleryItem = document.createElement('div');
        galleryItem.className = 'gallery-item';
        
        galleryItem.innerHTML = `
            <img src="images/gallery/${img}" alt="Galerie ${index + 1}" loading="lazy">
        `;
        galleryContainer.appendChild(galleryItem);
    });
    
    // Reinitialize gallery effects after updating
    if (typeof initializeGalleryEffects === 'function') {
        initializeGalleryEffects();
    }
}

function updateEvents(eventsData) {
    const eventsContainer = document.querySelector('#Akce .events-container');

    if (!eventsContainer || !eventsData || eventsData.length === 0) return;

    // Get existing event cards
    const existingCards = eventsContainer.querySelectorAll('.event-card');
    
    // Update or create events based on data
    eventsData.forEach((event, index) => {
        if (event.title || event.description) { // Only show events with data
            let eventCard = existingCards[index];
            
            if (eventCard) {
                // Update existing card
                const dateElement = eventCard.querySelector('.event-date');
                const titleElement = eventCard.querySelector('h3');
                const descriptionElement = eventCard.querySelector('p');
                
                if (dateElement) dateElement.textContent = event.date || '';
                if (titleElement) titleElement.textContent = event.title || '';
                if (descriptionElement) descriptionElement.textContent = event.description || '';
            } else {
                // Create new card if it doesn't exist
                eventCard = document.createElement('div');
                eventCard.className = 'event-card';
                
                eventCard.innerHTML = `
                    <div class="event-date">${event.date || ''}</div>
                    <h3>${event.title || ''}</h3>
                    <p>${event.description || ''}</p>
                `;
                eventsContainer.appendChild(eventCard);
            }
        }
    });
    
    // Remove extra cards if there are more existing cards than data
    for (let i = eventsData.length; i < existingCards.length; i++) {
        if (existingCards[i]) {
            existingCards[i].remove();
        }
    }
}

function getDefaultRentalImage(index) {
    // Default images for rental items - now in rental directory
    const defaultImages = [
        'images/rental/grill.png',
        'images/rental/pípa.png', 
        'images/rental/pivset (2).png',
        'images/rental/bomba.png'
    ];
    return defaultImages[index] || 'images/rental/grill.png';
}
