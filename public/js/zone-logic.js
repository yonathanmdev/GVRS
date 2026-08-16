document.addEventListener('DOMContentLoaded', function() {
    

    // ============================================================
    // 1. EDIT — ሞዳሉን መረጃ ሞልቶ መክፈት
    // ============================================================
    document.addEventListener('click', function (e) {

    const btn = e.target.closest('.edit-branch');

    if (!btn) {
        return;
    }

    const id   = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const type = btn.getAttribute('data-type');

    document.getElementById('edit_branch_id').value = id;
    document.getElementById('edit_branch_name').value = name;
    document.getElementById('edit_branch_type').value = type;

    // Show modal
    $('#editBranchModal').modal('show');
});

    // ============================================================
    // 2. EDIT — የተስተካከለውን መረጃ መላክ
    // ============================================================
 // ============================================================
// 2. EDIT — የተስተካከለውን መረጃ መላክ
// ============================================================
const editForm = document.getElementById('editBranchForm');

if (editForm) {

    editForm.addEventListener('submit', function (e) {

        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');

        // Disable button to prevent double-click
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> በማደስ ላይ...';

        // Update URL
        const targetUrl =
            (typeof BASE_URL !== 'undefined' ? BASE_URL : '') +
            '/?action=update-zone-process';

        fetch(targetUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        })

        .then(async response => {

            const text = await response.text();

            let data;

            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error('Invalid JSON response:', text);
                throw new Error(
                    'SERVER_INVALID_RESPONSE'
                );
            }

            if (!response.ok) {

                if (response.status === 403) {
                    throw new Error('FORBIDDEN');
                }

                throw new Error(
                    data.message || `HTTP ${response.status}`
                );
            }

            return data;
        })

        .then(data => {

            if (data.status === 'success') {

                $('#editBranchModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'ተሳክቷል!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });

            } else {

                Swal.fire(
                    'ስህተት',
                    data.message || 'መረጃውን ማደስ አልተቻለም።',
                    'error'
                );
            }
        })

        .catch(error => {

    console.error('Error Details:', error);

    if (
        error.message === 'FORBIDDEN' ||
        error.message === 'ACCESS_DENIED'
    ) {

        Swal.fire({
            icon: 'warning',
            title: 'መግባት አልተሳካም',
            text: 'መግባት አልተሳካም ወይም ፈቃድ የለዎትም። እባክዎ እንደገና ይግቡ።',
            confirmButtonText: 'ወደ መግቢያ ገጽ ሂድ'
        }).then(() => {
            window.location.href =
                (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/login';
        });

    } else if (error.message === 'SERVER_INVALID_RESPONSE') {

        Swal.fire(
            'ስህተት',
            'ሰርቨሩ ትክክለኛ JSON መልስ አልላከም። የPHP error log ይመልከቱ።',
            'error'
        );

    } else {

        // Show the ACTUAL server message instead of a generic fallback
        Swal.fire(
            'ስህተት',
            error.message || 'መረጃውን ማደስ አልተቻለም። እባክዎ ኮንሶሉን (F12) ይፈትሹ።',
            'error'
        );
    }
})
        .finally(() => {

            submitBtn.disabled = false;

            submitBtn.innerHTML =
                '<i class="fas fa-edit mr-1"></i>አስተካክል';
        });
    });
}

});