document.addEventListener('click', function (e) {

    const btn = e.target.closest('.edit-branch');

    if (!btn) {
        return;
    }

    const id       = btn.getAttribute('data-id');
    const name     = btn.getAttribute('data-name');
    const type     = btn.getAttribute('data-type');
    const zoneUuid = btn.getAttribute('data-zone-uuid');

    // Set branch ID
    document.getElementById('edit_branch_id').value = id;

    // Set branch name
    document.getElementById('edit_branch_name').value = name;

    // Get zone and branch type selects
    const zoneSelect = document.getElementById('edit_zone');
    const branchTypeSelect = document.getElementById('edit_branch_type');

    // Select existing zone
    zoneSelect.value = zoneUuid;

    // Get selected zone
    const selectedZone =
        zoneSelect.options[zoneSelect.selectedIndex];

    const zoneType =
        selectedZone
            ? selectedZone.getAttribute('data-type')
            : null;

    // Clear branch type options
    branchTypeSelect.innerHTML = `
        <option value="" disabled>
            ይምረጡ
        </option>
    `;

    // If zone is regio
    if (zoneType === 'regio') {

        branchTypeSelect.innerHTML += `
            <option value="kifle_ketema">
                ክ/ከተማ
            </option>
        `;

    }

    // If zone is zone
    else if (zoneType === 'zone') {

        branchTypeSelect.innerHTML += `
            <option value="woreda">
                ወረዳ
            </option>

            <option value="ketema_woreda">
                ከተማ ወረዳ
            </option>
        `;
    }

    // Set existing branch type
    branchTypeSelect.value = type;

    // Show modal
    $('#editBranchModal').modal('show');

document.getElementById('edit_zone')
    .addEventListener('change', function () {

        const selectedZone =
            this.options[this.selectedIndex];

        const zoneType =
            selectedZone.getAttribute('data-type');

        const branchTypeSelect =
            document.getElementById('edit_branch_type');

        branchTypeSelect.innerHTML = `
            <option value="" disabled selected>
                ይምረጡ
            </option>
        `;

        if (zoneType === 'regio') {

            branchTypeSelect.innerHTML += `
                <option value="kifle_ketema">
                    ክ/ከተማ
                </option>
            `;

        } else if (zoneType === 'zone') {

            branchTypeSelect.innerHTML += `
                <option value="woreda">
                    ወረዳ
                </option>

                <option value="ketema_woreda">
                    ከተማ ወረዳ
                </option>
            `;
        }
    });
    });