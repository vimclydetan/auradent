<?php
/**
 * Email Template: Cancellation Request Approved
 * 
 * Payload variables:
 *   $patient_name, $queue_number, $appointment_date, $appointment_time,
 *   $dentist_name, $patient_code
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
    <title>Cancellation Approved - <?= esc($clinic_name) ?></title>
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
                    <td style="background-color:#16a34a;padding:14px 40px;text-align:center;">
                        <p style="margin:0;color:#ffffff;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Cancellation Approved</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="background-color:#ffffff;padding:36px 40px;">

                        <!-- Greeting -->
                        <p style="margin:0 0 8px 0;color:#1e293b;font-size:18px;font-weight:700;">Hello, <?= esc($patient_name) ?>,</p>
                        <p style="margin:0 0 28px 0;color:#475569;font-size:14px;line-height:1.6;">
                            Your request to cancel your appointment at <strong><?= esc($clinic_name) ?></strong> has been <strong style="color:#16a34a;">approved</strong>. No further action is needed.
                        </p>

                        <!-- Cancelled Appointment Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:28px;">
                            <tr>
                                <td colspan="2" style="background-color:#f8fafc;padding:12px 20px;border-bottom:1px solid #e2e8f0;border-radius:6px 6px 0 0;">
                                    <p style="margin:0;color:#1e293b;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Cancelled Appointment</p>
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
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Original Date</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;"><?= esc($appointment_date) ?></td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Original Time</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;"><?= esc($appointment_time) ?></td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;color:#64748b;font-size:13px;border-bottom:1px solid #f1f5f9;">Dentist</td>
                                <td style="padding:12px 20px;color:#1e293b;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;"><?= esc($dentist_name) ?></td>
                            </tr>
                        </table>

                        <!-- Rebooking Section -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 10px 0;color:#0c4a6e;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Need to Book Again?</p>
                                    <p style="margin:0 0 16px 0;color:#0369a1;font-size:14px;line-height:1.5;">
                                        We're here whenever you're ready to schedule your next visit.
                                    </p>
                                    <a href="<?= base_url('appointments/book') ?>" 
                                       style="background-color:#0284c7;color:#ffffff!important;padding:10px 20px;border-radius:6px;font-weight:600;font-size:13px;text-decoration:none;display:inline-block;">
                                        Book a New Appointment
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Reminders -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fefce8;border:1px solid #fde68a;border-radius:6px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0 0 10px 0;color:#78350f;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Before Your Next Visit</p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <?php
                                        $reminders = [
                                            'Arrive 10 to 15 minutes before your scheduled time.',
                                            'Bring a valid ID and your patient code for faster check-in.',
                                            'Inform our staff of any medical conditions or allergies.',
                                            'For rescheduling, please contact us at least 24 hours in advance.',
                                        ];
                                        foreach ($reminders as $reminder):
                                        ?>
                                        <tr>
                                            <td style="padding:3px 0;color:#92400e;font-size:13px;line-height:1.5;">
                                                <!-- ✅ Bullet per user preference (no em-dash) -->
                                                <span style="color:#d97706;margin-right:8px;font-weight:700;">•</span><?= esc($reminder) ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Contact Info -->
                        <p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">
                            Questions? Contact us at
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