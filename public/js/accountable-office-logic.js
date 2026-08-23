// Shared option-builder so the dropdown markup lives in one place,
// sourced from window.BRANCH_TYPES (rendered server-side from the
// branch_type table) instead of a hardcoded list.
function buildBranchTypeOptions(selectEl, selectedType) {

    const types = window.BRANCH_TYPES || [];

    let optionsHtml = '<option value="" disabled selected>ይምረጡ</option>';

    types.forEach(function (type) {
        optionsHtml +=
            '<option value="' + type.type_in_eng + '">' +
            type.type_in_am +
            '</option>';
    });

    selectEl.innerHTML = optionsHtml;

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

    document.getElementById('edit_branch_id').value = id;
    document.getElementById('edit_branch_name').value = name;

    const parentSelect     = document.getElementById('edit_parent');
    const branchTypeSelect = document.getElementById('edit_branch_type');

    parentSelect.value = parentUuid;

    buildBranchTypeOptions(branchTypeSelect, type);

    $('#editBranchModal').modal('show');
});

// Registered ONCE, outside the click handler.
document.getElementById('edit_parent').addEventListener('change', function () {
    const branchTypeSelect = document.getElementById('edit_branch_type');
    buildBranchTypeOptions(branchTypeSelect, null);
});