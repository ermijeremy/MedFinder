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
    });
}(window.jQuery));
