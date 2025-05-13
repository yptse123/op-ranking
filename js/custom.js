$(document).ready(function () 
{
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

    const apiBaseUrl = 'api/';

    const bannerStats = {};

    function initBannerStats(bannerId) {
        if (!bannerStats[bannerId]) {
            bannerStats[bannerId] = { impressions: 0, clicks: 0 };
        }
    }

    function logAllBannerStats() {

        if (Object.keys(bannerStats).length === 0) {
            console.log('bannerStats empty');
            return;
        }

        jQuery.ajax({
            url: apiBaseUrl + "log_banner.php",
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-Token': csrfToken },
            data: JSON.stringify({ banner_stats: bannerStats, csrf_token: csrfToken }),
            success: function(response) {
                if (response.status === 'success') {
                    console.log(`log_banner.php logged:`, response.message);
                    Object.keys(bannerStats).forEach(bannerId => {
                        bannerStats[bannerId] = { impressions: 0, clicks: 0 };
                    });
                } else {
                    console.error(`log_banner.php failed:`, response.message);
                }
            },
            error: function(xhr, status, err) {
                console.error(`Error logging log_banner.php:`, status, err);
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
        initBannerStats(firstBannerId);
        bannerStats[firstBannerId].impressions++;
        // logEvent(firstBannerId, 'log_impression.php');
    }

    owl.on('changed.owl.carousel', function(event) {
        const currentItem = $(event.target).find('.owl-item').eq(event.item.index).find('.banner-item');
        const bannerId = currentItem.data('banner-id');
        if (bannerId) {
            initBannerStats(bannerId);
            bannerStats[bannerId].impressions++;
            // logEvent(bannerId, 'log_impression.php');
        }
    })

    $('.owl-carousel').on('click', '.banner-item .banner', function() {
        const bannerId = $(this).parent().data('banner-id');
        if (bannerId) {
            initBannerStats(bannerId);
            bannerStats[bannerId].clicks++;
            // logEvent(bannerId, 'log_click.php', function() {
            // });
        } else {
            console.error('invalid banner_id');
        }
    });

    setInterval(logAllBannerStats, 20000);

    let currentIndex = 0;

    function showAnnouncement(index) {
        if (index >= announcements.length) {
            $('#announcementModal').modal('hide');
            return;
        }

        const announcement = announcements[index];
        $('#announcementImage').attr('src', announcement.image_url);
        $('#announcementContent').html(announcement.content);
        $('#announcementModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    if (announcements.length > 0) {
        showAnnouncement(currentIndex);
    }

    $('#announcementModal').on('hidden.bs.modal', function (e) {
        currentIndex++;
        showAnnouncement(currentIndex);
    });
});