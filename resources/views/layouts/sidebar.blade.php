  <!-- Sidebar -->
  <div class="iq-sidebar sidebar-default">
        <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="header-logo">
                <img src="{{ asset('build/assets/images/logo.png') }}" class="img-fluid rounded-normal light-logo" alt="logo">
                <h5 class="logo-title light-logo ml-3">POSDash</h5>
            </a>
            <div class="iq-menu-bt-sidebar ml-0">
                <i class="las la-bars wrapper-menu"></i>
            </div>
        </div>
        <div class="data-scrollbar" data-scroll="1">
            <nav class="iq-sidebar-menu">
                <ul id="iq-sidebar-toggle" class="iq-menu">

                    <li class="{{ Route::is('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="svg-icon">
                            <svg class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span class="ml-4">Dashboards</span>
                        </a>
                    </li>

                    <li class="{{ Route::is('role.list') || Route::is('role.create') ? 'active' : '' }}">
                        <a href="#role"
                           class="{{ Route::is('role.list') || Route::is('role.create') ? '' : 'collapsed' }}"
                           data-toggle="collapse"
                           aria-expanded="{{ Route::is('role.list') || Route::is('role.create') ? 'true' : 'false' }}">

                            {{-- SVG Icon for Roles --}}
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span class="ml-4">Roles</span>

                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="role" class="iq-submenu collapse {{ Route::is('role.list') || Route::is('role.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('role.list') ? 'active' : '' }}">
                                <a href="{{ route('role.list') }}">
                                    <i class="las la-minus"></i><span>Roles List</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('role.create') ? 'active' : '' }}">
                                <a href="{{ route('role.create') }}">
                                    <i class="las la-minus"></i><span>Add Role</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('user.list') || Route::is('user.create') ? 'active' : '' }}">
                        <a href="#user"
                           class="{{ Route::is('user.list') || Route::is('user.create') ? '' : 'collapsed' }}"
                           data-toggle="collapse"
                           aria-expanded="{{ Route::is('user.list') || Route::is('user.create') ? 'true' : 'false' }}">

                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                              <circle cx="12" cy="7" r="4"></circle>
                              <path d="M5.5 21a8.38 8.38 0 0 1 13 0"></path>
                            </svg>
                            <span class="ml-4">Users</span>

                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="user" class="iq-submenu collapse {{ Route::is('user.list') || Route::is('user.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('user.list') ? 'active' : '' }}">
                                <a href="{{ route('user.list') }}">
                                    <i class="las la-minus"></i><span>Users List</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('user.create') ? 'active' : '' }}">
                                <a href="{{ route('user.create') }}">
                                    <i class="las la-minus"></i><span>Add User</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('branch.*') ? 'active' : '' }}">
                        <a href="#branch" class="{{ Route::is('branch.*') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('branch.*') ? 'true' : 'false' }}">
                            <svg class="svg-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 12h18M3 6h18M3 18h18"></path>
                            </svg>
                            <span class="ml-4">Branches</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="branch" class="iq-submenu collapse {{ Route::is('branch.*') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('branch.list') ? 'active' : '' }}">
                                <a href="{{ route('branch.list') }}"><i class="las la-minus"></i><span>Branches List</span></a>
                            </li>
                            <li class="{{ Route::is('branch.create') ? 'active' : '' }}">
                                <a href="{{ route('branch.create') }}"><i class="las la-minus"></i><span>Add Branch</span></a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('warehouse.list') || Route::is('warehouse.create') ? 'active' : '' }}">
                        <a href="#warehouse"
                           class="{{ Route::is('warehouse.list') || Route::is('warehouse.create') ? '' : 'collapsed' }}"
                           data-toggle="collapse"
                           aria-expanded="{{ Route::is('warehouse.list') || Route::is('warehouse.create') ? 'true' : 'false' }}">
                    
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 9l9-6 9 6v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span class="ml-4">Warehouses</span>
                    
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                    
                        <ul id="warehouse" class="iq-submenu collapse {{ Route::is('warehouse.list') || Route::is('warehouse.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('warehouse.list') ? 'active' : '' }}">
                                <a href="{{ route('warehouse.list') }}">
                                    <i class="las la-minus"></i><span>Warehouses List</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('warehouse.create') ? 'active' : '' }}">
                                <a href="{{ route('warehouse.create') }}">
                                    <i class="las la-minus"></i><span>Add Warehouse</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('units.list') || Route::is('units.create') ? 'active' : '' }}">
                        <a href="#unitMenu"
                           class="{{ Route::is('units.list') || Route::is('units.create') ? '' : 'collapsed' }}"
                           data-toggle="collapse"
                           aria-expanded="{{ Route::is('units.list') || Route::is('units.create') ? 'true' : 'false' }}">

                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            <span class="ml-4">Units</span>

                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="unitMenu" class="iq-submenu collapse {{ Route::is('units.list') || Route::is('units.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('units.list') ? 'active' : '' }}">
                                <a href="{{ route('units.list') }}">
                                    <i class="las la-minus"></i><span>Units List</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('units.create') ? 'active' : '' }}">
                                <a href="{{ route('units.create') }}">
                                    <i class="las la-minus"></i><span>Add Unit</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('categories.list') || Route::is('categories.create') || Route::is('categories.edit') ? 'active' : '' }}">
                        <a href="#categories" class="{{ Route::is('categories.list') || Route::is('categories.create') || Route::is('categories.edit') ? '' : 'collapsed' }}"
                           data-toggle="collapse" aria-expanded="{{ Route::is('categories.list') || Route::is('categories.create') || Route::is('categories.edit') ? 'true' : 'false' }}">

                            <!-- Folder Icon for Categories -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <path d="M3 7a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            </svg>

                            <span class="ml-4">Categories</span>

                            <!-- Dropdown Arrow Icon -->
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="categories" class="iq-submenu collapse {{ Route::is('categories.list') || Route::is('categories.create') || Route::is('categories.edit') ? 'show' : '' }}"
                            data-parent="#iq-sidebar-toggle">

                            <li class="{{ Route::is('categories.list') ? 'active' : '' }}">
                                <a href="{{ route('categories.list') }}">
                                    <i class="las la-minus"></i><span>Categories List</span>
                                </a>
                            </li>

                            <li class="{{ Route::is('categories.create') ? 'active' : '' }}">
                                <a href="{{ route('categories.create') }}">
                                    <i class="las la-minus"></i><span>Add Category</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('products.list') || Route::is('products.create') ? 'active' : '' }}">
                        <a href="#product" class="{{ Route::is('products.list') || Route::is('products.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('products.list') || Route::is('products.create') ? 'true' : 'false' }}">
                            <!-- Product Icon -->
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M20 13V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8m16 0v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6m16 0H4" />
                            </svg>
                            <span class="ml-4">Product</span>
                            <!-- Arrow -->
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="product" class="iq-submenu collapse {{ Route::is('products.list') || Route::is('products.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('products.list') ? 'active' : '' }}">
                                <a href="{{ route('products.list') }}">
                                    <i class="las la-minus"></i><span>Products List</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('products.create') ? 'active' : '' }}">
                                <a href="{{ route('products.create') }}">
                                    <i class="las la-minus"></i><span>Add Product</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('suppliers.list') || Route::is('suppliers.create') ? 'active' : '' }}">
                        <a href="#supplier" class="{{ Route::is('suppliers.list') || Route::is('suppliers.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('suppliers.list') || Route::is('suppliers.create') ? 'true' : 'false' }}">
                            <!-- Supplier Icon -->
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M3 4a2 2 0 0 1 2-2h3.5a2 2 0 0 1 1.41.59L12 4.09l2.09-1.5A2 2 0 0 1 15.5 2H19a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4z" />
                            </svg>
                            <span class="ml-4">Suppliers</span>
                            <!-- Arrow -->
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="supplier" class="iq-submenu collapse {{ Route::is('suppliers.list') || Route::is('suppliers.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('suppliers.list') ? 'active' : '' }}">
                                <a href="{{ route('suppliers.list') }}">
                                    <i class="las la-minus"></i><span>Suppliers List</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('suppliers.create') ? 'active' : '' }}">
                                <a href="{{ route('suppliers.create') }}">
                                    <i class="las la-minus"></i><span>Add Supplier</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('customers.list') || Route::is('customers.create') ? 'active' : '' }}">
                        <a href="#customer" class="{{ Route::is('customers.list') || Route::is('customers.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('customers.list') || Route::is('customers.create') ? 'true' : 'false' }}">
                            <svg class="svg-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 13V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8m16 0v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6m16 0H4"/>
                            </svg>
                            <span class="ml-4">Customer</span>
                            <!-- Arrow -->
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="customer" class="iq-submenu collapse {{ Route::is('customers.list') || Route::is('customers.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('customers.list') ? 'active' : '' }}">
                                <a href="{{ route('customers.list') }}"><i class="las la-minus"></i><span>Customers List</span></a>
                            </li>
                            <li class="{{ Route::is('customers.create') ? 'active' : '' }}">
                                <a href="{{ route('customers.create') }}"><i class="las la-minus"></i><span>Add Customer</span></a>
                            </li>
                        </ul>
                    </li>


                    {{-- <li class="{{ Route::is('company.list') || Route::is('company.create') ? 'active' : '' }}">
                        <a href="#company" class="{{ Route::is('company.list') || Route::is('company.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('company.list') || Route::is('company.create') ? 'true' : 'false' }}">
                            <!-- Company Icon -->
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> 
                                <path d="M3 21v-13a2 2 0 012-2h2v2h10V6h2a2 2 0 012 2v13" />
                                <path d="M13 9h-2v2h2zM9 13h2v2H9zM13 13h2v2h-2z" />
                            </svg>
                            <span class="ml-4">Company</span>
                            <!-- Arrow -->
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="company" class="iq-submenu collapse {{ Route::is('company.list') || Route::is('company.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('company.list') ? 'active' : '' }}">
                                <a href="{{ route('company.list') }}">
                                    <i class="las la-minus"></i><span>List Company</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('company.create') ? 'active' : '' }}">
                                <a href="{{ route('company.create') }}">
                                    <i class="las la-minus"></i><span>Add Company</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('vendor.list') || Route::is('vendor.create') ? 'active' : '' }}">
                        <a href="#vendor" class="{{ Route::is('vendor.list') || Route::is('vendor.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('vendor.list') || Route::is('vendor.create') ? 'true' : 'false' }}">
                            <!-- Vendor Icon (Briefcase) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8h18V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2zm0 0v12h18V8H3z" />
                            </svg>
                            <span class="ml-4">Vendor</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                    
                        <ul id="vendor" class="iq-submenu collapse {{ Route::is('vendor.list') || Route::is('vendor.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('vendor.list') ? 'active' : '' }}">
                                <a href="{{ route('vendor.list') }}">
                                    <i class="las la-minus"></i><span>List Vendors</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('vendor.create') ? 'active' : '' }}">
                                <a href="{{ route('vendor.create') }}">
                                    <i class="las la-minus"></i><span>Add Vendor</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="{{ Route::is('customer.list') || Route::is('customer.create') ? 'active' : '' }}">
                        <a href="#customer" class="{{ Route::is('customer.list') || Route::is('customer.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('customer.list') || Route::is('customer.create') ? 'true' : 'false' }}">
                            <!-- Customer Icon (Person) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M5 20c0-2 4-3 7-3s7 1 7 3"></path>
                            </svg>
                            <span class="ml-4">Customer</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                    
                        <ul id="customer" class="iq-submenu collapse {{ Route::is('customer.list') || Route::is('customer.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('customer.list') ? 'active' : '' }}">
                                <a href="{{ route('customer.list') }}">
                                    <i class="las la-minus"></i><span>List Customers</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('customer.create') ? 'active' : '' }}">
                                <a href="{{ route('customer.create') }}">
                                    <i class="las la-minus"></i><span>Add Customer</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="{{ Route::is('loan.list') || Route::is('loan.create') ? 'active' : '' }}">
                        <a href="#loan" class="{{ Route::is('loan.list') || Route::is('loan.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('loan.list') || Route::is('loan.create') ? 'true' : 'false' }}">
                            <!-- Loan Icon (Money Bag or Bank) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 21h12c1.104 0 1.988-.895 1.988-2.002V5.002c0-1.106-.884-2.002-1.988-2.002H6C4.896 3 4 3.895 4 5.002v13.996C4 20.105 4.896 21 6 21zM8 12h8" />
                            </svg>
                            <span class="ml-4">Loan</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                    
                        <ul id="loan" class="iq-submenu collapse {{ Route::is('loan.list') || Route::is('loan.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('loan.list') ? 'active' : '' }}">
                                <a href="{{ route('loan.list') }}">
                                    <i class="las la-minus"></i><span>List Loans</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('loan.create') ? 'active' : '' }}">
                                <a href="{{ route('loan.create') }}">
                                    <i class="las la-minus"></i><span>Add Loan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="{{ Route::is('expense.list') || Route::is('expense.create') ? 'active' : '' }}">
                        <a href="#expense" class="{{ Route::is('expense.list') || Route::is('expense.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('expense.list') || Route::is('expense.create') ? 'true' : 'false' }}">
                            <!-- Expense Icon (Money Bill) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 16v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2zM5 12h14v2H5zm0-4h14v2H5zm8-7h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z" />
                            </svg>
                            <span class="ml-4">Expenses</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                    
                        <ul id="expense" class="iq-submenu collapse {{ Route::is('expense.list') || Route::is('expense.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('expense.list') ? 'active' : '' }}">
                                <a href="{{ route('expense.list') }}">
                                    <i class="las la-minus"></i><span>List Expenses</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('expense.create') ? 'active' : '' }}">
                                <a href="{{ route('expense.create') }}">
                                    <i class="las la-minus"></i><span>Add Expense</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('sale.list') || Route::is('sale.create') ? 'active' : '' }}">
                        <a href="#sale" 
                           class="{{ Route::is('sale.list') || Route::is('sale.create') ? '' : 'collapsed' }}" 
                           data-toggle="collapse" 
                           aria-expanded="{{ Route::is('sale.list') || Route::is('sale.create') ? 'true' : 'false' }}">

                            <!-- Sales Icon -->
                            <svg class="svg-icon" id="sales-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 17L10 10L21 21"></path>
                                <path d="M7 3H4a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1z"></path>
                            </svg>

                            <span class="ml-4">Sales</span>

                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="sale" class="iq-submenu collapse {{ Route::is('sale.list') || Route::is('sale.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('sale.list') ? 'active' : '' }}">
                                <a href="{{ route('sale.list') }}">
                                    <i class="las la-minus"></i><span>List Sales</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('sale.create') ? 'active' : '' }}">
                                <a href="{{ route('sale.create') }}">
                                    <i class="las la-minus"></i><span>Add Sale</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('purchase.list') || Route::is('purchase.create') ? 'active' : '' }}">
                        <a href="#purchase" class="{{ Route::is('purchase.list') || Route::is('purchase.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('purchase.list') || Route::is('purchase.create') ? 'true' : 'false' }}">
                            <!-- New Purchases Icon (Shopping Cart) -->
                            <svg class="svg-icon" id="purchase-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 6H18L16 14H8L6 6Z"></path>
                                <circle cx="9" cy="18" r="2"></circle>
                                <circle cx="15" cy="18" r="2"></circle>
                            </svg>
                            <span class="ml-4">Purchases</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="purchase" class="iq-submenu collapse {{ Route::is('purchase.list') || Route::is('purchase.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('purchase.list') ? 'active' : '' }}">
                                <a href="{{ route('purchase.list') }}">
                                    <i class="las la-minus"></i><span>List Purchases</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('purchase.create') ? 'active' : '' }}">
                                <a href="{{ route('purchase.create') }}">
                                    <i class="las la-minus"></i><span>Add Purchase</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('payment.list') || Route::is('payment.create') ? 'active' : '' }}">
                        <a href="#payment" class="{{ Route::is('payment.list') || Route::is('payment.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('payment.list') || Route::is('payment.create') ? 'true' : 'false' }}">
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 3h-1a2 2 0 0 0-2 2v2h6V5a2 2 0 0 0-2-2z"></path>
                            </svg>
                            <span class="ml-4">Payments</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="payment" class="iq-submenu collapse {{ Route::is('payment.list') || Route::is('payment.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('payment.list') ? 'active' : '' }}">
                                <a href="{{ route('payment.list') }}">
                                    <i class="las la-minus"></i><span>List Payments</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('payment.create') ? 'active' : '' }}">
                                <a href="{{ route('payment.create') }}">
                                    <i class="las la-minus"></i><span>Add Payment</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('profit.list') || Route::is('profit.create') ? 'active' : '' }}">
                        <a href="#profit" class="{{ Route::is('profit.list') || Route::is('profit.create') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Route::is('profit.list') || Route::is('profit.create') ? 'true' : 'false' }}">
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 3v18h18"></path><path d="M18 9l-6 6-4-4"></path>
                            </svg>
                            <span class="ml-4">Profits</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                    
                        <ul id="profit" class="iq-submenu collapse {{ Route::is('profit.list') || Route::is('profit.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('profit.list') ? 'active' : '' }}">
                                <a href="{{ route('profit.list') }}">
                                    <i class="las la-minus"></i><span>List Profits</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('profit.create') ? 'active' : '' }}">
                                <a href="{{ route('profit.create') }}">
                                    <i class="las la-minus"></i><span>Add Profit</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::is('stock_adjustments.list') || Route::is('stock_adjustments.create') ? 'active' : '' }}">
                        <a href="#stock_adjustments"
                           class="{{ Route::is('stock_adjustments.list') || Route::is('stock_adjustments.create') ? '' : 'collapsed' }}"
                           data-toggle="collapse"
                           aria-expanded="{{ Route::is('stock_adjustments.list') || Route::is('stock_adjustments.create') ? 'true' : 'false' }}">

                            <!-- Warehouse/Stock Icon -->
                            <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 6l9-4 9 4v14H3V6z"></path>
                                <path d="M9 22V12h6v10"></path>
                            </svg>
                            <span class="ml-4">Stock Adjustments</span>

                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 viewBox="0 0 24 24">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="stock_adjustments" class="iq-submenu collapse {{ Route::is('stock_adjustments.list') || Route::is('stock_adjustments.create') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                            <li class="{{ Route::is('stock_adjustments.list') ? 'active' : '' }}">
                                <a href="{{ route('stock_adjustments.list') }}">
                                    <i class="las la-minus"></i><span>List Adjustments</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('stock_adjustments.create') ? 'active' : '' }}">
                                <a href="{{ route('stock_adjustments.create') }}">
                                    <i class="las la-minus"></i><span>Add Adjustment</span>
                                </a>
                            </li>
                        </ul>
                    </li> --}}
                  
                </ul>
            </nav>
            <div class="p-3"></div>
        </div>
    </div>

    <!-- Top Navbar -->
    <div class="iq-top-navbar">
        <div class="iq-navbar-custom">
            <nav class="navbar navbar-expand-lg navbar-light p-0">
                <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                    <i class="ri-menu-line wrapper-menu"></i>
                    <a href="{{ route('dashboard') }}" class="header-logo">
                        <img src="{{ asset('build/assets/images/logo.png') }}" class="img-fluid rounded-normal" alt="logo">
                        <h5 class="logo-title ml-3">POS</h5>
                    </a>
                </div>
                <div class="d-flex align-items-center ml-auto">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                        <i class="ri-menu-3-line"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto navbar-list align-items-center">
                            <li class="nav-item nav-icon dropdown caption-content">
                                <a href="#" class="search-toggle dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{ asset('build/assets/images/user/01.jpg') }}" class="img-fluid rounded" alt="user">
                                </a>
                                <div class="iq-sub-dropdown dropdown-menu">
                                    <div class="card shadow-none m-0">
                                        <div class="card-body p-0 text-center">
                                            <div class="media-body profile-detail text-center">
                                                <img src="{{ asset('build/assets/images/page-img/profile-bg.jpg') }}" class="rounded-top img-fluid mb-4" alt="profile-bg">
                                                <img src="{{ asset('build/assets/images/user/01.jpg') }}" class="rounded profile-img img-fluid avatar-70" alt="profile-img">
                                            </div>
                                            <div class="p-3">
                                                <h5 class="mb-1">{{ Auth::user()->email }}</h5>
                                                <div class="d-flex align-items-center justify-content-center mt-3">
                                                    <a href="{{ route('profile.edit') }}" class="btn border mr-2">Profile</a>
                                                    <a href="{{ route('logout') }}" class="btn border">Log Out</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>