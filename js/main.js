(function ($) {
    $(function () {
        var $header = $('.site-header');
        var $toggle = $('.nav-toggle');

        if (!$header.length || !$toggle.length) {
            return;
        }

        $toggle.on('click', function () {
            var isOpen = !$header.hasClass('nav-open');

            $header.toggleClass('nav-open', isOpen);
            $toggle.attr('aria-expanded', isOpen ? 'true' : 'false');
        });

        $(document).on('keyup', function (event) {
            if (event.key === 'Escape' && $header.hasClass('nav-open')) {
                $header.removeClass('nav-open');
                $toggle.attr('aria-expanded', 'false');
            }
        });

        function setupTableFilter(config) {
            var $search = $(config.searchSelector);
            var $status = $(config.statusSelector);
            var $table = $(config.tableSelector);
            var $count = $(config.countSelector);

            if (!$search.length || !$status.length || !$table.length) {
                return;
            }

            function updateCount() {
                if (!$count.length) {
                    return;
                }

                $count.text($table.find('tbody tr:visible').length + ' items');
            }

            function applyFilter() {
                var searchTerm = $search.val().toString().trim().toLowerCase();
                var selectedStatus = $status.val().toString().trim().toLowerCase();

                $table.find('tbody tr').each(function () {
                    var $row = $(this);
                    var rowText = ($row.attr('data-search-text') || $row.text()).toLowerCase();
                    var rowStatus = ($row.attr('data-status') || '').toLowerCase();
                    var rowCategory = ($row.attr('data-category') || '').toLowerCase();
                    var matchesSearch = searchTerm === '' || rowText.indexOf(searchTerm) !== -1;
                    var matchesStatus = selectedStatus === '' || rowStatus === selectedStatus || rowCategory === selectedStatus;

                    $row.toggle(matchesSearch && matchesStatus);
                });

                updateCount();
            }

            $search.on('input', applyFilter);
            $status.on('change', applyFilter);
            applyFilter();
        }

        setupTableFilter({
            searchSelector: '#medicine-search',
            statusSelector: '#medicine-category',
            tableSelector: '[data-table="medicine-catalog"]',
            countSelector: '[data-count="medicine-catalog"]'
        });

        setupTableFilter({
            searchSelector: '#pharmacy-search',
            statusSelector: '#pharmacy-status',
            tableSelector: '[data-table="pharmacy-list"]',
            countSelector: '[data-count="pharmacy-list"]'
        });

        setupTableFilter({
            searchSelector: '#inventory-search',
            statusSelector: '#inventory-status',
            tableSelector: '[data-table="inventory-list"]',
            countSelector: '[data-count="inventory-list"]'
        });

        // Favorite Button Logic
        $('.favorite-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var pharmacyId = $btn.data('pharmacy-id');
            var isFavorited = $btn.data('is-favorited') == '1';
            var $heart = $btn.find('.heart-icon');
            
            var url = isFavorited ? '../customer/remove-favorite.php' : '../customer/add-favorite.php';
            // If we are at the root level (search-results.php), adjust url
            if (window.location.pathname.indexOf('/customer/') === -1) {
                url = url.replace('../', '');
            }

            $btn.prop('disabled', true);

            $.ajax({
                url: url,
                method: 'POST',
                data: JSON.stringify({ pharmacy_id: pharmacyId }),
                contentType: 'application/json',
                success: function(data) {
                    if (data.status === 'ok') {
                        if (isFavorited) {
                            $heart.text('🤍');
                            $btn.data('is-favorited', '0');
                            $btn.attr('title', 'Add to favorites');
                        } else {
                            $heart.text('❤️');
                            $btn.data('is-favorited', '1');
                            $btn.attr('title', 'Remove from favorites');
                        }
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        // Dashboard Remove Favorite Logic
        $('.remove-favorite').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var pharmacyId = $btn.data('pharmacy-id');
            var $card = $btn.closest('.pharmacy-card');

            if (!confirm('Are you sure you want to remove this pharmacy from favorites?')) {
                return;
            }

            $btn.prop('disabled', true);

            $.ajax({
                url: 'remove-favorite.php',
                method: 'POST',
                data: JSON.stringify({ pharmacy_id: pharmacyId }),
                contentType: 'application/json',
                success: function(data) {
                    if (data.status === 'ok') {
                        $card.fadeOut(function() {
                            $(this).remove();
                            // Update count if necessary or show empty state
                            var count = $('.pharmacy-card').length;
                            $('.panel-title').first().text('My Favorite Pharmacies (' + count + ')');
                            if (count === 0) {
                                location.reload(); // Quick way to show empty state
                            }
                        });
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        });
    });
}(window.jQuery));
