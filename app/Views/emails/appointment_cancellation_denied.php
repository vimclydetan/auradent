<?php
/**
 * Email Template: Cancellation Request Denied
 * 
 * Payload variables:
 *   $patient_name, $queue_number, $appointment_date, $appointment_time,
 *   $dentist_name, $patient_code, $denial_reason
 * 
 * Clinic data: Loaded from Config\Clinic
 */

helper('email');
$clinic = config('Clinic');

// 👤 Fallbacks for safety
$patient_name     = $patient_name     ?? 'Valued Patient';
$queue_number     = $queue_number     ?? 'N/A';
$appointment_date = $appointment_date ?? 'N/A';
$appointment_time = $appointment_time ?? 'N/A';
$dentist_name     = $dentist_name     ?? 'TBD';
$patient_code     = $patient_code     ?? 'N/A';
$denial_reason    = $denial_reason    ?? 'Cancellation requests must be submitted at least 24 hours before the scheduled appointment.';

// 🏢 Clinic config
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
    <title>Cancellation Request Update - <?= esc($clinic_name) ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:32px 16px;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

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
                        <p style="margin:0;color:#ffffff;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Cancellation Request Denied</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="background-color:#ffffff;padding:36px 40px;">

                        <!-- Greeting -->
                        <p style="margin:0 0 8px 0;color:#1e293b;font-size:18px;font-weight:700;">Hello, <?= esc($patient_name) ?>,</p>
                        <p style="margin:0 0 28px 0;color:#475569;font-size:14px;line-height:1.6;">
                            Regarding your request to cancel your appointment at <strong><?= esc($clinic_name) ?></strong>, we regret to inform you that it has been <strong style="color:#dc2626;">declined</strong>.
                        </p>

                        <!-- Appointment Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:28px;">
                            <tr>
                                <td colspan="2" style="background-color:#f8fafc;padding:12px 20px;border-bottom:1px solid #e2e8f0;border-radius:6px 6px 0 0;">
                                    <p style="margin:0;color:#1e293b;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Appointment Details</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;width:40%;border-bottom:1px solid #f1f5f9;">Queue Number</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:700;border-bottom:1px solid #f1f5f9;font-family:monospace;">
                                    #<?= esc($queue_number) ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Patient Code</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;font-family:monospace;">
                                    <?= esc($patient_code) ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Scheduled Date</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;"><?= esc($appointment_date) ?></td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Scheduled Time</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;"><?= esc($appointment_time) ?></td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Dentist</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;"><?= esc($dentist_name) ?></td>
                            </tr>
                        </table>

                        <!-- Denial Reason Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff1f2;border:1px solid #fecdd3;border-left:4px solid #dc2626;border-radius:6px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 8px 0;color:#991b1b;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Reason for Denial</p>
                                    <p style="margin:0;color:#64748b;font-size:14px;line-height:1.6;">
                                        <?= esc($denial_reason) ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Your Options -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 10px 0;color:#0c4a6e;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Your Options</p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <?php
                                        $options = [
                                            'Keep your current appointment as scheduled.',
                                            'Request to reschedule to a different date/time.',
                                            'Contact our clinic to discuss your situation.',
                                        ];
                                        foreach ($options as $option):
                                        ?>
                                        <tr>
                                            <td style="padding:3px 0;color:#0369a1;font-size:13px;line-height:1.5;">
                                                <!-- ✅ Bullet per user preference (no em-dash) -->
                                                <span style="color:#0284c7;margin-right:8px;font-weight:700;">•</span><?= esc($option) ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Action Buttons -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="padding:4px 0;width:50%;padding-right:8px;">
                                    <a href="<?= base_url('appointments/view/' . ($appointment_id ?? '#')) ?>" 
                                       style="background-color:#2563eb;color:#ffffff!important;padding:10px 20px;border-radius:6px;font-weight:600;font-size:13px;text-decoration:none;display:inline-block;width:100%;text-align:center;box-sizing:border-box;">
                                        View Appointment
                                    </a>
                                </td>
                                <td style="padding:4px 0;width:50%;padding-left:8px;">
                                    <a href="<?= base_url('appointments/reschedule/' . ($appointment_id ?? '#')) ?>" 
                                       style="background-color:#64748b;color:#ffffff!important;padding:10px 20px;border-radius:6px;font-weight:600;font-size:13px;text-decoration:none;display:inline-block;width:100%;text-align:center;box-sizing:border-box;">
                                        Reschedule Instead
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Contact Info -->
                        <p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">
                            We understand that circumstances change. If you need assistance, contact us at
                            <a href="tel:<?= format_phone_for_link($clinic_phone) ?>" 
                               style="color:#1e3a5f;font-weight:600;text-decoration:none;">
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