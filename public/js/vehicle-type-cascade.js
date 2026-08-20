(function () {
    'use strict';

    const brandSelect    = document.getElementById('car_brand_select');
    const typeSelect      = document.getElementById('car_type_select');
    const serviceDisplay  = document.getElementById('car_service_name_display');
    const measurementDisplay = document.getElementById('car_measurement_display');

    if (!brandSelect || !typeSelect) return;

    function resetType() {
        typeSelect.innerHTML = '<option value="" disabled selected>መጀመሪያ ብራንድ ይምረጡ</option>';
        typeSelect.disabled = true;
        serviceDisplay.value = '';
        measurementDisplay.value = '';
    }

    brandSelect.addEventListener('change', function () {
        resetType();

        const brand = this.value;
        if (!brand) return;

        const url = window.BASE_URL + '/vehicles-car-types?brand=' + encodeURIComponent(brand);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                typeSelect.innerHTML = '<option value="" disabled selected>ይምረጡ</option>';
                (data || []).forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id; // car_type uuid - this IS what gets submitted as car_type_id
                    opt.textContent = item.name;
                    opt.dataset.serviceName = item.service_name || '';
                    opt.dataset.measurement = item.measurement || '';
                    typeSelect.appendChild(opt);
                });
                typeSelect.disabled = false;
            })
            .catch(() => {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', text: 'ዝርዝሩን መጫን አልተቻለም', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
                }
            });
    });

    typeSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        serviceDisplay.value = opt?.dataset.serviceName || '';
        measurementDisplay.value = opt?.dataset.measurement || '';
    });
})();