document.addEventListener('DOMContentLoaded', function() {

    document.addEventListener('click', function (e) {

        // ── Delete branch
        const deleteButton = e.target.closest('.delete-branch');

        if (deleteButton) {

            const type = deleteButton.dataset.type;

            const amharicTypes = {
                bureau: 'ቢሮ',
                authority: 'ባለስልጣን',
                commission: 'ኮሚሽን',    
                institution:'ኢንስቲቲዩት',
                enterprise: 'ኢንተርፕሪይዝ',
                memriya: 'መምሪያ',
                tsfet_bet: 'ጽፈት ቤት',
                college: 'ኮሌጅ',
                zone: 'ዞን',
                regio: 'ከተማ አስተዳደር',
                kifle_ketema: 'ክ/ከተማ',
                woreda: 'ወረዳ',
                ketema_woreda: 'ከተማ ወረዳ'
            };

            const amharicType = amharicTypes[type];

            confirmDelete({
                endpoint: 'delete-branch-process',

                id: deleteButton.dataset.id,

                name: deleteButton.dataset.name,

                type: type,

                task: 'delete',

                title: `"${deleteButton.dataset.name}" ይሰረዝ?`,

                warning:
                    `<strong>"${deleteButton.dataset.name}"</strong> ` +
                    `${amharicType} ከዝርዝር ለማስወገድ ነው።`,

                confirmText:
                    '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',

                successText:
                    `${amharicType} ተሰርዟል።`,

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