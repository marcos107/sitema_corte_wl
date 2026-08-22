(function () {
    function isDataTableActive(table) {
        return !!(
            table
            && window.jQuery
            && $.fn
            && $.fn.DataTable
            && $.fn.DataTable.isDataTable(table)
        );
    }

    function normalizeText(value) {
        return String(value || '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function parseDateValue(value) {
        var match = normalizeText(value).match(
            /^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{2})(?::(\d{2}))?)?$/
        );

        if (!match) {
            return null;
        }

        var day = parseInt(match[1], 10);
        var month = parseInt(match[2], 10) - 1;
        var year = parseInt(match[3], 10);
        var hour = parseInt(match[4] || '0', 10);
        var minute = parseInt(match[5] || '0', 10);
        var second = parseInt(match[6] || '0', 10);

        var date = new Date(year, month, day, hour, minute, second);
        if (Number.isNaN(date.getTime())) {
            return null;
        }

        return date.getTime();
    }

    function parseNumberValue(value) {
        var normalized = normalizeText(value)
            .replace(/[%\s]/g, '')
            .replace(/\./g, '')
            .replace(',', '.');

        if (!/^-?\d+(?:\.\d+)?$/.test(normalized)) {
            return null;
        }

        var number = Number(normalized);
        return Number.isFinite(number) ? number : null;
    }

    function extractCellText(cell) {
        if (!cell) {
            return '';
        }

        if (cell.dataset && cell.dataset.sortValue) {
            return normalizeText(cell.dataset.sortValue);
        }

        var checkbox = cell.querySelector('input[type="checkbox"], input[type="radio"]');
        if (checkbox) {
            return checkbox.checked ? '1' : '0';
        }

        return normalizeText(cell.innerText || cell.textContent || '');
    }

    function coerceSortValue(value) {
        var text = normalizeText(value);
        if (text === '') {
            return { type: 'empty', value: '' };
        }

        var dateValue = parseDateValue(text);
        if (dateValue !== null) {
            return { type: 'date', value: dateValue };
        }

        var numberValue = parseNumberValue(text);
        if (numberValue !== null) {
            return { type: 'number', value: numberValue };
        }

        return { type: 'string', value: text.toLocaleLowerCase('pt-BR') };
    }

    function compareValues(left, right) {
        if (left.type === 'empty' && right.type === 'empty') {
            return 0;
        }

        if (left.type === 'empty') {
            return 1;
        }

        if (right.type === 'empty') {
            return -1;
        }

        if (left.type === right.type && (left.type === 'number' || left.type === 'date')) {
            return left.value - right.value;
        }

        return String(left.value).localeCompare(String(right.value), 'pt-BR', {
            numeric: true,
            sensitivity: 'base'
        });
    }

    function updateHeaderState(table, activeIndex, direction) {
        var headers = table.querySelectorAll('thead th');
        headers.forEach(function (header, index) {
            header.classList.remove('wl-sort-asc', 'wl-sort-desc');
            header.removeAttribute('aria-sort');
            header.dataset.wlSortDirection = '';

            if (index === activeIndex) {
                header.dataset.wlSortDirection = direction;
                header.classList.add(direction === 'asc' ? 'wl-sort-asc' : 'wl-sort-desc');
                header.setAttribute('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
            }
        });
    }

    function sortPlainTable(table, columnIndex, direction) {
        if (!table || isDataTableActive(table) || !table.tBodies.length) {
            return;
        }

        var tbody = table.tBodies[0];
        var rows = Array.from(tbody.rows);
        if (rows.length <= 1) {
            updateHeaderState(table, columnIndex, direction);
            return;
        }

        var mappedRows = rows.map(function (row, originalIndex) {
            var cell = row.cells[columnIndex];
            return {
                row: row,
                originalIndex: originalIndex,
                sortValue: coerceSortValue(extractCellText(cell))
            };
        });

        mappedRows.sort(function (left, right) {
            var result = compareValues(left.sortValue, right.sortValue);
            if (result === 0) {
                result = left.originalIndex - right.originalIndex;
            }

            return direction === 'desc' ? result * -1 : result;
        });

        var fragment = document.createDocumentFragment();
        mappedRows.forEach(function (item) {
            fragment.appendChild(item.row);
        });
        tbody.appendChild(fragment);

        updateHeaderState(table, columnIndex, direction);
    }

    function headerIsSortable(header) {
        if (!header) {
            return false;
        }

        if (header.dataset && header.dataset.wlNosort === 'true') {
            return false;
        }

        if (header.classList.contains('wl-col-acoes') || header.classList.contains('wl-no-sort')) {
            return false;
        }

        var title = normalizeText(header.innerText || header.textContent || '').toLowerCase();
        return title !== 'acao' && title !== 'acoes';
    }

    function enhancePlainTable(table) {
        if (!table || table.dataset.wlSortReady === '1' || !table.tHead || !table.tBodies.length) {
            return;
        }

        var headers = table.querySelectorAll('thead th');
        if (!headers.length) {
            return;
        }

        headers.forEach(function (header, index) {
            if (!headerIsSortable(header)) {
                return;
            }

            header.classList.add('wl-sortable-header');
            header.tabIndex = 0;
            header.setAttribute('role', 'button');

            var sortHandler = function (event) {
                if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                if (isDataTableActive(table)) {
                    return;
                }

                event.preventDefault();
                var currentDirection = header.dataset.wlSortDirection === 'asc' ? 'asc' : 'desc';
                var nextDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                sortPlainTable(table, index, nextDirection);
            };

            header.addEventListener('click', sortHandler);
            header.addEventListener('keydown', sortHandler);
        });

        table.dataset.wlSortReady = '1';
    }

    function enhanceAllPlainTables(root) {
        var scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll('table.table').forEach(enhancePlainTable);
    }

    function ensureStyles() {
        if (document.getElementById('wl-table-sort-style')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'wl-table-sort-style';
        style.textContent = [
            'table.table thead th.wl-sortable-header { cursor: pointer; user-select: none; position: relative; }',
            'table.table thead th.wl-sortable-header::after { content: "\\2195"; margin-left: .4rem; font-size: .78em; opacity: .35; }',
            'table.table thead th.wl-sortable-header.wl-sort-asc::after { content: "\\2191"; opacity: .9; }',
            'table.table thead th.wl-sortable-header.wl-sort-desc::after { content: "\\2193"; opacity: .9; }'
        ].join('');
        document.head.appendChild(style);
    }

    function scheduleEnhancement() {
        window.clearTimeout(scheduleEnhancement._timer);
        scheduleEnhancement._timer = window.setTimeout(function () {
            enhanceAllPlainTables(document);
        }, 50);
    }

    ensureStyles();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            enhanceAllPlainTables(document);
        });
    } else {
        enhanceAllPlainTables(document);
    }

    if (window.jQuery) {
        $(document).ajaxComplete(function () {
            scheduleEnhancement();
        });
    }

    document.addEventListener('shown.bs.modal', function (event) {
        enhanceAllPlainTables(event.target);
    });

    if (window.MutationObserver && document.body) {
        var observer = new MutationObserver(function () {
            scheduleEnhancement();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    window.WLTableSort = {
        enhanceAll: enhanceAllPlainTables,
        sortTable: sortPlainTable
    };
})();
