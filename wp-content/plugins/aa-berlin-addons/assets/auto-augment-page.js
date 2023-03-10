(function (jQuery, __) {

    const regexQuotes = /"/g;
    const regexZoomMeetingId = /zoom\.us\/j\/(\d+)/i;
    const regexTriplets = /(\d{3})/g;

    const options = aa_berlin_addons_options;

    const streamDomains = String(options.stream_domains_pattern).split(/\s*,\s*/g);
    const msPerDay = 24 * 3600 * 1000;
    const msBeforeActivationOfStreams = 30 * 60 * 1000;
    const readableTimeBeforeActivationOfStreams = '30min';

    const now = new Date(options.server_time).getTime();
    const nowPlusOneWeek = new Date(options.server_time_plus_one_week).getTime();

    const markerTextWarning = String(options.warning_prefix).replace(regexQuotes, '');
    const markerTextSuccess = String(options.success_prefix).replace(regexQuotes, '');
    const markerTextInfo = String(options.info_prefix).replace(regexQuotes, '');

    // translators: %s is the zoom meeting id
    const zoomMeetingIdText = __('<abbr title="You can use this to access this meeting via phone.">Zoom Meeting ID #:</abbr><strong>%s</strong><em>xxx-xxx-xxx</em>', 'aa-berlin-addons');
    // translators: %s is the time until activation
    const zoomMeetingOutsideScheduleText = '<span class="aa-berlin-addons-outside-schedule">' + sprintf(__('The Meeting Link and Meeting ID will activate %s before the meeting starts.', 'aa-berlin-addons'), readableTimeBeforeActivationOfStreams) + '</span>';
    // translators: %s is the link's generated text (usually its host part)
    const externalLinkTextTemplate = __('External link to %s', 'aa-berlin-addons');
    // translators: %s is the link's generated text (usually the phone number)
    const phoneLinkTextTemplate = __('Call the number %s', 'aa-berlin-addons');

    const isStream = function (link) {
        const domain = new URL(link).host;
        let preconditionsMet = true;

        if (domain === 'zoom.us') {
            preconditionsMet = preconditionsMet && regexZoomMeetingId.test(link);
        }

        if (!preconditionsMet) {
            return false;
        }

        for (let i = 0, length = streamDomains.length; i < length; i ++) {
            if (domain === streamDomains[i]) {
                return true;
            }
        }

        return false;
    };

    let currentElementId = 0;
    const getId = function (el) {
        el = jQuery(el).eq(0);

        if (!el) {
            return null;
        }

        if (el.attr('id')) {
            return el.attr('id');
        }

        currentElementId = currentElementId + 1;

        const id = 'aa-berlin-addons-id-' + currentElementId;

        el.attr('id', id);

        return id;
    };

    const extractZoomMeetingId = function (link) {
        const match = regexZoomMeetingId.exec(link);

        if (match) {
            return match[1].replace(regexTriplets, '-$1').substr(1);
        }

        return null;
    };

    jQuery(function ($) {
        options.insert_notices && $('p:contains("' + markerTextWarning + '"), p:contains("' + markerTextSuccess + '"), p:contains("' + markerTextInfo + '")').each(function (i, paragraph) {
            paragraph = $(paragraph);
            paragraph.addClass('aa-berlin-addons-auto-highlight-notice');

            if (paragraph.is(':contains("' + markerTextWarning + '")')) {
                paragraph.addClass('type-warning');
            } else if (paragraph.is(':contains("' + markerTextSuccess + '")')) {
                paragraph.addClass('type-success');
            } else {
                paragraph.addClass('type-info');
            }
        });

        const augmentedLinkHintTemplate = $('#aa-berlin-addons-hint-for-augmented-links');
        options.insert_links && $('p, li').each(function (i, paragraph) {
            paragraph = $(paragraph);

            if (paragraph.children().length) {
                // will not try replace urls with auto links, if other markup present (an editor wrote it this way)
                return;
            }

            let domainEncountered = null;
            let httpLinkWasSubstituted = false;
            let phoneLinkWasSubstituted = false;
            let html = paragraph.text();

            html = html.replace(/https:\/\/([^/\s]+)([\S]*)/ig, function (link, domain, uri) {
                httpLinkWasSubstituted = true;
                domainEncountered = domain;

                const externalLinkText = sprintf(externalLinkTextTemplate, domain);
                link = link.replace(/[.?!]$/, '');

                let meetingIdHtml = '';
                if (options.append_zoom_meeting_id) {
                    const zoomMeetingId = extractZoomMeetingId(link);

                    if (zoomMeetingId) {
                        meetingIdHtml = sprintf(zoomMeetingIdText, zoomMeetingId);
                        meetingIdHtml = '<span class="aa-berlin-addons-auto-meeting-id">' + meetingIdHtml + '</span>';
                    }
                }

                const isExternal = domain !== location.host;

                return [
                    '<a href="',
                    link,
                    '" title="',
                    isExternal ? externalLinkText : '',
                    '" class="aa-berlin-addons-auto-link" ',
                    isExternal ? 'target="_blank"' : '',
                    '>',
                    domain,
                    '</a>',
                    meetingIdHtml
                ].join('');
            });

            html = html.replace(/\+\d+(?:\s*\(\d+\))?[\s\d]+(\d)/g, function (number) {
                phoneLinkWasSubstituted = true;

                const phoneLinkText = sprintf(phoneLinkTextTemplate, number);

                return [
                    '<a href="tel:',
                    number,
                    '" title="',
                    phoneLinkText,
                    '" class="aa-berlin-addons-auto-link">',
                    number,
                    '</a>'
                ].join('');
            });


            let hintId = null;
            if (httpLinkWasSubstituted) {
                const hints = $(augmentedLinkHintTemplate.html()).filter(':has([data-if-link-domain-is="' + domainEncountered + '"])');
                hintId = getId(hints);
                hints.insertAfter(paragraph);
            }

            if (phoneLinkWasSubstituted || httpLinkWasSubstituted) {
                paragraph.html(html);
                paragraph.addClass('aa-berlin-addons-contains-auto-link');
            }

            if (httpLinkWasSubstituted && hintId) {
                paragraph.find('a').attr('aria-describedBy', hintId);
            }
        });

        const deactivateLink = function (link) {
            // FIXME: ran into trouble with daylight saving time of future date, disabling
            //return;

            link.attr({
                'stream-href': link.attr('href'),
                'role': 'link',
                'aria-disabled': true,
            });
            link.removeAttr('href');
            link.parent().addClass('aa-berlin-addons-contains-disabled-auto-link');
        };

        options.disable_outside_schedule && $('.list-group-item.meeting-info').each(function (i, meetingInfo) {
            meetingInfo = $(meetingInfo);

            const link = meetingInfo.find('a[href]').filter(function (i, link) {
                return isStream(link);
            });

            if (!link.length) {
                return;
            }

            let startTime = meetingInfo.find('.meeting-time').attr('content');
            startTime = new Date(startTime);

            let endTime = meetingInfo.find('.meeting-time').attr('data-end-date');
            endTime = new Date(endTime);

            if (isNaN(startTime.getTime()) || isNaN(endTime.getTime())) {
                // no start or end time, do nothing
                return;
            }

            startTime = startTime.getTime();
            startTime = startTime - msBeforeActivationOfStreams;

            endTime = endTime.getTime();
            endTime = endTime + msBeforeActivationOfStreams;

            if (endTime < startTime) {
                endTime += msPerDay;
            }

            const isActive = nowPlusOneWeek >= startTime && nowPlusOneWeek <= endTime;

            const msTillActivation = startTime - nowPlusOneWeek;
            const msTillDeactivationAgain = endTime - nowPlusOneWeek;

            if (!isActive) {
                deactivateLink(link);
            }

            link.parent().prepend(zoomMeetingOutsideScheduleText);

            if (msTillActivation > 0) {
                setTimeout(function () {
                    link.attr('href', link.attr('stream-href'));
                    link.removeAttr('aria-disabled');
                    link.parent().removeClass('aa-berlin-addons-contains-disabled-auto-link');
                }, msTillActivation);
            }

            if (msTillDeactivationAgain > 0) {
                setTimeout(function () {
                    deactivateLink(link);
                }, msTillDeactivationAgain);
            }
        });

        options.wrap_single_entry_links_with_h2 && $('.wp-block-latest-posts').each(function (i, latestPosts) {
            latestPosts = $(latestPosts);

            if (latestPosts.children().length !== 1) {
                return;
            }

            latestPosts.find('> * > a[href]:first-child').insertBefore(latestPosts).wrap('<h2 class="aa-berlin-addons-auto-headline">');
        });
    });

    jQuery(function ($) {
        const template = $('#aa-berlin-addons-share-template').html();

        if (!template) {
            return;
        }

        $('#meeting #map').each(function (i, map) {
            const shareEl = $(template);
            const canvas = $('<canvas class="aa-berlin-addons-share-qr-image">');

            const url = $('link[rel="shortlink"]').attr('href') + '#';
            const name = $('#meeting h1').text().trim();
            const body = $(document.body);
            const isInactive = body.is('.attendance-inactive');
            const isOpen = !body.is('.tsml-type-c');
            const isOnline = !isInactive && !body.is('.attendance-hybrid, .attendance-in_person');
            const type = (isInactive ? 'INACTIVE, ' : '') + (isOnline ? 'Online Only, ' : '') + (isOpen ? 'Open' : 'Closed') + ' Meeting';
            const time = $('#meeting .meeting-info .meeting-time').clone().find('.zenzero-aa-timezone').remove().end().text().trim();
            const address = isOnline ? '' : '\n' + $('#meeting .location-address').html().replace(/[\r\n\s]*<br>[\r\n\s]*/g, ', ').trim();
            const text = url + '\n' + name + ' (' + type + ')\n' + time + address;

            QRCode.toCanvas(canvas.get(0), text, {
                margin: 0,
                errorCorrectionLevel: 'M',
                width: 180,
                color: {
                    dark: '#337ab7ff',
                    light: '#ffffffff',
                }
            }, function (error) {
                if (error) {
                    console.error('qerror', error);
                }
            });

            $('<dd class="aa-berlin-addons-share-qr">').append(canvas).appendTo(shareEl);
            $('<dd class="aa-berlin-addons-share-text">').html(text.replace(/\n/g, '<br>')).appendTo(shareEl);

            if (navigator.clipboard) {
                const button = $('<button type=button class="btn btn-default aa-berlin-addons-share-copy">').html(shareEl.data('copyLabel'));
                button.append('<i class="icon" data-feather=clipboard>').appendTo(shareEl);
                button.on('click', function () {
                    navigator.clipboard.writeText(text);
                });

                if (window.feather) {
                    setTimeout(function () {
                        feather.replace();
                    }, 10);
                }
            }

            shareEl.insertAfter(map);
        });
    });

})(jQuery, wp.i18n.__, wp.i18n.sprintf);
