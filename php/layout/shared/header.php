<?php
require_once __DIR__ . "/../../services/config.php";
require_once __DIR__ . "/../../services/product-service.php";

use Configuration\Router;
use User\UserService;
use Product\ProductService;

$productTypes = ProductService::getProductTypes();
?>

<nav class="navbar bg-body-secondary navbar-expand-lg fixed-top flex-wrap">
    <div class="container-fluid w-100">
        <a class="navbar-brand homelink" href="<?= Router::getRoute('home') ?>">
            <img src="assets/img/logo-192x192.png" alt="Logo" height="45px" class="d-inline-block align-text-top">
            <?= $appname ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav nav-underline ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $route === 'products' ? 'active' : '' ?>"
                        href="<?= Router::getRoute('products') ?>">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $route === 'about' ? 'active' : '' ?>"
                        href="<?= Router::getRoute('about') ?>">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $route === 'contact' ? 'active' : '' ?>"
                        href="<?= Router::getRoute('contact') ?>">Contact Us</a>
                </li>
                <li
                    class="nav-item dropdown <?= UserService::getUserIsLoggedIn() ? 'border rounded bg-success' : '' ?>">
                    <a class="nav-link dropdown-toggle <?= UserService::getUserIsLoggedIn() ? 'text-white' : '' ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <?= UserService::getUserInfo()->firstname ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (!UserService::getUserIsLoggedIn()): ?>
                            <li><a class="dropdown-item <?= $route === 'login' ? 'active' : '' ?>"
                                    href="<?= Router::getRoute('login') ?>">Login</a></li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item <?= $route === 'cart' ? 'active' : '' ?>"
                                    href="<?= Router::getRoute('cart') ?>">
                                    <i class="bi bi-cart2"></i>
                                    Cart
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item bg-danger-subtle text-danger-emphasis"
                                    href="<?= Router::getRoute('logout') ?>">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <?php if ($route === 'products'): ?>
        <div class="container-fluid w-100">
            <!-- mobile: show only on xs–sm, hide on md+ -->
            <select id="type-select" class="form-select mb-3 d-block d-md-none">
                <option value="all">All Products</option>
                <?php foreach ($productTypes as $pt): ?>
                    <option value="<?= $pt->id ?>">
                        <?= htmlspecialchars($pt->descriptionEng) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- desktop: hide on xs–sm, show on md+ -->
            <ul class="nav nav-pills nav-fill m-3 d-none d-md-flex w-100">
                <li class="nav-item">
                    <a class="nav-link active filter-btn" data-type="all" href="#">All Products</a>
                </li>
                <?php foreach ($productTypes as $pt): ?>
                    <li class="nav-item">
                        <a class="nav-link filter-btn" data-type="<?= $pt->id ?>"
                            href=""><?= htmlspecialchars($pt->descriptionEng) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</nav>