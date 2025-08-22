document.addEventListener('DOMContentLoaded', function () {
    var buttons = document.querySelectorAll('button[name="vacancy_scraper_ua_action"]');

    buttons.forEach(function (btn) {
        var action = btn.value;

        if (action === 'delete_workua' || action === 'delete_rabotaua') {
            btn.addEventListener('click', function (e) {
                if (!confirm(VacancyScraperUA.confirmDelete)) {
                    e.preventDefault();
                }
            });
        }

        if (action === 'fetch_workua' || action === 'fetch_rabotaua') {
            btn.addEventListener('click', function (e) {
                if (!confirm(VacancyScraperUA.confirmFetch)) {
                    e.preventDefault();
                }
            });
        }
    });
});
