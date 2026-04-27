<?php
/**
 * Email Template: Appointment Request Rejected
 * 
 * Payload variables (from controller/DB):
 *   $patient_name, $appointment_date, $appointment_time, $dentist_name,
 *   $patient_code, $rejection_reason
 * 
 * Clinic data: Automatically loaded from Config\Clinic
 */

// 📦 Load email helper
helper('email');

// 🏥 Load clinic config
$clinic = config('Clinic');

// 👤 Appointment-specific variables (with fallbacks)
$patient_name     = $patient_name     ?? 'Valued Patient';
$appointment_date = $appointment_date ?? 'N/A';
$appointment_time = $appointment_time ?? 'N/A';
$dentist_name     = $dentist_name     ?? 'TBD';
$patient_code     = $patient_code     ?? 'N/A';
$rejection_reason = $rejection_reason ?? 'The requested time slot is no longer available.';

// 🏢 Clinic data — from Config\Clinic
$clinic_name    = $clinic->name;
$clinic_phone   = $clinic->phone;
$clinic_email   = $clinic->email;
$clinic_address = $clinic->address;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Appointment Request Update - <?= esc($clinic_name) ?></title>
    <style type="text/css">
        .preheader { display:none !important; visibility:hidden; mso-hide:all; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content-pad { padding: 24px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">

<!-- Preheader for inbox preview -->
<div class="preheader">
    Your appointment request at <?= esc($clinic_name) ?> has been declined. Please review the reason and next steps.
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:32px 16px;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table class="container" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#1e3a5f;border-radius:8px 8px 0 0;padding:28px 40px;text-align:center;">
                        <p style="margin:0 0 4px 0;color:#93c5fd;font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;">Dental Care</p>
                        <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:0.5px;"><?= esc($clinic_name) ?></h1>
                    </td>
                </tr>

                <!-- Status Banner -->
                <tr>
                    <td style="background-color:#dc2626;padding:14px 40px;text-align:center;">
                        <p style="margin:0;color:#ffffff;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Request Declined</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="content-pad" style="background-color:#ffffff;padding:36px 40px;">

                        <!-- Greeting -->
                        <p style="margin:0 0 8px 0;color:#1e293b;font-size:18px;font-weight:700;">Hello, <?= esc($patient_name) ?>,</p>
                        <p style="margin:0 0 24px 0;color:#475569;font-size:14px;line-height:1.6;">
                            We regret to inform you that your appointment request has been <strong style="color:#dc2626;">declined</strong>. 
                            Please review the details and reason below.
                        </p>

                        <!-- Request Details Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:8px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px;">
                                    <h3 style="margin:0 0 16px 0;color:#991b1b;font-size:16px;font-weight:600;border-bottom:1px solid #fecaca;padding-bottom:12px;">
                                        Request Details
                                    </h3>
                                    
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:8px 0;width:45%;color:#64748b;font-size:13px;">Patient Code</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;font-family:monospace;"><?= esc($patient_code) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Requested Date</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($appointment_date) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Requested Time</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($appointment_time) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Preferred Dentist</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($dentist_name) ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Reason for Rejection -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff1f2;border-left:4px solid #dc2626;border-radius:0 8px 8px 0;margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 6px 0;color:#991b1b;font-size:13px;font-weight:700;">• Reason for Decline</p>
                                    <p style="margin:0;color:#64748b;font-size:14px;line-height:1.6;"><?= esc($rejection_reason) ?></p>
                                </td>
                            </tr>
                        </table>

                        <!-- Next Steps -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <p style="margin:0 0 8px 0;color:#1e293b;font-size:15px;font-weight:600;">What You Can Do Next</p>
                                    <p style="margin:0 0 16px 0;color:#475569;font-size:14px;line-height:1.6;">
                                        • Submit a new appointment request with a different date or time<br>
                                        • Choose from our available dentists for faster scheduling<br>
                                        • Call our clinic directly for immediate assistance
                                    </p>
                                    <a href="<?= base_url('appointments/book') ?>" style="background-color:#2563eb;color:#ffffff!important;padding:12px 24px;border-radius:6px;font-weight:600;font-size:14px;text-decoration:none;display:inline-block;">
                                        Book a New Appointment
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Contact Info -->
                        <p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">
                            We apologize for any inconvenience. If you need immediate assistance, contact us at 
                            <a href="tel:<?= format_phone_for_link($clinic_phone) ?>" style="color:#2563eb;font-weight:600;text-decoration:none;">
                                <?= esc($clinic_phone) ?>
                            </a>
                            or visit us at <?= esc($clinic_address) ?>.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f8fafc;border-top:1px solid #e2e8f0;border-radius:0 0 8px 8px;padding:20px 40px;text-align:center;">
                        <p style="margin:0 0 4px 0;color:#94a3b8;font-size:12px;"><?= esc($clinic_name) ?> &nbsp;|&nbsp; <?= esc($clinic_address) ?></p>
                        <p style="margin:0;color:#cbd5e1;font-size:11px;">This is an automated message. Please do not reply directly to this email.</p>
                    </td>
                </tr>

            </table>
            <!-- End Container -->

        </td>
    </tr>
</table>

</body>
</html>