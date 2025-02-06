jQuery(document).ready(function($) {
    const container = $('.battery-builder-container');
    const specForm = $('#battery-filter-form');
    const useCaseForm = $('#use-case-form');
    const resultsContainer = $('#battery-results');

    // Set initial state
    specForm.show().addClass('active');
    useCaseForm.hide().removeClass('active');

    // Handle path selection
    container.on('click', '.path-selector', function() {
        const path = $(this).data('path');
        
        // Hide both forms
        specForm.slideUp(300).removeClass('active');
        useCaseForm.slideUp(300).removeClass('active');
        resultsContainer.slideUp(300);

        // Show the selected form after a brief delay
        setTimeout(() => {
            if (path === 'specifications') {
                specForm.slideDown(300).addClass('active');
            } else if (path === 'use-case') {
                useCaseForm.slideDown(300).addClass('active');
            }
        }, 300);

        // Highlight active button
        $('.path-selector').removeClass('active');
        $(this).addClass('active');
    });

    // Handle specifications form submission
    specForm.on('click', '#build-battery-btn', function(e) {
        e.preventDefault();
        filterBatteries(specForm);
    });

    // Handle use case form submission
    useCaseForm.on('click', '#find-by-use-case-btn', function(e) {
        e.preventDefault();
        if (!useCaseForm.find('input[name="use_cases[]"]:checked').length) {
            alert('Please select at least one use case.');
            return;
        }
        filterBatteries(useCaseForm);
    });

    function filterBatteries(form) {
        const formData = new FormData();
        formData.append('action', 'filter_batteries');
        formData.append('nonce', byobData.nonce);

        // Add radio button values
        form.find('input[type="radio"]:checked').each(function() {
            formData.append($(this).attr('name'), $(this).val());
        });

        // Add checkbox values
        form.find('input[type="checkbox"]:checked').each(function() {
            formData.append($(this).attr('name'), $(this).val());
        });

        // Show loading state
        resultsContainer.html('<p class="loading">Finding the perfect battery for you...</p>').slideDown();

        // Make AJAX request
        $.ajax({
            url: byobData.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    if (response.data.count > 0) {
                        resultsContainer.html(response.data.html).slideDown();
                    } else {
                        resultsContainer.html('<p class="no-results">No batteries found matching your criteria. Please try different options.</p>').slideDown();
                    }
                    // Scroll to results
                    $('html, body').animate({
                        scrollTop: resultsContainer.offset().top - 50
                    }, 500);
                } else {
                    resultsContainer.html('<p class="error">Error loading results. Please try again.</p>').slideDown();
                }
            },
            error: function() {
                resultsContainer.html('<p class="error">Error connecting to server. Please try again.</p>').slideDown();
            }
        });
    }

    // If no use cases available, show specifications form by default
    if (!$('.selection-paths').length) {
        specForm.removeClass('hidden');
    }
}); 