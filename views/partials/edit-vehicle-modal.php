<style>
    /* ============================================================
   VEHICLE EDIT MODAL — PROFESSIONAL UI
   Visual-only enhancement
   Does not change IDs, names, fields or functionality
   ============================================================ */

#vehicleEditModal {
    font-family: "Noto Sans Ethiopic", "Segoe UI", Arial, sans-serif;
}

/* ------------------------------------------------------------
   MODAL
   ------------------------------------------------------------ */

#vehicleEditModal .modal-dialog {
    max-width: 980px;
}

#vehicleEditModal .modal-content {
    border: 0;
    border-radius: 14px;
    overflow: hidden;
    background: #f7f8fa;
    box-shadow:
        0 24px 70px rgba(15, 23, 42, 0.20),
        0 8px 24px rgba(15, 23, 42, 0.10);
}

/* ------------------------------------------------------------
   HEADER
   ------------------------------------------------------------ */

#vehicleEditModal .modal-header {
    position: relative;
    min-height: 70px;
    padding: 18px 24px;
    border: 0;
    background: #ffffff;
    border-bottom: 1px solid #e8ebef;
}

#vehicleEditModal .modal-header::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #f6c23e;
}

#vehicleEditModal .modal-title {
    display: flex;
    align-items: center;
    margin: 0;
    color: #263238;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: -0.01em;
}

#vehicleEditModal .modal-title i {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px !important;
    border-radius: 9px;
    background: #fff8df;
    color: #d9a400 !important;
    font-size: 14px;
}

#vehicleEditModal .modal-header .close {
    width: 36px;
    height: 36px;
    margin: 0;
    padding: 0;
    border-radius: 8px;
    color: #6b7280;
    opacity: 1;
    transition: all .18s ease;
}

#vehicleEditModal .modal-header .close:hover {
    background: #f3f4f6;
    color: #374151;
}

#vehicleEditModal .modal-header .close:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(246, 194, 62, .20);
}

/* ------------------------------------------------------------
   BODY
   ------------------------------------------------------------ */

#vehicleEditModal .modal-body {
    padding: 22px 24px 26px;
    background: #f7f8fa;
    max-height: calc(100vh - 190px);
    overflow-y: auto;
}

/* Smooth scrollbar */

#vehicleEditModal .modal-body::-webkit-scrollbar {
    width: 7px;
}

#vehicleEditModal .modal-body::-webkit-scrollbar-track {
    background: transparent;
}

#vehicleEditModal .modal-body::-webkit-scrollbar-thumb {
    background: #cbd1d8;
    border-radius: 20px;
}

#vehicleEditModal .modal-body::-webkit-scrollbar-thumb:hover {
    background: #aeb6c0;
}

/* ------------------------------------------------------------
   SECTIONS
   ------------------------------------------------------------ */

#vehicleEditModal .vehicle-section {
    position: relative;
    margin-bottom: 18px;
    padding: 20px 20px 4px;
    background: #ffffff;
    border: 1px solid #e4e7eb;
    border-left: 3px solid #d8dde3;
    border-radius: 11px;
    box-shadow: 0 2px 7px rgba(15, 23, 42, 0.035);
    transition:
        border-color .18s ease,
        box-shadow .18s ease;
}

#vehicleEditModal .vehicle-section:hover {
    box-shadow: 0 5px 16px rgba(15, 23, 42, 0.055);
}

/* Current office section */

#vehicleEditModal .vehicle-section[style*="border-left-color:#f6c23e"] {
    background: linear-gradient(
        135deg,
        #fffdf7 0%,
        #ffffff 55%
    );
    border-color: #eadfb9;
    border-left-color: #f6c23e !important;
}

/* ------------------------------------------------------------
   SECTION TITLE
   ------------------------------------------------------------ */

#vehicleEditModal .vehicle-section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 38px;
    margin: -2px 0 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid #edf0f2;
}

#vehicleEditModal .vehicle-section-title-main {
    display: flex;
    align-items: center;
    gap: 10px;
}

#vehicleEditModal .vehicle-section-title-main i {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #f4f6f8;
    color: #5f6b76;
    font-size: 13px;
}

#vehicleEditModal .vehicle-section-title-main span {
    color: #263238;
    font-size: .91rem;
    font-weight: 700;
}

/* Yellow section icon */

#vehicleEditModal .vehicle-section[style*="border-left-color:#f6c23e"]
.vehicle-section-title-main i {
    background: #fff7dc;
    color: #c89500;
}

/* ------------------------------------------------------------
   FORM ROW
   ------------------------------------------------------------ */

#vehicleEditModal .form-row {
    margin-left: -8px;
    margin-right: -8px;
}

#vehicleEditModal .form-row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
}

/* ------------------------------------------------------------
   FORM GROUP
   ------------------------------------------------------------ */

#vehicleEditModal .form-group {
    margin-bottom: 18px;
}

/* ------------------------------------------------------------
   LABELS
   ------------------------------------------------------------ */

#vehicleEditModal label {
    display: block;
    margin-bottom: 7px;
    color: #4b5563;
    font-size: .79rem;
    font-weight: 600;
    line-height: 1.45;
}

#vehicleEditModal .required-mark {
    margin-left: 3px;
    color: #dc3545;
    font-weight: 700;
}

/* ------------------------------------------------------------
   INPUTS / SELECTS
   ------------------------------------------------------------ */

#vehicleEditModal .form-control,
#vehicleEditModal select.form-control {
    min-height: 40px;
    border: 1px solid #d8dde3;
    border-radius: 7px;
    background-color: #ffffff;
    color: #29323a;
    font-size: .86rem;
    box-shadow: none;
    transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background-color .18s ease;
}

#vehicleEditModal .form-control {
    padding: 8px 11px;
}

#vehicleEditModal select.form-control {
    padding: 7px 34px 7px 11px;
}

/* Placeholder */

#vehicleEditModal .form-control::placeholder {
    color: #a8afb7;
}

/* Hover */

#vehicleEditModal .form-control:hover {
    border-color: #b9c1ca;
}

/* Focus */

#vehicleEditModal .form-control:focus,
#vehicleEditModal select.form-control:focus {
    border-color: #e5b52e;
    background-color: #fffef9;
    box-shadow:
        0 0 0 3px rgba(246, 194, 62, .13);
    outline: none;
}

/* Readonly */

#vehicleEditModal .form-control[readonly] {
    background-color: #f5f6f8;
    color: #68737d;
    border-color: #e1e5e9;
    cursor: default;
}

/* Disabled */

#vehicleEditModal .form-control:disabled,
#vehicleEditModal select.form-control:disabled {
    background-color: #f0f2f4;
    color: #8a929a;
    cursor: not-allowed;
}

/* ------------------------------------------------------------
   CURRENT OFFICE
   ------------------------------------------------------------ */

#vehicleEditModal #edit-current-office {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 6px 11px;
    border-radius: 7px;
    background: #ffffff;
    border: 1px solid #eadfb9;
    color: #4d5963;
    font-size: .86rem;
    font-weight: 600;
}

/* ------------------------------------------------------------
   WARNING / FIELD HINT
   ------------------------------------------------------------ */

#vehicleEditModal .field-hint {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    margin-top: 10px;
    padding: 10px 12px;
    border: 1px solid #f1df9c;
    border-radius: 7px;
    background: #fffaf0;
    color: #80651b !important;
    font-size: .75rem;
    line-height: 1.65;
}

#vehicleEditModal .field-hint i {
    flex-shrink: 0;
    margin-top: 3px;
}

/* ------------------------------------------------------------
   INPUT GROUP — PRICE
   ------------------------------------------------------------ */

#vehicleEditModal .input-group .form-control {
    border-right: 0;
}

#vehicleEditModal .input-group .form-control:focus {
    border-right: 0;
}

#vehicleEditModal .input-group-text {
    min-width: 48px;
    justify-content: center;
    border: 1px solid #d8dde3;
    border-left: 0;
    border-radius: 0 7px 7px 0;
    background: #f5f6f8;
    color: #68737d;
    font-size: .8rem;
    font-weight: 600;
}

/* ------------------------------------------------------------
   FOOTER
   ------------------------------------------------------------ */

#vehicleEditModal .modal-footer {
    min-height: 68px;
    padding: 14px 24px;
    border-top: 1px solid #e5e8ec;
    background: #ffffff;
}

/* ------------------------------------------------------------
   BUTTONS
   ------------------------------------------------------------ */

#vehicleEditModal .modal-footer .btn {
    min-height: 39px;
    padding: 7px 17px;
    border-radius: 7px;
    font-size: .82rem;
    font-weight: 600;
    transition:
        transform .15s ease,
        box-shadow .15s ease,
        background-color .15s ease;
}

#vehicleEditModal .modal-footer .btn:hover {
    transform: translateY(-1px);
}

#vehicleEditModal .btn-outline-secondary {
    border-color: #d5d9de;
    color: #59636d;
    background: #ffffff;
}

#vehicleEditModal .btn-outline-secondary:hover {
    background: #f5f6f7;
    border-color: #c4c9cf;
    color: #374151;
}

#vehicleEditModal #vehicle-edit-submit-btn {
    min-width: 125px;
    border: 0;
    background: #f6c23e;
    color: #3f3210;
    box-shadow: 0 3px 8px rgba(246, 194, 62, .22);
}

#vehicleEditModal #vehicle-edit-submit-btn:hover {
    background: #eab52f;
    box-shadow: 0 5px 13px rgba(246, 194, 62, .28);
}

#vehicleEditModal #vehicle-edit-submit-btn:focus {
    box-shadow:
        0 0 0 3px rgba(246, 194, 62, .20),
        0 4px 10px rgba(246, 194, 62, .20);
}

#vehicleEditModal #vehicle-edit-submit-btn:disabled {
    opacity: .65;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* ------------------------------------------------------------
   VALIDATION
   ------------------------------------------------------------ */

#vehicleEditModal .form-control:invalid:not(:focus):not(:placeholder-shown) {
    border-color: #e2a4ab;
}

#vehicleEditModal .form-control:valid:not(:focus) {
    border-color: #d8dde3;
}

/* ------------------------------------------------------------
   MODAL BACKDROP
   ------------------------------------------------------------ */

.modal-backdrop.show {
    opacity: .58;
}

/* ------------------------------------------------------------
   RESPONSIVE
   ------------------------------------------------------------ */

@media (max-width: 767.98px) {

    #vehicleEditModal .modal-dialog {
        margin: .6rem;
    }

    #vehicleEditModal .modal-header {
        padding: 15px 17px;
    }

    #vehicleEditModal .modal-body {
        padding: 15px;
        max-height: calc(100vh - 155px);
    }

    #vehicleEditModal .modal-footer {
        padding: 12px 15px;
    }

    #vehicleEditModal .vehicle-section {
        padding: 16px 14px 2px;
        margin-bottom: 14px;
    }

    #vehicleEditModal .vehicle-section-title {
        margin-bottom: 15px;
    }

    #vehicleEditModal .modal-title {
        font-size: .95rem;
    }

    #vehicleEditModal .modal-footer .btn {
        flex: 1;
    }
}

@media (max-width: 575.98px) {

    #vehicleEditModal .modal-dialog {
        margin: 0;
        min-height: 100vh;
    }

    #vehicleEditModal .modal-content {
        min-height: 100vh;
        border-radius: 0;
    }

    #vehicleEditModal .modal-body {
        max-height: calc(100vh - 140px);
    }

    #vehicleEditModal .modal-footer {
        gap: 8px;
    }

    #vehicleEditModal .modal-footer .btn {
        padding-left: 10px;
        padding-right: 10px;
    }
}
    </style>
<?php use App\Helpers\ViewHelper; ?>
<div class="modal fade"
     id="vehicleEditModal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered"
         role="document">

        <div class="modal-content">

            <!-- =================================================
                 HEADER
                 ================================================= -->
            <div class="modal-header">

                <div class="modal-header-text">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2 text-warning"></i>
                        የተሽከርካሪ መረጃ አስተካክል
                    </h5>
                </div>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>


            <!-- =================================================
                 BODY
                 ================================================= -->
            <div class="modal-body">

                <!-- =============================================
                     CURRENT OFFICE
                     ============================================= -->
                <div class="vehicle-section"
                     style="border-left-color:#f6c23e;">

                    <div class="vehicle-section-title">

                        <div class="vehicle-section-title-main">

                            <i class="fas fa-building"></i>

                            <span>
                                የተመዘገበበት ተቋም
                            </span>

                        </div>

                    </div>

                    <p class="mb-1">
                        <strong id="edit-current-office">
                            —
                        </strong>
                    </p>

                    <div class="field-hint"
                         style="color:#9b7412;">

                        <i class="fas fa-exclamation-triangle mr-1"></i>

                        ተቋሙ/ዞኑ/ወረዳው የተሳሳተ ከሆነ፣
                        እዚህ ላይ ማስተካከል አይቻልም።
                        ይህን ተሽከርካሪ ሰርዘው
                        በትክክለኛው ተቋም ስር እንደገና ይመዝግቡት።

                    </div>

                </div>


                <!-- =============================================
                     FORM
                     ============================================= -->
                <form id="vehicle-edit-form" novalidate>

                    <?= \App\Helpers\Csrf::field(); ?>

                    <input type="hidden"
                           id="edit_vehicle_id"
                           name="id"
                           value="">


                    <fieldset id="vehicle-edit-fields">

                        <!-- =====================================
                             IDENTIFICATION
                             ===================================== -->
                        <div class="vehicle-section">

                            <div class="vehicle-section-title">

                                <div class="vehicle-section-title-main">

                                    <i class="fas fa-id-card"></i>

                                    <span>
                                        መለያ መረጃ
                                    </span>

                                </div>

                            </div>


                            <div class="form-row">

                                <!-- BRAND -->
                                <div class="form-group col-md-4">

                                    <label for="edit_car_brand_select">

                                        ብራንድ

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        class="form-control"
                                        id="edit_car_brand_select"
                                        data-loads="#car_type_select"
                                        data-endpoint="/vehicles-car-types"
                                        name="brand_id"
                                        required>

                                        <option value="" selected disabled>
                                            ይምረጡ
                                        </option>

                                        <?php foreach ($brands as $brand): ?>

                                            <option
                                                value="<?= ViewHelper::e($brand['brand_id']) ?>">

                                                <?= ViewHelper::e($brand['brand_name']) ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>


                                <!-- VEHICLE TYPE -->
                                <div class="form-group col-md-4">

                                    <label for="edit_car_type_select">

                                        የተሽከርካሪው/ማሽነሪው ዓይነት

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        class="form-control"
                                        id="edit_car_type_select"
                                        name="vehicle_type_id"
                                        required>

                                        <option value="">
                                            መጀመሪያ ብራንድ ይምረጡ
                                        </option>

                                    </select>

                                </div>


                                <!-- SERVICE -->
                                <div class="form-group col-md-4">

                                    <label for="edit_car_service_name_display">
                                        የአገልግሎት ዓይነት
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="edit_car_service_name_display"
                                        readonly
                                        tabindex="-1">

                                </div>


                                <!-- PLATE -->
                                <div class="form-group col-md-4">

                                    <label for="edit_plate_number">

                                        ሰሌዳ ቁጥር

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="plate_number"
                                        id="edit_plate_number"
                                        required>

                                </div>


                                <!-- MODEL -->
                                <div class="form-group col-md-4">

                                    <label for="edit_model">
                                        ሞዴል
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="model"
                                        id="edit_model">

                                </div>


                                <!-- CHASSIS -->
                                <div class="form-group col-md-4">

                                    <label for="edit_chassis_number">
                                        ቻንሲ ቁጥር
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="chassis_number"
                                        id="edit_chassis_number">

                                </div>


                                <!-- ENGINE -->
                                <div class="form-group col-md-4">

                                    <label for="edit_engine_number">
                                        የሞተር ቁጥር
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="engine_number"
                                        id="edit_engine_number">

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             CAPACITY / PRICE / STATUS
                             ===================================== -->
                        <div class="vehicle-section">

                            <div class="vehicle-section-title">

                                <div class="vehicle-section-title-main">

                                    <i class="fas fa-file-invoice-dollar"></i>

                                    <span>
                                        አቅም፣ ዋጋ እና ሁኔታ
                                    </span>

                                </div>

                            </div>


                            <div class="form-row">

                                <!-- MEASUREMENT -->
                                <div class="form-group col-md-2">

                                    <label for="edit_car_measurement_display">
                                        መለኪያ
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="edit_car_measurement_display"
                                        readonly
                                        tabindex="-1">

                                </div>


                                <!-- CAPACITY -->
                                <div class="form-group col-md-4">

                                    <label for="edit_capacity">

                                        የመጫን አቅም

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="capacity"
                                        id="edit_capacity"
                                        required>

                                </div>


                                <!-- PRICE -->
                                <div class="form-group col-md-6">

                                    <label for="edit_estimated_price">

                                        ተሽከርካሪ/ማሽነሪ ግምታዊ ዋጋ

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <div class="input-group">

                                        <input
                                            type="number"
                                            class="form-control"
                                            name="estimated_price"
                                            id="edit_estimated_price"
                                            step="any"
                                            min="0"
                                            required>

                                        <div class="input-group-append">

                                            <span class="input-group-text">
                                                ብር
                                            </span>

                                        </div>

                                    </div>

                                </div>


                                <!-- MANUFACTURED YEAR -->
                                <div class="form-group col-md-3">

                                    <label for="edit_manufactured_year">

                                        የተመረተበት ዓ.ም(GC)

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        name="manufactured_year"
                                        id="edit_manufactured_year"
                                        min="1950"
                                        max="<?= date('Y') ?>"
                                        required>

                                </div>


                                <!-- PURCHASE YEAR -->
                                <div class="form-group col-md-3">

                                    <label for="edit_purchase_year">

                                        የተገዛበት ዓ.ም(GC)

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        name="purchase_year"
                                        id="edit_purchase_year"
                                        min="1950"
                                        max="<?= date('Y') ?>"
                                        required>

                                </div>


                                <!-- STATUS -->
                                <div class="form-group col-md-6">

                                    <label for="edit_vehicle_status">

                                        አሁናዊ ሁኔታ

                                        <span class="required-mark">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        class="form-control"
                                        name="vehicle_status"
                                        id="edit_vehicle_status"
                                        required>

                                        <option value="">
                                            ይምረጡ
                                        </option>

                                        <option value="active">
                                            በአካል ያለ
                                        </option>

                                        <option value="lost">
                                            የጠፋ
                                        </option>

                                        <option value="destroyed">
                                            የወደመ
                                        </option>

                                        <option value="disposed">
                                            የተወገደ
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>

                    </fieldset>

                </form>

            </div>


            <!-- =================================================
                 FOOTER
                 ================================================= -->
            <div class="modal-footer justify-content-end">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-dismiss="modal">

                    <i class="fas fa-times mr-1"></i>
                    ዝጋ

                </button>

                <button
                    type="submit"
                    form="vehicle-edit-form"
                    class="btn btn-warning px-4"
                    id="vehicle-edit-submit-btn">

                    <i class="fas fa-save mr-1"></i>
                    አስተካክል

                </button>

            </div>

        </div>

    </div>

</div>


<script nonce="<?= $GLOBALS['nonce'] ?? '' ?>">
document.addEventListener('DOMContentLoaded', function () {

    'use strict';


    /* =========================================================
       DEPENDENCY CHECK

       This modal relies on window.VehicleTypeLoader, which
       must be loaded BEFORE this inline script runs. See:

           /assets/js/vehicle-type-loader.js

       ========================================================= */

    if (!window.VehicleTypeLoader) {

        console.error(
            'Vehicle edit modal: VehicleTypeLoader is not loaded. ' +
            'Make sure vehicle-type-loader.js is included before this script.'
        );

        return;
    }


    /* =========================================================
       ELEMENTS
       ========================================================= */

    const editModalEl =
        document.getElementById('vehicleEditModal');

    const editModal =
        editModalEl
            ? $(editModalEl)
            : null;

    const editForm =
        document.getElementById('vehicle-edit-form');

    const editSubmitBtn =
        document.getElementById(
            'vehicle-edit-submit-btn'
        );

    const editBrandSelect =
        document.getElementById(
            'edit_car_brand_select'
        );

    const editTypeSelect =
        document.getElementById(
            'edit_car_type_select'
        );

    const editServiceDisplay =
        document.getElementById(
            'edit_car_service_name_display'
        );

    const editMeasurementDisplay =
        document.getElementById(
            'edit_car_measurement_display'
        );


    /*
     * Make sure required elements exist.
     */
    if (
        !editModal ||
        !editForm ||
        !editSubmitBtn ||
        !editBrandSelect ||
        !editTypeSelect
    ) {
        console.error(
            'Vehicle edit modal: required elements are missing.'
        );

        return;
    }


    /* =========================================================
       SHARED ERROR TOAST
       ========================================================= */

    function showLoadError() {

        if (window.Swal) {

            Swal.fire({

                icon: 'error',

                text:
                    'የተሽከርካሪ ዓይነት ' +
                    'ዝርዝሩን መጫን አልተቻለም',

                toast: true,

                position: 'top-end',

                timer: 3000,

                showConfirmButton: false

            });

        }

    }


    /* =========================================================
       EDIT BRAND CHANGE

       This is only for when the user manually changes
       the brand while editing. Delegates to the shared
       VehicleTypeLoader used by vehicle-type-cascade.js.
       ========================================================= */

    editBrandSelect.addEventListener(
        'change',
        function () {

            VehicleTypeLoader
                .load(
                    this.value,
                    editTypeSelect,
                    editServiceDisplay,
                    editMeasurementDisplay
                )
                .catch(function (error) {

                    console.error(
                        'Vehicle type loading error:',
                        error
                    );

                    showLoadError();

                });

        }
    );


    /* =========================================================
       EDIT TYPE CHANGE
       ========================================================= */

    editTypeSelect.addEventListener(
        'change',
        function () {

            const option =
                this.options[
                    this.selectedIndex
                ];


            editServiceDisplay.value =
                option?.dataset.serviceName || '';


            editMeasurementDisplay.value =
                option?.dataset.measurement || '';

        }
    );


    /* =========================================================
       OPEN EDIT MODAL
       ========================================================= */

    document.addEventListener(
        'click',
        async function (e) {

            const editBtn =
                e.target.closest('.edit-vehicle');


            if (!editBtn) {
                return;
            }


            const vehicleId =
                editBtn.dataset.id;


            if (!vehicleId) {

                Swal.fire({

                    icon: 'error',

                    text:
                        'የተሽከርካሪው ID አልተገኘም',

                    toast: true,

                    position: 'top-end',

                    timer: 3000,

                    showConfirmButton: false

                });

                return;
            }


            /*
             * Show loading state
             */
            editSubmitBtn.disabled = true;


            try {

                /*
                 * =============================================
                 * GET VEHICLE DATA
                 * =============================================
                 */

                const response =
                    await fetch(
                        window.BASE_URL +
                        '/vehicles-edit-data?id=' +
                        encodeURIComponent(vehicleId),
                        {

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'
                            }

                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'HTTP ' +
                        response.status
                    );

                }


                const data =
                    await response.json();


                if (!data.success) {

                    throw new Error(
                        data.message ||
                        'መረጃ ማምጣት አልተቻለም'
                    );

                }


                const v =
                    data.vehicle;


                /*
                 * =============================================
                 * BASIC DATA
                 * =============================================
                 */

                document.getElementById(
                    'edit_vehicle_id'
                ).value =
                    vehicleId;


                document.getElementById(
                    'edit-current-office'
                ).textContent =
                    data.office_label || '—';


                document.getElementById(
                    'edit_plate_number'
                ).value =
                    v.plate_number ?? '';


                document.getElementById(
                    'edit_model'
                ).value =
                    v.model ?? '';


                document.getElementById(
                    'edit_chassis_number'
                ).value =
                    v.chassis_number ?? '';


                document.getElementById(
                    'edit_engine_number'
                ).value =
                    v.engine_number ?? '';


                document.getElementById(
                    'edit_capacity'
                ).value =
                    v.capacity ?? '';


                document.getElementById(
                    'edit_estimated_price'
                ).value =
                    v.estimated_price ?? '';


                document.getElementById(
                    'edit_manufactured_year'
                ).value =
                    v.manufactured_year ?? '';


                document.getElementById(
                    'edit_purchase_year'
                ).value =
                    v.purchase_year ?? '';


                document.getElementById(
                    'edit_vehicle_status'
                ).value =
                    v.vehicle_status ?? '';


                /*
                 * =============================================
                 * BRAND + TYPE
                 * =============================================
                 */

                const brandId =
                    v.brand_id ?? '';


                const vehicleTypeId =
                    v.vehicle_type_id ?? '';


                /*
                 * Set the existing brand.
                 */
                editBrandSelect.value =
                    brandId;
                try {

                    await VehicleTypeLoader.load(
                        brandId,
                        editTypeSelect,
                        editServiceDisplay,
                        editMeasurementDisplay,
                        vehicleTypeId
                    );

                } catch (loadError) {

                    console.error(
                        'Vehicle type loading error:',
                        loadError
                    );

                    showLoadError();

                }


                /*
                 * =============================================
                 * SHOW MODAL
                 * =============================================
                 */

                editModal.modal('show');


            } catch (error) {

                console.error(
                    'Vehicle edit loading error:',
                    error
                );


                if (window.Swal) {

                    Swal.fire({

                        icon: 'error',

                        text:
                            error.message ||
                            'መረጃ ማምጣት አልተቻለም',

                        toast: true,

                        position: 'top-end',

                        timer: 3500,

                        showConfirmButton: false

                    });

                }

            } finally {

                editSubmitBtn.disabled = false;

            }

        }
    );


    /* =========================================================
       SUBMIT EDIT
       ========================================================= */

editForm.addEventListener(
    'submit',
    async function (e) {

        /*
         * Always prevent the browser's normal submission.
         * The AJAX request below handles the submission.
         */
        e.preventDefault();

        /*
         * Make sure the global submit handler knows
         * this submission has intentionally been prevented.
         */
        $(editForm).data('prevent-submit', true);

        /*
         * ================================================
         * REQUIRED FIELD VALIDATION
         * ================================================
         */

        const requiredFields = [
            {
                element: document.getElementById('edit_plate_number'),
                message: 'ሰሌዳ ቁጥር ማስገባት አለብዎት።'
            },
            {
                element: document.getElementById('edit_car_brand_select'),
                message: 'ብራንድ መምረጥ አለብዎት።'
            },
            {
                element: document.getElementById('edit_car_type_select'),
                message: 'የተሽከርካሪው/ማሽነሪው ዓይነት መምረጥ አለብዎት።'
            },
            {
                element: document.getElementById('edit_capacity'),
                message: 'የመጫን አቅም ማስገባት አለብዎት።'
            },
            {
                element: document.getElementById('edit_manufactured_year'),
                message: 'የተመረተበትን ዓ.ም ማስገባት አለብዎት።'
            },
            {
                element: document.getElementById('edit_estimated_price'),
                message: 'ግምታዊ ዋጋ ማስገባት አለብዎት።'
            },
            {
                element: document.getElementById('edit_purchase_year'),
                message: 'የተገዛበትን ዓ.ም ማስገባት አለብዎት።'
            },
            {
                element: document.getElementById('edit_vehicle_status'),
                message: 'የአሁኑን ሁኔታ መምረጥ አለብዎት።'
            }
        ];

        /*
         * Find first empty required field.
         */
        const emptyField = requiredFields.find(function (item) {
            return !item.element || !String(item.element.value).trim();
        });

        if (emptyField) {

            if (emptyField.element) {
                emptyField.element.focus();
            }

            Swal.fire({
                icon: 'warning',
                text: emptyField.message,
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });

            /*
             * IMPORTANT:
             * Do not disable the submit button.
             */
            $(editForm).data('submitting', false);

            editSubmitBtn.disabled = false;

            return;
        }


        /*
         * ================================================
         * HTML5 VALIDATION
         * ================================================
         */

        if (!editForm.checkValidity()) {

            editForm.reportValidity();

            /*
             * Validation failed, therefore the button
             * must remain enabled.
             */
            $(editForm).data('submitting', false);

            editSubmitBtn.disabled = false;

            return;
        }


        /*
         * ================================================
         * YEAR VALIDATION
         * ================================================
         */

        const manufacturedYear =
            parseInt(
                document.getElementById('edit_manufactured_year').value,
                10
            );

        const purchaseYear =
            parseInt(
                document.getElementById('edit_purchase_year').value,
                10
            );

        if (
            !Number.isNaN(manufacturedYear) &&
            !Number.isNaN(purchaseYear) &&
            purchaseYear < manufacturedYear
        ) {

            Swal.fire({
                icon: 'warning',
                text:
                    'የተገዛበት ዓ.ም ከተመረተበት ዓ.ም በፊት ሊሆን አይችልም።',
                toast: true,
                position: 'top-end',
                timer: 3500,
                showConfirmButton: false
            });

            document
                .getElementById('edit_purchase_year')
                .focus();

            /*
             * Keep submit button enabled.
             */
            $(editForm).data('submitting', false);

            editSubmitBtn.disabled = false;

            return;
        }


        /*
         * ================================================
         * VALIDATION PASSED
         * ================================================
         *
         * Only now do we disable the button.
         */
let vehEdit_spinnerStyleInjected = false;

function vehEdit_ensureSpinnerStyle() {

    if (vehEdit_spinnerStyleInjected) {
        return;
    }

    const style = document.createElement('style');

    style.textContent =
        '.veh-edit-inline-spinner{display:inline-block;width:1rem;height:1rem;' +
        'margin-inline-start:.5rem;border:2px solid rgba(201,161,59,.28);' +
        'border-top-color:#c9a13b;border-radius:50%;vertical-align:middle;' +
        'animation:veh-edit-spin .6s linear infinite;}' +
        '@keyframes veh-edit-spin{to{transform:rotate(360deg);}}';

    document.head.appendChild(style);

    vehEdit_spinnerStyleInjected = true;

}

function setButtonLoading(btn, isLoading, loadingText) {

    vehEdit_ensureSpinnerStyle();

    if (isLoading) {

        if (!btn.hasAttribute('data-veh-edit-original-html')) {

            btn.setAttribute(
                'data-veh-edit-original-html',
                btn.innerHTML
            );

        }

        btn.innerHTML =
            '<span class="veh-edit-inline-spinner" ' +
            'style="margin-inline-start:0;margin-inline-end:.5rem;"></span>' +
            (loadingText || '');

    } else if (btn.hasAttribute('data-veh-edit-original-html')) {

        btn.innerHTML =
            btn.getAttribute('data-veh-edit-original-html');

        btn.removeAttribute('data-veh-edit-original-html');

    }

}
        $(editForm).data('submitting', true);

        editSubmitBtn.disabled = true;

        setButtonLoading(
            editSubmitBtn,
            true,
            'በማስተካከል ላይ....'
        );


        try {

            const formData =
                new FormData(editForm);

            const response =
                await fetch(
                    window.BASE_URL +
                    '/vehicle-update',
                    {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'
                        }
                    }
                );


            const responseText =
                await response.text();

            let data;

            try {

                data =
                    JSON.parse(responseText);

            } catch (jsonError) {

                console.error(
                    'Invalid JSON response:',
                    responseText
                );

                throw new Error(
                    'Server returned an invalid response.'
                );
            }


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'የአገልግሎት ስህተት ተፈጥሯል'
                );
            }


            if (data.success) {

                Swal.fire({
                    icon: 'success',
                    text:
                        data.message ||
                        'መረጃው በትክክል ተዘምኗል',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });

                editModal.modal('hide');

                if (data.vehicle) {
                    updateRowInPlace(
                        data.vehicle
                    );
                }

            } else {

                Swal.fire({
                    icon: 'error',
                    text:
                        data.message ||
                        'ስህተት ተፈጥሯል',
                    toast: true,
                    position: 'top-end',
                    timer: 3500,
                    showConfirmButton: false
                });
            }


        } catch (error) {

            console.error(
                'Vehicle update error:',
                error
            );

            Swal.fire({
                icon: 'error',
                text:
                    error.message ||
                    'ግንኙነት ላይ ስህተት ተፈጥሯል',
                toast: true,
                position: 'top-end',
                timer: 3500,
                showConfirmButton: false
            });

        } finally {

            setButtonLoading(
                editSubmitBtn,
                false
            );

            /*
             * Always restore the button after the
             * AJAX request finishes.
             */
            editSubmitBtn.disabled = false;

            $(editForm).data('submitting', false);

            /*
             * Clear the prevent flag so a future
             * submission can proceed normally.
             */
            $(editForm).removeData('prevent-submit');
        }

    }
);


 /* =========================================================
   UPDATE TABLE ROW IN PLACE
   ========================================================= */

function updateRowInPlace(v) {

    if (!v || !v.uuid) {

        console.warn(
            'Invalid vehicle returned from server:',
            v
        );

        return;
    }


    /*
     * Find the existing row.
     */
    const row = document.getElementById(
        'row-' + v.uuid
    );


    if (!row) {

        console.warn(
            'Vehicle row not found:',
            v.uuid
        );

        return;
    }


    /*
     * Get all cells.
     */
    const cells = row.querySelectorAll('td');


    /*
     * Expected structure:
     *
     * 0 = row number
     * 1 = plate number
     * 2 = brand
     * 3 = vehicle type
     * 4 = model
     * 5 = branch
     * 6 = zone
     * 7 = manufactured year
     * 8 = purchase year
     * 9 = status
     * 10 = actions
     */
    if (cells.length < 10) {

        console.warn(
            'Unexpected vehicle table row structure.',
            cells.length
        );

        return;
    }


    /* =========================================================
       PLATE NUMBER
       ========================================================= */

    if (v.plate_number !== undefined) {

        cells[1].textContent =
            v.plate_number ?? '';
    }


    /* =========================================================
       BRAND
       ========================================================= */

    if (v.brand_name !== undefined) {

        cells[2].textContent =
            v.brand_name ?? '';
    }


    /* =========================================================
       VEHICLE TYPE
       ========================================================= */

    if (v.type_name !== undefined) {

        cells[3].textContent =
            v.type_name ?? '';
    }


    /*
     * Some queries may return vehicle_type_name instead
     * of type_name. Support both without changing your
     * existing table.
     */
    else if (
        v.vehicle_type_name !== undefined
    ) {

        cells[3].textContent =
            v.vehicle_type_name ?? '';
    }


    /* =========================================================
       MODEL
       ========================================================= */

    if (v.model !== undefined) {

        cells[4].textContent =
            v.model ?? '';
    }


    /* =========================================================
       BRANCH
       ========================================================= */

    if (v.branch_name !== undefined) {

        cells[5].textContent =
            v.branch_name ?? '';
    }


    /* =========================================================
       ZONE
       ========================================================= */

    if (v.zone_name !== undefined) {

        cells[6].textContent =
            v.zone_name ?? '';
    }


    /* =========================================================
       MANUFACTURED YEAR
       ========================================================= */

    if (v.manufactured_year !== undefined) {

        cells[7].textContent =
            v.manufactured_year ?? '';
    }


    /* =========================================================
       PURCHASE YEAR
       ========================================================= */

    if (v.purchase_year !== undefined) {

        cells[8].textContent =
            v.purchase_year ?? '';
    }


    /* =========================================================
       VEHICLE STATUS
       ========================================================= */

    if (v.vehicle_status !== undefined) {

        const status =
            v.vehicle_status ?? '';


        /*
         * Same labels as your PHP table.
         */
        const statusMap = {

            active:
                'በአገልግሎት ላይ',

            lost:
                'የጠፋ',

            destroyed:
                'የወደመ',

            disposed:
                'የተወገደ'
        };


        /*
         * Same Bootstrap badge classes as your PHP table.
         */
        const badgeMap = {

            active:
                'success',

            lost:
                'warning',

            destroyed:
                'danger',

            disposed:
                'secondary'
        };


        const statusText =
            statusMap[status] ?? status;


        const badgeClass =
            badgeMap[status] ?? 'secondary';


        /*
         * Escape the text before inserting it into HTML.
         */
        const safeStatusText =
            String(statusText)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');


        cells[9].innerHTML =
            '<span class="badge badge-' +
            badgeClass +
            '">' +
            safeStatusText +
            '</span>';
    }


    /* =========================================================
       UPDATE ROW DATA ATTRIBUTES
       ========================================================= */

    if (v.brand_id !== undefined) {

        row.dataset.brandId =
            v.brand_id;
    }


    if (v.vehicle_type_id !== undefined) {

        row.dataset.vehicleTypeId =
            v.vehicle_type_id;
    }


    if (v.vehicle_status !== undefined) {

        row.dataset.vehicleStatus =
            v.vehicle_status;
    }


    /* =========================================================
       HIGHLIGHT UPDATED ROW
       ========================================================= */

    row.classList.add(
        'table-success'
    );


    setTimeout(
        function () {

            row.classList.remove(
                'table-success'
            );

        },
        1500
    );
}
});
</script>