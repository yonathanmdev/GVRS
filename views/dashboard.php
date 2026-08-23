<?php
// Ensure session and variables are ready
$is_dashboard = true;
$branch_id = $_SESSION['user']['branch_id'] ?? null;
$role = $_SESSION['user']['role'] ?? ''; 
?>

<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

<section class="content">
  <div class="container-fluid">

    <div class="row mb-3">
      <div class="col-12">
        <div class="card card-default shadow-sm border-0" style="border-radius: 12px; background: #ffffff;">
          <div class="card-body py-4 px-4 d-flex align-items-center justify-content-start" style="gap: 24px;">
            
            <?php if (!empty($_SESSION['user']['logo_url'])): ?>
              <img src="<?= rtrim($_ENV['BASE_URL'] ?? '', '/') ?>/serve-file?file=<?= htmlspecialchars($_SESSION['user']['logo_url']) ?>&type=image" 
                   alt="<?= htmlspecialchars($_SESSION['user']['alt_name'] ?? '') ?>"    class="GVRS-bureau-logo" style="height: 65px; width: auto; object-fit: contain;">
            <?php else: ?>
              <img src="images/logo_transparent.png" 
                   alt="System Logo" 
                   class="GVRS-bureau-logo" 
                   style="height: 95px; width: auto; object-fit: contain; background: transparent !important; flex-shrink: 0;">
            <?php endif; ?>
            
            <div class="GVRS-title-container">
              <p class="GVRS-title-sub text-muted font-weight-bold" style="font-size: 13px; color: #4a5568; letter-spacing: 0.4px; margin: 0;">
               <?= \__('brand_name') ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">

     <?php if ($role === 'system_admin' || $role === 'admin'|| $role === 'mgmt'|| $role === 'officer'|| $role === 'data_encoder'): ?>

        <div class="col-md-4 col-sm-6 mb-3">
  <!-- ሲነካ በቀጥታ ወደ አዲሱ ቻርቶች ገጽ ይወስዳል ።-->
<a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/bureau-analytics" style="text-decoration: none; color: inherit;">
  <div class="report-type-card card card-outline card-primary h-100 shadow-sm" style="border-radius: 12px; cursor: pointer;">
    <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
      <span class="badge badge-primary float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
         <?= number_format($total_bureaus) ?> ቢሮና ተጠሪ ተቋማት
      </span>
      <i class="fas fa-id-card fa-2x text-primary mb-3 mt-2"></i>
      <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ቢሮና ተጠሪ ተቋማት</h6>
    </div>
  </div>
</a>
</div>

        <div class="col-md-4 col-sm-6 mb-3">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/zone-analytics" style="text-decoration: none; color: inherit;">
          <div class="report-type-card card card-outline card-success h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-success float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($total_zonal) ?> Total
              </span>
              <i class="fas fa-industry fa-2x text-success mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">አስተዳደራዊ መዋቅር</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/vehicle-status-analytics" style="text-decoration: none; color: inherit;">
          <div class="report-type-card card card-outline card-warning h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-warning float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px; color: #ffffff;">
                <?= number_format($total_vichel) ?> Total
              </span>
              <i class="fas fa-handshake fa-2x text-warning mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ተሽከርካሪዎቹ ያለበት ሁኔታ</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
  <!-- ሲነካ በቀጥታ ወደ አዲሱ ቻርት ገጽ ይወስዳል -->
<a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/awareness-all-analytics" style="text-decoration: none; color: inherit;">
  <div class="report-type-card card card-outline card-primary h-100 shadow-sm" style="border-radius: 12px; cursor: pointer;">
    <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
      <span class="badge badge-primary float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
         <?= number_format($total_vichel) ?> Total
      </span>
      <i class="far fa-lightbulb fa-2x text-primary mb-3 mt-2"></i>
      <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ተሸከርካሪዎቹ በአገልግሎት ዓይነት</h6>
    </div>
  </div>
</a>
</div>

<div class="col-md-4 col-sm-6 mb-3" >
    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/orgteam-analytics" style="text-decoration: none; color: inherit;">
          <div class="report-type-card card card-outline card-success h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-success float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($total_vichel) ?> Total
              </span>
              <i class="fas fa-sitemap fa-2x text-success mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ተሽከርካሪዎቹ ያሉበት ሁኔታ</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/uuuu" style="text-decoration: none; color: inherit;">
          <div class="report-type-card card card-outline card-warning h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-warning float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px; color: #ffffff;">
                <?= number_format($total_vichel) ?> Total
              </span>
              <i class="fas fa-users-cog fa-2x text-warning mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ተሽከርካሪዎቹ በእድሜ</h6>
            </div>
          </div>
        </div>

      <?php endif; ?>

    </div>
  </div>
</section>