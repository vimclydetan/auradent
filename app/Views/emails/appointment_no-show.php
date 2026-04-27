<?php
/**
 * Email Template: Appointment No-Show
 * 
 * Payload variables (from controller/DB):
 *   $patient_name, $queue_number, $appointment_date, $appointment_time,
 *   $dentist_name, $patient_code
 * 
 * Clinic data: Automatically loaded from Config\Clinic
 */

// 📦 Load email helper for format_phone_for_link()
helper('email');

// 🏥 Load clinic config
$clinic = config('Clinic');

// 👤 Appointment-specific variables (with fallbacks)
$patient_name     = $patient_name     ?? 'Valued Patient';
$queue_number     = $queue_number     ?? 'N/A';
$appointment_date = $appointment_date ?? 'N/A';
$appointment_time = $appointment_time ?? 'N/A';
$dentist_name     = $dentist_name     ?? 'TBD';
$patient_code     = $patient_code     ?? 'N/A';  // ✅ Replaces appointment_id

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
    <title>Appointment Status Update - <?= esc($clinic_name) ?></title>
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
    We noticed you missed your appointment at <?= esc($clinic_name) ?>. Learn how to reschedule.
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
                    <td style="background-color:#f59e0b;padding:14px 40px;text-align:center;">
                        <p style="margin:0;color:#ffffff;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Appointment No-Show</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="content-pad" style="background-color:#ffffff;padding:36px 40px;">

                        <!-- Greeting -->
                        <p style="margin:0 0 8px 0;color:#1e293b;font-size:18px;font-weight:700;">Hello, <?= esc($patient_name) ?>,</p>
                        <p style="margin:0 0 24px 0;color:#475569;font-size:14px;line-height:1.6;">
                            We noticed you were unable to attend your scheduled appointment. 
                            Your appointment has been marked as <strong style="color:#f59e0b;">No-Show</strong>.
                        </p>

                        <!-- Missed Appointment Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffbeb;border:1px solid #fcd34d;border-radius:8px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px;">
                                    <h3 style="margin:0 0 16px 0;color:#92400e;font-size:16px;font-weight:600;border-bottom:1px solid #fcd34d;padding-bottom:12px;">
                                        Missed Appointment Details
                                    </h3>
                                    
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:8px 0;width:45%;color:#64748b;font-size:13px;">Queue Number</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;font-family:monospace;">#<?= esc($queue_number) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Patient Code</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;font-family:monospace;"><?= esc($patient_code) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Scheduled Date</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($appointment_date) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Scheduled Time</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($appointment_time) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Dentist</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($dentist_name) ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Policy Notice -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fefce8;border:1px solid #fef08a;border-radius:8px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 8px 0;color:#854d0e;font-size:13px;font-weight:700;">• Important Notice</p>
                                    <p style="margin:0;color:#713f12;font-size:13px;line-height:1.6;">
                                        Repeated no-shows may affect your ability to book future appointments. 
                                        If you cannot attend, please cancel or reschedule at least <strong>24 hours</strong> in advance.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Rebooking Section -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <p style="margin:0 0 8px 0;color:#1e293b;font-size:15px;font-weight:600;">Ready to Reschedule?</p>
                                    <p style="margin:0 0 16px 0;color:#475569;font-size:14px;line-height:1.6;">
                                        We're still here to provide the care you need. Book your next appointment at your convenience.
                                    </p>
                                    <!-- Note: Links in emails may have limited styling support -->
                                    <a href="<?= base_url('/') ?>" style="background-color:#2563eb;color:#ffffff!important;padding:12px 24px;border-radius:6px;font-weight:600;font-size:14px;text-decoration:none;display:inline-block;">
                                        Schedule a New Appointment
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Contact Info -->
                        <p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">
                            If you missed your appointment due to an emergency, please contact us at 
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