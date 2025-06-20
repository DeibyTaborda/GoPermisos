function initializeDataTable(tableId) {
    $(document).ready(function() {
        $(tableId).DataTable({
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip><"clear">',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            paging: true,
            info: false,
            searching: true,
            order: [[0, 'desc']]
        });
        
        // Tooltips para botones de acción
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    });
}

initializeDataTable('#departmentTable');
initializeDataTable('#leaveTable');
initializeDataTable('#leaveHistoryTable');
initializeDataTable('#sedeTable');