document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Delete vehicle
        const deleteButton = e.target.closest('.delete-vehicle');

        if (!deleteButton) {
            return;
        }

        const vehicleName = deleteButton.dataset.name;
        const vehicleId = deleteButton.dataset.id;

        confirmDelete({

            endpoint: 'delete-vehicle-process',

            id: vehicleId,

            name: vehicleName,

            type: null,

            task: 'delete',

            title: `"${vehicleName}" ይሰረዝ?`,

            warning:
                `የሰሌዳ ቁጥሩ <strong>"${vehicleName}"</strong> የሆነ ` +
                `ተሽከርካሪ/ማሽነሪ ከዝርዝር ለማስወገድ ነው።`,

            confirmText:
                '<i class="fas fa-trash-alt mr-1"></i> አዎ፣ ሰርዝ!',

            successText:
                `"${vehicleName}" ተሰርዟል።`,

            requireReason: true,

            requirePassword: true,

            onSuccess: () => {
    const table = $('#example1').DataTable();
    table.row($('#row-' + vehicleId)).remove().draw(false);
}
        });

    });

});