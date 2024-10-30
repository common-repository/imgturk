jQuery(document).ready(function() {

    jQuery('.imgturk-widget').on('click', '.imgturk-media-item', function() {
        var id = jQuery(this).data('id');
    });

    jQuery('.imgturk-widget').each(function(i, e) {
        var element = jQuery(e);
        var id = element.data('id');
        var type = element.data('type');
        var linkto = element.data('linkto');
        var rows = parseInt(element.data('rows')) || '3';
        var columns = parseInt(element.data('columns')) || '3';

        var maxResults = rows * columns;
        var url = 'https://imgturk.com' + (type == 'user' ? '/_api/user/' + id : '/_api/tag/' + id);

        function renderError() {
            element.find('.imgturk-media')
                .text('No media found')
                .addClass('imgturk-error');
        }

        jQuery.ajax({
            url: url,
            jsonp: 'callback',
            dataType: 'jsonp',
        }).done(function(data) {
            var mediaList = element.find('.imgturk-media');
            mediaList.html('');
            mediaList.parent().removeClass('loading');
            element.find('.loader').css('display', 'none');

            if (data.data) {
                var media = data.data.media;

                if (media.length > maxResults) {
                    media = media.slice(0, maxResults);
                }

                if (media.length == 0) {
                    renderError();
                } else {
                    jQuery(media).each(function(i, m) {
                        var link = 'https://instagram.com/p/' + m.id;
                        var html = jQuery('<div class="imgturk-media-item"><a target="_blank"><img></a></div>');
                        html.attr('id', 'imgturk-media-' + m.id);
                        html.data('id', m.id);
                        html.find('a').attr('href', link)
                        html.find('img')
                            .attr('src', m.thumbnail)
                            .attr('width', '200px')
                            .attr('height', '200px')
                            .on('load', function(e) {
                                jQuery(e.target).addClass('loaded');
                            });

                        mediaList.append(html);
                    });
                }
            } else {
                renderError();
            }
        });
    })
});
