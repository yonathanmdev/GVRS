<?php
//use App\Helpers\ViewHelper;  
$is_car_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools">
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#branchModal">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline-block">አዲስ የመኪና ብራንድ መመዝገብ</span>
          </button>
          
        </div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም የክልል ተጠሪ ተቋም የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ብራንድ ስም</th>
      
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $index => $row): ?>
          <tr id="row-<?=  $row['uud'] ?>">
            <td><?= $index + 1 ?></td>
            <td><?=  $row['brand_name'] ?></td>
            
           
          <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button class="btn btn-outline-secondary btn-sm  edit-branch" 
                      data-id="<?php echo $row['uud']; ?>" 
                      data-name="<?php echo $row['brand_name'] ?>" 
                    
                       title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-outline-danger btn-sm delete-branch" 
                      data-id="<?php echo $row['uud']; ?>" 
                      data-name="<?php echo $row['brand_name'] ?>"
                       
                      title="ሰርዝ">

                 <i class="fas fa-trash-alt me-1"></i>
              </button>
  </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
      </div>

    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-car-modal.php'; ?>


<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="branchModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-car-brand-process" enctype="multipart/form-data">
        <?= \App\Helpers\Csrf::field(); ?>

        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> የመኪና ብራንድ መመዝገብ
          </h6>

          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">

          <div class="form-group mb-2">
            <label for="branch_name" class="mb-1">
              <small class="font-weight-bold">
                የመኪናዉ ብራንድ ስም
              </small>
            </label>

            <input 
              type="text" 
              id="car_brand_name" 
              class="form-control form-control-sm" 
              name="car_brand_name" 
              placeholder="ስም ያስገቡ ለምሳሌ፡ ቲዮታ ፣ ኒሳን ፣ ወዘተ" 
              required
            >
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            ዝጋ
          </button>

          <button type="submit" class="btn btn-primary btn-sm">
            መዝግብ
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
