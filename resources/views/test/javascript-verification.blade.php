<!-- Verification Test Page for JavaScript Form Fixes -->
<!-- Place this in resources/views/test/ directory for manual testing -->

@extends('components.backend.layouts.master', ['pageTitle' => 'JavaScript Form Fix Verification'])

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-flask mr-2"></i>JavaScript Form Fix Verification</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Test Cases for Form Submission Fixes</h5>

                        <!-- Test 1: SweetAlert Functionality -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-left-primary">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">✅ Test 1: SweetAlert Basic</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">Click button to test SweetAlert dialog appearance</p>
                                        <button class="btn btn-primary" id="testAlert">
                                            <i class="fas fa-info-circle mr-2"></i>Test Alert
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-left-success">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">✅ Test 2: Form Validation</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="testValidationForm">
                                            <div class="form-group mb-2">
                                                <label>Required Field</label>
                                                <input type="text" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-sm">Test Validation</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test 2: Button State Management -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-left-warning">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">✅ Test 3: Button Loading State</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">Click to test button disable and spinner</p>
                                        <button class="btn btn-warning" id="testLoadingBtn">
                                            <i class="fas fa-save mr-2"></i>Test Loading State
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-left-info">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">✅ Test 4: Double Submit Prevention</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">Click button multiple times to test prevention</p>
                                        <form id="testDoubleSubmitForm">
                                            <button type="submit" class="btn btn-info btn-sm">
                                                <i class="fas fa-check mr-2"></i>Test Double Submit
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test 3: Collapse State -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-left-danger">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">✅ Test 5: Collapse State Persistence</h6>
                                        <small class="text-muted">Expand/collapse, then refresh page</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <a href="#collapseTest" data-bs-toggle="collapse" class="btn btn-link">
                                                <i class="fas fa-chevron-down transition-rotate mr-2"></i>Toggle Collapse
                                            </a>
                                        </div>
                                        <div id="collapseTest" class="collapse">
                                            <p>This is test content. It should remain collapsed/expanded after page refresh.
                                            </p>
                                            <code>Collapse State: <span id="collapseState">Hidden</span></code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Console -->
                        <div class="card border-left-secondary">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">📋 Browser Console Output</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Open browser DevTools (F12) → Console tab to see test results</p>
                                <code class="d-block p-3 bg-dark text-light" style="max-height: 300px; overflow-y: auto;">
                                    <div id="consoleOutput" style="font-family: monospace; white-space: pre-wrap;">
                                        // Console output will appear here
                                    </div>
                                </code>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-book mr-2"></i>Testing Instructions</h6>
                            <ol class="mb-0">
                                <li>Open Browser DevTools (F12) and go to Console tab</li>
                                <li>Click each test button and verify expected behavior</li>
                                <li>Check browser console for any JavaScript errors</li>
                                <li>Verify SweetAlert dialogs appear and work correctly</li>
                                <li>Test form validation by leaving fields empty</li>
                                <li>Test button disable/enable during submission</li>
                                <li>Test double-submit prevention by clicking multiple times</li>
                                <li>Refresh page and verify collapse state persists</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script>
    // Test Console Helper
    const testConsole = {
        logs: [],
        log: function(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
            this.logs.push(logEntry);
            console.log(logEntry);
            this.updateDisplay();
        },
        updateDisplay: function() {
            $('#consoleOutput').text(this.logs.join('\n'));
        }
    };

    $(document).ready(function() {
        testConsole.log('Test suite initialized');

        // Test 1: SweetAlert
        $('#testAlert').on('click', function() {
            testConsole.log('SweetAlert test initiated');
            Swal.fire({
                title: 'Test SweetAlert',
                text: 'This is a test SweetAlert dialog',
                icon: 'info',
                confirmButtonColor: '#667eea',
                customClass: {
                    popup: 'swal-popup-custom',
                    confirmButton: 'swal-btn-custom'
                }
            }).then(() => {
                testConsole.log('SweetAlert dialog confirmed', 'success');
            });
        });

        // Test 2: Form Validation
        $('#testValidationForm').on('submit', function(e) {
            e.preventDefault();
            testConsole.log('Form validation test initiated');

            if (!this.checkValidity()) {
                testConsole.log('Form validation failed - required fields missing', 'warning');
                $(this).addClass('was-validated');
                return false;
            }

            testConsole.log('Form validation passed', 'success');
            $(this).removeClass('was-validated');
        });

        // Test 3: Button Loading State
        $('#testLoadingBtn').on('click', function(e) {
            e.preventDefault();
            testConsole.log('Button loading state test initiated');

            var btn = $(this);
            var originalText = btn.html();

            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Loading...');
            testConsole.log('Button disabled and spinner shown', 'success');

            setTimeout(function() {
                btn.prop('disabled', false);
                btn.html(originalText);
                testConsole.log('Button re-enabled after 2 seconds', 'success');
            }, 2000);
        });

        // Test 4: Double Submit Prevention
        let submitCount = 0;
        $('#testDoubleSubmitForm').on('submit', function(e) {
            e.preventDefault();
            submitCount++;
            testConsole.log(`Form submit attempt #${submitCount}`, 'info');

            var btn = $(this).find('button[type="submit"]');

            if (btn.prop('disabled')) {
                testConsole.log('Submit prevented - button already disabled', 'warning');
                return false;
            }

            btn.prop('disabled', true);
            testConsole.log('Form submitted (prevented actual submit)', 'success');

            setTimeout(function() {
                btn.prop('disabled', false);
                testConsole.log('Button re-enabled', 'success');
            }, 1500);
        });

        // Test 5: Collapse State
        $('#collapseTest').on('show.bs.collapse', function() {
            testConsole.log('Collapse shown', 'success');
            $('#collapseState').text('Shown');
            sessionStorage.setItem('#collapseTest', true);
        }).on('hide.bs.collapse', function() {
            testConsole.log('Collapse hidden', 'success');
            $('#collapseState').text('Hidden');
            sessionStorage.setItem('#collapseTest', false);
        });

        testConsole.log('All tests ready - click buttons to begin', 'success');
    });
</script>
