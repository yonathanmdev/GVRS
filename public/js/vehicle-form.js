(function () {
    'use strict';

    const form          = document.getElementById('vehicle-register-form');
    const branchIdInput = document.getElementById('branch_id');
    const zoneIdInput   = document.getElementById('zone_id');
    const fieldset       = document.getElementById('vehicle-fields');
    const submitBtn      = document.getElementById('vehicle-submit-btn');

    const groups = {
        regional:    document.getElementById('group-regional'),
        institution: document.getElementById('group-institution'),
        department:  document.getElementById('group-memriya'),
        woreda:      document.getElementById('group-woreda')
    };

    // ---- spinner helpers (additive only, injected via JS - no HTML/CSS files touched) ----

    let vfSpinnerStyleInjected = false;
    function ensureSpinnerStyle() {
        if (vfSpinnerStyleInjected) return;
        const style = document.createElement('style');
        style.textContent =
            '.vf-inline-spinner{display:inline-block;width:1rem;height:1rem;margin-inline-start:.5rem;' +
            'border:2px solid rgba(78,115,223,.25);border-top-color:#4e73df;border-radius:50%;' +
            'vertical-align:middle;animation:vf-spin .6s linear infinite;}' +
            '@keyframes vf-spin{to{transform:rotate(360deg);}}';
        document.head.appendChild(style);
        vfSpinnerStyleInjected = true;
    }

    function showFieldSpinner(afterEl) {
        ensureSpinnerStyle();
        hideFieldSpinner(afterEl);
        const spinner = document.createElement('span');
        spinner.className = 'vf-inline-spinner';
        spinner.setAttribute('data-vf-spinner-for', afterEl.id || '');
        afterEl.insertAdjacentElement('afterend', spinner);
        return spinner;
    }
    function hideFieldSpinner(afterEl) {
        const existing = afterEl.parentElement
            ? afterEl.parentElement.querySelector('[data-vf-spinner-for="' + (afterEl.id || '') + '"]')
            : null;
        if (existing) existing.remove();
    }

    function setButtonLoading(btn, isLoading, loadingText) {
        if (isLoading) {
            if (!btn.hasAttribute('data-vf-original-html')) {
                btn.setAttribute('data-vf-original-html', btn.innerHTML);
            }
            ensureSpinnerStyle();
            btn.innerHTML = '<span class="vf-inline-spinner" style="margin-inline-start:0;margin-inline-end:.5rem;"></span>' + (loadingText || '');
        } else if (btn.hasAttribute('data-vf-original-html')) {
            btn.innerHTML = btn.getAttribute('data-vf-original-html');
            btn.removeAttribute('data-vf-original-html');
        }
    }

    // ---- helpers -------------------------------------------------------

    function hideAllGroups() {
        Object.values(groups).forEach(g => { g.style.display = 'none'; });
    }

    function resetSelect(selectEl) {
        selectEl.innerHTML = '<option value="" disabled selected>ይምረጡ</option>';
        selectEl.disabled = true;
    }

    function deactivateFields() {
        branchIdInput.value = '';
        zoneIdInput.value = '';
        fieldset.disabled = true;
        submitBtn.disabled = true;
    }

    function activateFields(branchId) {
        branchIdInput.value = branchId;
        fieldset.disabled = false;
        submitBtn.disabled = false;
    }

    // Generic cascading loader: fetches options for a "child" select
    // based on the value of a "parent" select, using the parent's
    // data-endpoint / data-param / data-loads attributes.
    function wireCascade(parentSelect) {
        const endpoint    = parentSelect.getAttribute('data-endpoint');
        const paramName   = parentSelect.getAttribute('data-param');
        const childSelect = document.querySelector(parentSelect.getAttribute('data-loads'));
        const childLabel  = childSelect.previousElementSibling; // the <label> right before it

        parentSelect.addEventListener('change', function () {
            deactivateFields();
            resetSelect(childSelect);
            childSelect.style.display = 'none';
            if (childLabel && childLabel.tagName === 'LABEL') {
                childLabel.style.display = 'none';
            }

            const parentValue = parentSelect.value;
            if (!parentValue) {
                return;
            }

            const url = window.BASE_URL + endpoint + '?' + paramName + '=' + encodeURIComponent(parentValue);

            const spinner = showFieldSpinner(parentSelect);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    childSelect.innerHTML = '<option value="" disabled selected>ይምረጡ</option>';
                    (data || []).forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        childSelect.appendChild(opt);
                    });
                    childSelect.disabled = false;
                    childSelect.style.display = '';
                    if (childLabel && childLabel.tagName === 'LABEL') {
                        childLabel.style.display = '';
                    }
                })
                .catch(() => {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', text: 'ዝርዝሩን መጫን አልተቻለም', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
                    }
                })
                .finally(() => {
                    spinner.remove();
                });
        });

        // Final selection inside the loaded child -> activates fields
        childSelect.addEventListener('change', function () {
            if (childSelect.value) {
                activateFields(childSelect.value);
            } else {
                deactivateFields();
            }
        });
    }

    // ---- category switch -------------------------------------------------

    document.querySelectorAll('input[name="ownership_category"]').forEach(radio => {
        radio.addEventListener('change', function () {
            hideAllGroups();
            deactivateFields();

            // reset every select in every group so a stale value from a
            // previously-chosen category can't leak into the new one
            document.querySelectorAll('.category-group select').forEach(sel => {
                sel.selectedIndex = 0;
            });

            const category = this.value;
            const activeGroup = groups[category];
            if (activeGroup) {
                activeGroup.style.display = '';
            }
        });
    });

    // regional: single select, activates immediately on its own change
    document.getElementById('regional_bureau_select').addEventListener('change', function () {
        if (this.value) {
            activateFields(this.value);
        } else {
            deactivateFields();
        }
    });

    // institution: bureau (እናት መ/ቤት) -> institution list
    wireCascade(document.getElementById('inst_bureau_select'));

    // woreda: zone -> woreda list
    wireCascade(document.getElementById('woreda_zone_select'));

    // Mirror the selected zone into the hidden zone_id field once a
    // woreda is chosen, same as department does for memriya_zone_select.
    // wireCascade() already sets branch_id via activateFields() on this
    // same 'change' event — this listener just adds zone_id alongside it.
    const woredaZoneSelect = document.getElementById('woreda_zone_select');
    const woredaSelect     = document.getElementById('woreda_select');

    woredaSelect.addEventListener('change', function () {
        if (this.value) {
            zoneIdInput.value = woredaZoneSelect.value;
        } else {
            zoneIdInput.value = '';
        }
    });

    // department: memriya IS the office (sets branch_id directly), zone is a
    // second, separately required field that also gets stored as zone_id.
    // Both must be picked before fields activate.
    const memriyaSelect     = document.getElementById('memriya_select');
    const memriyaZoneSelect = document.getElementById('memriya_zone_select');
    const memriyaZoneLabel  = document.getElementById('memriya-zone-label');

    function tryActivateDepartment() {
        if (memriyaSelect.value && memriyaZoneSelect.value) {
            branchIdInput.value = memriyaSelect.value;
            zoneIdInput.value = memriyaZoneSelect.value;
            fieldset.disabled = false;
            submitBtn.disabled = false;
        } else {
            deactivateFields();
        }
    }

   memriyaSelect.addEventListener('change', function () {
    if (this.value) {
        memriyaZoneSelect.style.display = '';
        memriyaZoneLabel.style.display = '';
        // If a zone is already chosen, keep the form enabled with the
        // new memriya; otherwise it stays disabled until a zone is picked.
        tryActivateDepartment();
    } else {
        memriyaZoneSelect.style.display = 'none';
        memriyaZoneLabel.style.display = 'none';
        memriyaZoneSelect.selectedIndex = 0;
        deactivateFields();
    }
});

    memriyaZoneSelect.addEventListener('change', tryActivateDepartment);

    // ---- submit + partial reset ------------------------------------------

// ---- submit + partial reset ------------------------------------------

const finalSubmitBtn = document.getElementById('vehicle-final-submit-btn');
const manufacturedYear = document.getElementById('manufactured_year');
const purchaseYear = document.getElementById('purchase_year');

function validatePurchaseYear() {
    const mfgVal = parseInt(manufacturedYear.value, 10);
    const purVal = parseInt(purchaseYear.value, 10);

    if (!isNaN(mfgVal) && !isNaN(purVal) && purVal < mfgVal) {
        purchaseYear.setCustomValidity('የተገዛበት ዓ.ም ከተመረተበት ዓ.ም በፊት ሊሆን አይችልም');
    } else {
        purchaseYear.setCustomValidity('');
    }
}

manufacturedYear.addEventListener('input', validatePurchaseYear);
purchaseYear.addEventListener('input', validatePurchaseYear);

form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!branchIdInput.value) {
        return;
    }

    // Re-run the year check right before validity is checked, in case
    // fields were filled out of order or via autofill without firing 'input'.
    validatePurchaseYear();

    // Run native HTML5 validation (required fields, min/max, and the
    // custom purchase/manufactured year rule) and show the browser's
    // built-in messages if something's missing. Button stays enabled
    // the whole time — the user can fix the field immediately.
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    finalSubmitBtn.disabled = true;
    setButtonLoading(finalSubmitBtn, true, 'በመመዝገብ ላይ...');

    fetch(window.BASE_URL + '/vehicles-store', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (window.Swal) {
                    Swal.fire({ icon: 'success', text: data.message, toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                }
                // Only clear the vehicle-detail inputs.
                // Category, bureau/zone/institution/woreda selections
                // and branch_id stay exactly as they are, so the next
                // vehicle for the same office can be entered right away.
                fieldset.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    input.setCustomValidity(''); // clear any leftover custom error before next entry
                });
                fieldset.querySelectorAll('select:not(.persist-value)').forEach(select => {
                    select.selectedIndex = 0; // resets to the placeholder option
                    select.setCustomValidity('');
                });
              document.getElementById('car_brand_select')?.focus();
            } else {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', text: data.message || 'ስህተት ተፈጥሯል', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
                }
            }
        })
        .catch(() => {
            if (window.Swal) {
                Swal.fire({ icon: 'error', text: 'ግንኙነት ላይ ስህተት ተፈጥሯል', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
            }
        })
        .finally(() => {
            setButtonLoading(finalSubmitBtn, false);
            finalSubmitBtn.disabled = false;
        });
});
})();
