<?php
/**
 * Email Template: 24-Hour Appointment Reminder
 * 
 * Payload variables (from controller/DB):
 *   $patient_name, $queue_number, $appointment_date, $appointment_time,
 *   $dentist_name, $patient_code
 * 
 * Clinic  Automatically loaded from Config\Clinic
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
$patient_code     = $patient_code     ?? 'N/A';

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
    <title>Appointment Reminder - <?= esc($clinic_name) ?></title>
    <style type="text/css">
        .preheader { display:none !important; visibility:hidden; mso-hide:all; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content-pad { padding: 24px !important; }
            .btn { display: block !important; width: 100% !important; text-align: center !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">

<!-- Preheader for inbox preview -->
<div class="preheader">
    Reminder: Your appointment at <?= esc($clinic_name) ?> is tomorrow at <?= esc($appointment_time) ?>.
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
                    <td style="background-color:#3b82f6;padding:14px 40px;text-align:center;">
                        <p style="margin:0;color:#ffffff;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Appointment Reminder</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="content-pad" style="background-color:#ffffff;padding:36px 40px;">

                        <!-- Greeting -->
                        <p style="margin:0 0 8px 0;color:#1e293b;font-size:18px;font-weight:700;">Hello, <?= esc($patient_name) ?>,</p>
                        <p style="margin:0 0 24px 0;color:#475569;font-size:14px;line-height:1.6;">
                            This is a friendly reminder that you have an appointment scheduled at 
                            <strong><?= esc($clinic_name) ?></strong> tomorrow.
                        </p>

                        <!-- Appointment Details Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px;">
                                    <h3 style="margin:0 0 16px 0;color:#1e40af;font-size:16px;font-weight:600;border-bottom:1px solid #bfdbfe;padding-bottom:12px;">
                                        Your Appointment Details
                                    </h3>
                                    
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:8px 0;width:45%;color:#64748b;font-size:13px;">Patient Code</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;font-family:monospace;"><?= esc($patient_code) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Queue Number</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;font-family:monospace;">#<?= esc($queue_number) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Date</td>
                                            <td style="padding:8px 0;color:#1e293b;font-size:14px;font-weight:600;"><?= esc($appointment_date) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 0;color:#64748b;font-size:13px;">Time</td>
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

                        <!-- Pre-Visit Checklist -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fefce8;border:1px solid #fde68a;border-radius:8px;margin-bottom:24px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 10px 0;color:#78350f;font-size:13px;font-weight:700;">• Before Your Visit</p>
                                    <p style="margin:0;color:#92400e;font-size:13px;line-height:1.8;">
                                        • Arrive 10 to 15 minutes early for check-in<br>
                                        • Bring a valid ID and your queue number<br>
                                        • Inform staff of any medical conditions or allergies<br>
                                        • Avoid eating 30 minutes before cleaning procedures
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Reschedule/Cancel Notice -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <p style="margin:0 0 8px 0;color:#1e293b;font-size:15px;font-weight:600;">Need to Reschedule or Cancel?</p>
                                    <p style="margin:0 0 16px 0;color:#475569;font-size:14px;line-height:1.6;">
                                        If you can no longer attend, please contact us at least 
                                        <strong>24 hours in advance</strong> to avoid no-show fees 
                                        and allow other patients to book the slot.
                                    </p>
                                    <a href="tel:<?= format_phone_for_link($clinic_phone) ?>" 
                                       class="btn"
                                       style="background-color:#2563eb;color:#ffffff!important;padding:12px 24px;border-radius:6px;font-weight:600;font-size:14px;text-decoration:none;display:inline-block;">
                                        Call Us Now
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Contact Info -->
                        <p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">
                            Questions? Contact us at 
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
                        <p style="margin:0;color:#cbd5e1;font-size:11px;">This is an automated reminder. Please do not reply directly to this email.</p>
                    </td>
                </tr>

            </table>
            <!-- End Container -->

        </td>
    </tr>
</table>

</body>
</html>