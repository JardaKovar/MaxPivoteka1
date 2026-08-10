document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item img');
    
    galleryItems.forEach(img => {
        img.addEventListener('click', function() {
            const fullscreenContainer = document.createElement('div');
            fullscreenContainer.style.position = 'fixed';
            fullscreenContainer.style.top = '0';
            fullscreenContainer.style.left = '0';
            fullscreenContainer.style.width = '100%';
            fullscreenContainer.style.height = '100%';
            fullscreenContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
            fullscreenContainer.style.display = 'flex';
            fullscreenContainer.style.justifyContent = 'center';
            fullscreenContainer.style.alignItems = 'center';
            fullscreenContainer.style.zIndex = '1000';
            fullscreenContainer.style.cursor = 'pointer';

            const zoomedImg = document.createElement('img');
            zoomedImg.src = this.src;
            zoomedImg.style.maxWidth = '90%';
            zoomedImg.style.maxHeight = '90vh';
            zoomedImg.style.objectFit = 'contain';
            zoomedImg.style.transform = 'scale(0.9)';
            zoomedImg.style.transition = 'transform 0.3s ease';

            fullscreenContainer.appendChild(zoomedImg);
            document.body.appendChild(fullscreenContainer);
            zoomedImg.offsetHeight;
            zoomedImg.style.transform = 'scale(1)';

            fullscreenContainer.addEventListener('click', function() {
                zoomedImg.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    document.body.removeChild(fullscreenContainer);
                }, 300);
            });
        });
    });
});
