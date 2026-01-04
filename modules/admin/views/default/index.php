<?php
$this->title = 'Dashboard';
?>
<div class="admin-default-index">
  <h1 class="mb-4">Dashboard</h1>

  <div class="row">
    <div class="col-md-3">
      <div class="card mb-4 text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-people-fill"></i> Users</h5>
          <h2 class="card-text"><?= $countUsers ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-4 text-white bg-success">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-file-text-fill"></i> Articles</h5>
          <h2 class="card-text"><?= $countArticles ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-4 text-dark bg-warning">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-chat-left-text-fill"></i> Comments</h5>
          <h2 class="card-text"><?= $countComments ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-4 text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-eye-fill"></i> Total Views</h5>
          <h2 class="card-text"><?= (int)$totalViews ?></h2>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
          <a href="<?= \yii\helpers\Url::to(['topic/create']) ?>" class="btn btn-outline-light me-2">Add New Topic</a>
          <a href="<?= \yii\helpers\Url::to(['user/index']) ?>" class="btn btn-outline-light">Manage Admins</a>
        </div>
      </div>
    </div>
  </div>
</div>