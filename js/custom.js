$(document).ready(function () 
{
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

    const apiBaseUrl = 'api/';

    function logEvent(bannerId, endpoint, onSuccess) {
        if (!bannerId || !Number.isInteger(bannerId) || bannerId <= 0) {
            console.error('invalid banner_id:', bannerId);
            return;
        }

        jQuery.ajax({
            url: apiBaseUrl + endpoint,
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-Token': csrfToken },
            data: JSON.stringify({ banner_id: bannerId, csrf_token: csrfToken }),
            success: function(response) {
                if (response.status === 'success') {
                    console.log(`${endpoint} logged:`, response.log_id);
                if (onSuccess) onSuccess();
                } else {
                    console.error(`${endpoint} failed:`, response.message);
                }
            },
            error: function(xhr, status, err) {
                console.error(`Error logging ${endpoint}:`, status, err);
                if (xhr.status === 403) {
                    console.error('CSRF verify fail');
                }
            }
            });
        }

	const owl = $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        autoplay:true,
        autoplayTimeout: 5000,
        items: 1,
    })

    const firstItem = $('.owl-carousel .owl-item.active .banner-item');
    const firstBannerId = firstItem.data('banner-id');
    if (firstBannerId) {
        logEvent(firstBannerId, 'log_impression.php');
    }

    owl.on('changed.owl.carousel', function(event) {
        const currentItem = $(event.target).find('.owl-item').eq(event.item.index).find('.banner-item');
        const bannerId = currentItem.data('banner-id');
        if (bannerId) {
            logEvent(bannerId, 'log_impression.php');
        }
    })

    $('.owl-carousel').on('click', '.banner-item .banner', function() {
        const bannerId = $(this).parent().data('banner-id');
        if (bannerId) {
            logEvent(bannerId, 'log_click.php', function() {
            });
        } else {
            console.error('invalid banner_id');
        }
    });
});