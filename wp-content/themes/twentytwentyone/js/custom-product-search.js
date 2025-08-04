jQuery(function($) {
    // Debug info
    console.log('Custom product search script loaded');
    console.log('REST API URL:', wc_search_params.rest_url);
    console.log('Nonce:', wc_search_params.nonce);
    
    // Target all WooCommerce search inputs
    var $searchInputs = $('input[type="search"], .search-field, .wp-block-search__input');
    
    if ($searchInputs.length === 0) {
        console.log('No search inputs found on page');
        return;
    }
    
    console.log('Found', $searchInputs.length, 'search inputs');
    
    $searchInputs.each(function() {
        var $input = $(this);
        
        $input.autocomplete({
            source: function(request, response) {
                // Show loading indicator
                $input.addClass('loading');
                console.log('Sending request to:', wc_search_params.rest_url);
                
                $.ajax({
                    url: wc_search_params.rest_url,
                    data: {
                        search: request.term
                    },
                    dataType: 'json',
                    beforeSend: function(xhr) {
                        console.log('Sending search request for:', request.term);
                        xhr.setRequestHeader('X-WP-Nonce', wc_search_params.nonce);
                    },
                    success: function(data) {
                        console.log('Search response:', data);
                        
                        // Remove loading indicator
                        $input.removeClass('loading');
                        
                        if (data && data.length) {
                            // Add "View all results" as the last item
                            data.push({
                                title: 'View all results',
                                url: '/cms/?s=' + request.term + '&post_type=product',
                                viewAll: true
                            });
                            response(data);
                        } else {
                            response([{
                                title: 'No products found',
                                url: '#',
                                noResults: true
                            }]);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search error:', status, error);
                        console.error('Response text:', xhr.responseText);
                        
                        // Remove loading indicator
                        $input.removeClass('loading');
                        
                        response([{
                            title: 'Error searching products',
                            url: '#',
                            noResults: true
                        }]);
                    }
                });
            },
            minLength: 2,
            delay: 300,
            select: function(event, ui) {
                if (!ui.item.noResults) {
                    window.location.href = ui.item.url;
                }
                return false;
            },
            focus: function(event, ui) {
                return false; // Prevent auto-filling of the search box on focus
            }
        })
        .data('ui-autocomplete')._renderItem = function(ul, item) {
            // Don't render anything for "no results" placeholder
            if (item.noResults) {
                return $('<li class="ui-autocomplete-no-results">')
                    .append('<div class="no-results">' + item.title + '</div>')
                    .appendTo(ul);
            }
            
            // Special case for "View all results"
            if (item.viewAll) {
                return $('<li class="ui-autocomplete-view-all">')
                    .append('<div class="view-all-results"><a href="' + item.url + '">View all results</a></div>')
                    .appendTo(ul);
            }
            
            // Create HTML for each suggestion item
            var html = '<div class="autocomplete-item">';
            
            // Add product image
            html += '<div class="autocomplete-image">';
            html += '<img src="' + item.image + '" alt="' + item.title + '">';
            html += '</div>';
            
            // Add product details
            html += '<div class="autocomplete-details">';
            html += '<div class="autocomplete-title">' + item.title + '</div>';
            
            // Add price if available
            if (item.price) {
                html += '<div class="autocomplete-price">' + item.price + '</div>';
            }
            
            // Add categories if available
            if (item.categories) {
                html += '<div class="autocomplete-category">' + item.categories + '</div>';
            }
            
            // Add stock status
            if (typeof item.in_stock !== 'undefined') {
                var stockClass = item.in_stock ? 'in-stock' : 'out-of-stock';
                var stockText = item.in_stock ? 'In Stock' : 'Out of Stock';
                html += '<div class="autocomplete-stock ' + stockClass + '">' + stockText + '</div>';
            }
            
            html += '</div>'; // Close details div
            html += '</div>'; // Close item div
            
            return $('<li>')
                .append(html)
                .appendTo(ul);
        };
    });
    
    // Handle Enter key in search box
    $(document).on('keydown', '.ui-autocomplete-input', function(e) {
        if (e.keyCode === 13 && $('.ui-autocomplete').is(':visible')) {
            e.preventDefault();
            $('.ui-state-focus').click();
        }
    });
    
    // Add loading indicator styles
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .woocommerce-product-search input[type="search"].loading,
            .search-field.loading {
                background-image: url('${wc_search_params.admin_url}images/spinner.gif');
                background-position: right 10px center;
                background-repeat: no-repeat;
                background-size: 20px 20px;
                padding-right: 40px;
            }
            
            .autocomplete-stock {
                font-size: 0.8em;
                margin-top: 2px;
            }
            
            .autocomplete-stock.in-stock {
                color: #7ad03a;
            }
            
            .autocomplete-stock.out-of-stock {
                color: #a44;
            }
        `)
        .appendTo('head');
});