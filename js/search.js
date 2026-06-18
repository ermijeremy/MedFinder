(function ($) {
    function toRadians(value) {
        return value * Math.PI / 180;
    }

    function calculateDistanceKm(lat1, lng1, lat2, lng2) {
        var earthRadiusKm = 6371;
        var deltaLat = toRadians(lat2 - lat1);
        var deltaLng = toRadians(lng2 - lng1);
        var a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
            Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
            Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return earthRadiusKm * c;
    }

    function getVisibleCards($cards) {
        return $cards.filter(':visible');
    }

    $(function () {
        var $form = $('.filter-form');
        var $resetButton = $('[data-reset-filters]');
        var $cards = $('.result-card');
        var $resultsCount = $('.results-toolbar .pill');
        var $locationButton = $('[data-use-location]');
        var $locationMessage = $('[data-location-message]');
        var currentLocation = null;

        if (!$cards.length) {
            return;
        }

        function updateCount() {
            if ($resultsCount.length) {
                $resultsCount.text(getVisibleCards($cards).length + ' pharmacies');
            }
        }

        function updateEmptyState() {
            var $emptyState = $('[data-empty-state]');

            if (!$emptyState.length) {
                return;
            }

            $emptyState.prop('hidden', getVisibleCards($cards).length > 0);
        }

        function renderDistanceLabel($card, distanceKm) {
            var $details = $card.find('.card-details');
            var existingLabel = $card.find('[data-distance-label]');
            var labelText = distanceKm < 1 ? Math.round(distanceKm * 1000) + ' m away' : distanceKm.toFixed(1) + ' km away';

            if (existingLabel.length) {
                existingLabel.text(labelText);
                return;
            }

            $details.append('<span data-distance-label>' + labelText + '</span>');
        }

        function getSortValue($card, sortMode) {
            if (sortMode === 'price_asc') {
                return parseFloat($card.attr('data-price')) || Number.POSITIVE_INFINITY;
            }
            if (sortMode === 'price_desc') {
                return -(parseFloat($card.attr('data-price')) || 0);
            }

            if (sortMode === 'updated') {
                return parseFloat($card.attr('data-updated-minutes')) || Number.POSITIVE_INFINITY;
            }

            if (sortMode === 'nearest') {
                if (!currentLocation) {
                    return Number.POSITIVE_INFINITY;
                }

                return parseFloat($card.attr('data-distance-km')) || Number.POSITIVE_INFINITY;
            }

            return 0;
        }

        function sortVisibleCards() {
            var sortMode = $('#sort-results').val().toString();

            if (sortMode === 'nearest' && !currentLocation) {
                return;
            }

            var sortedCards = $cards.get().sort(function (leftElement, rightElement) {
                var leftValue = getSortValue($(leftElement), sortMode);
                var rightValue = getSortValue($(rightElement), sortMode);

                if (leftValue === rightValue && sortMode === 'nearest') {
                    return getSortValue($(leftElement), 'price_asc') - getSortValue($(rightElement), 'price_asc');
                }

                return leftValue - rightValue;
            });

            $('.result-list').append(sortedCards);
        }

        function applyFilters() {
            var searchTerm = $('#inline-search').val().toString().trim().toLowerCase();
            var selectedNeighborhood = $('#filter-neighborhood').val().toString().toLowerCase();
            var selectedStatus = $('input[name="status"]:checked').val().toString().toLowerCase();

            $cards.each(function () {
                var $card = $(this);
                var name = ($card.attr('data-name') || $card.find('h3').text()).toLowerCase();
                var neighborhood = ($card.attr('data-neighborhood') || '').toLowerCase();
                var status = ($card.attr('data-status') || '').toLowerCase();
                var matchesSearch = searchTerm === '' || name.indexOf(searchTerm) !== -1 || neighborhood.indexOf(searchTerm) !== -1;
                var matchesNeighborhood = selectedNeighborhood === '' || neighborhood === selectedNeighborhood;
                var matchesStatus = selectedStatus === '' || status === selectedStatus;

                $card.toggle(matchesSearch && matchesNeighborhood && matchesStatus);
            });

            updateCount();
            updateEmptyState();
            sortVisibleCards();
        }

        function setLocationMessage(message, isError) {
            if (!$locationMessage.length) {
                return;
            }

            $locationMessage.text(message);
            $locationMessage.toggleClass('note-error', Boolean(isError));
            $locationMessage.toggleClass('note-success', !isError);
        }

        function requestLocation() {
            if (!window.navigator.geolocation) {
                setLocationMessage('Location is not available in this browser.', true);
                return;
            }

            setLocationMessage('Requesting your location so we can rank nearby pharmacies.', false);

            window.navigator.geolocation.getCurrentPosition(function (position) {
                currentLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };

                $cards.each(function () {
                    var $card = $(this);
                    var latitude = parseFloat($card.attr('data-lat'));
                    var longitude = parseFloat($card.attr('data-lng'));

                    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                        return;
                    }

                    var distanceKm = calculateDistanceKm(
                        currentLocation.latitude,
                        currentLocation.longitude,
                        latitude,
                        longitude
                    );

                    $card.attr('data-distance-km', distanceKm.toFixed(2));
                    renderDistanceLabel($card, distanceKm);
                });

                setLocationMessage('Showing pharmacies sorted by proximity.', false);
                sortVisibleCards();
                applyFilters();
            }, function () {
                setLocationMessage('Location permission was denied. Pharmacies remain in their default order.', true);
            }, {
                enableHighAccuracy: false,
                timeout: 8000,
                maximumAge: 300000
            });
        }

        if ($form.length) {
            $form.on('change input', 'input, select', applyFilters);
        }

        if ($resetButton.length && $form.length) {
            $resetButton.on('click', function (event) {
                event.preventDefault();
                $form[0].reset();
                $cards.show();
                updateCount();
                if (currentLocation) {
                    sortByDistance();
                }
            });
        }

        if ($locationButton.length) {
            $locationButton.on('click', function () {
                requestLocation();
            });
        }

        $('#sort-results').on('change', function () {
            if ($(this).val() === 'nearest' && !currentLocation) {
                requestLocation();
            } else {
                sortVisibleCards();
            }
        });

        applyFilters();
        updateCount();
        updateEmptyState();

        if ($('#sort-results').val() === 'nearest' && !currentLocation) {
            requestLocation();
        }
    });
}(window.jQuery));
