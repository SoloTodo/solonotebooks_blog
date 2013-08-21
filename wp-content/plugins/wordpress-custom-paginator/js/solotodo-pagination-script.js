jQuery(function() {
    jQuery('.custom_paginator').change(function() {
        window.location = jQuery('.custom_paginator').val();
    });
});