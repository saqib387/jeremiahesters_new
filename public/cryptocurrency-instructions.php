<?php
// Display cryptocurrency data installation instructions
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cryptocurrency Test Data Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-block {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .method-card {
            transition: transform 0.2s;
        }
        .method-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Cryptocurrency Test Data Installation</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Note:</strong> This page provides instructions for installing cryptocurrency test data in your application.
                        </div>
                        
                        <h3 class="mt-4">Available Methods</h3>
                        <div class="row">
                            <!-- Web Route Method -->
                            <div class="col-md-6 mb-4">
                                <div class="card method-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <span class="badge bg-success me-2">1</span>
                                            Web Route (Easiest)
                                        </h5>
                                        <p class="card-text">Simply visit the following URL in your browser after logging in:</p>
                                        <div class="code-block"><?php echo htmlspecialchars('/seed-cryptocurrency'); ?></div>
                                        <p class="mb-0 text-muted">This will insert the sample data and show you a confirmation page.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Artisan Command Method -->
                            <div class="col-md-6 mb-4">
                                <div class="card method-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <span class="badge bg-success me-2">2</span>
                                            Artisan Command
                                        </h5>
                                        <p class="card-text">Run the following command from your terminal in the project root:</p>
                                        <div class="code-block"><?php echo htmlspecialchars('php artisan seed:cryptocurrency'); ?></div>
                                        <p class="mb-0 text-muted">This will insert the sample data and show progress in the terminal.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PHP Script Method -->
                            <div class="col-md-6 mb-4">
                                <div class="card method-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <span class="badge bg-primary me-2">3</span>
                                            PHP Script
                                        </h5>
                                        <p class="card-text">Execute the following PHP script:</p>
                                        <div class="code-block"><?php echo htmlspecialchars('php crypto_sample_data.php'); ?></div>
                                        <p class="mb-0 text-muted">This PHP script will connect to the database and insert sample records.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SQL Script Method -->
                            <div class="col-md-6 mb-4">
                                <div class="card method-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <span class="badge bg-primary me-2">4</span>
                                            SQL Script
                                        </h5>
                                        <p class="card-text">Execute the SQL script directly in your database:</p>
                                        <div class="code-block"><?php echo htmlspecialchars('mysql -u your_username -p your_database < crypto_sample_sql.sql'); ?></div>
                                        <p class="mb-0 text-muted">You can also copy and paste the contents of crypto_sample_sql.sql into your database management tool.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="mt-4">Test the UI</h3>
                        <p>You can test the cryptocurrency UI components by visiting:</p>
                        <div class="code-block"><a href="/cryptocurrency/test-ui">/cryptocurrency/test-ui</a></div>
                        <p>This test page doesn't require database access and will help verify that all cryptocurrency UI components and JavaScript functionality are working correctly.</p>
                        
                        <h3 class="mt-4">Troubleshooting</h3>
                        <div class="accordion" id="troubleshootingAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Database Connection Issues
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#troubleshootingAccordion">
                                    <div class="accordion-body">
                                        <p>If you're experiencing database connection issues:</p>
                                        <ol>
                                            <li>Verify your database connection settings in <code>.env</code> file</li>
                                            <li>Make sure the database server is running</li>
                                            <li>Ensure the database exists and user has proper permissions</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Missing Tables
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#troubleshootingAccordion">
                                    <div class="accordion-body">
                                        <p>If you're getting errors about missing tables:</p>
                                        <ol>
                                            <li>Make sure all migrations are run: <code>php artisan migrate</code></li>
                                            <li>Check if cryptocurrencies, crypto_wallets, and crypto_transactions tables exist</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="d-flex justify-content-between">
                            <div>Cryptocurrency Module Documentation</div>
                            <div>See <code>cryptocurrency-readme.md</code> for more details</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 