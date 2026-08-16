// Shared option-builder so the dropdown markup lives in one place
function buildBranchTypeOptions(selectEl, selectedType) {
    selectEl.innerHTML = `
        <option value="" disabled selected>ይምረጡ</option>
        <option value="authority">ባለ ስልጣን</option>
        <option value="commission">ኮሚሽን</option>
        <option value="institution">ኢንስቲቲዩት</option>
        <option value="enterprise">ኢንተርፕራይዝ</option>
        <option value="memriya">መምሪያ</option>
        <option value="tsfet_bet">ጽፈት ቤት</option>
        <option value="college">ኮሌጅ</option>
    `;
    if (selectedType) {
        selectEl.value = selectedType;
    }
}

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.edit-branch');

    if (!btn) {
        return;
    }

    const id         = btn.getAttribute('data-id');
    const name       = btn.getAttribute('data-name');
    const type       = btn.getAttribute('data-type');
    const parentUuid = btn.getAttribute('data-parent-uuid');

    // Set branch ID
    document.getElementById('edit_branch_id').value = id;

    // Set branch name
    document.getElementById('edit_branch_name').value = name;

    // Get zone and branch type selects
    const parentSelect     = document.getElementById('edit_parent');
    const branchTypeSelect = document.getElementById('edit_branch_type');

    // Select existing zone
    parentSelect.value = parentUuid;

    // Rebuild branch type options and restore the existing selection
    buildBranchTypeOptions(branchTypeSelect, type);

    // Show modal
    $('#editBranchModal').modal('show');
});

// Registered ONCE, outside the click handler, so it doesn't
// pile up a new listener on every click elsewhere on the page.
document.getElementById('edit_parent').addEventListener('change', function () {
    const branchTypeSelect = document.getElementById('edit_branch_type');
    // No previous selection to preserve on a manual change
    buildBranchTypeOptions(branchTypeSelect, null);
});