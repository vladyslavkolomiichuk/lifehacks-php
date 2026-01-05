<?php

use yii\helpers\Url;

$this->title = 'Dashboard';
?>

<style>
  /* Локальні стилі для карток Dashboard */
  .stat-card {
    border: none;
    border-radius: 15px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
  }

  /* Градієнти для карток */
  .bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
  }

  .bg-gradient-success {
    background: linear-gradient(45deg, #1cc88a, #13855c);
  }

  .bg-gradient-warning {
    background: linear-gradient(45deg, #f6c23e, #dda20a);
    color: #fff !important;
  }

  /* Білий текст для жовтого */
  .bg-gradient-danger {
    background: linear-gradient(45deg, #e74a3b, #be2617);
  }

  /* Стиль контенту */
  .stat-card-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.8;
    margin-bottom: 5px;
  }

  .stat-card-value {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1.2;
  }

  /* Велика іконка праворуч */
  .stat-card-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 4rem;
    opacity: 0.2;
    /* Напівпрозора */
    pointer-events: none;
  }

  /* Посилання знизу */
  .stat-card-link {
    display: block;
    padding: 10px 20px;
    background: rgba(0, 0, 0, 0.1);
    color: #fff;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: background 0.2s;
  }

  .stat-card-link:hover {
    background: rgba(0, 0, 0, 0.2);
    color: #fff;
    text-decoration: none;
  }
</style>

<div class="admin-default-index">
  <h1 class="mb-4 text-white border-bottom border-secondary pb-2">
    Dashboard
  </h1>

  <div class="row g-4 mb-5">

    <div class="col-md-3">
      <div class="stat-card bg-gradient-primary text-white h-100">
        <div class="card-body p-4">
          <div class="position-relative z-1">
            <div class="stat-card-title">Users</div>
            <div class="stat-card-value"><?= $countUsers ?></div>
          </div>
          <i class="bi bi-people-fill stat-card-icon"></i>
        </div>
        <a href="<?= Url::to(['user/index']) ?>" class="stat-card-link">
          View Details <i class="bi bi-arrow-right float-end"></i>
        </a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-card bg-gradient-success text-white h-100">
        <div class="card-body p-4">
          <div class="position-relative z-1">
            <div class="stat-card-title">Articles</div>
            <div class="stat-card-value"><?= $countArticles ?></div>
          </div>
          <i class="bi bi-file-text-fill stat-card-icon"></i>
        </div>
        <a href="<?= Url::to(['article/index']) ?>" class="stat-card-link">
          View Details <i class="bi bi-arrow-right float-end"></i>
        </a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-card bg-gradient-warning text-white h-100">
        <div class="card-body p-4">
          <div class="position-relative z-1">
            <div class="stat-card-title">Comments</div>
            <div class="stat-card-value"><?= $countComments ?></div>
          </div>
          <i class="bi bi-chat-left-text-fill stat-card-icon"></i>
        </div>
        <a href="<?= Url::to(['comment/index']) ?>" class="stat-card-link">
          View Details <i class="bi bi-arrow-right float-end"></i>
        </a>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-card bg-gradient-danger text-white h-100">
        <div class="card-body p-4">
          <div class="position-relative z-1">
            <div class="stat-card-title">Total Views</div>
            <div class="stat-card-value"><?= (int)$totalViews ?></div>
          </div>
          <i class="bi bi-eye-fill stat-card-icon"></i>
        </div>
        <div class="stat-card-link" style="cursor: default;">
          Across all articles
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-header border-secondary text-white bg-transparent py-3">
          <h5 class="m-0">Quick Actions</h5>
        </div>
        <div class="card-body p-4">
          <a href="<?= Url::to(['topic/create']) ?>" class="btn btn-outline-info btn-lg me-3">
            <i class="bi bi-plus-circle me-2"></i> Add Topic
          </a>
          <a href="<?= Url::to(['user/index']) ?>" class="btn btn-outline-light btn-lg me-3">
            <i class="bi bi-person-gear me-2"></i> Manage Admins
          </a>
          <a href="<?= Url::to(['article/index']) ?>" class="btn btn-outline-success btn-lg">
            <i class="bi bi-pencil-square me-2"></i> Manage Articles
          </a>
        </div>
      </div>
    </div>
  </div>
</div>