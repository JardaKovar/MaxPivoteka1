// Dashboard data loading functionality
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

async function loadDashboardData() {
    try {
        // Load tap list data
        const tapResponse = await fetch('data/taplist.json');
        if (tapResponse.ok) {
            const tapData = await tapResponse.json();
            populateTapForm(tapData);
        }
    } catch (error) {
        console.log('Could not load tap data');
    }

    try {
        // Load rental list data
        const rentalResponse = await fetch('data/rentallist.json');
        if (rentalResponse.ok) {
            const rentalData = await rentalResponse.json();
            populateRentalForm(rentalData);
        }
    } catch (error) {
        console.log('Could not load rental data');
    }

    try {
        // Load events data
        const eventsResponse = await fetch('data/events.json');
        if (eventsResponse.ok) {
            const eventsData = await eventsResponse.json();
            populateEventsForm(eventsData);
        }
    } catch (error) {
        console.log('Could not load events data');
    }

    // Load gallery images
    loadGalleryImages();
    loadCenikImages();
}

function populateTapForm(tapData) {
    tapData.forEach((tap, index) => {
        if (index < 5) { // Only populate first 5 rows
            const breweryInput = document.querySelector(`input[name="taplist[${index}][brewery]"]`);
            const beerInput = document.querySelector(`input[name="taplist[${index}][beer]"]`);
            const alcInput = document.querySelector(`input[name="taplist[${index}][alc]"]`);
            const epmInput = document.querySelector(`input[name="taplist[${index}][epm]"]`);
            const price05lInput = document.querySelector(`input[name="taplist[${index}][price_05l]"]`);

            if (breweryInput) breweryInput.value = tap.brewery || '';
            if (beerInput) beerInput.value = tap.beer || '';
            if (alcInput) alcInput.value = tap.alc || '';
            if (epmInput) epmInput.value = tap.epm || '';
            if (price05lInput) price05lInput.value = tap.price_05l || '';
        }
    });
}

function populateRentalForm(rentalData) {
    rentalData.forEach((rental, index) => {
        if (index < 5) { // Only populate first 5 rows
            const desc1Input = document.querySelector(`input[name="rentallist[${index}][desc1]"]`);
            const desc2Input = document.querySelector(`input[name="rentallist[${index}][desc2]"]`);
            const depositInput = document.querySelector(`input[name="rentallist[${index}][deposit]"]`);
            const dayInput = document.querySelector(`input[name="rentallist[${index}][day]"]`);
            const weekendInput = document.querySelector(`input[name="rentallist[${index}][weekend]"]`);
            const weekInput = document.querySelector(`input[name="rentallist[${index}][week]"]`);
            const monthInput = document.querySelector(`input[name="rentallist[${index}][month]"]`);

            if (desc1Input) desc1Input.value = rental.desc1 || '';
            if (desc2Input) desc2Input.value = rental.desc2 || '';
            if (depositInput) depositInput.value = rental.deposit || '';
            if (dayInput) dayInput.value = rental.day || '';
            if (weekendInput) weekendInput.value = rental.weekend || '';
            if (weekInput) weekInput.value = rental.week || '';
            if (monthInput) monthInput.value = rental.month || '';
        }
    });
}

function populateEventsForm(eventsData) {
    eventsData.forEach((event, index) => {
        if (index < 3) { // Only populate first 3 events
            const dateInput = document.querySelector(`input[name="events[${index}][date]"]`);
            const titleInput = document.querySelector(`input[name="events[${index}][title]"]`);
            const descriptionTextarea = document.querySelector(`textarea[name="events[${index}][description]"]`);

            if (dateInput) dateInput.value = event.date || '';
            if (titleInput) titleInput.value = event.title || '';
            if (descriptionTextarea) descriptionTextarea.value = event.description || '';
        }
    });
}

function loadGalleryImages() {
    // This would load existing gallery images for management
    // Implementation depends on how gallery images are stored
}

function loadCenikImages() {
    // This would load existing ceník images for management
    // Implementation depends on how ceník images are stored
}
