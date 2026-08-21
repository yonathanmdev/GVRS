document.addEventListener('DOMContentLoaded', function() {

    document.addEventListener('click', function (e) {

        // ── Delete branch
        const deleteButton = e.target.closest('.delete-branch');

        if (deleteButton) {

            const type = deleteButton.dataset.name;

            const amharicTypes = {
                bureau: 'ቢሮ',
                authority: 'ባለስልጣን'
            };

            const amharicType = amharicTypes[type];

            confirmDelete({
                endpoint: 'delete-car-brand-process',

                id: deleteButton.dataset.id,

                name: deleteButton.dataset.name,

                type: type,

                task: 'delete',

                title: `"${deleteButton.dataset.name}" መኪና ይሰረዝ?`,

                warning:
                    `<strong>"${deleteButton.dataset.name}"</strong> ` +
                    `${amharicType} መኪና ከዝርዝር ለማስወገድ ነው።`,

                confirmText:
                    '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',

                successText:
                    ` መኪናዉ ተሰርዟል።`,

                requireReason: true,

                requirePassword: true,

                onSuccess: () =>
                    document
                        .getElementById(`row-${deleteButton.dataset.id}`)
                        ?.remove()
            });

            return;
        }
    });

});