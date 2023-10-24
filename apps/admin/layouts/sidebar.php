<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link " href="/<?php echo home; ?>/admin">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <!-- Post components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Posts</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('postCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Post</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('postList'); ?>">
          <i class="bi bi-circle"></i><span>All Posts</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('postCatCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Category</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('postCatList'); ?>">
          <i class="bi bi-circle"></i><span>All Categories</span>
        </a>
      </li>
    </ul>
  </li>
  <!-- Page components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-pages" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Pages</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-pages" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('pageCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Page</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('pageList'); ?>">
          <i class="bi bi-circle"></i><span>All Page</span>
        </a>
      </li>

    </ul>
  </li>
  <!-- Slider components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-sliders" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Sliders</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-sliders" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('sliderCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Slider</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('sliderList'); ?>">
          <i class="bi bi-circle"></i><span>All Sliders</span>
        </a>
      </li>

    </ul>
  </li>
  <!-- <li class="nav-item">
    <a class="nav-link collapsed" href="<?php //echo BASEURI . route('paymentList') ?>">
      <i class="bi bi-menu-button-wide"></i><span>Payment</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
  </li> -->
  <!-- Porducts components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#products-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Category</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="products-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('productCatCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Category</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('productCatList'); ?>">
          <i class="bi bi-circle"></i><span>Category</span>
        </a>
      </li>
      <hr>
    </ul>
  </li>
  <!-- user components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-users" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-users" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'user']); ?>">
          <i class="bi bi-circle"></i><span>Add user</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'user']); ?>">
          <i class="bi bi-circle"></i><span>All users</span>
        </a>
      </li>
    </ul>
  </li>
  <!-- Fuels component -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-ptrols" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Petrols</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-ptrols" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('fuelCreate', ['fg' => 'petrol']); ?>">
          <i class="bi bi-circle"></i><span>Add/Deduct</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('fuelList', ['fg' => 'petrol']); ?>">
          <i class="bi bi-circle"></i><span>List</span>
        </a>
      </li>
    </ul>
  </li>
  <!-- orders component -->
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-orders" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Orders </span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-orders" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('orderListApi',['fg'=>'food']); ?>">
          <i class="bi bi-circle"></i><span>List</span>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-drivers" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Drivers</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-drivers" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'driver']); ?>">
          <i class="bi bi-circle"></i><span>Add driver</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'driver']); ?>">
          <i class="bi bi-circle"></i><span>All drivers</span>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-admins" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Admins</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-admins" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'admin']); ?>">
          <i class="bi bi-circle"></i><span>Add Admin</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'admin']); ?>">
          <i class="bi bi-circle"></i><span>All admin</span>
        </a>
      </li>

    </ul>
  </li>
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-comments" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Comments</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-comments" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('commentList', ['cg' => 'post']); ?>">
          <i class="bi bi-menu-button-wide"></i><span>Inbox</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('commentList', ['cg' => 'spam']); ?>">
          <i class="bi bi-menu-button-wide"></i><span>Spam</span>
        </a>
      </li>

    </ul>
  </li>
  <!-- End Components  -->

</ul>

</aside>