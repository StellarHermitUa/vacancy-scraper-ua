document.querySelectorAll('.dropdown_header').forEach(header => {
    header.addEventListener('click', function (e) {
        e.stopPropagation();
        this.parentElement.classList.toggle('active');
    });
});

document.querySelectorAll('.dropdown_item').forEach(item => {
    item.addEventListener('click', function () {
        const text = this.textContent;
        const fvalue = this.dataset.value;

        const wrapper = this.closest('.dropdown_wrapper');
        wrapper.querySelector('.selected_text').textContent = text;
        wrapper.classList.remove('active');

        const jobFiltersBox = this.closest('.job_filters_box');
        const jobBox = jobFiltersBox ? jobFiltersBox.nextElementSibling : null;

        if (!jobBox || !jobBox.classList.contains('job_box')) return;

        const jobItems = jobBox.querySelectorAll('.job_item');

        if (fvalue === '') {
            jobItems.forEach(job => job.style.display = '');
        } else {
            jobItems.forEach(job => {
                const itemTags = job.dataset.city || '';
                job.style.display = itemTags.includes(fvalue) ? '' : 'none';
            });
        }
    });
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.dropdown_wrapper')) {
        document.querySelectorAll('.dropdown_wrapper').forEach(wrapper => {
            wrapper.classList.remove('active');
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
  const PAGINATION_LIMIT = 30;

  document.querySelectorAll('.vacancy_block').forEach(container => {
    const allItems = Array.from(container.querySelectorAll('.job_item'));
    const paginationWrapper = document.createElement('div');
    paginationWrapper.className = 'pagination_wrapper';
    container.appendChild(paginationWrapper);

    let currentCity = '';
    let currentPage = 1;

    const getFilteredItems = () =>
      allItems.filter(item => currentCity === '' || item.dataset.city === currentCity);

    const render = () => {
      const filtered = getFilteredItems();
      const start = (currentPage - 1) * PAGINATION_LIMIT;
      const end = start + PAGINATION_LIMIT;

      allItems.forEach(el => (el.style.display = 'none'));
      filtered.slice(start, end).forEach(el => (el.style.display = ''));

      renderPagination(filtered.length);
    };

    const renderPagination = total => {
      const pageCount = Math.ceil(total / PAGINATION_LIMIT);
      paginationWrapper.innerHTML = '';
      if (pageCount <= 1) return;

      const maxVisible = 5;

      const addButton = (n, label = null, isDisabled = false) => {
        const btn = document.createElement('button');
        btn.textContent = label || n;
        if (isDisabled) {
          btn.disabled = true;
          btn.classList.add('dots');
        } else {
          if (n === currentPage) btn.classList.add('active');
          btn.addEventListener('click', () => {
            currentPage = n;
            render();
          });
        }
        paginationWrapper.appendChild(btn);
      };

      if (pageCount <= maxVisible + 2) {
        for (let i = 1; i <= pageCount; i++) addButton(i);
      } else {
        addButton(1);
        if (currentPage > 3) addButton(null, '...', true);

        const start = Math.max(2, currentPage - 1);
        const end = Math.min(pageCount - 1, currentPage + 1);
        for (let i = start; i <= end; i++) addButton(i);

        if (currentPage < pageCount - 2) addButton(null, '...', true);
        addButton(pageCount);
      }
    };

    const filterBox = container.querySelector('.job_filters_box');
    if (filterBox) {
      filterBox.addEventListener('click', e => {
        if (e.target.classList.contains('dropdown_item')) {
          currentCity = e.target.dataset.value;
          currentPage = 1;

          const selectedText = filterBox.querySelector('.selected_text');
          if (selectedText) selectedText.textContent = e.target.textContent;

          container.querySelector('.dropdown_wrapper')?.classList.remove('active');
          render();
        }
      });
    }

    render();
  });
});


