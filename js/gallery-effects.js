
(function() {
    'use strict';
    
    let galleryImages = [];
    let modal = null;
    let modalImg = null;
    let closeBtn = null;
    let prevBtn = null;
    let nextBtn = null;
    let currentImageSpan = null;
    let totalImagesSpan = null;
    let currentImageIndex = 0;
    let imageScale = 1;
    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let translateX = 0;
    let translateY = 0;
    let isNavigating = false; // Prevent rapid navigation

    function initGallery() {
        console.log('Initializing gallery...');
        galleryImages = document.querySelectorAll('.gallery-item img');
        modal = document.querySelector('.gallery-modal');
        
        if (!modal) {
            console.error('Gallery modal not found');
            return;
        }
        
        if (!galleryImages.length) {
            console.error('No gallery images found');
            return;
        }
        modalImg = modal.querySelector('img');
        closeBtn = modal.querySelector('.gallery-modal-close');
        prevBtn = modal.querySelector('.gallery-prev');
        nextBtn = modal.querySelector('.gallery-next');
        currentImageSpan = modal.querySelector('.current-image');
        totalImagesSpan = modal.querySelector('.total-images');
        if (totalImagesSpan) {
            totalImagesSpan.textContent = galleryImages.length;
        }
        galleryImages.forEach(function(img, index) {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function(e) {
                handleImageClick(e, index);
            });
            
            img.addEventListener('mousedown', function(e) {
                if (e.button === 0) { // Left mouse button
                    handleImageClick(e, index);
                }
            });
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                showPrevImage();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                showNextImage();
            });
        }
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function(e) {
            if (modal.classList.contains('active')) {
                switch(e.key) {
                    case 'Escape':
                        closeModal();
                        break;
                    case 'ArrowLeft':
                        showPrevImage();
                        break;
                    case 'ArrowRight':
                        showNextImage();
                        break;
                }
            }
        });
        if (modalImg) {
            modalImg.addEventListener('wheel', handleZoom);
            modalImg.addEventListener('dblclick', resetZoom);
            modalImg.addEventListener('mousedown', startDrag);
            modalImg.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        }
        
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', endDrag);
        
        console.log('Gallery initialized with', galleryImages.length, 'images');
    }
    
    function handleImageClick(e, index) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Gallery image clicked:', index + 1);
        const img = galleryImages[index];
        img.classList.add('apple-effect');
        setTimeout(function() {
            img.classList.remove('apple-effect');
        }, 300);
        currentImageIndex = index;
        showImage(currentImageIndex);
        openModal();
    }
    
    function showImage(index) {
        if (index >= 0 && index < galleryImages.length && modalImg) {
            const img = galleryImages[index];
            modalImg.src = img.src;
            modalImg.alt = img.alt;
            
            if (currentImageSpan) {
                currentImageSpan.textContent = index + 1;
            }
            
            resetImageTransform();
            console.log('Showing image:', index + 1, 'of', galleryImages.length);
        }
    }
    
    function openModal() {
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            console.log('Modal opened');
            setTimeout(function() {
                if (modalImg) {
                    modalImg.style.opacity = '1';
                    modalImg.style.transform = 'scale(1)';
                }
            }, 50);
        }
    }
    
    function closeModal() {
        if (modal && modalImg) {
            modalImg.style.opacity = '0';
            modalImg.style.transform = 'scale(0.9)';
            document.body.style.overflow = '';
            
            setTimeout(function() {
                modal.classList.remove('active');
            }, 300);
            
            console.log('Modal closed');
        }
    }
    
    function showPrevImage() {
        if (isNavigating) return; // Prevent rapid navigation
        
        if (currentImageIndex > 0) {
            isNavigating = true;
            currentImageIndex--;
            showImage(currentImageIndex);
            console.log('Previous image');
            setTimeout(function() {
                isNavigating = false;
            }, 300);
        }
    }
    
    function showNextImage() {
        if (isNavigating) return; // Prevent rapid navigation
        
        if (currentImageIndex < galleryImages.length - 1) {
            isNavigating = true;
            currentImageIndex++;
            showImage(currentImageIndex);
            console.log('Next image');
            setTimeout(function() {
                isNavigating = false;
            }, 300);
        }
    }
    
    function resetImageTransform() {
        imageScale = 1;
        translateX = 0;
        translateY = 0;
        updateImageTransform();
    }
    
    function updateImageTransform() {
        if (modalImg) {
            modalImg.style.transform = 'scale(' + imageScale + ') translate(' + translateX + 'px, ' + translateY + 'px)';
        }
    }
    
    function handleZoom(e) {
        e.preventDefault();
        
        const zoomIntensity = 0.1;
        const wheel = e.deltaY < 0 ? 1 : -1;
        const zoom = Math.exp(wheel * zoomIntensity);
        
        imageScale *= zoom;
        imageScale = Math.min(Math.max(0.5, imageScale), 3);
        
        updateImageTransform();
    }
    
    function resetZoom(e) {
        e.preventDefault();
        resetImageTransform();
    }
    
    function startDrag(e) {
        if (imageScale <= 1) return;
        
        isDragging = true;
        modalImg.style.cursor = 'grabbing';
        
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        
        e.preventDefault();
    }
    
    function drag(e) {
        if (!isDragging) return;
        
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        
        updateImageTransform();
        e.preventDefault();
    }
    
    function endDrag() {
        if (isDragging) {
            isDragging = false;
            if (modalImg) {
                modalImg.style.cursor = imageScale > 1 ? 'grab' : 'default';
            }
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGallery);
    } else {
        initGallery();
    }
    setTimeout(initGallery, 100);
    window.initializeGalleryEffects = initGallery;
    
})();
