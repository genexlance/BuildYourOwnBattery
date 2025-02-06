jQuery(document).ready(function($) {
    console.log('Battery Options admin script loaded');
    
    const optionsContainer = $('.battery-options-container');
    const saveButton = $('#save-battery-options');
    const spinner = $('.spinner');
    const successNotice = $('.notice-success');
    const errorNotice = $('.notice-error');

    if (!saveButton.length) {
        console.error('Save button not found');
        return;
    }

    // Add new option
    optionsContainer.on('click', '.add-option', function(e) {
        e.preventDefault();
        const target = $(this).data('target');
        console.log('Adding new option for:', target);
        
        const optionList = $(`#${target}`);
        if (!optionList.length) {
            console.error(`Option list not found for target: ${target}`);
            return;
        }

        const newOption = $(`
            <div class="option-item">
                <input type="text" name="${target === 'voltage-options' ? 'voltage_options[]' : 
                                       target === 'capacity-options' ? 'capacity_options[]' : 
                                       'use_cases[]'}" 
                       class="regular-text">
                <button type="button" class="button remove-option">Remove</button>
            </div>
        `);
        optionList.append(newOption);
    });

    // Remove option
    optionsContainer.on('click', '.remove-option', function(e) {
        e.preventDefault();
        $(this).closest('.option-item').remove();
    });

    // Save options
    saveButton.on('click', function(e) {
        e.preventDefault();
        console.log('Save button clicked');

        // Hide any existing notices
        successNotice.hide();
        errorNotice.hide();
        
        // Show spinner
        spinner.addClass('is-active');

        const formData = new FormData();
        formData.append('action', 'byob_save_options');
        formData.append('nonce', byobSettings.nonce);

        // Get all voltage options
        const voltageOptions = [];
        $('#voltage-options input[name="voltage_options[]"]').each(function() {
            const value = $(this).val().trim();
            if (value) {
                voltageOptions.push(value);
                formData.append('voltage_options[]', value);
            }
        });

        // Get all capacity options
        const capacityOptions = [];
        $('#capacity-options input[name="capacity_options[]"]').each(function() {
            const value = $(this).val().trim();
            if (value) {
                capacityOptions.push(value);
                formData.append('capacity_options[]', value);
            }
        });

        // Get all use cases
        const useCases = [];
        $('#use-cases input[name="use_cases[]"]').each(function() {
            const value = $(this).val().trim();
            if (value) {
                useCases.push(value);
                formData.append('use_cases[]', value);
            }
        });

        // Get custom CSS
        const customCss = $('#custom-css').val();
        formData.append('custom_css', customCss);

        // Debug log to check what's being sent
        console.log('Sending data:', {
            voltage_options: voltageOptions,
            capacity_options: capacityOptions,
            use_cases: useCases,
            custom_css: customCss?.substring(0, 100) + '...' // Only show first 100 chars of CSS
        });

        // Log FormData contents
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: byobSettings.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Save response:', response);
                spinner.removeClass('is-active');
                
                if (response.success) {
                    successNotice.fadeIn();
                } else {
                    errorNotice.fadeIn();
                    console.error('Error saving options:', response);
                }
                
                // Hide notices after 3 seconds
                setTimeout(function() {
                    successNotice.fadeOut();
                    errorNotice.fadeOut();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error details:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                
                spinner.removeClass('is-active');
                errorNotice.fadeIn();
                
                // Hide error notice after 3 seconds
                setTimeout(function() {
                    errorNotice.fadeOut();
                }, 3000);
            }
        });
    });
}); 