<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Support Ticket Management Softwear from NTG, MIS Department" />
    <meta name="author" content="Md. Hasibul Islam Santo, MIS, NTG" />
    <title> {{ $pageTitle ?? 'TIL' }} </title>

    <!-- <link href="css/styles.css" rel="stylesheet" /> -->

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- bootstrap 5 cdn  -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/js/bootstrap.min.js"></script>


    <!-- font-awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>

    <!-- Bootstrap core icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />



    <!-- sweetalert2 cdn-->

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- DataTable -->

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

    <!-- Custom CSS -->

    <link href="{{ asset('ui/backend/css/styles.css') }}" rel="stylesheet" />

    <!-- Master Layout Optimized Styles (Performance) -->
    <style>
        /* ========== Global Styles (No Animations) ========== */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #f5f7fa;
            color: #333;
            opacity: 1;
            overflow-x: hidden;
        }

        /* ========== Container Enhancements ========== */
        .container-fluid {
            background-color: transparent !important;
            padding: 1.5rem !important;
            min-height: auto;
        }

        /* ========== Main Card Styling ========== */
        .container-fluid>.card {
            background: #ffffff !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
            overflow: visible;
        }

        /* ========== Typography ========== */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
            color: #2d3748;
        }

        /* ========== Form Controls (Select Text Visibility) ========== */
        select.form-control,
        select.form-select,
        .form-control,
        .form-select {
            color: #212529;
            background-color: #ffffff;
        }

        select.form-control option,
        select.form-select option {
            color: #212529;
            background-color: #ffffff;
        }

        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            color: #212529;
            background-color: #ffffff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #212529;
        }

        .select2-results__option {
            color: #212529;
        }

        .dataTable-selector {
            color: #212529;
            background-color: #ffffff;
        }

        .dataTable-selector option {
            color: #212529;
            background-color: #ffffff;
        }

        /* ========== Button Styling ========== */
        .btn {
            border-radius: 0.375rem;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border: 1px solid transparent;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .btn:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn:active {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: #667eea;
            color: #fff;
            border-color: #667eea;
        }

        .btn-primary:hover {
            background: #5568d3;
            border-color: #5568d3;
        }

        .btn-success {
            background: #38ef7d;
            color: #fff;
            border-color: #38ef7d;
        }

        .btn-success:hover {
            background: #2dd96f;
            border-color: #2dd96f;
        }

        .btn-danger {
            background: #f45c43;
            color: #fff;
            border-color: #f45c43;
        }

        .btn-danger:hover {
            background: #e04a34;
            border-color: #e04a34;
        }

        .btn-warning {
            background: #f2c94c;
            color: #333;
            border-color: #f2c94c;
        }

        .btn-warning:hover {
            background: #e0b83f;
            border-color: #e0b83f;
        }

        .btn-info {
            background: #00f2fe;
            color: #333;
            border-color: #00f2fe;
        }

        .btn-info:hover {
            background: #00d9e5;
            border-color: #00d9e5;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
            border-color: #5a6268;
        }

        /* ========== Card Component ========== */
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #ffffff;
        }

        .card:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ========== Table Styling ========== */
        .table {
            border-collapse: collapse;
        }

        .table thead th {
            background: #667eea;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border: none;
            padding: 0.75rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody tr {
            background: white;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .table tbody td {
            border-bottom: 1px solid #e0e0e0;
            padding: 0.75rem;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ========== Badge Styling ========== */
        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-primary {
            background: #667eea;
            color: white;
        }

        .badge-success {
            background: #38ef7d;
            color: white;
        }

        .badge-danger {
            background: #f45c43;
            color: white;
        }

        .badge-warning {
            background: #f2c94c;
            color: #333;
        }

        .badge-info {
            background: #00f2fe;
            color: #333;
        }

        /* ========== Form Styling ========== */
        .form-control,
        .form-select {
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            padding: 0.625rem;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* ========== Alert Styling ========== */
        .alert {
            border: 1px solid;
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .alert-info {
            background: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        /* ========== Dropdown Styling ========== */
        .dropdown-menu {
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            background: #fff;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #667eea;
        }

        /* ========== Loading State ========== */
        body {
            opacity: 1;
        }

        body.loaded {
            opacity: 1;
        }

        /* ========== Scrollbar Styling ========== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* ========== Utility Classes ========== */
        .shadow-sm {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .shadow-lg {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .rounded {
            border-radius: 0.375rem;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-primary {
            color: #667eea;
        }

        .text-success {
            color: #38ef7d;
        }

        .text-danger {
            color: #f45c43;
        }

        .text-warning {
            color: #f2c94c;
        }

        .text-info {
            color: #00f2fe;
        }

        /* ========== Mobile Optimization ========== */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 0.75rem !important;
            }

            .container-fluid>.card {
                border-radius: 0.5rem !important;
                margin: 0.25rem !important;
            }

            h1 {
                font-size: 1.5rem;
            }

            .table {
                font-size: 0.85rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>

    <!-- receive all push styles -->
    @stack('styles')

    <!-- receive all push JavaScript -->
    @stack('scripts')

</head>

<body>

    <div class="container-fluid">
        <div class="card mx-1 my-1">
            {{-- <div class="mx-1 my-1" style="background-color: #ccd9fe ; "> --}}
            {{-- {{ $breadCrumb ?? '' }} --}}
            {!! $breadCrumb ?? '' !!}

            {{ $slot ?? '' }}
            {{-- </div> --}}
        </div>



    </div>

    <!-- Core theme JS-->
    <script src="{{ asset('ui/backend/js/scripts.js') }}"></script>

    <!-- Bootstrap core JS (5.3 bundle includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>



    <!-- DataTable JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="{{ asset('ui/backend/js/datatables-simple-demo.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- receive all push scripts -->
    @stack('scripts')

    <!-- Global Enhancement Scripts -->
    <script>
        $(document).ready(function() {
            // ========== Smooth Scrolling (Disabled for Performance) ==========
            // Smooth scrolling disabled - causes jank and performance issues
            // Users can use smooth scroll CSS if needed

            // ========== Basic Tooltips (No Animation) ==========
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el, {
                    animation: false,
                    delay: 0
                });
            });

            // ========== Auto-hide Alerts ==========
            $('.alert').not('.alert-permanent').each(function() {
                var alert = $(this);
                setTimeout(function() {
                    alert.hide();
                    $(this).remove();
                }, 5000);
            });

            // ========== Button Ripple Effect (Disabled) ==========
            // Ripple effect removed for performance - no animation overhead

            // ========== Form Validation Enhancement ==========
            // Only apply to forms without custom handlers (data-no-default-handler)
            $('form:not([data-no-default-handler])').on('submit', function(e) {
                // Skip if form has specific submit button handlers (like updateStatusBtn, completeTicketBtn)
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');

                // Don't interfere with forms that have custom button handlers
                if (form.find('#updateStatusBtn, #completeTicketBtn').length) {
                    return true;
                }

                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');

                // Re-enable after 3 seconds as failsafe
                setTimeout(function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(submitBtn.data('original-text') || 'Submit');
                }, 3000);
            });

            // Store original button text
            $('button[type="submit"]').each(function() {
                $(this).data('original-text', $(this).html());
            });

            // ========== Table Row Click Enhancement ==========
            $('.table tbody tr[data-href]').on('click', function() {
                window.location = $(this).data('href');
            }).css('cursor', 'pointer');

            // ========== Card Collapse Animation ==========
            $('.card-header[data-bs-toggle="collapse"]').on('click', function() {
                $(this).find('.collapse-icon').toggleClass('rotate-180');
            });

            // ========== Select2 Enhancement ==========
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'default',
                    width: '100%'
                });
            }

            // ========== Page Load Animation ==========
            $('body').addClass('loaded');

            // ========== Sticky Navigation on Scroll ==========
            // DISABLED: Fixed positioning was preventing page scrolling
            // var breadcrumb = $('.breadcrumb-header');
            // var breadcrumbOffset = breadcrumb.offset() ? breadcrumb.offset().top : 0;
            //
            // $(window).scroll(function() {
            //     if ($(window).scrollTop() > breadcrumbOffset + 100) {
            //         breadcrumb.addClass('sticky-breadcrumb');
            //     } else {
            //         breadcrumb.removeClass('sticky-breadcrumb');
            //     }
            // });

            // ========== Confirmation Dialogs Enhancement ==========
            $('[data-confirm]').on('click', function(e) {
                e.preventDefault();
                var message = $(this).data('confirm');
                var href = $(this).attr('href') || $(this).closest('form').attr('action');

                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#eb3349',
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-popup-custom',
                        confirmButton: 'swal-btn-custom',
                        cancelButton: 'swal-btn-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if ($(this).is('a')) {
                            window.location = href;
                        } else {
                            $(this).closest('form').submit();
                        }
                    }
                });
            });
        });

        // ========== Page Visibility Change Handler ==========
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                document.title = '💤 Away - TIL';
            } else {
                document.title = '{{ $pageTitle ?? 'TIL' }}';
            }
        });
    </script>

    <style>
        /* ========== Additional Global Styles ========== */
        body {
            opacity: 1;
        }

        body.loaded {
            opacity: 1;
        }

        .ripple-effect {
            display: none;
        }

        .sticky-breadcrumb {
            /* DISABLED: position fixed was preventing page scrolling */
            position: relative;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        /* ========== SweetAlert2 Custom Styling ========== */
        .swal-popup-custom {
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .swal-btn-custom {
            border-radius: 0.375rem;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* ========== Loading State ========== */
        .btn[disabled] {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>


</body>

</html>
