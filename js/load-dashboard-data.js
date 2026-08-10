
let lastTapListData = null;
let lastRentalListData = null;
let lastEventsData = null;
let pollingIntervals = {};

document.addEventListener('DOMContentLoaded', function() {
    if (!window.location.pathname.includes('dashboard.php')) {
        loadTapList();
        loadRentalList();
        loadEvents();
        loadCenik();
        loadGalleryImages();
        startRealTimePolling();
    }
});
function startRealTimePolling() {
    pollingIntervals.tapList = setInterval(async () => {
        await pollTapList();
    }, 10000);
    pollingIntervals.rentalList = setInterval(async () => {
        await pollRentalList();
    }, 10000);
    pollingIntervals.events = setInterval(async () => {
        await pollEvents();
    }, 10000);
}
function stopRealTimePolling() {
    Object.values(pollingIntervals).forEach(interval => {
        if (interval) clearInterval(interval);
    });
    pollingIntervals = {};
}
function showUpdateNotification(sectionName) {
    const notification = document.createElement('div');
    notification.className = 'update-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">🔄</span>
            <span class="notification-text">${sectionName} byl aktualizován</span>
        </div>
    `;
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
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
function hasDataChanged(oldData, newData) {
    return JSON.stringify(oldData) !== JSON.stringify(newData);
}
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
        console.log('No gallery images found, keeping static gallery');
        
    } catch (error) {
        console.log('Could not load gallery images:', error);
    }
}
async function loadCenik() {
    try {
        const response = await fetch('get_cenik.php?t=' + Date.now());
        if (response.ok) {
            const data = await response.json();
            const container = document.getElementById('keg-pricing-container');
            if (!container) return;

            container.innerHTML = '';

            let cenikItems = Array.isArray(data) ? data : (data.title ? [data] : []);

            if (cenikItems.length === 0) {
                container.innerHTML = '<p style="color: #94a3b8; padding: 20px; font-size: 1.1rem;">Žádný ceník není k dispozici.</p>';
                return;
            }

            cenikItems.forEach(item => {
                const wrapper = document.createElement('div');
                wrapper.className = 'keg-wrapper';
                wrapper.title = 'Klikněte pro stažení ' + (item.title || 'ceníku');

                wrapper.innerHTML = `
                    <img src="images/keg_cenik.png" alt="Pivní sud Ceník" class="keg-image">
                    <div class="keg-text-overlay">
                        <span class="keg-title">${item.title || 'Ceník'}</span>
                    </div>
                `;

                wrapper.onclick = function() {
                    wrapper.classList.remove('keg-shake');
                    void wrapper.offsetWidth; // trigger reflow
                    wrapper.classList.add('keg-shake');
                    setTimeout(() => {
                        const pdfUrl = item.pdf || 'uploads/cenik.pdf';
                        const link = document.createElement('a');
                        link.href = pdfUrl;
                        link.download = (item.title || 'Cenik') + '.pdf';
                        link.target = '_blank';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }, 350);
                };

                container.appendChild(wrapper);
            });
        }
    } catch (error) {
        console.log('Error loading cenik:', error);
    }
}
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
    tbody.innerHTML = '';
    tapData.forEach((tap, index) => {
        if (tap.brewery || tap.beer) { // Only show rows with data
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${tap.number || index + 1}</td>
                <td>${tap.brewery || ''}</td>
                <td>${tap.beer || ''}</td>
                <td>${tap.alc || ''}</td>
                <td>${tap.epm || ''}</td>
                <td>${tap.price_05l || ''}</td>
            `;
            tbody.appendChild(row);
        }
    });
}

function updateRentalListTable(rentalData) {
    const tbody = document.getElementById('rental-list-tbody');
    const rentalSection = document.getElementById('Pujcovna');

    if (!rentalData || rentalData.length === 0) {
        if (rentalSection) rentalSection.style.display = 'none';
        return;
    }

    if (!tbody) return;
    tbody.innerHTML = '';
    const isDashboard = window.location.pathname.includes('dashboard.php');

    if (isDashboard) {
        if (rentalSection) rentalSection.style.display = '';
        
        rentalData.forEach((rental, index) => {
            if (rental.desc1 || rental.desc2 || rental.deposit || rental.day || rental.weekend || rental.week || rental.month) { // Keep original condition for dashboard
                const row = document.createElement('tr');
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
                tbody.appendChild(row);
            }
        });
    } else {
        const visibleRentals = rentalData.filter(rental => rental.desc1 || rental.desc2); // Stricter: only if key descriptions present
        
        if (visibleRentals.length === 0) {
            if (rentalSection) rentalSection.style.display = 'none';
            return;
        } else {
            if (rentalSection) rentalSection.style.display = '';
        }
        const rentalSelect = document.getElementById('rental_item');
        if (rentalSelect) {
            let options = '<option value="">Vyberte předmět</option>';
            visibleRentals.forEach(rental => {
                if (rental.desc1 && rental.desc2) {
                    options += `<option value="${rental.desc1} - ${rental.desc2}">${rental.desc1} - ${rental.desc2}</option>`;
                }
            });
            rentalSelect.innerHTML = options;
        }

        visibleRentals.forEach((rental, visibleIndex) => {
            const row = document.createElement('tr');
            const displayNumber = (rental.number && rental.number > 0) ? rental.number : (visibleIndex + 1);
            const imageSrc = rental.image ? `images/rental/${rental.image}` : getDefaultRentalImage(visibleIndex);
            row.innerHTML = `
                <td>${displayNumber}</td>
                <td>${rental.desc1 || ''}</td>
                <td><img src="${imageSrc}" alt="${rental.desc1 || 'Rental item'}" style="max-height: 80px;"></td>
                <td>${rental.desc2 || ''}</td>
                <td>${rental.deposit || ''}</td>
                <td>${rental.day || ''}</td>
                <td>${rental.weekend || ''}</td>
                <td>${rental.week || ''}</td>
                <td>${rental.month || ''}</td>
            `;
            tbody.appendChild(row);
        });
    }
}

function updateGallery(galleryImages) {
    const galleryContainer = document.querySelector('#Galerie .gallery-container');
    if (!galleryContainer || !galleryImages || galleryImages.length === 0) return;
    galleryContainer.innerHTML = '';
    galleryImages.forEach((img, index) => {
        const galleryItem = document.createElement('div');
        galleryItem.className = 'gallery-item';
        
        galleryItem.innerHTML = `
            <img src="images/gallery/${img}" alt="Galerie ${index + 1}" loading="lazy">
        `;
        galleryContainer.appendChild(galleryItem);
    });
    if (typeof initializeGalleryEffects === 'function') {
        initializeGalleryEffects();
    }
}

function updateEvents(eventsData) {
    const eventsSection = document.getElementById('Akce');
    const eventsContainer = document.querySelector('#Akce .events-container');

    const validEvents = Array.isArray(eventsData) ? eventsData.filter(event => (event.title && event.title.trim()) || (event.description && event.description.trim())) : [];

    if (validEvents.length === 0) {
        if (eventsSection) eventsSection.style.display = 'none';
        const navLink = document.querySelector('.nav-links a[href="#Akce"]');
        if (navLink) navLink.style.display = 'none';
        return;
    }

    if (eventsSection) eventsSection.style.display = '';
    const navLink = document.querySelector('.nav-links a[href="#Akce"]');
    if (navLink) navLink.style.display = '';

    if (!eventsContainer) return;

    eventsContainer.innerHTML = '';
    validEvents.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';
        eventCard.innerHTML = `
            <div class="event-date">${event.date || ''}</div>
            <h3>${event.title || ''}</h3>
            <p>${event.description || ''}</p>
        `;
        eventsContainer.appendChild(eventCard);
    });
}

function getDefaultRentalImage(index) {
    const defaultImages = [
        'images/rental/grill.png',
        'images/rental/pípa.png', 
        'images/rental/pivset (2).png',
        'images/rental/bomba.png'
    ];
    return defaultImages[index] || 'images/rental/grill.png';
}


// Check and render site popup announcement
async function checkAndDisplayPopup() {
    try {
        const response = await fetch('get_popup.php?t=' + Date.now());
        if (!response.ok) return;

        const popup = await response.json();
        if (!popup || !popup.active) return;

        const now = new Date();

        // Check start date/time constraint
        if (popup.start_datetime) {
            const startDate = new Date(popup.start_datetime);
            if (now < startDate) return;
        }

        // Check end date/time constraint
        if (popup.end_datetime) {
            const endDate = new Date(popup.end_datetime);
            if (now > endDate) return;
        }

        // Check if user has already dismissed this specific popup in current session
        const popupKey = 'dismissed_popup_' + (popup.updated_at || popup.title || 'active');
        if (sessionStorage.getItem(popupKey)) return;

        const modal = document.getElementById('site-popup-modal');
        const titleEl = document.getElementById('site-popup-title');
        const textEl = document.getElementById('site-popup-text');
        const imgContainer = document.getElementById('site-popup-image-container');
        const imgEl = document.getElementById('site-popup-image');

        if (!modal) return;

        if (titleEl) titleEl.textContent = popup.title || 'Oznámení';
        if (textEl) textEl.textContent = popup.text || '';

        if (popup.image) {
            if (imgEl) imgEl.src = popup.image;
            if (imgContainer) imgContainer.style.display = 'block';
        } else {
            if (imgContainer) imgContainer.style.display = 'none';
        }

        modal.style.display = 'flex';
        window.currentPopupKey = popupKey;
    } catch (e) {
        console.log('Error loading popup:', e);
    }
}

function closeSitePopup() {
    const modal = document.getElementById('site-popup-modal');
    if (modal) modal.style.display = 'none';
    if (window.currentPopupKey) {
        sessionStorage.setItem(window.currentPopupKey, 'true');
    }
}

// Automatically check popup on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    if (!window.location.pathname.includes('dashboard.php')) {
        checkAndDisplayPopup();
    }
});
