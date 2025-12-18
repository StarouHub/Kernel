<?php
// Moyasar Payment Callback Handler
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../controller/controller.php';

// Get payment data from Moyasar callback
$paymentId = $_GET['id'] ?? '';
$status = $_GET['status'] ?? '';
$message = $_GET['message'] ?? '';

// Get our custom parameters
$tenderId = $_GET['tender_id'] ?? 0;
$amount = $_GET['amount'] ?? 0;
$projectName = $_GET['project'] ?? 'Projet';

// Moyasar Secret Key for verification (optional - for webhook validation)
$moyasar_secret_key = 'mk_1Sd5XDIghmeylVfhoWBlCfym';

// Check payment status
if ($status === 'paid') {
    // Payment successful - save to database
    $controller = new InvestmentController();
    $result = $controller->createInvestment([
        'tenderId' => $tenderId,
        'projectName' => $projectName,
        'amount' => $amount,
        'roi' => 15,
        'sector' => 'Investissement',
        'paymentId' => $paymentId
    ]);
    
    // Redirect to success page
    header("Location: payment-success.php?tender_id=$tenderId&amount=$amount&project=" . urlencode($projectName) . "&method=moyasar&payment_id=$paymentId&status=success");
    exit;
} else if ($status === 'failed') {
    // Payment failed
    header("Location: payment-failed.php?tender_id=$tenderId&amount=$amount&project=" . urlencode($projectName) . "&error=" . urlencode($message));
    exit;
} else {
    // Unknown status or pending
    header("Location: payment.php?tender_id=$tenderId&amount=$amount&project=" . urlencode($projectName) . "&error=pending");
    exit;
}
?>
